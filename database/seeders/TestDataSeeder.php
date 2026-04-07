<?php

namespace Database\Seeders;

use App\Models\School;
use App\Models\User;
use App\Models\Coach;
use App\Models\ParentModel;
use App\Models\Branch;
use App\Models\SportBranch;
use App\Models\ClassModel;
use App\Models\Student;
use App\Models\FeePlan;
use App\Models\StudentFee;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;

class TestDataSeeder extends Seeder
{
    public function run(): void
    {
        echo "=== Test Verileri Oluşturuluyor ===\n\n";

        // 1. Admin kullanıcısının okulunu bul veya oluştur
        $adminUser = User::where('email', 'admin@spordosyam.com')->first();
        
        if ($adminUser && $adminUser->school_id) {
            // Admin'in mevcut okulunu kullan
            $school = School::find($adminUser->school_id);
            echo "✅ Mevcut Admin Okulu Kullanılıyor: {$school->name} (ID: {$school->id})\n";
        } else {
            // Okul yoksa oluştur
            $school = School::firstOrCreate(
                ['slug' => Str::slug('Örnek Spor Okulu')],
                [
                    'name' => 'Örnek Spor Okulu',
                    'description' => 'Test amaçlı oluşturulmuş örnek spor okulu',
                    'phone' => '0212 555 00 01',
                    'email' => 'info@orneksporokulu.com',
                    'address' => 'İstanbul, Kadıköy, Test Mahallesi, Test Sokak No:1',
                    'is_active' => true,
                    'is_demo' => false,
                    'iyzico_commission_rate' => 5.00,
                ]
            );
            
            // Admin kullanıcısını oluştur veya güncelle
            if (!$adminUser) {
                $adminUser = User::create([
                    'name' => 'Admin User',
                    'email' => 'admin@spordosyam.com',
                    'password' => Hash::make('123456'),
                    'role' => 'admin',
                    'school_id' => $school->id,
                    'is_active' => true,
                ]);
            } else {
                $adminUser->update(['school_id' => $school->id]);
            }
            echo "✅ Okul: {$school->name} (ID: {$school->id})\n";
        }
        
        echo "✅ Admin: {$adminUser->email} (School ID: {$adminUser->school_id})\n";

        // 2. Şubeler oluştur
        $branches = [
            ['name' => 'Kadıköy Şubesi', 'address' => 'İstanbul, Kadıköy, Bağdat Caddesi No:100', 'phone' => '0216 555 00 01'],
            ['name' => 'Beşiktaş Şubesi', 'address' => 'İstanbul, Beşiktaş, Barbaros Bulvarı No:50', 'phone' => '0212 555 00 02'],
            ['name' => 'Şişli Şubesi', 'address' => 'İstanbul, Şişli, Halaskargazi Caddesi No:75', 'phone' => '0212 555 00 03'],
        ];

        $createdBranches = [];
        foreach ($branches as $branchData) {
            $branch = Branch::firstOrCreate(
                ['school_id' => $school->id, 'name' => $branchData['name']],
                array_merge($branchData, [
                    'school_id' => $school->id,
                    'is_active' => true,
                ])
            );
            $createdBranches[] = $branch;
            echo "✅ Şube: {$branch->name}\n";
        }

        // 3. Branşlar oluştur
        $sportBranches = [
            ['name' => 'Futbol', 'description' => 'Futbol eğitimi ve antrenmanları'],
            ['name' => 'Basketbol', 'description' => 'Basketbol eğitimi ve antrenmanları'],
            ['name' => 'Voleybol', 'description' => 'Voleybol eğitimi ve antrenmanları'],
            ['name' => 'Yüzme', 'description' => 'Yüzme eğitimi ve antrenmanları'],
            ['name' => 'Tenis', 'description' => 'Tenis eğitimi ve antrenmanları'],
        ];

        $createdSportBranches = [];
        foreach ($sportBranches as $sportBranchData) {
            $sportBranch = SportBranch::firstOrCreate(
                ['school_id' => $school->id, 'name' => $sportBranchData['name']],
                array_merge($sportBranchData, [
                    'school_id' => $school->id,
                    'is_active' => true,
                ])
            );
            $createdSportBranches[] = $sportBranch;
            echo "✅ Branş: {$sportBranch->name}\n";
        }

        // 4. Antrenörler oluştur (antrenor@spordosyam.com, antrenor1@spordosyam.com, ...)
        $coaches = [];
        for ($i = 0; $i < 5; $i++) {
            $email = $i === 0 ? 'antrenor@spordosyam.com' : "antrenor{$i}@spordosyam.com";
            $name = $i === 0 ? 'Antrenör User' : "Antrenör {$i}";
            
            $coachUser = User::firstOrCreate(
                ['email' => $email],
                [
                    'name' => $name,
                    'password' => Hash::make('123456'),
                    'role' => 'coach',
                    'school_id' => $school->id,
                    'is_active' => true,
                ]
            );

            $coach = Coach::firstOrCreate(
                ['user_id' => $coachUser->id],
                [
                    'school_id' => $school->id,
                    'phone' => '0555 ' . str_pad(100 + $i, 3, '0', STR_PAD_LEFT) . ' ' . str_pad(10 + $i, 2, '0', STR_PAD_LEFT) . str_pad(50 + $i, 2, '0', STR_PAD_LEFT),
                    'bio' => "{$name} - Deneyimli antrenör",
                    'is_active' => true,
                ]
            );
            $coaches[] = $coach;
            echo "✅ Antrenör: {$coachUser->email} ({$coachUser->name})\n";
        }

        // 5. Sınıflar oluştur
        $classNames = [
            'Futbol U10 (6-10 Yaş)',
            'Futbol U14 (11-14 Yaş)',
            'Basketbol U12 (8-12 Yaş)',
            'Basketbol U16 (13-16 Yaş)',
            'Voleybol U13 (9-13 Yaş)',
            'Yüzme Başlangıç',
            'Yüzme İleri Seviye',
            'Tenis Başlangıç',
        ];

        $createdClasses = [];
        $classDays = ['Pazartesi', 'Çarşamba', 'Cuma'];
        
        foreach ($classNames as $index => $className) {
            $branch = $createdBranches[$index % count($createdBranches)];
            $sportBranch = $createdSportBranches[$index % count($createdSportBranches)];
            $coach = $coaches[$index % count($coaches)];

            $class = ClassModel::firstOrCreate(
                [
                    'school_id' => $school->id,
                    'name' => $className,
                ],
                [
                    'branch_id' => $branch->id,
                    'sport_branch_id' => $sportBranch->id,
                    'coach_id' => $coach->id,
                    'description' => "{$className} sınıfı - {$sportBranch->name} branşı",
                    'capacity' => 20,
                    'class_time' => ($index % 3 === 0 ? '10:00' : ($index % 3 === 1 ? '14:00' : '18:00')),
                    'class_days' => $classDays,
                    'is_active' => true,
                ]
            );
            $createdClasses[] = $class;
            echo "✅ Sınıf: {$class->name} (Antrenör: {$coach->user->name})\n";
        }

        // 6. Veliler oluştur
        $parentNames = [
            ['name' => 'Ahmet Yılmaz', 'email' => 'veli@spordosyam.com'],
            ['name' => 'Mehmet Demir', 'email' => 'veli1@spordosyam.com'],
            ['name' => 'Ayşe Kaya', 'email' => 'veli2@spordosyam.com'],
            ['name' => 'Fatma Şahin', 'email' => 'veli3@spordosyam.com'],
            ['name' => 'Ali Öztürk', 'email' => 'veli4@spordosyam.com'],
            ['name' => 'Zeynep Arslan', 'email' => 'veli5@spordosyam.com'],
            ['name' => 'Mustafa Çelik', 'email' => 'veli6@spordosyam.com'],
            ['name' => 'Elif Yıldız', 'email' => 'veli7@spordosyam.com'],
        ];

        $createdParents = [];
        foreach ($parentNames as $index => $parentData) {
            $parentUser = User::firstOrCreate(
                ['email' => $parentData['email']],
                [
                    'name' => $parentData['name'],
                    'password' => Hash::make('123456'),
                    'role' => 'parent',
                    'school_id' => $school->id,
                    'is_active' => true,
                ]
            );

            $parent = ParentModel::firstOrCreate(
                ['user_id' => $parentUser->id],
                [
                    'school_id' => $school->id,
                    'phone' => '0555 ' . str_pad(200 + $index, 3, '0', STR_PAD_LEFT) . ' ' . str_pad(20 + $index, 2, '0', STR_PAD_LEFT) . str_pad(30 + $index, 2, '0', STR_PAD_LEFT),
                    'address' => 'İstanbul, Test Mahallesi, Test Sokak No:' . ($index + 1),
                    'is_active' => true,
                ]
            );
            $createdParents[] = $parent;
            echo "✅ Veli: {$parentUser->email} ({$parentUser->name})\n";
        }

        // 7. Öğrenciler oluştur (her veliye 1-2 öğrenci)
        $studentNames = [
            ['first_name' => 'Ali', 'last_name' => 'Yılmaz', 'gender' => 'male', 'birth_date' => '2015-05-15'],
            ['first_name' => 'Zeynep', 'last_name' => 'Yılmaz', 'gender' => 'female', 'birth_date' => '2017-08-20'],
            ['first_name' => 'Emre', 'last_name' => 'Demir', 'gender' => 'male', 'birth_date' => '2014-03-10'],
            ['first_name' => 'Selin', 'last_name' => 'Kaya', 'gender' => 'female', 'birth_date' => '2016-11-25'],
            ['first_name' => 'Berk', 'last_name' => 'Şahin', 'gender' => 'male', 'birth_date' => '2015-07-12'],
            ['first_name' => 'Deniz', 'last_name' => 'Öztürk', 'gender' => 'male', 'birth_date' => '2014-09-18'],
            ['first_name' => 'Ece', 'last_name' => 'Arslan', 'gender' => 'female', 'birth_date' => '2016-02-14'],
            ['first_name' => 'Can', 'last_name' => 'Çelik', 'gender' => 'male', 'birth_date' => '2015-12-05'],
            ['first_name' => 'Merve', 'last_name' => 'Yıldız', 'gender' => 'female', 'birth_date' => '2017-04-22'],
            ['first_name' => 'Kaan', 'last_name' => 'Yıldız', 'gender' => 'male', 'birth_date' => '2014-10-30'],
        ];

        $createdStudents = [];
        foreach ($studentNames as $index => $studentData) {
            $parent = $createdParents[$index % count($createdParents)];
            $class = $createdClasses[$index % count($createdClasses)];

            $student = Student::firstOrCreate(
                [
                    'school_id' => $school->id,
                    'first_name' => $studentData['first_name'],
                    'last_name' => $studentData['last_name'],
                ],
                array_merge($studentData, [
                    'school_id' => $school->id,
                    'class_id' => $class->id,
                    'phone' => $parent->phone,
                    'email' => strtolower($studentData['first_name'] . '.' . $studentData['last_name']) . '@test.com',
                    'address' => $parent->address,
                    'is_active' => true,
                ])
            );

            // Öğrenci-Veli ilişkisi
            if (!$student->parents()->where('parent_id', $parent->id)->exists()) {
                $relationships = ['mother', 'father', 'guardian', 'other'];
                $student->parents()->attach($parent->id, [
                    'relationship' => $relationships[$index % count($relationships)],
                    'is_primary' => $index % 2 === 0,
                ]);
            }

            $createdStudents[] = $student;
            echo "✅ Öğrenci: {$student->first_name} {$student->last_name} (Veli: {$parent->user->name}, Sınıf: {$class->name})\n";
        }

        // 8. Aidat Planları oluştur (sadece aylık, ad + tutar)
        $feePlans = [
            ['name' => 'Aylık Standart', 'amount' => 500.00],
            ['name' => 'Aylık Premium', 'amount' => 750.00],
        ];

        $createdFeePlans = [];
        foreach ($feePlans as $feePlanData) {
            $feePlan = FeePlan::firstOrCreate(
                ['school_id' => $school->id, 'name' => $feePlanData['name']],
                [
                    'school_id' => $school->id,
                    'name' => $feePlanData['name'],
                    'amount' => $feePlanData['amount'],
                    'is_active' => true,
                ]
            );
            $createdFeePlans[] = $feePlan;
            echo "✅ Aidat Planı: {$feePlan->name} ({$feePlan->amount} TL/ay)\n";
        }

        // 9. Öğrenci Aidatları oluştur (bazı öğrenciler için)
        foreach ($createdStudents as $index => $student) {
            if ($index % 2 === 0) { // Her iki öğrenciden birine aidat ekle
                $feePlan = $createdFeePlans[$index % count($createdFeePlans)];
                $dueDate = Carbon::now()->addDays(15);

                $studentFee = StudentFee::firstOrCreate(
                    [
                        'student_id' => $student->id,
                        'fee_plan_id' => $feePlan->id,
                        'due_date' => $dueDate->format('Y-m-d'),
                    ],
                    [
                        'amount' => $feePlan->amount,
                        'status' => 'pending',
                        'notes' => 'Aylık aidat',
                    ]
                );
                echo "✅ Öğrenci Aidatı: {$student->first_name} {$student->last_name} - {$feePlan->name} ({$studentFee->amount} TL)\n";
            }
        }

        echo "\n=== Test Verileri Başarıyla Oluşturuldu ===\n";
        echo "Toplam:\n";
        echo "- Okul: 1\n";
        echo "- Şube: " . count($createdBranches) . "\n";
        echo "- Branş: " . count($createdSportBranches) . "\n";
        echo "- Antrenör: " . count($coaches) . "\n";
        echo "- Sınıf: " . count($createdClasses) . "\n";
        echo "- Veli: " . count($createdParents) . "\n";
        echo "- Öğrenci: " . count($createdStudents) . "\n";
        echo "- Aidat Planı: " . count($createdFeePlans) . "\n";
        echo "\nGiriş Bilgileri:\n";
        echo "- Admin: admin@spordosyam.com / 123456\n";
        echo "- Antrenörler: antrenor@spordosyam.com, antrenor1@spordosyam.com, ... / 123456\n";
        echo "- Veliler: veli@spordosyam.com, veli1@spordosyam.com, ... / 123456\n";
    }
}
