<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;
    protected $table = 'reports';
    protected $fillable = [
        'user_id',
        'assigned_to',
        'tanggal',
        'lokasi',
        'lokasi_area',
        'lokasi_detail',
        'lokasi_catatan',
        'kategori',
        'permasalahan',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function assignee()
    {
        return $this->belongsTo(User::class, 'assigned_to', 'id');
    }

    public function detailFotoReports()
    {
        return $this->hasMany(DetailFotoReport::class, 'report_id', 'id');
    }

    public function latestStatus()
    {
        return $this->hasOne(DetailStatusReport::class)->latestOfMany();
    }


    public function detailStatusReports()
    {
        return $this->hasMany(DetailStatusReport::class, 'report_id', 'id');
    }

    public function detailFotoReportSelesais()
    {
        return $this->hasMany(DetailFotoReportSelesai::class, 'report_id', 'id');
    }
}
