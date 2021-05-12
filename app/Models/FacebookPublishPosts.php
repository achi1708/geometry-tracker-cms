<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Empresas;

class FacebookPublishPosts extends Model
{
    use HasFactory;

    protected $table = 'facebook_publish_posts';

    protected $fillable = [
        'empresa_id',
        'post_id',
        'message',
        'application',
        'is_expired',
        'is_hidden',
        'is_popular',
        'is_published',
        'message_tags',
        'picture',
        'properties',
        'insights',
        'created_time'
    ];

    protected $casts = [
        'application' => 'array',
        'message_tags' => 'array',
        'properties' => 'array',
        'insights' => 'array'
    ];

    public function empresas()
    {
        return $this->belongsTo(Empresas::class, 'empresa_id');
    }
}
