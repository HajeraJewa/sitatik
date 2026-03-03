<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;   
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SitatikExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $data;
    protected $type;

    public function __construct($data, $type)
    {
        $this->data = $data;
        $this->type = $type;
    }

    public function collection()
    {
        return $this->data;
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }

    public function headings(): array
    {
        if ($this->type == 'pengguna')
            return ['INSTANSI / PERANGKAT DAERAH', 'EMAIL LOGIN', 'LATITUDE', 'LONGITUDE', 'TANGGAL REGISTRASI'];
        if ($this->type == 'opd')
            return ['KODE OPD', 'NAMA PERANGKAT DAERAH', 'ALIAS / SINGKATAN'];
        return ['INSTANSI PENGUSUL', 'NAMA TABEL / KEGIATAN', 'KATEGORI', 'STATUS', 'TANGGAL'];
    }

    public function map($row): array
    {
        if ($this->type == 'pengguna') {
            return [
                $row->perangkatDaerah->nama_opd ?? 'N/A',
                $row->email,
                $row->latitude,
                $row->longitude,
                $row->created_at->format('d/m/Y')
            ];
        }
        if ($this->type == 'opd') {
            return [
                $row->kode_opd,
                $row->nama_opd,
                $row->alias_opd
            ];
        }
        return [
            $row->perangkatDaerah->nama_opd ?? 'N/A',
            $row->table_name,
            $row->category,
            $row->status,
            $row->created_at->format('d/m/Y')
        ];
    }
}