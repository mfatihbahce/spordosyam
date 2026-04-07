<?php

namespace App\Services;

use App\Models\ClassModel;
use App\Models\MakeupSession;
use Carbon\Carbon;

class ClassScheduleService
{
    /**
     * Belirli bir tarihte okulun dolu saat aralıklarını getir (classes + makeup_sessions)
     */
    public function getOccupiedSlots(int $schoolId, string $date): array
    {
        $carbonDate = Carbon::parse($date);
        $dayName = strtolower($carbonDate->format('l'));

        $slots = [];

        // Normal dersler
        $classes = ClassModel::where('school_id', $schoolId)
            ->where('is_active', true)
            ->where(function ($q) use ($carbonDate) {
                $q->whereNull('end_date')
                    ->orWhere('end_date', '>=', $carbonDate->toDateString());
            })
            ->get();

        foreach ($classes as $class) {
            $schedule = $class->class_schedule[$dayName] ?? null;
            if (!$schedule || !is_array($schedule)) {
                continue;
            }
            $startTime = $schedule['start_time'] ?? null;
            $endTime = $schedule['end_time'] ?? null;
            if (!$startTime) {
                continue;
            }
            if (!$endTime) {
                $endTime = date('H:i', strtotime($startTime) + 90 * 60);
            }
            $slots[] = [
                'start' => $startTime,
                'end' => $endTime,
                'source' => 'class',
                'id' => $class->id,
            ];
        }

        // Telafi dersi oturumları (aynı tarih)
        $sessions = MakeupSession::where('school_id', $schoolId)
            ->whereDate('scheduled_date', $date)
            ->get();

        foreach ($sessions as $session) {
            $start = $session->start_time instanceof \DateTimeInterface
                ? $session->start_time->format('H:i')
                : \Carbon\Carbon::parse($session->start_time)->format('H:i');
            $end = $session->end_time instanceof \DateTimeInterface
                ? $session->end_time->format('H:i')
                : \Carbon\Carbon::parse($session->end_time)->format('H:i');
            $slots[] = [
                'start' => $start,
                'end' => $end,
                'source' => 'makeup_session',
                'id' => $session->id,
            ];
        }

        return $slots;
    }

    /**
     * Verilen tarih ve saat aralığının çakışıp çakışmadığını kontrol et
     * @param int|null $excludeMakeupSessionId Güncelleme sırasında kendi oturumunu hariç tutmak için
     */
    public function hasScheduleConflict(
        int $schoolId,
        string $date,
        string $startTime,
        string $endTime,
        ?int $excludeMakeupSessionId = null
    ): bool {
        $slots = $this->getOccupiedSlots($schoolId, $date);
        $newStart = strtotime($startTime);
        $newEnd = strtotime($endTime);

        foreach ($slots as $slot) {
            if ($slot['source'] === 'makeup_session' && $excludeMakeupSessionId && ($slot['id'] ?? null) == $excludeMakeupSessionId) {
                continue;
            }
            $slotStart = strtotime($slot['start']);
            $slotEnd = strtotime($slot['end']);
            if ($newStart < $slotEnd && $newEnd > $slotStart) {
                return true;
            }
        }
        return false;
    }

    /**
     * Seçilen antrenörün bu tarih/saatte başka dersi veya telafi oturumu var mı?
     * Yoklama için antrenör çakışmasına izin verilmez.
     */
    public function hasCoachConflict(
        int $schoolId,
        int $coachId,
        string $date,
        string $startTime,
        string $endTime,
        ?int $excludeMakeupSessionId = null
    ): bool {
        $carbonDate = Carbon::parse($date);
        $dayName = strtolower($carbonDate->format('l'));
        $newStart = strtotime($startTime);
        $newEnd = strtotime($endTime);

        $classes = ClassModel::where('school_id', $schoolId)
            ->where('coach_id', $coachId)
            ->where('is_active', true)
            ->where(function ($q) use ($carbonDate) {
                $q->whereNull('end_date')
                    ->orWhere('end_date', '>=', $carbonDate->toDateString());
            })
            ->get();

        foreach ($classes as $class) {
            $schedule = $class->class_schedule[$dayName] ?? null;
            if (!$schedule || !is_array($schedule)) {
                continue;
            }
            $slotStart = strtotime($schedule['start_time'] ?? '00:00');
            $slotEnd = isset($schedule['end_time']) ? strtotime($schedule['end_time']) : ($slotStart + 90 * 60);
            if ($newStart < $slotEnd && $newEnd > $slotStart) {
                return true;
            }
        }

        $sessions = MakeupSession::where('school_id', $schoolId)
            ->where('coach_id', $coachId)
            ->whereDate('scheduled_date', $date)
            ->get();

        foreach ($sessions as $session) {
            if ($excludeMakeupSessionId && $session->id == $excludeMakeupSessionId) {
                continue;
            }
            $start = $session->start_time instanceof \DateTimeInterface
                ? $session->start_time->format('H:i')
                : Carbon::parse($session->start_time)->format('H:i');
            $end = $session->end_time instanceof \DateTimeInterface
                ? $session->end_time->format('H:i')
                : Carbon::parse($session->end_time)->format('H:i');
            $slotStart = strtotime($start);
            $slotEnd = strtotime($end);
            if ($newStart < $slotEnd && $newEnd > $slotStart) {
                return true;
            }
        }

        return false;
    }
}
