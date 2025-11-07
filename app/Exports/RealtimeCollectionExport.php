<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Illuminate\Contracts\Support\Arrayable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Maatwebsite\Excel\Events\AfterSheet;
use App\Exports\Traits\SanitizesSheetTitle;

class RealtimeCollectionExport implements
  FromCollection,
  WithHeadings,
  WithStyles,
  WithEvents,
  WithTitle,
  ShouldAutoSize
{
  use SanitizesSheetTitle;

  protected $transactions;
  protected $meta;

  public function __construct($transactions, $meta)
  {
    $this->transactions = collect($transactions)->values();
    $this->meta = $meta;
  }

  public function collection()
  {
    return $this->transactions->values()->map(function ($tx, $i) {
      $type = $tx->type ?? "-";
      $date = $tx->date ? \Carbon\Carbon::parse($tx->date)->format("d M Y") : "-";
      $kodeGrup = $tx->kode_grup ?? "-";
      $itemName = optional($tx->item)->name ?? "-";
      $category = optional(optional($tx->item)->category)->name ?? "-";
      $qty = $tx->qty ?? "-";
      $partner = $type === "in" ? $tx->sender ?? "-" : $tx->receiver ?? "-";
      $note = $tx->note ?? "-";

      return [
        "No" => $i + 1,
        "Type" => $type,
        "Tanggal" => $date,
        "Kode Grup" => $kodeGrup,
        "Nama Barang" => $itemName,
        "Kategori" => $category,
        "Qty" => $qty,
        "Penerima" => $partner,
        "Catatan" => $note,
      ];
    });
  }

  public function headings(): array
  {
    return [
      "No",
      "Type",
      "Tanggal",
      "Kode Grup",
      "Nama Barang",
      "Kategori",
      "Qty",
      "Penerima",
      "Catatan",
    ];
  }

  public function registerEvents(): array
  {
    return [
      AfterSheet::class => function (AfterSheet $event) {
        $sheet = $event->sheet->getDelegate();

        // Sisipkan kop surat 4 baris
        $sheet->insertNewRowBefore(1, 4);

        $drawing = new Drawing();
        $drawing->setName("Logo");
        $drawing->setDescription("Logo PT Annur Maknah Wisata");
        $drawing->setPath(public_path("images/logo-amw.png"));
        $drawing->setHeight(60);
        $drawing->setCoordinates("A1");
        $drawing->setOffsetX(10);
        $drawing->setWorksheet($sheet);

        // Merge & isi kop
        $sheet->mergeCells("B1:I1");
        $sheet->mergeCells("B2:I2");
        $sheet->mergeCells("B3:I3");
        $sheet->mergeCells("B4:I4");
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

        $sheet->getStyle("B1")->applyFromArray([
          "font" => ["bold" => true, "size" => 14],
          "alignment" => ["horizontal" => Alignment::HORIZONTAL_CENTER],
        ]);
        $sheet->getStyle("B2:B4")->applyFromArray([
          "font" => ["size" => 11],
          "alignment" => ["horizontal" => Alignment::HORIZONTAL_CENTER],
        ]);

        // Styling tabel
        $headerRow = 5;
        $lastRow = $sheet->getHighestRow();

        // Header fill hijau + tebal
        $sheet
          ->getStyle("A" . $headerRow . ":I" . $headerRow)
          ->getFont()
          ->setBold(true);
        $sheet
          ->getStyle("A" . $headerRow . ":I" . $headerRow)
          ->getFill()
          ->setFillType(Fill::FILL_SOLID)
          ->getStartColor()
          ->setARGB("E8F5E9");

        // Border seluruh tabel
        $sheet->getStyle("A" . $headerRow . ":I" . $lastRow)->applyFromArray([
          "borders" => [
            "allBorders" => [
              "borderStyle" => Border::BORDER_THIN,
              "color" => ["argb" => "000000"],
            ],
          ],
        ]);

        // Perataan angka/kolom tertentu
        $sheet
          ->getStyle("A" . $headerRow . ":A" . $lastRow)
          ->getAlignment()
          ->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet
          ->getStyle("C" . $headerRow . ":C" . $lastRow)
          ->getAlignment()
          ->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet
          ->getStyle("G" . $headerRow . ":G" . $lastRow)
          ->getAlignment()
          ->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Auto width
        foreach (range("A", "I") as $col) {
          $sheet->getColumnDimension($col)->setAutoSize(true);
        }
      },
    ];
  }

  public function styles(Worksheet $sheet)
  {
    // All styling handled in AfterSheet
  }

  public function title(): string
  {
    return $this->sanitizeSheetTitle("Realtime");
  }
}
