<?php

namespace App\Exports;

use App\Models\DataParkir;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class ParkirExport implements FromCollection, WithHeadings, WithStyles, WithTitle, WithEvents
{
    protected $from;
    protected $to;

    public function __construct($from, $to)
    {
        $this->from = $from;
        $this->to = $to;
    }

    public function collection(): Collection
    {
        $data = DataParkir::with([
            'platNomor',
            'merkKendaraan',
            'tipeKendaraan',
            'warnaKendaraan'
        ])
            ->whereBetween('waktu_masuk', [$this->from, $this->to])
            ->take(500)
            ->get();

        return $data->map(function ($item) {
            return [
                'kode_parkir'    => $item->kode_parkir,
                'plat_nomor'     => optional($item->platNomor)->plat_nomor ?? '-',
                'merk_kendaraan' => optional($item->merkKendaraan)->merk_kendaraan ?? '-',
                'tipe_kendaraan' => optional($item->tipeKendaraan)->jenis->nama_kendaraan ?? '-',
                'warna_kendaraan' => optional($item->warnaKendaraan)->warna ?? '-',
                'waktu_masuk'    => $item->waktu_masuk,
                'waktu_keluar'   => $item->waktu_keluar ?? '-',
                'status'         => $item->status,
                'lokasi'         => $item->lokasi ?? '-',
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Kode Parkir',
            'Plat Nomor',
            'Merk Kendaraan',
            'Tipe Kendaraan',
            'Warna Kendaraan',
            'Waktu Masuk',
            'Waktu Keluar',
            'Status',
            'Lokasi',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Header row bold & background color
            1 => ['font' => ['bold' => true], 'alignment' => ['horizontal' => 'center']],
        ];
    }

    public function title(): string
    {
        return 'Data Parkir';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                // Auto-size all columns
                foreach (range('A', 'I') as $col) {
                    $event->sheet->getDelegate()->getColumnDimension($col)->setAutoSize(true);
                }

                // Set header background color
                $event->sheet->getStyle('A1:I1')->applyFromArray([
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'color' => ['rgb' => 'F87171'] // merah muda
                    ],
                    'font' => [
                        'color' => ['rgb' => 'FFFFFF'],
                        'bold' => true
                    ],
                ]);
            },
        ];
    }
}
