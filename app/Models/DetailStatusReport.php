<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailStatusReport extends Model
{
    use HasFactory;
    protected $table = 'detail_status_reports';
    protected $fillable = [
        'report_id',
        'status',
        'keterangan',
        'feedback',
    ];

    public function report()
    {
        return $this->belongsTo(Report::class, 'report_id', 'id');
    }
}
