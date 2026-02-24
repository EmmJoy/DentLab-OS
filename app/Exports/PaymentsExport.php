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

class PaymentsExport implements FromCollection, WithHeadings, WithStyles, WithEvents
{
    protected Collection $data;
    protected string $title;

    public function __construct(Collection $data, string $title = 'Payments')
    {
        $this->data = $data;
        $this->title = $title;
    }

    public function collection()
    {
        return $this->data->map(function ($r) {
            return [
                'Predict3DId' => $r->predict3d_id,
                'Patient' => $r->patient_full_name ?? '',
                'Phone' => $r->patient_phone ?? '',
                'Plan ID' => $r->plan_id ?? '',
                'Payment Date' => optional($r->payment_date)->format('Y-m-d'),
                'Payment Method' => $r->payment_method ?? '',
                'Total Amount' => (float) ($r->total_amount ?? 0),
                'Total Paid' => (float) ($r->total_paid ?? 0),
                'Remaining' => (float) ($r->remaining_amount ?? 0),
                'Type' => !empty($r->is_installment) ? 'Installment' : 'Full',
                'Next Payment Date' => !empty($r->is_installment) ? (optional($r->next_payment_date)->format('Y-m-d')) : '',
                'Processed By' => $r->processor_name ?? '',
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Predict3DId','Patient','Phone','Plan ID','Payment Date','Payment Method',
            'Total Amount','Total Paid','Remaining','Type','Next Payment Date','Processed By'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->setCellValue('A1', $this->title);
        $sheet->mergeCells('A1:L1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal('center');
        return [ 2 => ['font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']]] ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Headings row
                $sheet->fromArray([$this->headings()], null, 'A2');

                // Data
                $data = $this->collection()->toArray();
                if (!empty($data)) {
                    $sheet->fromArray($data, null, 'A3');
                }

                // Style header
                $sheet->getStyle('A2:L2')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('FF374151');

                $highestRow = max(3, $sheet->getHighestRow());
                $sheet->getStyle('A2:L' . $highestRow)->getBorders()->getAllBorders()
                    ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN)
                    ->getColor()->setARGB('FFCBD5E1');

                // Per-column fills
                $colors = [
                    'FFFDE68A','FFA7F3D0','FFBFDBFE','FFFBCFE8','FFE9D5FF','FFC7D2FE',
                    'FFBBF7D0','FFFEE2E2','FFFDE68A','FFA7F3D0','FFBFDBFE','FFFBCFE8',
                ];
                foreach (range('A', 'L') as $idx => $col) {
                    $sheet->getStyle($col.'3:'.$col.$highestRow)->getFill()
                        ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                        ->getStartColor()->setARGB($colors[$idx]);
                    $sheet->getColumnDimension($col)->setAutoSize(true);
                }
            }
        ];
    }
}
