<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Empresas;

class InstragramMedia extends Model
{
    use HasFactory;

    protected $table = 'instagram_media';

    protected $fillable = [
        'empresa_id',
        'media_id',
        'ig_id',
        'caption',
        'comments_count',
        'like_count',
        'media_product_type',
        'media_type',
        'media_url',
        'owner',
        'permalink',
        'timestamp',
        'username',
        'insights'
    ];

    protected $casts = [
        'owner' => 'array',
        'insights' => 'array'
    ];

    public function empresas()
    {
        return $this->belongsTo(Empresas::class, 'empresa_id');
    }
}
