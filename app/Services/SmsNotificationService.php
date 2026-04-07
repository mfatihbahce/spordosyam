<?php

namespace App\Services;

use App\Models\SiteSetting;
use App\Models\User;
use App\Notifications\ParentSmsNotification;

class SmsNotificationService
{
    public const TYPES = [
        // Veli
        'parent_welcome_credentials' => ['label' => 'Veli kaydı: Kullanıcı adı ve şifre SMS ile gönderilsin', 'group' => 'veli'],
        'fee_reminder' => ['label' => 'Aidat hatırlatması (vade yaklaşırken)', 'group' => 'veli'],
        'fee_overdue' => ['label' => 'Aidat gecikmesi bildirimi', 'group' => 'veli'],
        'payment_received' => ['label' => 'Ödeme alındı bildirimi', 'group' => 'veli'],
        'class_cancelled' => ['label' => 'Ders iptali bildirimi', 'group' => 'veli'],
        'makeup_assigned' => ['label' => 'Telafi dersi atandı bildirimi', 'group' => 'veli'],
        'coach_new_message' => ['label' => 'Antrenörden yeni mesaj bildirimi', 'group' => 'veli'],
        'lesson_reminder' => ['label' => 'Ders saati hatırlatması (veli)', 'group' => 'veli'],
        'attendance_absent' => ['label' => 'Devamsızlık bildirimi', 'group' => 'veli'],
        // Antrenör
        'coach_class_cancelled' => ['label' => 'Ders iptali bildirimi', 'group' => 'antrenor'],
        'coach_makeup_assigned' => ['label' => 'Telafi dersi atandı bildirimi', 'group' => 'antrenor'],
        'coach_parent_message' => ['label' => 'Veliden yeni mesaj bildirimi', 'group' => 'antrenor'],
        'coach_lesson_reminder' => ['label' => 'Ders saati hatırlatması', 'group' => 'antrenor'],
    ];

    protected NetGsmService $netgsm;

    public function __construct(NetGsmService $netgsm)
    {
        $this->netgsm = $netgsm;
    }

    public static function getTypesConfig(): array
    {
        return self::TYPES;
    }

    /** Açık olan SMS türlerini döndür (key => true) */
    public static function getEnabledTypes(): array
    {
        $saved = SiteSetting::get('netgsm_sms_types', []);
        if (!is_array($saved)) {
            return [];
        }
        $defaults = array_fill_keys(array_keys(self::TYPES), false);
        return array_merge($defaults, $saved);
    }

    public static function isEnabled(string $type): bool
    {
        $enabled = self::getEnabledTypes();
        return !empty($enabled[$type]);
    }

    /**
     * SMS türü açıksa ve numara varsa gönder.
     * Veli bildirimlerinde $user verilirse, mobil uygulama bildirim ekranında da gösterilir.
     *
     * @param  User|null  $user  Veli User (mobil bildirim için - sadece veli türlerinde)
     * @return array ['sent' => bool, 'error' => string|null]
     */
    public function sendIfEnabled(string $type, ?string $phone, string $message, ?User $user = null): array
    {
        if (!self::isEnabled($type)) {
            return ['sent' => false, 'error' => 'Bu bildirim türü kapalı'];
        }
        $phone = trim((string) $phone);
        if (empty($phone) || strlen(preg_replace('/\D/', '', $phone)) < 10) {
            return ['sent' => false, 'error' => 'Geçerli telefon numarası yok'];
        }
        if (!$this->netgsm->isConfigured()) {
            return ['sent' => false, 'error' => 'NetGSM ayarları yapılmamış'];
        }
        $result = $this->netgsm->send($phone, $message);

        // SMS gönderildiyse ve veli bildirimi ise, mobil uygulama için DB'ye kaydet
        if ($result['success'] && $user && (self::TYPES[$type]['group'] ?? '') === 'veli') {
            $title = self::TYPES[$type]['label'] ?? $type;
            $user->notify(new ParentSmsNotification($type, $message, $title));
        }

        return [
            'sent' => $result['success'],
            'error' => $result['error'] ?? null,
        ];
    }
}
