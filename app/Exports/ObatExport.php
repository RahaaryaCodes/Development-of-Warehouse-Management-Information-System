<?php

namespace App\Exports;

use App\Models\DrugsModel;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ObatExport implements FromCollection, WithHeadings, WithMapping
{
    protected $search;
    protected $kategori;
    protected $golongan;

    public function __construct($search = null, $kategori = null, $golongan = null)
    {
        $this->search = $search;
        $this->kategori = $kategori;
        $this->golongan = $golongan;
    }

    public function collection()
    {
        $query = DrugsModel::query();

        // Apply search filter
        if ($this->search) {
            $query->where('nama_obat', 'like', '%' . $this->search . '%');
        }

        // Apply kategori filter
        if ($this->kategori) {
            $query->where('kategori_obat', $this->kategori);
        }
        
        // Apply golongan filter
        if ($this->golongan) {
            $query->where('golongan_obat', $this->golongan);
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'Nama Obat',
            'Kategori Obat',
            'Jenis Obat',
            'Golongan Obat',
            'Satuan Dasar',
            'Stock Minimum'
        ];
    }

    public function map($obat): array
    {
        return [
            $obat->nama_obat,
            $obat->kategori_obat,
            $obat->jenis_obat,
            $obat->golongan_obat,
            $obat->satuan_dasar,
            $obat->stock_minimum
        ];
    }
}
