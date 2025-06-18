<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pesan extends Model
{
    use SoftDeletes;

    protected $table = 'm_pesan';
    protected $primaryKey = 'pesan_id';
    public $incrementing = false; // Karena menggunakan UUID
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'pesan_id',
        'nama_pengirim',
        'perusahaan_pengirim',
        'email_pengirim',
        'telepon_pengirim',
        'pesan_pengirim',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
        'deleted_at',
        'deleted_by',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];
}