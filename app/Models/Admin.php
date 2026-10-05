<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;

class Admin extends Model implements AuthenticatableContract
{
    use Authenticatable;

    // No $fillable existed before — needed now so Auth::user()->update(...)
    // can actually persist the Google Calendar token below.
    protected $fillable = [
        'name',
        'email',
        'password',
        'status',
        'hr_panel_access',
        'google_calendar_token',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'otp',
    ];

    protected $casts = [
        // The full token Google's SDK returns (access_token, refresh_token,
        // expires_in, created, scope, ...) — stored as-is so it can be fed
        // straight back into Client::setAccessToken().
        'google_calendar_token' => 'array',
    ];

    public function canAccessHrPanel()
    {
        return $this->hr_panel_access == 'active';
    }

    public function hasGoogleCalendarConnected(): bool
    {
        return ! empty($this->google_calendar_token['refresh_token'] ?? null);
    }
}
