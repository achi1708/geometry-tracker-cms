<?php

namespace App\Exports;

use App\Models\FacebookPageInsights;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;

class FacebookPageInsightsPerSheet implements FromQuery, WithTitle, WithHeadings
{
    private $empresa_id;
    private $metric;

    public function __construct($empresa, $metric)
    {
        $this->empresa_id = $empresa;
        $this->metric  = $metric;
    }

    public function query()
    {
        return FacebookPageInsights::select('metric_date', 'metric_value')
            ->where('empresa_id', $this->empresa_id)
            ->where('metric', $this->metric)
            ->orderBy('metric_date', 'desc');
    }

    public function title(): string
    {
        return 'Metrica ' . $this->metric;
    }

    public function headings(): array
    {
        return ['Fecha', 'Valor'];
    }
}
