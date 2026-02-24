<?php

namespace App\Exports;

use Illuminate\Contracts\Support\Responsable;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PatientsExport implements FromCollection, WithHeadings, WithStyles, WithEvents
{
    protected Collection $data;
    protected string $title;

    public function __construct(Collection $data, string $title = 'Patients')
    {
        $this->data = $data;
        $this->title = $title;
    }

    public function collection()
    {
        return $this->data->map(function ($p) {
            return [
                'Predict3DId' => $p->Predict3DId,
                'Full Name' => $p->FullName ?? '',
                'Phone Number' => $p->PhoneNumber ?? '',
                'Gender' => $p->Gender ?? '',
                'Date Of Birth' => optional($p->DateOfBirth)->format('Y-m-d'),
                'Doctor Name' => $p->DoctorName ?? '',
                'Doctor Email' => $p->doctor_email ?? '',
                'Address' => $p->Address ?? '',
                'Territory' => $p->TerritoryName ?? '',
                'Regional' => $p->RegionalName ?? '',
                'Case Type' => $p->case_type ?? '',
                'Created At' => optional($p->created_at)->format('Y-m-d H:i:s'),
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Predict3DId', 'Full Name', 'Phone Number', 'Gender', 'Date Of Birth',
            'Doctor Name', 'Doctor Email', 'Address', 'Territory', 'Regional', 'Case Type', 'Created At'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Title row
        $sheet->setCellValue('A1', $this->title);
        $sheet->mergeCells('A1:L1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal('center');

        // Move headings down to row 2
        // Headings styling will be handled in events too
        return [
            2 => ['font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']]],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Write headings into row 2 (since row 1 is title)
                $headings = $this->headings();
                $sheet->fromArray([$headings], null, 'A2');

                // Write data starting row 3
                $data = $this->collection()->toArray();
                if (!empty($data)) {
                    $sheet->fromArray($data, null, 'A3');
                }

                // Header fill
                $sheet->getStyle('A2:L2')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('FF374151');

                // Borders and autosize
                $highestRow = max(3, $sheet->getHighestRow());
                $sheet->getStyle('A2:L' . $highestRow)->getBorders()->getAllBorders()
                    ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN)
                    ->getColor()->setARGB('FFCBD5E1');

                // Per-column colors (pastel)
                $colors = [
                    'FFFDE68A','FFA7F3D0','FFBFDBFE','FFFBCFE8','FFE9D5FF','FFC7D2FE',
                    'FFBBF7D0','FFFEE2E2','FFFDE68A','FFA7F3D0','FFBFDBFE','FFFBCFE8',
                ];
                foreach (range('A', 'L') as $idx => $col) {
                    // Apply fill for data rows
                    $sheet->getStyle($col.'3:'.$col.$highestRow)->getFill()
                        ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                        ->getStartColor()->setARGB($colors[$idx]);
                    // Autosize
                    $sheet->getColumnDimension($col)->setAutoSize(true);
                }
            }
        ];
    }
}
