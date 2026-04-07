<?php

namespace App\Console\Commands;

use App\Models\ClassModel;
use App\Models\StudentFee;
use App\Services\SmsNotificationService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendSmsReminders extends Command
{
    protected $signature = 'sms:send-reminders';

    protected $description = 'Aidat ve ders hatırlatma SMS\'lerini (NetGSM) gönderir';

    public function handle(): int
    {
        $this->info('SMS hatırlatma görevi çalışıyor...');

        $today = Carbon::today();
        $this->sendFeeReminders($today);
        $this->sendLessonReminders($today);

        $this->info('SMS hatırlatma görevi tamamlandı.');
        return self::SUCCESS;
    }

    protected function sendFeeReminders(Carbon $today): void
    {
        $sms = app(SmsNotificationService::class);

        // Vade yaklaşırken (örn. 3 gün kala)
        if (SmsNotificationService::isEnabled('fee_reminder')) {
            $reminderDate = $today->copy()->addDays(3)->toDateString();
            $fees = StudentFee::where('status', 'pending')
                ->whereDate('due_date', $reminderDate)
                ->with(['student.parents.user'])
                ->get();

            foreach ($fees as $fee) {
                $student = $fee->student;
                if (!$student) {
                    continue;
                }
                $parent = $student->parents->sortByDesc(function ($p) {
                    return (int) ($p->pivot->is_primary ?? 0);
                })->first();
                if (!$parent || empty($parent->phone)) {
                    continue;
                }
                $studentName = trim($student->first_name . ' ' . $student->last_name);
                $dateStr = $fee->due_date->format('d.m.Y');
                $amountStr = number_format($fee->amount, 2);
                $msg = "{$studentName} aidati {$dateStr} tarihinde son. Tutar: {$amountStr} TL. Spordosyam";
                $sms->sendIfEnabled('fee_reminder', $parent->phone, $msg, $parent->user);
            }
            $this->info('Aidat hatırlatma SMS\'leri gönderildi: ' . $fees->count());
        }

        // Geciken aidatlar
        if (SmsNotificationService::isEnabled('fee_overdue')) {
            $overdueFees = StudentFee::whereIn('status', ['pending', 'overdue'])
                ->whereDate('due_date', '<', $today->toDateString())
                ->with(['student.parents.user'])
                ->get();

            foreach ($overdueFees as $fee) {
                $student = $fee->student;
                if (!$student) {
                    continue;
                }
                $parent = $student->parents->sortByDesc(function ($p) {
                    return (int) ($p->pivot->is_primary ?? 0);
                })->first();
                if (!$parent || empty($parent->phone)) {
                    continue;
                }
                // Status pending ise gecikmişe çekelim
                if ($fee->status === 'pending') {
                    $fee->update(['status' => 'overdue']);
                }
                $studentName = trim($student->first_name . ' ' . $student->last_name);
                $dateStr = $fee->due_date->format('d.m.Y');
                $amountStr = number_format($fee->amount, 2);
                $msg = "{$studentName} aidati icin {$dateStr} vadesi gecmistir. Tutar: {$amountStr} TL. Spordosyam";
                $sms->sendIfEnabled('fee_overdue', $parent->phone, $msg, $parent->user);
            }
            $this->info('Aidat gecikme SMS\'leri gönderildi: ' . $overdueFees->count());
        }
    }

    protected function sendLessonReminders(Carbon $today): void
    {
        if (
            !SmsNotificationService::isEnabled('lesson_reminder') &&
            !SmsNotificationService::isEnabled('coach_lesson_reminder')
        ) {
            return;
        }

        $sms = app(SmsNotificationService::class);

        $dayName = strtolower($today->format('l')); // monday, tuesday...

        // Aktif sınıflar - bugünün gününe göre programı olanlar
        $classes = ClassModel::active()
            ->with(['students.parents.user', 'coach.user'])
            ->get();

        foreach ($classes as $class) {
            $schedule = $class->class_schedule[$dayName] ?? null;
            if (!$schedule || !is_array($schedule)) {
                continue;
            }
            $startTime = $schedule['start_time'] ?? null;
            if (!$startTime) {
                continue;
            }

            $className = $class->name ?? 'Ders';
            $timeStr = $startTime;
            $dateStr = $today->format('d.m.Y');

            // Veli hatırlatması
            if (SmsNotificationService::isEnabled('lesson_reminder')) {
                foreach ($class->students as $student) {
                    if (!$student->is_active) {
                        continue;
                    }
                    $parent = $student->parents->sortByDesc(function ($p) {
                        return (int) ($p->pivot->is_primary ?? 0);
                    })->first();
                    if (!$parent || empty($parent->phone)) {
                        continue;
                    }
                    $studentName = trim($student->first_name . ' ' . $student->last_name);
                    $msg = "{$studentName} icin {$dateStr} {$timeStr} saatinde {$className} dersi var. Spordosyam";
                    $sms->sendIfEnabled('lesson_reminder', $parent->phone, $msg, $parent->user);
                }
            }

            // Antrenör hatırlatması
            if (SmsNotificationService::isEnabled('coach_lesson_reminder') && $class->coach && !empty($class->coach->phone)) {
                $msg = "{$dateStr} {$timeStr} saatinde {$className} dersiniz var. Spordosyam";
                $sms->sendIfEnabled('coach_lesson_reminder', $class->coach->phone, $msg);
            }
        }

        $this->info('Ders hatırlatma SMS\'leri kontrol edildi.');
    }
}

