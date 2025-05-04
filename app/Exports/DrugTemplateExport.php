<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class DrugTemplateExport implements FromArray, WithHeadings
{
    protected $headers;

    public function __construct($headers)
    {
        $this->headers = $headers;
    }

    /**
     * Data contoh yang akan diekspor sebagai template
     */
    public function array(): array
    {
        return [
            ['Paracetamol', 'Analgesik', 'Tablet', 'Obat Bebas', 'Strip', 10 ],
            ['Amoxicillin', 'Antibiotik', 'Kapsul',  'Obat Bebas', 'Strip', 5],
        ];
    }

    /**
     * Header kolom untuk template
     */ 
    public function headings(): array
{
    return [
        'Nama Obat',
        'Kategori Obat',
        'Jenis Obat',
        'Golongan Obat',
        'Satuan Dasar',
        'Stock Minimum',
    ];
}
}
