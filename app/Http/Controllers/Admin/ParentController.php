<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ParentModel;
use App\Models\User;
use App\Models\Student;
use App\Services\SmsNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ParentController extends Controller
{
    public function index(Request $request)
    {
        $schoolId = Auth::user()->school_id;
        $search = $request->filled('search') ? trim($request->search) : null;

        $query = ParentModel::where('school_id', $schoolId)->with(['user', 'students.currentEnrollments']);

        if ($search !== null && $search !== '') {
            $term = '%' . $search . '%';
            $query->where(function ($q) use ($term, $schoolId) {
                $q->whereHas('user', function ($u) use ($term) {
                    $u->where('name', 'like', $term);
                })
                ->orWhere('phone', 'like', $term)
                ->orWhereHas('students', function ($s) use ($term, $schoolId) {
                    $s->where('school_id', $schoolId)->where(function ($s2) use ($term) {
                        $s2->where('first_name', 'like', $term)
                            ->orWhere('last_name', 'like', $term)
                            ->orWhere('identity_number', 'like', $term);
                    });
                });
            });
        }

        $parents = $query->orderBy('id', 'desc')->paginate(15)->withQueryString();
        return view('admin.parents.index', compact('parents', 'search'));
    }

    public function create()
    {
        return view('admin.parents.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'student_ids' => 'nullable|array',
            'student_ids.*' => 'exists:students,id',
        ]);

        $schoolId = Auth::user()->school_id;

        // Kullanıcı oluştur
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'parent',
            'school_id' => $schoolId,
            'is_active' => true,
        ]);

        // Veli oluştur
        $parent = ParentModel::create([
            'school_id' => $schoolId,
            'user_id' => $user->id,
            'phone' => $validated['phone'] ?? null,
            'address' => $validated['address'] ?? null,
            'is_active' => true,
        ]);

        // Öğrenci-veli ilişkisi
        if (!empty($validated['student_ids'])) {
            foreach ($validated['student_ids'] as $index => $studentId) {
                $parent->students()->attach($studentId, [
                    'relationship' => $request->input("relationships.{$studentId}", 'mother'),
                    'is_primary' => $index === 0,
                ]);
            }
        }

        // Veli kaydı sonrası kullanıcı adı ve şifre SMS ile gönderilsin (superadmin ayarına göre)
        if (!empty($parent->phone)) {
            $loginUrl = route('login');
            $message = "Spordosyam veli paneli. Giris: {$loginUrl} Kullanici: {$user->email} Sifre: {$validated['password']}";
            app(SmsNotificationService::class)->sendIfEnabled('parent_welcome_credentials', $parent->phone, $message, $parent->user);
        }

        return redirect()->route('admin.parents.index')
            ->with('success', 'Veli başarıyla oluşturuldu.');
    }

    public function show(ParentModel $parent)
    {
        if ($parent->school_id !== Auth::user()->school_id) {
            abort(403);
        }
        
        $parent->load(['user', 'students.studentFees']);
        return view('admin.parents.show', compact('parent'));
    }

    public function edit(ParentModel $parent)
    {
        if ($parent->school_id !== Auth::user()->school_id) {
            abort(403);
        }
        $parent->load('students');
        return view('admin.parents.edit', compact('parent'));
    }

    public function update(Request $request, ParentModel $parent)
    {
        if ($parent->school_id !== Auth::user()->school_id) {
            abort(403);
        }
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $parent->user_id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'student_ids' => 'nullable|array',
            'student_ids.*' => 'exists:students,id',
            'is_active' => 'boolean',
        ]);

        // Kullanıcıyı güncelle
        $parent->user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
        ]);

        // Veli bilgilerini güncelle
        $parent->update([
            'phone' => $validated['phone'] ?? null,
            'address' => $validated['address'] ?? null,
            'is_active' => $validated['is_active'] ?? true,
        ]);

        // Öğrenci-veli ilişkilerini güncelle
        if (isset($validated['student_ids'])) {
            $parent->students()->sync([]);
            foreach ($validated['student_ids'] as $index => $studentId) {
                $parent->students()->attach($studentId, [
                    'relationship' => $request->input("relationships.{$studentId}", 'mother'),
                    'is_primary' => $index === 0,
                ]);
            }
        }

        return redirect()->route('admin.parents.index')
            ->with('success', 'Veli başarıyla güncellendi.');
    }

    public function destroy(ParentModel $parent)
    {
        if ($parent->school_id !== Auth::user()->school_id) {
            abort(403);
        }
        
        $parent->user->delete(); // User silindiğinde parent da cascade ile silinir
        $parent->delete();

        return redirect()->route('admin.parents.index')
            ->with('success', 'Veli başarıyla silindi.');
    }
}
