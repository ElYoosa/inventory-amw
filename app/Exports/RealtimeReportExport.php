<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use App\Exports\Traits\SanitizesSheetTitle;

class RealtimeReportExport implements FromView, WithTitle, WithEvents, WithStyles, ShouldAutoSize
{
    use SanitizesSheetTitle;

    protected $transactions;
    protected $meta;

    public function __construct($transactions, $meta)
    {
        $this->transactions = $transactions;
        $this->meta = $meta;
    }

    public function view(): View
    {
        return view('reports.realtime-excel-plain', [
            'transactions' => $this->transactions,
            'meta'         => $this->meta,
            'generated_at' => now()->format('d/m/Y H:i'),
        ]);
    }

    /** ✅ Sheet name pendek & aman */
    public function title(): string
    {
        return $this->sanitizeSheetTitle('Realtime');
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Tambah kop surat
                $sheet->insertNewRowBefore(1, 8);

                $drawing = new Drawing();
                $drawing->setName('Logo');
                $drawing->setDescription('Logo PT Annur Maknah Wisata');
                $drawing->setPath(public_path('images/logo-amw.png'));
                $drawing->setHeight(60);
                $drawing->setCoordinates('A1');
                $drawing->setOffsetX(10);
                $drawing->setWorksheet($sheet);

                $sheet->mergeCells('B1:I1');
                $sheet->mergeCells('B2:I2');
                $sheet->mergeCells('B3:I3');
                $sheet->mergeCells('B4:I4');

                $sheet->setCellValue('B1', 'PT ANNUR MAKNAH WISATA');
                $sheet->setCellValue('B2', 'Jl. KH Abdullah Syafei No.50 F12, Bukit Duri, Tebet, Jakarta Selatan 12840');
                $sheet->setCellValue('B3', 'WhatsApp: (+62) 821-1515-3335  |  Email: umroh.anamta@gmail.com');
                $sheet->setCellValue('B4', 'Dicetak: ' . now()->format('d F Y, H:i') . ' WIB');

                $sheet->getStyle('B1')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 14],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);

                $sheet->getStyle('B2:B4')->applyFromArray([
                    'font' => ['size' => 11],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);

                $sheet->getRowDimension(8)->setRowHeight(8);
            },
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $lastRow = $this->transactions->count() + 10;

        // Header tabel hijau lembut
        $sheet->getStyle('A9:I9')->applyFromArray([
            'font' => ['bold' => true],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER,
            ],
            'fill' => [
                'fillType'   => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'E8F5E9'],
            ],
        ]);

        // Border seluruh tabel
        $sheet->getStyle('A9:I' . $lastRow)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => '000000'],
                ],
            ],
        ]);

        foreach (range('A', 'I') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $sheet->getStyle('A10:C' . $lastRow)
            ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        return [];
    }
}
