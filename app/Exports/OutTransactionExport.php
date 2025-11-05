<?php

namespace App\Exports;

use App\Models\OutTransaction;
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
use Carbon\Carbon;

class OutTransactionExport implements FromCollection, WithHeadings, WithStyles, WithEvents
{
    protected $kodeGrup;
    protected $date;

    public function __construct($kodeGrup = null, $date = null)
    {
        $this->kodeGrup = $kodeGrup;
        $this->date = $date;
    }

    public function collection()
    {
        $query = OutTransaction::with('item.category')->orderBy('date', 'desc');
        if ($this->kodeGrup) $query->where('kode_grup', $this->kodeGrup);
        if ($this->date) $query->whereDate('date', $this->date);

        return $query->get()->map(function ($row, $index) {
            return [
                'No' => $index + 1,
                'Tanggal' => Carbon::parse($row->date)->format('d M Y'),
                'Kode Grup' => $row->kode_grup ?? '-',
                'Nama Barang' => $row->item->name ?? '-',
                'Kategori' => $row->item->category->name ?? '-',
                'Jumlah Keluar' => $row->qty,
                'Penerima' => $row->receiver,
                'Catatan' => $row->note ?? '-',
            ];
        });
    }

    public function headings(): array
    {
        return ['No', 'Tanggal', 'Kode Grup', 'Nama Barang', 'Kategori', 'Jumlah Keluar', 'Penerima', 'Catatan'];
    }

    public function styles(Worksheet $sheet)
    {
        // Styling dilakukan pada AfterSheet agar mengikuti penambahan kop surat
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
                $drawing->setName('Logo');
                $drawing->setDescription('Logo PT Annur Maknah Wisata');
                $drawing->setPath(public_path('images/logo-amw.png'));
                $drawing->setHeight(60);
                $drawing->setCoordinates('A1');
                $drawing->setOffsetX(10);
                $drawing->setWorksheet($sheet);

                // Merge sel untuk kop surat
                $sheet->mergeCells('B1:H1');
                $sheet->mergeCells('B2:H2');
                $sheet->mergeCells('B3:H3');
                $sheet->mergeCells('B4:H4');

                // Isi teks kop surat
                $sheet->setCellValue('B1', 'PT ANNUR MAKNAH WISATA');
                $sheet->setCellValue('B2', 'Jl. KH Abdullah Syafei No.50 F12, Bukit Duri, Tebet, Jakarta Selatan 12840');
                $sheet->setCellValue('B3', 'WhatsApp: (+62) 821-1515-3335  |  Email: umroh.anamta@gmail.com');
                $sheet->setCellValue('B4', 'Dicetak: ' . now()->format('d F Y, H:i') . ' WIB');

                // Gaya teks kop surat
                $sheet->getStyle('B1')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 14],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);
                $sheet->getStyle('B2:B4')->applyFromArray([
                    'font' => ['size' => 11],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);

                // === Styling tabel (deteksi baris header otomatis)
                $highestRow = $sheet->getHighestRow();
                $headerRow = 5; // default
                for ($row = 1; $row <= $highestRow; $row++) {
                    $cell = trim((string) $sheet->getCell('A' . $row)->getValue());
                    if (strcasecmp($cell, 'No') === 0) {
                        $headerRow = $row;
                        break;
                    }
                }
                $lastRow = max($headerRow, $highestRow);

                // Header tebal + fill hijau lembut
                $sheet->getStyle('A' . $headerRow . ':H' . $headerRow)->getFont()->setBold(true);
                $sheet->getStyle('A' . $headerRow . ':H' . $headerRow)->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('E8F5E9');

                // Border seluruh tabel termasuk header
                $sheet->getStyle('A' . $headerRow . ':H' . $lastRow)
                    ->applyFromArray([
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                                'color' => ['argb' => '000000'],
                            ],
                        ],
                    ]);

                // Lebar otomatis
                foreach (range('A', 'H') as $col) {
                    $sheet->getColumnDimension($col)->setAutoSize(true);
                }

                // Rata tengah kolom tertentu
                $sheet->getStyle('A' . $headerRow . ':A' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('B' . $headerRow . ':B' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('F' . $headerRow . ':F' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            },
        ];
    }
}
