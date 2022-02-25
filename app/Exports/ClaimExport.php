<?php

namespace App\Exports;

use App\Models\FileUpload;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class ClaimExport implements FromArray, WithHeadings
{
    public function headings():array{
        return[
            'Date',
            'Company ID',
            'Pt. File#',
            'Patient Name',
            'Invoice#',
            'Consultation',
            'Drugs',
            'Services',
            'Grand Total',
            'Discount',
            'Total',
        ];
    }

    protected $claims;

    public function __construct(array $claims)
    {
        $this->claims = $claims;
    }

    public function array(): array
    {
        return $this->claims;
    }
}
