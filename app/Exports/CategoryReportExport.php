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
        $data = Item::with('category')
            ->orderBy('category_id')
            ->get()
            ->map(function ($item, $index) {
                return [
                    'No' => $index + 1,
                    'Kategori' => $item->category->name ?? '-',
                    'Nama Barang' => $item->name,
                    'Satuan' => $item->unit,
                    'Stok' => $item->stock,
                    'Stok Minimum' => $item->min_stock,
                    'Status' => $item->stock <= $item->min_stock ? 'Menipis' : 'Aman',
                ];
            });

        return $data;
    }

    public function headings(): array
    {
        return [
            'No',
            'Kategori',
            'Nama Barang',
            'Satuan',
            'Stok',
            'Stok Minimum',
            'Status',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Header tabel (baris 9)
        $sheet->getStyle('A9:G9')->getFont()->setBold(true);
        $sheet->getStyle('A9:G9')->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setARGB('E8F5E9');

        $lastRow = $sheet->getHighestRow();

        // Border hitam di seluruh tabel
        $sheet->getStyle('A9:G' . $lastRow)
            ->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['argb' => '000000'],
                    ],
                ],
            ]);

        // Auto width
        foreach (range('A', 'G') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Rata tengah kolom angka
        $sheet->getStyle('A9:A' . $lastRow)->getAlignment()->setHorizontal('center');
        $sheet->getStyle('E9:F' . $lastRow)->getAlignment()->setHorizontal('center');
        $sheet->getStyle('G9:G' . $lastRow)->getAlignment()->setHorizontal('center');
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Sisipkan 8 baris untuk kop surat
                $sheet->insertNewRowBefore(1, 8);

                // Tambah logo
                $drawing = new Drawing();
                $drawing->setName('Logo');
                $drawing->setDescription('Logo PT Annur Maknah Wisata');
                $drawing->setPath(public_path('images/logo-amw.png'));
                $drawing->setHeight(60);
                $drawing->setCoordinates('A1');
                $drawing->setOffsetX(10);
                $drawing->setWorksheet($sheet);

                // Merge sel teks kop surat
                $sheet->mergeCells('B1:G1');
                $sheet->mergeCells('B2:G2');
                $sheet->mergeCells('B3:G3');
                $sheet->mergeCells('B4:G4');

                // Isi teks kop surat
                $sheet->setCellValue('B1', 'PT ANNUR MAKNAH WISATA');
                $sheet->setCellValue('B2', 'Jl. KH Abdullah Syafei No.50 F12, Bukit Duri, Tebet, Jakarta Selatan 12840');
                $sheet->setCellValue('B3', 'WhatsApp: (+62) 821-1515-3335  |  Email: umroh.anamta@gmail.com');
                $sheet->setCellValue('B4', 'Dicetak: ' . now()->format('d F Y, H:i') . ' WIB');

                // Styling kop surat
                $sheet->getStyle('B1')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 14],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);
                $sheet->getStyle('B2:B4')->applyFromArray([
                    'font' => ['size' => 11],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);
            },
        ];
    }
}
