<?php

namespace App\Listeners;

use App\Models\ActivityLog;
use Illuminate\Auth\Events\Logout;

class LogSuccessfulLogout
{
    public function handle(Logout $event)
    {
        if ($event->user) {
            ActivityLog::create([
                'user_id'      => $event->user->id,
                'action'       => 'logout',
                'description'  => "User logout dari sistem",
                'subject_type' => get_class($event->user),
                'subject_id'   => $event->user->id,
                'ip_address'   => request()->ip(),
                'user_agent'   => request()->userAgent(),
            ]);
        }
    }
}