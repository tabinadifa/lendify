<?php

namespace App\Listeners;

use App\Models\ActivityLog;
use Illuminate\Auth\Events\Login;

class LogSuccessfulLogin
{
    public function handle(Login $event)
    {
        ActivityLog::create([
            'user_id'      => $event->user->id,
            'action'       => 'login',
            'description'  => "User login ke sistem",
            'subject_type' => get_class($event->user),
            'subject_id'   => $event->user->id,
            'ip_address'   => request()->ip(),
            'user_agent'   => request()->userAgent(),
        ]);
    }
}