<?php

namespace App\Helpers;

use App\Models\JadwalKirim;
use App\Models\SalesOrder;
use Carbon\Carbon;

class NotificationHelper
{
    // Fungsi untuk mengecek dan memberikan notifikasi terkait delivery_date atau due_date
    public static function checkDeliveryDateNotification($salesOrderId, $deliveryDate)
    {
        $salesOrder = SalesOrder::find($salesOrderId);

        // Pastikan SalesOrder ada
        if (!$salesOrder) {
            return 'Sales Order tidak ditemukan!';
        }

        // Mengecek apakah delivery_date sudah lewat
        if ($deliveryDate && Carbon::parse($deliveryDate)->isToday()) {
            return 'Sales Order sudah jatuh tempo untuk pengiriman hari ini!';
        }

        // Cek apakah delivery_date setelah due_date
        if ($deliveryDate && $deliveryDate > $salesOrder->due_date) {
            return 'Tanggal pengiriman tidak boleh melewati tanggal jatuh tempo.';
        }

        return null;
    }

    // Fungsi lainnya untuk notifikasi pada Jadwal Kirim
    public static function checkJadwalKirimDateNotification($jadwalKirimId)
    {
        $jadwalKirim = JadwalKirim::find($jadwalKirimId);

        if (!$jadwalKirim) {
            return null;
        }

        $deliveryDate = $jadwalKirim->delivery_date;
        $salesOrderDueDate = $jadwalKirim->salesOrder->due_date;

        // Cek apakah delivery_date melebihi due_date SalesOrder terkait
        if (Carbon::parse($deliveryDate)->isAfter(Carbon::parse($salesOrderDueDate))) {
            return 'Perhatian: Tanggal pengiriman melebihi tanggal jatuh tempo Sales Order!';
        }

        return null;
    }
}
