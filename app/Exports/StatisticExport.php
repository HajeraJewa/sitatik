<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class StatisticExport implements FromCollection, WithHeadings, WithTitle, ShouldAutoSize, WithStyles
{
  protected $data;

  public function __construct($data)
  {
    $this->data = $data;
  }

  public function collection()
  {
    return collect($this->data->isi_data);
  }

  public function headings(): array
  {
    if (empty($this->data->isi_data)) {
      return [];
    }

    return array_keys($this->data->isi_data[0]);
  }

  public function title(): string
  {
    return 'Data Statistik ' . $this->data->tahun;
  }
  public function styles(Worksheet $sheet)
  {
    return [
      1 => ['font' => ['bold' => true]],
    ];
  }
}