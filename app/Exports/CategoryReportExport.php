<?php

namespace App\Exports;

use App\Models\Item;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Maatwebsite\Excel\Events\AfterSheet;

class CategoryReportExport implements FromCollection, WithHeadings, WithStyles, WithEvents
{
  public function collection()
  {
    // Urutkan sesuai tampilan UI: Kategori (A-Z), lalu Nama Barang (A-Z)
    $data = Item::query()
      ->select("items.*")
      ->with("category")
      ->leftJoin("categories", "categories.id", "=", "items.category_id")
      ->orderBy("categories.name")
      ->orderBy("items.name")
      ->get()
      ->map(function ($item, $index) {
        return [
          "No" => $index + 1,
          "Kategori" => $item->category->name ?? "-",
          "Nama Barang" => $item->name,
          "Satuan" => $item->unit,
          "Stok" => $item->stock,
          "Stok Minimum" => $item->min_stock,
          "Status" => $item->stock <= $item->min_stock ? "Menipis" : "Aman",
        ];
      });

    return $data;
  }

  public function headings(): array
  {
    return ["No", "Kategori", "Nama Barang", "Satuan", "Stok", "Stok Minimum", "Status"];
  }

  public function styles(Worksheet $sheet)
  {
    // Penataan utama dipindahkan ke AfterSheet agar posisi tepat
  }

  public function registerEvents(): array
  {
    return [
      AfterSheet::class => function (AfterSheet $event) {
        $sheet = $event->sheet->getDelegate();

        // Sisipkan 4 baris untuk kop surat (baris 1-4)
        $sheet->insertNewRowBefore(1, 4);

        // Tambah logo
        $drawing = new Drawing();
        $drawing->setName("Logo");
        $drawing->setDescription("Logo PT Annur Maknah Wisata");
        $drawing->setPath(public_path("images/logo-amw.png"));
        $drawing->setHeight(60);
        $drawing->setCoordinates("A1");
        $drawing->setOffsetX(10);
        $drawing->setWorksheet($sheet);

        // Merge sel teks kop surat
        $sheet->mergeCells("B1:G1");
        $sheet->mergeCells("B2:G2");
        $sheet->mergeCells("B3:G3");
        $sheet->mergeCells("B4:G4");

        // Isi teks kop surat
        $sheet->setCellValue("B1", "PT ANNUR MAKNAH WISATA");
        $sheet->setCellValue(
          "B2",
          "Jl. KH Abdullah Syafei No.50 F12, Bukit Duri, Tebet, Jakarta Selatan 12840",
        );
        $sheet->setCellValue(
          "B3",
          "WhatsApp: (+62) 821-1515-3335  |  Email: umroh.anamta@gmail.com",
        );
        $sheet->setCellValue("B4", "Dicetak: " . now()->format("d F Y, H:i") . " WIB");

        // Styling kop surat
        $sheet->getStyle("B1")->applyFromArray([
          "font" => ["bold" => true, "size" => 14],
          "alignment" => ["horizontal" => Alignment::HORIZONTAL_CENTER],
        ]);
        $sheet->getStyle("B2:B4")->applyFromArray([
          "font" => ["size" => 11],
          "alignment" => ["horizontal" => Alignment::HORIZONTAL_CENTER],
        ]);

        // === Styling tabel setelah kop tersisip ===
        $headerRow = 5;
        $lastRow = $sheet->getHighestRow();

        // Header dengan fill hijau muda dan bold
        $sheet
          ->getStyle("A" . $headerRow . ":G" . $headerRow)
          ->getFont()
          ->setBold(true);
        $sheet
          ->getStyle("A" . $headerRow . ":G" . $headerRow)
          ->getFill()
          ->setFillType(Fill::FILL_SOLID)
          ->getStartColor()
          ->setARGB("E8F5E9");

        // Border seluruh tabel dari judul hingga baris terakhir
        $sheet->getStyle("A" . $headerRow . ":G" . $lastRow)->applyFromArray([
          "borders" => [
            "allBorders" => [
              "borderStyle" => Border::BORDER_THIN,
              "color" => ["argb" => "000000"],
            ],
          ],
        ]);

        // Alignment angka dan kolom tertentu
        $sheet
          ->getStyle("A" . $headerRow . ":A" . $lastRow)
          ->getAlignment()
          ->setHorizontal("center");
        $sheet
          ->getStyle("E" . $headerRow . ":F" . $lastRow)
          ->getAlignment()
          ->setHorizontal("center");
        $sheet
          ->getStyle("G" . $headerRow . ":G" . $lastRow)
          ->getAlignment()
          ->setHorizontal("center");

        // Auto width kolom
        foreach (range("A", "G") as $col) {
          $sheet->getColumnDimension($col)->setAutoSize(true);
        }
      },
    ];
  }
}
