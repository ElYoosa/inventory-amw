<?php

namespace App\Exports;

use App\Models\Item;
use App\Models\Category;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Collection;

class ItemExport implements FromCollection, WithHeadings, WithStyles
{
  protected $category;

  public function __construct($category = null)
  {
    $this->category = $category;
  }

  public function collection()
  {
    $query = Item::with("category");

    if ($this->category) {
      $query->whereHas("category", fn($q) => $q->where("name", $this->category));
    }

    return $query->get()->map(function ($item) {
      return [
        "Kategori" => $item->category->name ?? "-",
        "Nama Barang" => $item->name,
        "Satuan" => $item->unit,
        "Stok" => $item->stock,
        "Stok Minimum" => $item->min_stock,
        "Status" => $item->stock <= $item->min_stock ? "Menipis" : "Aman",
      ];
    });
  }

  public function headings(): array
  {
    return ["Kategori", "Nama Barang", "Satuan", "Stok", "Stok Minimum", "Status"];
  }

  public function styles(Worksheet $sheet)
  {
    // 🔹 Set header bold dan border seluruh tabel
    $sheet->getStyle("A1:F1")->getFont()->setBold(true);
    $sheet
      ->getStyle("A1:F1")
      ->getFill()
      ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
      ->getStartColor()
      ->setARGB("E8F5E9");

    $sheet->getStyle("A1:F" . $sheet->getHighestRow())->applyFromArray([
      "borders" => [
        "allBorders" => [
          "borderStyle" => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
          "color" => ["argb" => "000000"],
        ],
      ],
    ]);

    // 🔹 Auto-size columns
    foreach (range("A", "F") as $col) {
      $sheet->getColumnDimension($col)->setAutoSize(true);
    }

    return [];
  }
}
