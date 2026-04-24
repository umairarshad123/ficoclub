<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Lead extends Model
{
    protected $fillable = [
        'uuid', 'first_name', 'last_name', 'phone', 'email',
        'ssn_encrypted', 'dob',
        'employment_status', 'annual_income', 'own_or_rent', 'monthly_housing',
        'address', 'city', 'state', 'zip', 'years_at_address',
        'sc_email', 'sc_password_encrypted', 'sc_ssn_last4',
        'funding_type',
        'biz_entity_type', 'biz_name', 'biz_phone', 'biz_email',
        'biz_website', 'biz_incorp_state', 'biz_has_directors',
        'biz_annual_revenue', 'biz_has_financials',
        'biz_address', 'biz_city', 'biz_state', 'biz_zip',
        'final_phone', 'final_email',
        'ip_address', 'user_agent',
        'sheets_pushed', 'ghl_pushed',
    ];

    protected static function booted(): void
    {
        static::creating(function ($lead) {
            $lead->uuid = (string) Str::uuid();
        });
    }

    public function files()
    {
        return $this->hasMany(LeadFile::class);
    }
}