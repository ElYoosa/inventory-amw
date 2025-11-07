<?php

return [
    'transaction_thresholds' => [
        // Minimal qty untuk memicu notifikasi transaksi agar tidak spam
        'in' => env('NOTIF_TX_IN_MIN_QTY', 10),
        'out' => env('NOTIF_TX_OUT_MIN_QTY', 10),
    ],
];

