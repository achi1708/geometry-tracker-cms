<?php

namespace App\Exports;

use App\Models\InstragramMedia;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use Maatwebsite\Excel\Concerns\WithHeadings;

class InstagramMediaExport implements FromCollection, WithStrictNullComparison, WithHeadings
{
    public function __construct($instagram_media)
    {
        $this->instagram_media = $instagram_media;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return $this->instagram_media;
    }

    public function headings(): array
    {
        return [
                'media_id', 'ig_id', 'caption', 'comments_count', 'like_count', 'media_product_type', 'media_type', 'media_url', 
                'permalink',
                'username',
                'creado',
                'engagement',
                'impressions',
                'reach',
                'saved',
                'video_views',
                'carousel_album_engagement',
                'carousel_album_impressions',
                'carousel_album_reach',
                'carousel_album_saved',
                'carousel_album_video_views'
            ];
    }
}
