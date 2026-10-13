<?php

namespace App\Services\Wcpu;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class PortalActionNotifier
{
    public static function send(string $action, Model $record): void
    {
        MultiroleActionNotifications::record($action, $record);
        // Action notifications are presented in the in-app activity list.
        Log::info('Portal action recorded for in-app notifications.', [
            'action' => $action,
            'record_type' => class_basename($record),
            'record_id' => $record->getKey(),
        ]);
    }
}
