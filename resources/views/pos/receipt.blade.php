<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk {{ $order->invoice_number }}</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Space+Mono:ital,wght@0,400;0,700;1,400&display=swap');

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Space Mono', monospace, courier, sans-serif;
            font-size: 12px;
            color: #000;
            background: #e2e8f0;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 20px;
        }

        .receipt-container {
            width: 320px; /* Lebar standar thermal printer 80mm/58mm */
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }

        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        .text-uppercase { text-transform: uppercase; }

        .divider {
            border-top: 1px dashed #000;
            margin: 10px 0;
        }

        .double-divider {
            border-top: 1px double #000;
            margin: 10px 0;
        }

        .header-store h2 {
            font-size: 16px;
            margin-bottom: 2px;
            letter-spacing: 1px;
        }

        .header-store p {
            font-size: 10px;
            color: #333;
        }

        .meta-table, .items-table, .totals-table {
            width: 100%;
            border-collapse: collapse;
        }

        .meta-table td {
            font-size: 11px;
            padding: 2px 0;
        }

        .items-table td {
            padding: 3px 0;
            vertical-align: top;
        }

        .item-note {
            font-size: 9px;
            color: #555;
            font-style: italic;
            padding-left: 8px;
        }

        .totals-table td {
            padding: 2px 0;
            font-size: 11px;
        }

        .footer {
            margin-top: 15px;
            font-size: 10px;
            text-align: center;
            line-height: 1.4;
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
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .btn-outline {
            background: #fff;
            color: #0f172a;
            border: 1px solid #cbd5e1;
        }

        @media print {
            body {
                background: #fff;
                padding: 0;
            }
            .receipt-container {
                width: 100%;
                box-shadow: none;
                padding: 5px;
                border-radius: 0;
            }
            .btn-actions {
                display: none !important;
            }
        }
    </style>
</head>
<body>

    <div class="receipt-container">
        <!-- Header Toko dari Pengaturan Admin -->
        <div class="header-store text-center">
            <h2>☕ {{ $storeName ?? 'CAFE NUSA POS' }}</h2>
            <p>{{ $storeAddress ?? 'Jl. Melati Utama No. 88, Jakarta' }}</p>
            <p>Telp: {{ $storePhone ?? '0812-3456-7890' }}</p>
        </div>

        <div class="double-divider"></div>

        <!-- Meta Data Transaksi -->
        <table class="meta-table">
            <tr>
                <td>Faktur</td>
                <td class="text-right font-bold">{{ $order->invoice_number }}</td>
            </tr>
            <tr>
                <td>Waktu</td>
                <td class="text-right">{{ $order->created_at->format('d/m/Y H:i') }}</td>
            </tr>
            <tr>
                <td>Kasir</td>
                <td class="text-right">{{ $order->cashier_name }}</td>
            </tr>
            <tr>
                <td>Tipe / Meja</td>
                <td class="text-right font-bold">
                    {{ $order->order_type === 'dine_in' ? 'Dine In' : 'Takeaway' }}
                    @if($order->table) - {{ $order->table->name }} @endif
                </td>
            </tr>
            @if($order->split_bill_code)
            <tr>
                <td style="color:#d97706; font-weight:bold;">PISAH BAYAR</td>
                <td class="text-right font-bold" style="color:#d97706;">{{ $order->split_bill_code }}</td>
            </tr>
            @endif
            <tr>
                <td>Pelanggan</td>
                <td class="text-right">{{ $order->customer_name }}</td>
            </tr>
        </table>

        <div class="divider"></div>

        <!-- Daftar Menu -->
        <table class="items-table">
            @foreach($order->items as $item)
                <tr>
                    <td colspan="2" class="font-bold">{{ $item->product_name }}</td>
                </tr>
                <tr>
                    <td>{{ $item->quantity }} x {{ number_format($item->price, 0, ',', '.') }}</td>
                    <td class="text-right font-bold">{{ number_format($item->subtotal, 0, ',', '.') }}</td>
                </tr>
                @if($item->notes)
                    <tr>
                        <td colspan="2" class="item-note">* {{ $item->notes }}</td>
                    </tr>
                @endif
            @endforeach
        </table>

        <div class="divider"></div>

        <!-- Total, Biaya Layanan, Pajak PB1, & Diskon -->
        <table class="totals-table">
            <tr>
                <td>Subtotal</td>
                <td class="text-right">{{ number_format($order->subtotal, 0, ',', '.') }}</td>
            </tr>

            <!-- Service Charge Resto -->
            @if($order->service_charge_amount > 0)
                <tr>
                    <td>Service Charge ({{ $order->service_charge_percent }}%)</td>
                    <td class="text-right">{{ number_format($order->service_charge_amount, 0, ',', '.') }}</td>
                </tr>
            @endif

            <!-- Pajak Restoran (PB1) -->
            @if($order->tax_amount > 0)
                <tr>
                    <td>Pajak Restoran ({{ $order->tax_percent }}%)</td>
                    <td class="text-right">{{ number_format($order->tax_amount, 0, ',', '.') }}</td>
                </tr>
            @endif

            <!-- Kupon Promo & Diskon -->
            @if($order->discount_amount > 0)
                <tr>
                    <td>
                        Diskon
                        @if($order->coupon_code)
                            <span style="font-size: 9px;">({{ $order->coupon_code }})</span>
                        @endif
                    </td>
                    <td class="text-right">-{{ number_format($order->discount_amount, 0, ',', '.') }}</td>
                </tr>
            @endif

            <tr class="font-bold" style="font-size: 13px;">
                <td>TOTAL</td>
                <td class="text-right">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
            </tr>
            <tr><td colspan="2" style="padding: 2px 0;"></td></tr>
            <tr>
                <td>Metode</td>
                <td class="text-right text-uppercase font-bold">{{ $order->payment_method ?: 'Tunai' }}</td>
            </tr>
            <tr>
                <td>Bayar</td>
                <td class="text-right">{{ number_format($order->paid_amount, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Kembalian</td>
                <td class="text-right font-bold">{{ number_format($order->change_amount, 0, ',', '.') }}</td>
            </tr>
        </table>

        <div class="double-divider"></div>

        <!-- Footer -->
        <div class="footer">
            <p class="font-bold">TERIMA KASIH</p>
            <p>Silakan Berkunjung Kembali!</p>
            <p style="margin-top: 5px; font-size: 8px; color: #888;">Powered by Nusa POS</p>
        </div>
    </div>

    <!-- Tombol Cetak & Kembali -->
    <div class="btn-actions">
        <button class="btn" style="background:#f59e0b; color:#000;" onclick="window.print()">
            🖨️ Cetak Struk
        </button>
        <button class="btn" onclick="togglePaperSize()" id="btnSize">
            Ukuran: 58mm
        </button>
        <button class="btn btn-outline" onclick="window.close(); if(!window.opener) history.back();">
            Kembali
        </button>
    </div>

    <script>
        function togglePaperSize() {
            const card = document.querySelector('.receipt-container');
            const btn = document.getElementById('btnSize');
            if (card.style.width === '380px' || card.style.width === '80mm') {
                card.style.width = '280px';
                btn.innerText = 'Ukuran: 58mm';
                localStorage.setItem('pos_receipt_size', '58mm');
            } else {
                card.style.width = '380px';
                btn.innerText = 'Ukuran: 80mm';
                localStorage.setItem('pos_receipt_size', '80mm');
            }
        }
        if (localStorage.getItem('pos_receipt_size') === '80mm') {
            document.querySelector('.receipt-container').style.width = '380px';
            document.getElementById('btnSize').innerText = 'Ukuran: 80mm';
        }
    </script>

</body>
</html>
