<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;

class UsersExport implements FromQuery, WithHeadings
{
    protected $startDate;
    protected $endDate;

    // Konstruktor buat nerima filter tanggal
    public function __construct($startDate = null, $endDate = null)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    // Query data user, bisa difilter pakai tanggal
    public function query()
    {
        $query = User::query();

        if ($this->startDate && $this->endDate) {
            $query->whereBetween('created_at', [$this->startDate, $this->endDate]);
        }

        return $query;
    }

    // Set header kolom di file Excel
    public function headings(): array
    {
        return [
            'ID',
            'Name',
            'Email',
            'Avatar',
            'Created At',
            'Updated At'
        ];
    }
}
