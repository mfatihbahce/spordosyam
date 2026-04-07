<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassCancellation;
use App\Models\ClassModel;
use App\Models\Coach;
use App\Models\MakeupClass;
use App\Models\MakeupSession;
use App\Models\Student;
use App\Models\StudentMakeupClass;
use App\Services\ClassScheduleService;
use App\Services\SmsNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ClassCancellationController extends Controller
{
    public function __construct(
        protected ClassScheduleService $scheduleService
    ) {}
    /**
     * Bekleyen telafi dersleri listesi
     */
    public function index()
    {
        $schoolId = Auth::user()->school_id;
        $school = Auth::user()->school;
        
        $makeupEnabled = $school && $school->makeup_class_enabled;

        // Bekleyen ve telafi atanmış dersler (yeni tarih görünsün diye scheduled da dahil)
        $cancellations = ClassCancellation::where('school_id', $schoolId)
            ->whereIn('status', ['pending', 'scheduled'])
            ->with(['classModel', 'cancelledBy'])
            ->orderBy('original_date', 'desc')
            ->paginate(15);

        $pendingCounts = [];
        if ($makeupEnabled) {
            $cancellationIds = $cancellations->pluck('id');
            $makeupClassIds = MakeupClass::whereIn('cancellation_id', $cancellationIds)->pluck('id');
            $pendingCounts = \Illuminate\Support\Facades\DB::table('student_makeup_classes')
                ->whereIn('makeup_class_id', $makeupClassIds)
                ->whereNull('makeup_session_id')
                ->join('makeup_classes', 'makeup_classes.id', '=', 'student_makeup_classes.makeup_class_id')
                ->selectRaw('makeup_classes.cancellation_id, count(*) as pending_count')
                ->groupBy('makeup_classes.cancellation_id')
                ->pluck('pending_count', 'cancellation_id');

            $cancellations->each(function ($c) use ($pendingCounts) {
                $c->pending_students_count = (int) ($pendingCounts[$c->id] ?? 0);
                $c->hide_from_list = $c->pending_students_count === 0;
            });
            $cancellations->setCollection($cancellations->getCollection()->reject(fn ($c) => $c->hide_from_list ?? false));
        } else {
            $cancellations->each(function ($c) {
                $c->pending_students_count = 0;
                $c->hide_from_list = false;
            });
        }

        return view('admin.class-cancellations.index', compact('cancellations', 'makeupEnabled'));
    }

    /**
     * Telafi Ekle sayfası (detay sayfası olarak)
     */
    public function addMakeupForm(ClassCancellation $classCancellation)
    {
        $schoolId = Auth::user()->school_id;
        $school = Auth::user()->school;
        if ($classCancellation->school_id !== $schoolId) {
            abort(403);
        }
        if (!$school || !$school->makeup_class_enabled) {
            abort(403, 'Telafi dersi özelliği aktif değil.');
        }
        if ($classCancellation->status !== 'pending') {
            return redirect()->route('admin.class-cancellations.index')
                ->with('info', 'Bu kayıt için telafi zaten planlandı.');
        }

        $makeupClassIds = MakeupClass::where('cancellation_id', $classCancellation->id)->pluck('id');
        $totalStudents = StudentMakeupClass::whereIn('makeup_class_id', $makeupClassIds)->count();
        $toBeAddedCount = StudentMakeupClass::whereIn('makeup_class_id', $makeupClassIds)
            ->whereNull('makeup_session_id')
            ->count();

        $coaches = Coach::where('school_id', $schoolId)->where('is_active', true)->with('user')->get();
        $classCancellation->load('classModel');

        return view('admin.class-cancellations.add-makeup', compact('classCancellation', 'coaches', 'totalStudents', 'toBeAddedCount'));
    }

    /**
     * İptal kaydı için öğrenci sayıları (AJAX - ihtiyaç halinde)
     */
    public function cancellationStats(ClassCancellation $classCancellation)
    {
        $schoolId = Auth::user()->school_id;
        if ($classCancellation->school_id !== $schoolId) {
            abort(403);
        }

        $makeupClassIds = MakeupClass::where('cancellation_id', $classCancellation->id)->pluck('id');
        $totalStudents = StudentMakeupClass::whereIn('makeup_class_id', $makeupClassIds)->count();
        $toBeAddedCount = StudentMakeupClass::whereIn('makeup_class_id', $makeupClassIds)
            ->whereNull('makeup_session_id')
            ->count();

        return response()->json([
            'total_students' => $totalStudents,
            'to_be_added_count' => $toBeAddedCount,
        ]);
    }

    /**
     * Bekleyen öğrenciler listesi (Detay popup için, sadece telafi bekleyenler)
     */
    public function waitingStudents(ClassCancellation $classCancellation)
    {
        $schoolId = Auth::user()->school_id;
        if ($classCancellation->school_id !== $schoolId) {
            abort(403);
        }

        $makeupClassIds = MakeupClass::where('cancellation_id', $classCancellation->id)->pluck('id');
        $items = StudentMakeupClass::whereIn('makeup_class_id', $makeupClassIds)
            ->whereNull('makeup_session_id')
            ->with(['student.parents.user'])
            ->get();

        $students = $items->map(function ($sm) {
            $s = $sm->student;
            $parent = $s ? $s->parents->sortByDesc(fn ($p) => $p->pivot->is_primary ? 1 : 0)->first() : null;
            return [
                'id' => $s?->id,
                'name' => $s ? trim($s->first_name . ' ' . $s->last_name) : '-',
                'parent_name' => $parent?->user?->name ?? '-',
                'parent_phone' => $parent?->phone ?? '-',
            ];
        });

        return response()->json([
            'class_name' => $classCancellation->classModel->name ?? '',
            'students' => $students->values()->all(),
        ]);
    }

    /**
     * Tarih/saat çakışma kontrolü (anlık popup için). Antrenör çakışması ayrı döner.
     */
    public function checkScheduleConflict(Request $request)
    {
        $schoolId = Auth::user()->school_id;
        $school = Auth::user()->school;
        if (!$school || !$school->makeup_class_enabled) {
            return response()->json(['conflict' => false, 'coach_conflict' => false]);
        }

        $date = $request->input('date');
        $startTime = $request->input('start_time');
        $endTime = $request->input('end_time');
        $coachId = $request->input('coach_id');
        if (!$date || !$startTime || !$endTime) {
            return response()->json(['conflict' => false, 'coach_conflict' => false, 'message' => null]);
        }

        $coachConflict = false;
        if ($coachId && Coach::where('id', $coachId)->where('school_id', $schoolId)->exists()) {
            $coachConflict = $this->scheduleService->hasCoachConflict(
                $schoolId,
                (int) $coachId,
                $date,
                $startTime,
                $endTime,
                null
            );
        }

        $conflict = $this->scheduleService->hasScheduleConflict(
            $schoolId,
            $date,
            $startTime,
            $endTime,
            null
        );

        if ($coachConflict) {
            return response()->json([
                'conflict' => true,
                'coach_conflict' => true,
                'message' => 'Seçilen antrenörün bu tarih ve saatte başka dersi var. Yoklama için farklı antrenör veya slot seçin.',
            ]);
        }
        if ($conflict) {
            return response()->json([
                'conflict' => true,
                'coach_conflict' => false,
                'message' => 'Bu tarih ve saatte başka bir ders veya telafi oturumu var. Yine de oluşturmak isterseniz onaylayabilirsiniz.',
            ]);
        }
        return response()->json(['conflict' => false, 'coach_conflict' => false, 'message' => null]);
    }

    /**
     * İptal kaydından telafi dersi oluştur (yeni tarih/saat/antrenör)
     */
    public function storeMakeupFromCancellation(Request $request, ClassCancellation $classCancellation)
    {
        $schoolId = Auth::user()->school_id;
        $school = Auth::user()->school;
        if ($classCancellation->school_id !== $schoolId) {
            abort(403);
        }
        if (!$school || !$school->makeup_class_enabled) {
            abort(403, 'Telafi dersi özelliği aktif değil.');
        }

        $validated = $request->validate([
            'scheduled_date' => 'required|date|after_or_equal:today',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'coach_id' => 'required|exists:coaches,id',
        ], [
            'end_time.after' => 'Bitiş saati, başlangıç saatinden sonra olmalıdır. Örneğin başlangıç 23:00 ise bitiş 10:00 olamaz.',
        ]);

        if ($validated['scheduled_date'] === now()->toDateString()) {
            $nowMinute = now()->format('H:i');
            if ($validated['start_time'] < $nowMinute) {
                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Bugün için geçmiş saat seçilemez. Başlangıç saati şu anki saatten ileri olmalıdır.',
                        'errors' => ['start_time' => ['Geçmiş saat seçilemez.']],
                    ], 422);
                }
                return redirect()->back()->withInput()->withErrors(['start_time' => 'Bugün için geçmiş saat seçilemez.']);
            }
        }

        Coach::where('id', $validated['coach_id'])->where('school_id', $schoolId)->firstOrFail();

        $coachConflict = $this->scheduleService->hasCoachConflict(
            $schoolId,
            (int) $validated['coach_id'],
            $validated['scheduled_date'],
            $validated['start_time'],
            $validated['end_time'],
            null
        );
        if ($coachConflict) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Seçilen antrenörün bu tarih ve saatte başka dersi var. Farklı antrenör veya farklı saat seçin.',
                    'errors' => ['coach_id' => ['Antrenör bu saatte başka derste.']],
                ], 422);
            }
            return redirect()->back()
                ->withInput()
                ->withErrors(['coach_id' => 'Seçilen antrenörün bu tarih ve saatte başka dersi var. Farklı antrenör veya farklı saat seçin.']);
        }

        $generalConflict = $this->scheduleService->hasScheduleConflict(
            $schoolId,
            $validated['scheduled_date'],
            $validated['start_time'],
            $validated['end_time'],
            null
        );
        $forceCreate = $request->boolean('force_create');
        if ($generalConflict && !$forceCreate) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bu tarih ve saatte başka bir ders var. Yine de oluşturmak için onaylayın.',
                    'require_confirm' => true,
                    'errors' => ['scheduled_date' => ['Çakışma var. Onayla oluşturabilirsiniz.']],
                ], 422);
            }
            return redirect()->back()
                ->withInput()
                ->withErrors(['scheduled_date' => 'Bu tarih ve saatte başka bir ders var. Onayla oluşturabilirsiniz.']);
        }

        $validated['school_id'] = $schoolId;
        $className = $classCancellation->classModel->name ?? '';
        $validated['name'] = 'Telafi Dersi - ' . ($className ? $className . ' - ' : '') . \Carbon\Carbon::parse($validated['scheduled_date'])->format('d.m.Y') . ' ' . $validated['start_time'];

        $makeupSession = MakeupSession::create($validated);

        $makeupClassIds = MakeupClass::where('cancellation_id', $classCancellation->id)->pluck('id');
        $pendingStudentMakeups = StudentMakeupClass::whereIn('makeup_class_id', $makeupClassIds)
            ->whereNull('makeup_session_id')
            ->get();

        foreach ($pendingStudentMakeups as $sm) {
            $sm->update([
                'makeup_session_id' => $makeupSession->id,
                'scheduled_date' => $makeupSession->scheduled_date,
                'status' => 'scheduled',
            ]);
            $sm->makeupClass?->update([
                'scheduled_date' => $makeupSession->scheduled_date,
                'status' => 'scheduled',
            ]);
        }

        $classCancellation->update(['status' => 'scheduled', 'new_date' => $validated['scheduled_date']]);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Telafi dersi oluşturuldu. ' . $pendingStudentMakeups->count() . ' öğrenci eklendi.',
                'redirect' => route('admin.makeup-sessions.show', $makeupSession),
            ]);
        }
        return redirect()->route('admin.makeup-sessions.show', $makeupSession)
            ->with('success', 'Telafi dersi oluşturuldu. ' . $pendingStudentMakeups->count() . ' öğrenci eklendi.');
    }

    /**
     * Ders iptal/erteleme formu
     */
    public function create()
    {
        $schoolId = Auth::user()->school_id;
        $school = Auth::user()->school;

        $classes = ClassModel::where('school_id', $schoolId)
            ->where('is_active', true)
            ->get();
        
        // Takvim için event'leri hazırla
        $calendarEvents = $this->getCalendarEvents($schoolId);
        
        return view('admin.class-cancellations.create', compact('classes', 'calendarEvents'));
    }
    
    /**
     * Takvim için event'leri getir
     */
    private function getCalendarEvents($schoolId)
    {
        $classes = ClassModel::where('school_id', $schoolId)
            ->where('is_active', true)
            ->where(function($q) {
                $q->whereNull('end_date')
                  ->orWhere('end_date', '>=', now()->toDateString());
            })
            ->with(['sportBranch', 'branch', 'coach.user'])
            ->get();

        $events = [];
        $dayNames = [
            'monday' => 'Pazartesi',
            'tuesday' => 'Salı',
            'wednesday' => 'Çarşamba',
            'thursday' => 'Perşembe',
            'friday' => 'Cuma',
            'saturday' => 'Cumartesi',
            'sunday' => 'Pazar'
        ];

        foreach ($classes as $class) {
            if (!$class->class_days || !$class->class_schedule) {
                continue;
            }

            foreach ($class->class_days as $day) {
                $schedule = $class->class_schedule[$day] ?? null;
                if (!$schedule) {
                    continue;
                }

                $startTime = is_array($schedule) ? ($schedule['start_time'] ?? null) : $schedule;
                $endTime = is_array($schedule) ? ($schedule['end_time'] ?? null) : null;
                
                if (!$startTime) {
                    continue;
                }

                $startDate = now()->startOfWeek();
                $maxEndDate = $class->end_date ? min(now()->addMonths(3)->endOfWeek(), $class->end_date) : now()->addMonths(3)->endOfWeek();
                $endDate = $maxEndDate;

                $dayNumber = $this->getDayNumber($day);
                
                $currentDate = $startDate->copy();
                while ($currentDate->lte($endDate)) {
                    if ($currentDate->dayOfWeek === $dayNumber) {
                        $eventDate = $currentDate->format('Y-m-d');
                        // FullCalendar için ISO format: YYYY-MM-DDTHH:mm:ss
                        $startDateTime = $eventDate . 'T' . $startTime . ':00';
                        $endDateTime = $endTime ? ($eventDate . 'T' . $endTime . ':00') : null;

                        $timeOnly = date('H:i', strtotime($startTime));
                        $shortClassName = mb_strlen($class->name) > 20 ? mb_substr($class->name, 0, 20) . '...' : $class->name;
                        $classColor = $this->getClassColor($class->id);
                        
                        $event = [
                            'title' => $timeOnly . ' ' . $shortClassName,
                            'start' => $startDateTime,
                            'color' => $classColor,
                            'backgroundColor' => $classColor,
                            'borderColor' => $classColor,
                            'textColor' => '#ffffff',
                            'extendedProps' => [
                                'class_id' => $class->id,
                                'sport' => $class->sportBranch->name ?? '',
                                'branch' => $class->branch->name ?? '',
                                'coach' => $class->coach->user->name ?? '',
                                'students' => $class->students->count(),
                                'capacity' => $class->capacity,
                                'day' => $dayNames[$day] ?? $day,
                                'full_class_name' => $class->name,
                                'start_time' => $startTime,
                                'end_time' => $endTime,
                            ]
                        ];
                        
                        if ($endDateTime) {
                            $event['end'] = $endDateTime;
                        }
                        
                        $events[] = $event;
                    }
                    $currentDate->addDay();
                }
            }
        }

        return $events;
    }
    
    private function getDayNumber($day)
    {
        $days = [
            'monday' => 1,
            'tuesday' => 2,
            'wednesday' => 3,
            'thursday' => 4,
            'friday' => 5,
            'saturday' => 6,
            'sunday' => 0
        ];
        return $days[$day] ?? 1;
    }
    
    private function getClassColor($classId)
    {
        $colors = [
            '#3B82F6', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6',
            '#EC4899', '#06B6D4', '#F97316', '#84CC16', '#6366F1',
            '#14B8A6', '#F43F5E', '#A855F7', '#0EA5E9', '#22C55E',
        ];
        return $colors[($classId - 1) % count($colors)];
    }

    /**
     * Ders iptal/erteleme kaydet
     */
    public function store(Request $request)
    {
        $schoolId = Auth::user()->school_id;
        $school = Auth::user()->school;
        
        $isAjax = $request->wantsJson() || $request->ajax() || $request->header('X-Requested-With') === 'XMLHttpRequest';
        $makeupEnabled = $school && $school->makeup_class_enabled;

        try {
            $validated = $request->validate([
                'class_id' => 'required|exists:classes,id',
                'cancellation_type' => 'required|in:cancelled,postponed',
                'original_date' => 'required|date',
                'new_date' => 'nullable|date|after_or_equal:today',
                'reason' => 'nullable|string|max:500',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($isAjax) {
                return response()->json([
                    'success' => false,
                    'message' => 'Doğrulama hatası',
                    'errors' => $e->errors()
                ], 422);
            }
            throw $e;
        }
        
        // Sınıfın bu okula ait olduğunu kontrol et
        $class = ClassModel::where('id', $validated['class_id'])
            ->where('school_id', $schoolId)
            ->first();
        
        if (!$class) {
            if ($isAjax) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bu sınıf bulunamadı veya size ait değil.'
                ], 404);
            }
            abort(404, 'Bu sınıf bulunamadı.');
        }
        
        $validated['school_id'] = $schoolId;
        $validated['cancelled_by_user_id'] = Auth::id();
        $newDate = $validated['new_date'] ?? null;
        $validated['status'] = ($newDate && !empty($newDate)) ? 'scheduled' : 'pending';
        
        try {
            $cancellation = ClassCancellation::create($validated);

            if ($makeupEnabled) {
                // Telafi veriyorsa: telafi kaydı oluştur, öğrenci ders hakkından düşme
                if ($newDate) {
                    $this->createMakeupClassForCancellation($cancellation, $class, $newDate);
                } else {
                    $this->createPendingMakeupClasses($cancellation, $class);
                }
            } else {
                // Telafi vermiyorsa: bu sınıftaki mevcut kayıtların (enrollment) used_credits'ini 1 artır
                \App\Models\StudentClassHistory::where('class_id', $class->id)
                    ->whereNull('left_at')
                    ->increment('used_credits');
            }
        } catch (\Exception $e) {
            if ($isAjax) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bir hata oluştu: ' . $e->getMessage()
                ], 500);
            }
            throw $e;
        }

        // Ders iptali / erteleme SMS bildirimleri (superadmin ayarına göre)
        $class->load(['students.parents', 'coach']);
        $originalDate = $cancellation->original_date ? \Carbon\Carbon::parse($cancellation->original_date)->format('d.m.Y') : '';
        $newDateStr = $cancellation->new_date ? \Carbon\Carbon::parse($cancellation->new_date)->format('d.m.Y') : '';
        $className = $class->name ?? 'Ders';
        $smsService = app(SmsNotificationService::class);
        foreach ($class->students as $student) {
            $parent = $student->parents->first();
            if ($parent && !empty($parent->phone)) {
                $msg = "{$originalDate} {$className} dersi iptal." . ($newDateStr ? " Telafi: {$newDateStr}" : '') . " Spordosyam";
                $smsService->sendIfEnabled('class_cancelled', $parent->phone, $msg, $parent->user);
            }
        }
        if ($class->coach && !empty($class->coach->phone)) {
            $msg = "{$originalDate} {$className} dersi iptal edildi. Spordosyam";
            $smsService->sendIfEnabled('coach_class_cancelled', $class->coach->phone, $msg);
        }

        // AJAX isteği ise JSON döndür
        if ($isAjax) {
            return response()->json([
                'success' => true,
                'message' => 'Ders ' . ($validated['cancellation_type'] === 'cancelled' ? 'iptal' : 'ertelendi') . ' edildi.',
                'redirect' => route('admin.class-cancellations.index')
            ]);
        }
        
        return redirect()->route('admin.class-cancellations.index')
            ->with('success', 'Ders ' . ($validated['cancellation_type'] === 'cancelled' ? 'iptal' : 'ertelendi') . ' edildi.');
    }

    /**
     * Telafi dersi için yeni tarih belirle
     */
    public function update(Request $request, ClassCancellation $classCancellation)
    {
        $schoolId = Auth::user()->school_id;
        
        if ($classCancellation->school_id !== $schoolId) {
            abort(403);
        }
        
        $isAjax = $request->expectsJson() || $request->ajax() || $request->header('X-Requested-With') === 'XMLHttpRequest';
        
        try {
            $validated = $request->validate([
                'new_date' => 'required|date|after_or_equal:today',
                'scheduled_class_id' => 'nullable|exists:classes,id',
                'start_time' => 'nullable|date_format:H:i',
                'end_time' => 'nullable|date_format:H:i|after:start_time',
            ], [
                'end_time.after' => 'Bitiş saati, başlangıç saatinden sonra olmalıdır. Örneğin başlangıç 23:00 ise bitiş 10:00 olamaz.',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($isAjax) {
                return response()->json([
                    'success' => false,
                    'message' => 'Doğrulama hatası',
                    'errors' => $e->errors()
                ], 422);
            }
            throw $e;
        }
        
        $newDate = $validated['new_date'] ?? null;
        $scheduledClassId = $validated['scheduled_class_id'] ?? null;
        $startTime = $validated['start_time'] ?? null;
        $endTime = $validated['end_time'] ?? null;
        
        // Yeni tarih + saat ile telafi belirleniyorsa çakışma kontrolü
        if ($newDate && $startTime && $endTime) {
            $conflict = $this->scheduleService->hasScheduleConflict($schoolId, $newDate, $startTime, $endTime, null);
            if ($conflict) {
                if ($isAjax) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Bu tarih ve saatte başka bir ders var. Çakışma olmaması için farklı bir gün veya saat seçin.',
                        'errors' => ['new_date' => ['Seçilen tarih/saat başka bir dersle çakışıyor.']],
                    ], 422);
                }
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['new_date' => 'Bu tarih ve saatte başka bir ders var. Çakışma olmaması için farklı bir gün veya saat seçin.']);
            }
        }

        try {
            $classCancellation->update([
                'new_date' => $newDate,
                'status' => 'scheduled',
            ]);
            
            // Telafi dersini oluştur veya güncelle
            $class = $classCancellation->classModel;
            if ($scheduledClassId) {
                // Var olan bir derse telafi olarak ekle
                $this->assignMakeupToExistingClass($classCancellation, $class, $scheduledClassId);
            } else {
                // Yeni tarih için telafi dersi oluştur
                if ($newDate) {
                    // Eğer saat bilgisi verildiyse, yeni bir sınıf oluştur veya mevcut telafi derslerini güncelle
                    if ($startTime && $endTime) {
                        $this->createMakeupClassWithTime($classCancellation, $class, $newDate, $startTime, $endTime);
                    } else {
                        $this->createMakeupClassForCancellation($classCancellation, $class, $newDate);
                    }
                }
            }
            
            if ($isAjax) {
                return response()->json([
                    'success' => true,
                    'message' => 'Telafi dersi tarihi belirlendi.',
                    'redirect' => route('admin.class-cancellations.index')
                ]);
            }
            
            return redirect()->route('admin.class-cancellations.index')
                ->with('success', 'Telafi dersi tarihi belirlendi.');
        } catch (\Exception $e) {
            \Log::error('Error in update method: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            if ($isAjax) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bir hata oluştu: ' . $e->getMessage()
                ], 500);
            }
            
            throw $e;
        }
    }

    /**
     * İptal/erteleme detayı
     */
    public function show(ClassCancellation $classCancellation)
    {
        $schoolId = Auth::user()->school_id;
        
        if ($classCancellation->school_id !== $schoolId) {
            abort(403);
        }
        
        $classCancellation->load(['classModel', 'cancelledBy', 'makeupClasses.studentMakeupClasses.student']);
        
        // Takvim için event'leri hazırla (dashboard'daki gibi)
        $calendarEvents = $this->getCalendarEvents($schoolId);
        
        // Event formatı zaten getCalendarEvents'te düzeltildi, ek işlem gerekmiyor
        
        return view('admin.class-cancellations.show', compact('classCancellation', 'calendarEvents'));
    }
    
    /**
     * AJAX için takvim event'lerini getir (haftalık görünüm - tüm dersler)
     */
    public function getCalendarEventsForWeek(Request $request)
    {
        $schoolId = Auth::user()->school_id;
        $startDate = $request->input('start');
        $endDate = $request->input('end');
        
        if (!$startDate || !$endDate) {
            return response()->json(['events' => []]);
        }
        
        try {
            $startCarbon = Carbon::parse($startDate);
            $endCarbon = Carbon::parse($endDate);
            
            // Tüm aktif dersleri getir
            $classes = ClassModel::where('school_id', $schoolId)
                ->where('is_active', true)
                ->where(function($query) use ($endCarbon) {
                    $query->whereNull('end_date')
                          ->orWhere('end_date', '>=', $endCarbon->toDateString());
                })
                ->with(['sportBranch', 'branch', 'coach.user'])
                ->get();
            
            $events = [];
            $dayNames = [
                'monday' => 'Pazartesi',
                'tuesday' => 'Salı',
                'wednesday' => 'Çarşamba',
                'thursday' => 'Perşembe',
                'friday' => 'Cuma',
                'saturday' => 'Cumartesi',
                'sunday' => 'Pazar'
            ];
            
            foreach ($classes as $class) {
                if (!$class->class_days || !$class->class_schedule) {
                    continue;
                }
                
                foreach ($class->class_days as $day) {
                    $schedule = $class->class_schedule[$day] ?? null;
                    if (!$schedule || !is_array($schedule)) {
                        continue;
                    }
                    
                    $startTime = $schedule['start_time'] ?? null;
                    $endTime = $schedule['end_time'] ?? null;
                    
                    if (!$startTime) {
                        continue;
                    }
                    
                    $dayNumber = $this->getDayNumber($day);
                    
                    // Hafta içindeki bu güne denk gelen tarihleri bul
                    $currentDate = $startCarbon->copy();
                    while ($currentDate->lte($endCarbon)) {
                        if ($currentDate->dayOfWeek === $dayNumber) {
                            $eventDate = $currentDate->format('Y-m-d');
                            
                            // ISO format için datetime oluştur
                            $startDateTime = $currentDate->copy()->setTimeFromTimeString($startTime)->toIso8601String();
                            $endDateTime = $endTime ? $currentDate->copy()->setTimeFromTimeString($endTime)->toIso8601String() : null;
                            
                            $timeOnly = date('H:i', strtotime($startTime));
                            $shortClassName = mb_strlen($class->name) > 15 ? mb_substr($class->name, 0, 15) . '...' : $class->name;
                            $classColor = $this->getClassColor($class->id);
                            
                            $event = [
                                'title' => $timeOnly . ' ' . $shortClassName,
                                'start' => $startDateTime,
                                'color' => $classColor,
                                'backgroundColor' => $classColor,
                                'borderColor' => $classColor,
                                'textColor' => '#ffffff',
                                'extendedProps' => [
                                    'class_id' => $class->id,
                                    'sport' => $class->sportBranch->name ?? '',
                                    'branch' => $class->branch->name ?? '',
                                    'coach' => $class->coach->user->name ?? '',
                                    'students' => $class->students->count(),
                                    'capacity' => $class->capacity,
                                    'start_time' => $startTime,
                                    'end_time' => $endTime,
                                    'full_class_name' => $class->name,
                                    'day' => $dayNames[$day] ?? $day,
                                ]
                            ];
                            
                            if ($endDateTime) {
                                $event['end'] = $endDateTime;
                            } else {
                                // End time yoksa varsayılan olarak 1.5 saat ekle
                                $event['end'] = $currentDate->copy()->setTimeFromTimeString($startTime)->addHours(1)->addMinutes(30)->toIso8601String();
                            }
                            
                            $events[] = $event;
                        }
                        $currentDate->addDay();
                    }
                }
            }
            
            \Log::info('Calendar events for week:', ['start' => $startDate, 'end' => $endDate, 'count' => count($events)]);
            
            return response()->json(['events' => $events]);
        } catch (\Exception $e) {
            \Log::error('Error in getCalendarEventsForWeek: ' . $e->getMessage());
            return response()->json(['events' => [], 'error' => $e->getMessage()], 500);
        }
    }
    
    /**
     * AJAX için takvim event'lerini getir (belirli bir tarih için)
     */
    public function getCalendarEventsForDate(Request $request)
    {
        $schoolId = Auth::user()->school_id;
        $date = $request->input('date');
        
        if (!$date) {
            return response()->json(['events' => []]);
        }
        
        try {
            $carbonDate = Carbon::parse($date);
            $dayName = strtolower($carbonDate->format('l')); // monday, tuesday, etc.
            
            // O gün için aktif dersleri bul
            $classes = ClassModel::where('school_id', $schoolId)
                ->where('is_active', true)
                ->where(function($query) use ($carbonDate) {
                    $query->whereNull('end_date')
                          ->orWhere('end_date', '>=', $carbonDate->toDateString());
                })
                ->with(['sportBranch', 'branch', 'coach.user'])
                ->get()
                ->filter(function($class) use ($dayName) {
                    $schedule = $class->class_schedule ?? [];
                    return isset($schedule[$dayName]);
                });
            
            $events = [];
            foreach ($classes as $class) {
                $schedule = $class->class_schedule[$dayName] ?? null;
                if (!$schedule || !is_array($schedule)) {
                    continue;
                }
                
                $startTime = $schedule['start_time'] ?? null;
                $endTime = $schedule['end_time'] ?? null;
                
                if (!$startTime) {
                    continue;
                }
                
                // ISO format için datetime oluştur (FullCalendar için)
                $startDateTime = $carbonDate->copy()->setTimeFromTimeString($startTime)->toIso8601String();
                $endDateTime = $endTime ? $carbonDate->copy()->setTimeFromTimeString($endTime)->toIso8601String() : null;
                
                $timeOnly = date('H:i', strtotime($startTime));
                $shortClassName = mb_strlen($class->name) > 15 ? mb_substr($class->name, 0, 15) . '...' : $class->name;
                $classColor = $this->getClassColor($class->id);
                
                $event = [
                    'title' => $timeOnly . ' ' . $shortClassName,
                    'start' => $startDateTime,
                    'color' => $classColor,
                    'backgroundColor' => $classColor,
                    'borderColor' => $classColor,
                    'textColor' => '#ffffff',
                    'extendedProps' => [
                        'class_id' => $class->id,
                        'sport' => $class->sportBranch->name ?? '',
                        'branch' => $class->branch->name ?? '',
                        'coach' => $class->coach->user->name ?? '',
                        'students' => $class->students->count(),
                        'capacity' => $class->capacity,
                        'start_time' => $startTime,
                        'end_time' => $endTime,
                        'full_class_name' => $class->name,
                    ]
                ];
                
                if ($endDateTime) {
                    $event['end'] = $endDateTime;
                } else {
                    // End time yoksa varsayılan olarak 1.5 saat ekle
                    $event['end'] = $carbonDate->copy()->setTimeFromTimeString($startTime)->addHours(1)->addMinutes(30)->toIso8601String();
                }
                
                $events[] = $event;
            }
            
            \Log::info('Calendar events for date ' . $date . ':', ['count' => count($events), 'events' => $events]);
            
            return response()->json(['events' => $events]);
        } catch (\Exception $e) {
            \Log::error('Error in getCalendarEventsForDate: ' . $e->getMessage());
            return response()->json(['events' => [], 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * İptal/erteleme düzenleme formu
     */
    public function edit(ClassCancellation $classCancellation)
    {
        $schoolId = Auth::user()->school_id;
        
        if ($classCancellation->school_id !== $schoolId) {
            abort(403);
        }
        
        $classes = ClassModel::where('school_id', $schoolId)
            ->where('is_active', true)
            ->get();
        
        return view('admin.class-cancellations.edit', compact('classCancellation', 'classes'));
    }

    /**
     * İptal/erteleme sil
     */
    public function destroy(ClassCancellation $classCancellation)
    {
        $schoolId = Auth::user()->school_id;
        
        if ($classCancellation->school_id !== $schoolId) {
            abort(403);
        }
        
        $classCancellation->delete();
        
        return redirect()->route('admin.class-cancellations.index')
            ->with('success', 'İptal/erteleme kaydı silindi.');
    }

    /**
     * İptal/erteleme için bekleyen telafi dersleri oluştur
     */
    private function createPendingMakeupClasses(ClassCancellation $cancellation, ClassModel $class)
    {
        $students = $class->students()->where('is_active', true)->get();
        
        foreach ($students as $student) {
            $makeupClass = MakeupClass::create([
                'school_id' => $cancellation->school_id,
                'cancellation_id' => $cancellation->id,
                'original_class_id' => $class->id,
                'type' => 'cancellation',
                'status' => 'pending',
            ]);
            
            StudentMakeupClass::create([
                'student_id' => $student->id,
                'makeup_class_id' => $makeupClass->id,
                'status' => 'pending',
            ]);
        }
    }

    /**
     * Yeni tarih için telafi dersi oluştur
     */
    private function createMakeupClassForCancellation(ClassCancellation $cancellation, ClassModel $class, $newDate)
    {
        $students = $class->students()->where('is_active', true)->get();
        
        foreach ($students as $student) {
            $makeupClass = MakeupClass::create([
                'school_id' => $cancellation->school_id,
                'cancellation_id' => $cancellation->id,
                'original_class_id' => $class->id,
                'scheduled_date' => $newDate,
                'type' => 'cancellation',
                'status' => 'scheduled',
            ]);
            
            StudentMakeupClass::create([
                'student_id' => $student->id,
                'makeup_class_id' => $makeupClass->id,
                'scheduled_date' => $newDate,
                'status' => 'scheduled',
            ]);
        }
    }

    /**
     * Var olan bir derse telafi olarak ekle
     */
    private function assignMakeupToExistingClass(ClassCancellation $cancellation, ClassModel $originalClass, $scheduledClassId)
    {
        $scheduledClass = ClassModel::findOrFail($scheduledClassId);
        $students = $originalClass->students()->where('is_active', true)->get();
        $scheduledDate = $cancellation->new_date ?? $cancellation->original_date;
        
        foreach ($students as $student) {
            $makeupClass = MakeupClass::create([
                'school_id' => $cancellation->school_id,
                'cancellation_id' => $cancellation->id,
                'original_class_id' => $originalClass->id,
                'scheduled_class_id' => $scheduledClassId,
                'scheduled_date' => $scheduledDate,
                'type' => 'cancellation',
                'status' => 'scheduled',
            ]);
            
            StudentMakeupClass::create([
                'student_id' => $student->id,
                'makeup_class_id' => $makeupClass->id,
                'scheduled_class_id' => $scheduledClassId,
                'scheduled_date' => $scheduledDate,
                'status' => 'scheduled',
            ]);
        }
    }
    
    /**
     * Belirli bir saat için telafi dersi oluştur
     */
    private function createMakeupClassWithTime(ClassCancellation $cancellation, ClassModel $class, $newDate, $startTime, $endTime)
    {
        $students = $class->students()->where('is_active', true)->get();
        
        foreach ($students as $student) {
            $makeupClass = MakeupClass::create([
                'school_id' => $cancellation->school_id,
                'cancellation_id' => $cancellation->id,
                'original_class_id' => $class->id,
                'scheduled_date' => $newDate,
                'type' => 'cancellation',
                'status' => 'scheduled',
            ]);
            
            StudentMakeupClass::create([
                'student_id' => $student->id,
                'makeup_class_id' => $makeupClass->id,
                'scheduled_date' => $newDate,
                'status' => 'scheduled',
            ]);
        }
        
        // Not: Saat bilgisi şu an için sadece kayıt amaçlı, 
        // gelecekte yeni bir sınıf oluşturulabilir veya mevcut telafi derslerine saat bilgisi eklenebilir
    }
}
