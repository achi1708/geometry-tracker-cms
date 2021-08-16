<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\FacebookPublishPosts;

class FacebookPublishPostsProperties extends Model
{
    use HasFactory;

    protected $table = 'facebook_publish_posts_properties';

    protected $fillable = [
        'post_id',
        'type',
        'metric',
        'metric_value'
    ];

    public function publish_post()
    {
        return $this->belongsTo(FacebookPublishPosts::class, 'post_id');
    }
}
