<?php

namespace App\Helpers;

class EnvHelper
{
    /**
     * .env dosyasındaki bir değeri güncelle
     */
    public static function updateEnv($key, $value)
    {
        $envFile = base_path('.env');
        
        if (!file_exists($envFile)) {
            return false;
        }

        $envContent = file_get_contents($envFile);
        
        // Eğer key zaten varsa güncelle
        if (preg_match("/^{$key}=.*/m", $envContent)) {
            $envContent = preg_replace(
                "/^{$key}=.*/m",
                "{$key}={$value}",
                $envContent
            );
        } else {
            // Eğer key yoksa ekle
            $envContent .= "\n{$key}={$value}\n";
        }

        return file_put_contents($envFile, $envContent) !== false;
    }

    /**
     * Birden fazla .env değerini güncelle
     */
    public static function updateEnvMultiple(array $data)
    {
        $envFile = base_path('.env');
        
        if (!file_exists($envFile)) {
            return false;
        }

        $envContent = file_get_contents($envFile);
        $lines = explode("\n", $envContent);

        foreach ($data as $key => $value) {
            // Değeri formatla
            $formattedValue = self::formatEnvValue($value);
            $found = false;

            // Mevcut satırları kontrol et ve güncelle
            foreach ($lines as $index => $line) {
                $line = trim($line);
                
                // Boş satırları ve yorumları atla
                if (empty($line) || strpos($line, '#') === 0) {
                    continue;
                }

                // Key'i bul (key=value formatında)
                if (preg_match("/^({$key})\s*=(.*)$/", $line, $matches)) {
                    $lines[$index] = "{$key}={$formattedValue}";
                    $found = true;
                    break;
                }
            }

            // Eğer key bulunamadıysa, dosyanın sonuna ekle
            if (!$found) {
                // Son satır boş değilse yeni satır ekle
                if (!empty(trim(end($lines)))) {
                    $lines[] = "";
                }
                $lines[] = "{$key}={$formattedValue}";
            }
        }

        // Dosyayı yaz
        $newContent = implode("\n", $lines);
        return file_put_contents($envFile, $newContent) !== false;
    }

    /**
     * .env değerini formatla
     */
    private static function formatEnvValue($value)
    {
        // Boolean değerler
        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        }

        // String değerler
        $value = (string) $value;
        
        // Eğer boş değer ise
        if (empty($value)) {
            return '""';
        }
        
        // Eğer zaten tırnak içindeyse olduğu gibi bırak
        if ((strpos($value, '"') === 0 && substr($value, -1) === '"') || 
            (strpos($value, "'") === 0 && substr($value, -1) === "'")) {
            return $value;
        }
        
        // Eğer boşluk, özel karakter varsa tırnak içine al
        if (preg_match('/[\s#\$]/', $value)) {
            return '"' . addslashes($value) . '"';
        }

        return $value;
    }
}
