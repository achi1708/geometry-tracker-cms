<?php

namespace App\Exports;

use App\Models\FacebookPublishPosts;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use Maatwebsite\Excel\Concerns\WithHeadings;

class FacebookPublishPostsExport implements FromCollection, WithStrictNullComparison, WithHeadings
{

    private $publish_post_titles;

    public function __construct($facebook_publish_posts, $insight_titles)
    {
        $this->facebook_publish_posts = $facebook_publish_posts;

        $this->publish_post_titles = array('mensaje', 'foto', 'oculto', 'expirado', 'popular', 'publicado', 'creado', 'tags');
        $this->publish_post_titles = array_merge($this->publish_post_titles, $insight_titles);

    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
       return $this->facebook_publish_posts;
    }

    public function headings(): array
    {
        return $this->publish_post_titles;
    }
}
