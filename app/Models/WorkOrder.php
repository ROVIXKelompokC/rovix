<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'report_id',
        'officer_id',
        'spk_number',
        'target_sla',
        'work_status',
        'after_photo_url',
        'repair_notes',
        'resolved_at',
    ];

    protected function casts(): array
    {
        return [
            'target_sla' => 'date',
            'resolved_at' => 'datetime',
        ];
    }

    public function report()
    {
        return $this->belongsTo(Report::class);
    }

    public function officer()
    {
        return $this->belongsTo(User::class, 'officer_id');
    }
}
