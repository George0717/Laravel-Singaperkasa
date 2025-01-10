<?php
namespace App\Exports;

use App\Models\Invoice;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class InvoiceExport implements FromCollection, WithHeadings
{
    protected $invoiceId;

    public function __construct($invoiceId)
    {
        $this->invoiceId = $invoiceId;
    }

    public function collection()
    {
        return Invoice::find($this->invoiceId)->salesOrder->suratJalans->map(function ($suratJalan) {
            return [
                'No Surat Jalan' => $suratJalan->no_surat_jalan ?? 'N/A',
                'Tanggal Pengiriman' => $suratJalan->tanggal_pengiriman ?? 'N/A',
                'Plat Angkutan' => $suratJalan->plat_angkutan ?? 'N/A',
                'Jumlah Barang' => $suratJalan->suratJalanDetails->sum('quantity') ?? 'N/A',
            ];
        });
    }

    public function headings(): array
    {
        return ['No Surat Jalan', 'Tanggal Pengiriman', 'Plat Angkutan', 'Jumlah Barang'];
    }
}
