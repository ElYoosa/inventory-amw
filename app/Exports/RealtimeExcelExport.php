<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use App\Exports\RealtimeReportExport;

class RealtimeExcelExport implements WithMultipleSheets
{
    protected $transactions;
    protected $meta;

    public function __construct($transactions, $meta)
    {
        $this->transactions = $transactions;
        $this->meta = $meta;
    }

    public function sheets(): array
    {
        return [
            new RealtimeReportExport($this->transactions, $this->meta),
        ];
    }
}
