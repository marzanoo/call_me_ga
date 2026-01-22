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
        'tanggal',
        'lokasi',
        'kategori',
        'permasalahan',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function detailFotoReports()
    {
        return $this->hasMany(DetailFotoReport::class, 'report_id', 'id');
    }

    public function detailStatusReports()
    {
        return $this->hasMany(DetailStatusReport::class, 'report_id', 'id');
    }
}
