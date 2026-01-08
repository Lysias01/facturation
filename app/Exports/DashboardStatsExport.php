<?php

namespace App\Exports;

use App\Models\Facture;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Carbon\Carbon;

class DashboardStatsExport implements FromCollection, WithHeadings
{
    protected $month;
    protected $year;

    public function __construct($month, $year)
    {
        $this->month = Carbon::parse($month);
        $this->year = $year;
    }

    public function collection()
    {
        $startOfMonth = $this->month->copy()->startOfMonth();
        $endOfMonth = $this->month->copy()->endOfMonth();

        $proformas = Facture::where('type','pro-forma')->whereBetween('created_at',[$startOfMonth,$endOfMonth])->sum('total');
        $recus = Facture::where('type','recu')->whereBetween('created_at',[$startOfMonth,$endOfMonth])->sum('total');

        return collect([
            [
                'Mois' => $this->month->format('F Y'),
                'Montant Réçu' => $recus,
                'Montant Pro-forma' => $proformas,
            ]
        ]);
    }

    public function headings(): array
    {
        return ['Mois','Montant Réçu','Montant Pro-forma'];
    }
}
