<?php

namespace App\Console\Commands;

use App\Models\ClassModel;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class UpdateClassSchedules extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'classes:update-schedules 
                            {--dry-run : Sadece kontrol et, değişiklik yapma}
                            {--default-days=* : Varsayılan günler (örn: monday,wednesday,friday)}
                            {--default-time=14:00 : Varsayılan saat}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mevcut sınıflara gün ve saat bilgisi ekler veya günceller';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');
        $defaultDays = $this->option('default-days');
        $defaultTime = $this->option('default-time');

        // Eğer varsayılan günler belirtilmemişse, Pazartesi, Çarşamba, Cuma kullan
        if (empty($defaultDays)) {
            $defaultDays = ['monday', 'wednesday', 'friday'];
        }

        $this->info('Sınıf gün ve saat bilgileri güncelleniyor...');
        $this->newLine();

        // Tüm sınıfları getir
        $classes = ClassModel::all();
        $totalClasses = $classes->count();

        if ($totalClasses === 0) {
            $this->warn('Veritabanında sınıf bulunamadı.');
            return Command::SUCCESS;
        }

        $this->info("Toplam {$totalClasses} sınıf bulundu.");
        $this->newLine();

        $updated = 0;
        $skipped = 0;
        $errors = 0;

        $tableData = [];

        foreach ($classes as $class) {
            $needsUpdate = false;
            $updateData = [];
            $reason = '';

            // class_days kontrolü
            $currentDays = $class->class_days ?? [];
            if (empty($currentDays)) {
                $updateData['class_days'] = $defaultDays;
                $needsUpdate = true;
                $reason = 'Gün bilgisi yok';
            }

            // class_schedule kontrolü
            $currentSchedule = $class->class_schedule ?? [];
            $daysToUse = $updateData['class_days'] ?? $currentDays;
            
            if (empty($currentSchedule) && !empty($daysToUse)) {
                // Seçilen günler için varsayılan saat ekle
                $schedule = [];
                foreach ($daysToUse as $day) {
                    $schedule[$day] = $defaultTime;
                }
                $updateData['class_schedule'] = $schedule;
                $needsUpdate = true;
                if ($reason) {
                    $reason .= ', ';
                }
                $reason .= 'Saat bilgisi yok';
            }

            if ($needsUpdate) {
                if (!$dryRun) {
                    try {
                        $class->update($updateData);
                        $updated++;
                        $status = '✓ Güncellendi';
                    } catch (\Exception $e) {
                        $errors++;
                        $status = '✗ Hata: ' . $e->getMessage();
                    }
                } else {
                    $updated++;
                    $status = '[DRY-RUN] Güncellenecek';
                }

                $daysToShow = $updateData['class_days'] ?? $currentDays;
                $scheduleToShow = $updateData['class_schedule'] ?? $currentSchedule;
                $scheduleText = '';
                if (!empty($scheduleToShow) && is_array($scheduleToShow)) {
                    $scheduleParts = [];
                    foreach ($daysToShow as $day) {
                        if (isset($scheduleToShow[$day])) {
                            $dayNames = ['monday' => 'Pzt', 'tuesday' => 'Sal', 'wednesday' => 'Çar', 'thursday' => 'Per', 'friday' => 'Cum', 'saturday' => 'Cmt', 'sunday' => 'Paz'];
                            $scheduleParts[] = ($dayNames[$day] ?? $day) . ': ' . $scheduleToShow[$day];
                        }
                    }
                    $scheduleText = implode(', ', $scheduleParts);
                } else {
                    $scheduleText = '-';
                }

                $tableData[] = [
                    'ID' => $class->id,
                    'Sınıf Adı' => $class->name,
                    'Günler' => !empty($daysToShow) ? implode(', ', array_map(fn($d) => ['monday' => 'Pazartesi', 'tuesday' => 'Salı', 'wednesday' => 'Çarşamba', 'thursday' => 'Perşembe', 'friday' => 'Cuma', 'saturday' => 'Cumartesi', 'sunday' => 'Pazar'][$d] ?? $d, $daysToShow)) : '-',
                    'Saatler' => $scheduleText,
                    'Durum' => $status,
                    'Sebep' => $reason,
                ];
            } else {
                $skipped++;
                $scheduleText = '';
                if (!empty($currentSchedule) && is_array($currentSchedule)) {
                    $scheduleParts = [];
                    foreach ($currentDays as $day) {
                        if (isset($currentSchedule[$day])) {
                            $dayNames = ['monday' => 'Pzt', 'tuesday' => 'Sal', 'wednesday' => 'Çar', 'thursday' => 'Per', 'friday' => 'Cum', 'saturday' => 'Cmt', 'sunday' => 'Paz'];
                            $scheduleParts[] = ($dayNames[$day] ?? $day) . ': ' . $currentSchedule[$day];
                        }
                    }
                    $scheduleText = implode(', ', $scheduleParts);
                } else {
                    $scheduleText = '-';
                }

                $tableData[] = [
                    'ID' => $class->id,
                    'Sınıf Adı' => $class->name,
                    'Günler' => !empty($currentDays) ? implode(', ', array_map(fn($d) => ['monday' => 'Pazartesi', 'tuesday' => 'Salı', 'wednesday' => 'Çarşamba', 'thursday' => 'Perşembe', 'friday' => 'Cuma', 'saturday' => 'Cumartesi', 'sunday' => 'Pazar'][$d] ?? $d, $currentDays)) : '-',
                    'Saatler' => $scheduleText,
                    'Durum' => 'Atlandı',
                    'Sebep' => 'Zaten güncel',
                ];
            }
        }

        // Tablo göster
        $this->table(
            ['ID', 'Sınıf Adı', 'Günler', 'Saatler', 'Durum', 'Sebep'],
            $tableData
        );

        $this->newLine();
        $this->info("Özet:");
        $this->line("  Toplam: {$totalClasses}");
        $this->line("  Güncellenen: {$updated}");
        $this->line("  Atlanan: {$skipped}");
        if ($errors > 0) {
            $this->error("  Hatalar: {$errors}");
        }

        if ($dryRun) {
            $this->newLine();
            $this->warn('DRY-RUN modu: Hiçbir değişiklik yapılmadı.');
            $this->info('Değişiklikleri uygulamak için --dry-run parametresini kaldırın.');
        } else {
            $this->newLine();
            $this->info('✓ İşlem tamamlandı!');
        }

        return Command::SUCCESS;
    }
}
