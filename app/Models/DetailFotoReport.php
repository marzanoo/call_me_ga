<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailFotoReport extends Model
{
    use HasFactory;
    protected $table = 'detail_foto_reports';
    protected $fillable = [
        'report_id',
        'image_path',
    ];
    public function report()
    {
        return $this->belongsTo(Report::class, 'report_id', 'id');
    }
}
