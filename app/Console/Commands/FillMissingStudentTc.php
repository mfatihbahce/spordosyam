<?php

namespace App\Console\Commands;

use App\Models\Student;
use Illuminate\Console\Command;

class FillMissingStudentTc extends Command
{
    protected $signature = 'students:fill-missing-tc {--dry-run : Sadece listele, güncelleme yapma}';

    protected $description = 'TC kimlik no su eksik olan öğrencilere benzersiz 11 haneli rastgele TC no atar';

    public function handle(): int
    {
        $dryRun = $this->option('dry-run');

        $students = Student::whereNull('identity_number')
            ->orWhere('identity_number', '')
            ->get();

        if ($students->isEmpty()) {
            $this->info('TC kimlik no su eksik olan öğrenci bulunamadı.');
            return self::SUCCESS;
        }

        $existing = Student::whereNotNull('identity_number')
            ->where('identity_number', '!=', '')
            ->pluck('identity_number')
            ->flip()
            ->all();

        $this->info('TC eksik öğrenci sayısı: ' . $students->count());
        if ($dryRun) {
            foreach ($students as $s) {
                $this->line('  - ' . $s->id . ': ' . $s->first_name . ' ' . $s->last_name . ' (TC: boş)');
            }
            $this->warn('Dry-run: Güncelleme yapılmadı.');
            return self::SUCCESS;
        }

        $updated = 0;
        foreach ($students as $student) {
            $tc = $this->generateUniqueTc($existing);
            $existing[$tc] = true;
            $student->update(['identity_number' => $tc]);
            $this->line('  Güncellendi: ' . $student->first_name . ' ' . $student->last_name . ' → TC: ' . $tc);
            $updated++;
        }

        $this->info('Toplam ' . $updated . ' öğrenciye TC kimlik no atandı.');
        return self::SUCCESS;
    }

    /**
     * Benzersiz 11 haneli rastgele TC üretir (ilk hane 0 olmaz).
     */
    private function generateUniqueTc(array &$existing): string
    {
        do {
            $first = (string) random_int(1, 9);
            for ($i = 0; $i < 10; $i++) {
                $first .= (string) random_int(0, 9);
            }
        } while (isset($existing[$first]));

        return $first;
    }
}
