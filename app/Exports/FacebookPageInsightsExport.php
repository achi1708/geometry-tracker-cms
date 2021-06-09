<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use App\Exports\FacebookPageInsightsPerSheet;

class FacebookPageInsightsExport implements WithMultipleSheets
{
    use Exportable;

    protected $empresa_id;
    protected $metrics;
    
    public function __construct($empresa, $metrics)
    {
        $this->empresa_id = $empresa;
        $this->metrics = $metrics;
    }

    public function sheets(): array
    {
        $sheets = [];

        foreach($this->metrics as $metric){
            $sheets[] = new FacebookPageInsightsPerSheet($this->empresa_id, $metric);
        }

        return $sheets;
    }
}
