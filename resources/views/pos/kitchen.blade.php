<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TIKET DAPUR - {{ $order->invoice_number }}</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Space+Mono:ital,wght@0,400;0,700;1,400&display=swap');

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Space Mono', monospace;
            background: #f8fafc;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 20px;
        }

        .ticket-container {
            width: 320px;
            background: #fff;
            padding: 20px;
            border: 2px dashed #000;
        }

        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }

        .divider {
            border-top: 2px dashed #000;
            margin: 12px 0;
        }

        .table-badge {
            font-size: 24px;
            font-weight: 900;
            background: #000;
            color: #fff;
            padding: 6px;
            text-align: center;
            margin: 8px 0;
        }

        .items-list {
            margin: 15px 0;
        }

        .item-row {
            margin-bottom: 12px;
            padding-bottom: 8px;
            border-bottom: 1px dotted #888;
        }

        .item-row:last-child {
            border-bottom: none;
        }

        .item-title {
            display: flex;
            justify-content: space-between;
            font-size: 14px;
            font-weight: bold;
        }

        .item-qty {
            font-size: 16px;
            font-weight: 900;
            background: #eee;
            padding: 0 6px;
            border-radius: 4px;
        }

        .item-note {
            font-size: 12px;
            font-weight: bold;
            color: #d97706; /* Amber */
            background: #fef3c7;
            padding: 3px 6px;
            margin-top: 4px;
            border-radius: 4px;
            display: inline-block;
        }

        .btn-actions {
            margin-top: 20px;
            display: flex;
            gap: 10px;
        }

        .btn {
            background: #0f172a;
            color: #fff;
            border: none;
            padding: 10px 20px;
            font-family: inherit;
            font-size: 12px;
            font-weight: bold;
            border-radius: 6px;
            cursor: pointer;
        }

        @media print {
            body { background: #fff; padding: 0; }
            .ticket-container { width: 100%; border: 1px solid #000; }
            .btn-actions { display: none !important; }
        }
    </style>
</head>
<body>

    <div class="ticket-container">
        <div class="text-center">
            <h1 style="font-size: 18px; font-weight: 900;">🔥 ORDER DAPUR / BAR 🔥</h1>
            <p style="font-size: 11px; margin-top: 2px;">{{ $order->created_at->format('d/m/Y H:i:s') }}</p>
            <p style="font-size: 11px;">#{{ $order->invoice_number }}</p>
        </div>

        <div class="table-badge">
            {{ $order->order_type === 'dine_in' ? ($order->table ? $order->table->name : 'DINE IN') : 'BUNGKUS / TAKEAWAY' }}
        </div>

        <p style="font-size: 11px; text-align: center;">Tamu: <strong>{{ $order->customer_name }}</strong></p>

        <div class="divider"></div>

        <div class="items-list">
            @foreach($order->items as $item)
                <div class="item-row">
                    <div class="item-title">
                        <span>{{ $item->product_name }}</span>
                        <span class="item-qty">x{{ $item->quantity }}</span>
                    </div>
                    @if($item->notes)
                        <div class="item-note">
                            👉 Req: {{ $item->notes }}
                        </div>
                    @endif
                </div>
            @endforeach
        </div>

        @if($order->notes)
            <div class="divider"></div>
            <p style="font-size: 11px; font-weight: bold;">Catatan Khusus Order:</p>
            <p style="font-size: 11px; font-style: italic;">{{ $order->notes }}</p>
        @endif

        <div class="divider"></div>
        <p class="text-center" style="font-size: 10px;">-- Harap siapkan pesanan tepat waktu --</p>
    </div>

    <div class="btn-actions">
        <button class="btn" onclick="window.print()">🖨️ Cetak Tiket</button>
        <button class="btn" style="background: #64748b;" onclick="window.close(); if(!window.opener) history.back();">Tutup</button>
    </div>

</body>
</html>
