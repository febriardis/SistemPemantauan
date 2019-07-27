<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Monitoring extends Model
{
    protected $table = 'tb_monitoring_data';

    protected $fillable = [
        'temperature',
        'ph',
        'turbidity',
        'status',
        // 'created_at'
    ];

    public $timestamps = false;

    public static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->created_at = \Carbon\Carbon::now('Asia/Jakarta');
        });
    }

}
