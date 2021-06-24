<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use App\Exports\FacebookAdsPerSheet;

class FacebookAdsExport implements WithMultipleSheets
{
    use Exportable;

    protected $empresa_id;
    protected $accounts_ids;
    
    public function __construct($empresa, $accounts_ids)
    { 
        $this->empresa_id = $empresa;
        $this->accounts_ids = $accounts_ids;
    }

    public function sheets(): array
    {
        $sheets = [];

        foreach($this->accounts_ids as $account_id){
            $sheets[] = new FacebookAdsPerSheet($this->empresa_id, $account_id);
        }

        return $sheets;
    }
}
