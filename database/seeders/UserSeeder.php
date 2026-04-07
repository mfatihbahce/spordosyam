<?php

namespace Database\Seeders;

use App\Models\School;
use App\Models\User;
use App\Models\Coach;
use App\Models\ParentModel;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Önce bir örnek okul oluştur (varsa kullan)
        $school = School::firstOrCreate(
            ['slug' => Str::slug('Ornek Spor Okulu')],
            [
                'name' => 'Örnek Spor Okulu',
                'description' => 'Örnek spor okulu açıklaması',
                'phone' => '0555 555 55 55',
                'email' => 'info@orneksporokulu.com',
                'address' => 'Örnek Adres',
                'is_active' => true,
                'is_demo' => false,
            ]
        );

        // Superadmin
        User::firstOrCreate(
            ['email' => 'superadmin@spordosyam.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('123456'),
                'role' => 'superadmin',
                'school_id' => null,
                'is_active' => true,
            ]
        );

        // Admin (Spor Okulu)
        User::firstOrCreate(
            ['email' => 'admin@spordosyam.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('123456'),
                'role' => 'admin',
                'school_id' => $school->id,
                'is_active' => true,
            ]
        );

        // Antrenör
        $coachUser = User::firstOrCreate(
            ['email' => 'antrenor@spordosyam.com'],
            [
                'name' => 'Antrenör User',
                'password' => Hash::make('123456'),
                'role' => 'coach',
                'school_id' => $school->id,
                'is_active' => true,
            ]
        );

        // Antrenör kaydı oluştur (yoksa)
        Coach::firstOrCreate(
            ['user_id' => $coachUser->id],
            [
                'school_id' => $school->id,
                'phone' => '0555 555 55 56',
                'is_active' => true,
            ]
        );

        // Veli
        $parentUser = User::firstOrCreate(
            ['email' => 'veli@spordosyam.com'],
            [
                'name' => 'Veli User',
                'password' => Hash::make('123456'),
                'role' => 'parent',
                'school_id' => $school->id,
                'is_active' => true,
            ]
        );

        // Veli kaydı oluştur (yoksa)
        ParentModel::firstOrCreate(
            ['user_id' => $parentUser->id],
            [
                'school_id' => $school->id,
                'phone' => '0555 555 55 57',
                'is_active' => true,
            ]
        );

        // Mevcut kullanıcılar için eksik kayıtları oluştur
        $this->createMissingRecords();
    }

    private function createMissingRecords()
    {
        // Coach rolündeki kullanıcılar için coach kaydı yoksa oluştur
        $coachUsers = User::where('role', 'coach')
            ->whereDoesntHave('coach')
            ->get();

        foreach ($coachUsers as $user) {
            Coach::create([
                'school_id' => $user->school_id,
                'user_id' => $user->id,
                'phone' => '0555 000 00 00',
                'is_active' => true,
            ]);
        }

        // Parent rolündeki kullanıcılar için parent kaydı yoksa oluştur
        $parentUsers = User::where('role', 'parent')
            ->whereDoesntHave('parent')
            ->get();

        foreach ($parentUsers as $user) {
            ParentModel::create([
                'school_id' => $user->school_id,
                'user_id' => $user->id,
                'phone' => '0555 000 00 00',
                'is_active' => true,
            ]);
        }
    }
}
