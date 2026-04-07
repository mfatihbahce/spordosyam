<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ClassModel;
use Carbon\Carbon;

class UpdateClassTimesAndDates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'classes:update-times-dates 
                            {--dry-run : Sadece önizleme yap, değişiklik yapma}
                            {--default-duration=90 : Varsayılan ders süresi (dakika)}
                            {--default-end-months=3 : Bitiş tarihi için varsayılan ay sayısı}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Derslerin bitiş saatlerini ve bitiş tarihlerini otomatik doldurur';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');
        $defaultDuration = (int) $this->option('default-duration');
        $defaultEndMonths = (int) $this->option('default-end-months');

        $this->info('Ders saatleri ve tarihleri güncelleniyor...');
        $this->newLine();

        $classes = ClassModel::all();
        $updated = 0;
        $skipped = 0;

        $headers = ['ID', 'Sınıf Adı', 'Gün', 'Eski Format', 'Yeni Format', 'Bitiş Tarihi', 'Durum'];
        $rows = [];

        foreach ($classes as $class) {
            $needsUpdate = false;
            $newSchedule = $class->class_schedule ?? [];
            $newEndDate = $class->end_date;

            // Bitiş tarihi kontrolü
            if (!$class->end_date) {
                $newEndDate = now()->addMonths($defaultEndMonths)->endOfMonth();
                $needsUpdate = true;
            }

            // Eski format kontrolü ve dönüştürme
            if ($class->class_schedule && is_array($class->class_schedule)) {
                foreach ($class->class_schedule as $day => $schedule) {
                    // Eski format: sadece string (örn: "14:00")
                    if (is_string($schedule) && !empty($schedule)) {
                        $startTime = $schedule;
                        $endTime = $this->addMinutesToTime($startTime, $defaultDuration);
                        
                        $newSchedule[$day] = [
                            'start_time' => $startTime,
                            'end_time' => $endTime,
                        ];
                        
                        $needsUpdate = true;
                        
                        $rows[] = [
                            $class->id,
                            $class->name,
                            $day,
                            $schedule,
                            "{$startTime} - {$endTime}",
                            $newEndDate ? $newEndDate->format('d.m.Y') : 'Eklenecek',
                            $dryRun ? 'Güncellenecek' : 'Güncellendi'
                        ];
                    }
                    // Yeni format ama end_time eksik
                    elseif (is_array($schedule) && !empty($schedule['start_time']) && empty($schedule['end_time'])) {
                        $startTime = $schedule['start_time'];
                        $endTime = $this->addMinutesToTime($startTime, $defaultDuration);
                        
                        $newSchedule[$day] = [
                            'start_time' => $startTime,
                            'end_time' => $endTime,
                        ];
                        
                        $needsUpdate = true;
                        
                        $rows[] = [
                            $class->id,
                            $class->name,
                            $day,
                            $schedule['start_time'],
                            "{$startTime} - {$endTime}",
                            $newEndDate ? $newEndDate->format('d.m.Y') : 'Eklenecek',
                            $dryRun ? 'Güncellenecek' : 'Güncellendi'
                        ];
                    }
                }
            }

            if ($needsUpdate) {
                if (!$dryRun) {
                    $class->update([
                        'class_schedule' => $newSchedule,
                        'end_date' => $newEndDate,
                    ]);
                }
                $updated++;
            } else {
                $skipped++;
            }
        }

        if (!empty($rows)) {
            $this->table($headers, $rows);
        }

        $this->newLine();
        $this->info("Özet:");
        $this->line("  Toplam: " . $classes->count());
        $this->line("  Güncellenen: " . $updated);
        $this->line("  Atlanan: " . $skipped);

        if ($dryRun) {
            $this->newLine();
            $this->warn('Bu bir önizleme idi. Değişiklikleri uygulamak için --dry-run parametresini kaldırın.');
        } else {
            $this->newLine();
            $this->info('✓ İşlem tamamlandı!');
        }

        return 0;
    }

    /**
     * Saate dakika ekle
     */
    private function addMinutesToTime($time, $minutes)
    {
        $timeParts = explode(':', $time);
        $hours = (int) $timeParts[0];
        $mins = (int) ($timeParts[1] ?? 0);
        
        $totalMinutes = ($hours * 60) + $mins + $minutes;
        $newHours = floor($totalMinutes / 60);
        $newMins = $totalMinutes % 60;
        
        return sprintf('%02d:%02d', $newHours, $newMins);
    }
}
