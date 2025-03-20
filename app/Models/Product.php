<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'products';
    public $timestamps = false;

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'productCode';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The data type of the primary key.
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'productCode',
        'productName',
        'productPrice'
    ];

    protected static function booted()
    {
        static::created(function ($model) {
            Log::create([
                'logID' => (string) Str::uuid(),
                'user' => session('email'),
                'table' => $model->getTable(),
                'type' => 'INSERT',
                'logTime' => now(),
            ]);
        });

        static::updated(function ($model) {
            Log::create([
                'logID' => (string) Str::uuid(),
                'user' => session('email'),
                'table' => $model->getTable(),
                'type' => 'UPDATE',
                'logTime' => now(),
            ]);
        });

        static::deleted(function ($model) {
            Log::create([
                'logID' => (string) Str::uuid(),
                'user' => session('email'),
                'table' => $model->getTable(),
                'type' => 'DELETE',
                'logTime' => now(),
            ]);
        });
    }
}
