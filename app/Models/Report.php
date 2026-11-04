<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'category_id',
        'district_id',
        'ticket_code',
        'description',
        'photo_url',
        'latitude',
        'longitude',
        'ai_validation',
        'ai_confidence',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'ai_validation' => 'boolean',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function district()
    {
        return $this->belongsTo(District::class);
    }

    public function category()
    {
        return $this->belongsTo(ReportCategory::class, 'category_id');
    }

    public function workOrder()
    {
        return $this->hasOne(WorkOrder::class);
    }

    public function rating()
    {
        return $this->hasOne(Rating::class);
    }

    public function auditLogs()
    {
        return $this->hasMany(AuditLog::class)->latest('created_at');
    }
}
