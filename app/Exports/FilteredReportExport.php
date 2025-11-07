<?php

namespace App\Exports;

use App\Models\OutTransaction;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class FilteredReportExport implements FromView
{
  protected $filters;

  public function __construct(array $filters)
  {
    $this->filters = $filters;
  }

  public function view(): View
  {
    $query = OutTransaction::with(["item.category"])
      ->when($this->filters["tanggal"], fn($q) => $q->whereDate("date", $this->filters["tanggal"]))
      ->when(
        $this->filters["kode_grup"],
        fn($q) => $q->where("kode_grup", $this->filters["kode_grup"]),
      )
      ->orderBy("date", "desc")
      ->get();

    return view("reports.export-excel", [
      "reports" => $query,
      "filters" => $this->filters,
    ]);
  }
}
