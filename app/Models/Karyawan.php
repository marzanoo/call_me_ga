<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Karyawan extends Model
{
    use HasFactory;

    protected $connection = 'hris';
    protected $table = 'karyawans';
    protected $primaryKey = 'emp_id';

    protected $fillable = [
        'emp_id',
        'emp_name',
        'user_id_hris',
        'company',
        'position_title',
        'dept',
        'supervisor_name',
        'email',
        'remarks',
        'created_at',
        'updated_at',
    ];
}
