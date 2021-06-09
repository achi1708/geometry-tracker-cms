<?php

namespace App\Exports;

use App\Models\FacebookPublishPosts;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use Maatwebsite\Excel\Concerns\WithHeadings;

class FacebookPublishPostsExport implements FromCollection, WithStrictNullComparison, WithHeadings
{

    public function __construct($facebook_publish_posts)
    {
        $this->facebook_publish_posts = $facebook_publish_posts;
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
        return [
                'mensaje', 'foto', 'oculto', 'expirado', 'popular', 'publicado', 'creado', 'tags', 
                'insight1',
                'insight_value1',
                'insight2',
                'insight_value2',
                'insight3',
                'insight_value3',
                'insight4',
                'insight_value4',
                'insight5',
                'insight_value5',
                'insight6',
                'insight_value6',
                'insight7',
                'insight_value7',
                'insight8',
                'insight_value8',
                'insight9',
                'insight_value9',
                'insight10',
                'insight_value10',
                'insight11',
                'insight_value11',
                'insight12',
                'insight_value12',
                'insight13',
                'insight_value13',
                'insight14',
                'insight_value14',
                'insight15',
                'insight_value15',
                'insight16',
                'insight_value16',
                'insight17',
                'insight_value17',
                'insight18',
                'insight_value18',
                'insight19',
                'insight_value19',
                'insight20',
                'insight_value20'
            ];
    }
}
