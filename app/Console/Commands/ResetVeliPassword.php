<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class ResetVeliPassword extends Command
{
    protected $signature = 'spordosyam:reset-veli-password {email=veli@spordosyam.com} {password=123456}';

    protected $description = 'Veli şifresini sıfırlar (test için)';

    public function handle(): int
    {
        $email = $this->argument('email');
        $password = $this->argument('password');

        $user = User::where('email', $email)->first();

        if (!$user) {
            $this->error("Kullanıcı bulunamadı: {$email}");
            $this->info('Önce seeder çalıştırın: php artisan db:seed --class=UserSeeder');
            return 1;
        }

        $user->password = Hash::make($password);
        $user->save();

        $this->info("✅ Şifre güncellendi: {$email} / {$password}");
        $this->info("Rol: {$user->role}");
        return 0;
    }
}
