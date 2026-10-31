<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk Kas Awal Kasir - #{{ $shift->id }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Courier New', Courier, monospace;
        }
        body {
            background-color: #f1f5f9;
            display: flex;
            justify-content: center;
            padding: 20px 10px;
        }
        .receipt {
            background: #fff;
            width: 58mm; /* Default 58mm thermal */
            padding: 12px 10px;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
            font-size: 11px;
            line-height: 1.35;
            color: #000;
        }
        .receipt.paper-80mm {
            width: 80mm;
            font-size: 13px;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        .divider {
            border-top: 1px dashed #000;
            margin: 8px 0;
        }
        .row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 2px;
        }
        .signature-box {
            margin-top: 25px;
            text-align: center;
        }
        .sign-line {
            margin-top: 40px;
            border-bottom: 1px solid #000;
            display: inline-block;
            min-width: 140px;
        }
        .no-print {
            margin-bottom: 15px;
            display: flex;
            gap: 8px;
            justify-content: center;
        }
        .btn {
            background: #0f172a;
            color: #fff;
            border: none;
            padding: 6px 14px;
            border-radius: 6px;
            font-size: 12px;
            cursor: pointer;
            font-family: sans-serif;
            font-weight: bold;
        }
        .btn-amber { background: #f59e0b; color: #000; }
        @media print {
            body {
                background: none;
                padding: 0;
            }
            .receipt {
                box-shadow: none;
                padding: 4px;
                width: 100% !important;
            }
            .no-print {
                display: none !important;
            }
        }
    </style>
</head>
<body>

    <div>
        <!-- Controls (Hidden saat dicetak) -->
        <div class="no-print">
            <button onclick="window.print()" class="btn btn-amber">🖨️ Cetak Struk</button>
            <button onclick="togglePaperSize()" id="btnSize" class="btn">Ukuran: 58mm</button>
            <button onclick="window.close()" class="btn" style="background:#e2e8f0; color:#334155;">Tutup</button>
        </div>

        <div class="receipt" id="receiptCard">
            <div class="text-center">
                <div class="font-bold" style="font-size: 13px; text-transform: uppercase;">{{ $storeName }}</div>
                <div style="font-size: 9px; color: #333;">{{ $storeTagline }}</div>
                <div class="divider"></div>
                <div class="font-bold" style="font-size: 12px; letter-spacing: 1px;">KAS AWAL KASIR</div>
                <div style="font-size: 9px;">NO. SHIFT: #{{ str_pad($shift->id, 5, '0', STR_PAD_LEFT) }}</div>
            </div>

            <div class="divider"></div>

            <div class="row">
                <span>KODE ID</span>
                <span class="font-bold">#{{ $shift->user_id }}</span>
            </div>
            <div class="row">
                <span>NAMA KASIR</span>
                <span class="font-bold">{{ strtoupper($shift->cashier_name) }}</span>
            </div>
            <div class="row">
                <span>STATION NO</span>
                <span>POS-01</span>
            </div>

            <div class="divider"></div>

            <div class="row">
                <span>TANGGAL</span>
                <span>{{ $shift->opened_at->format('d-m-Y') }}</span>
            </div>
            <div class="row">
                <span>JAM BUKA</span>
                <span>{{ $shift->opened_at->format('H:i:s') }}</span>
            </div>

            <div class="divider"></div>

            <div class="row font-bold" style="font-size: 12px; padding: 4px 0;">
                <span>KAS AWAL</span>
                <span>Rp {{ number_format($shift->starting_cash, 0, ',', '.') }}</span>
            </div>

            @if($shift->notes)
                <div style="font-size: 9px; margin-top: 4px; color: #444;">
                    Catatan: {{ $shift->notes }}
                </div>
            @endif

            <div class="divider"></div>

            <div class="signature-box">
                <div style="font-size: 10px;">TANDA TANGAN KASIR</div>
                <div class="sign-line"></div>
                <div class="font-bold" style="font-size: 10px; margin-top: 3px;">( {{ strtoupper($shift->cashier_name) }} )</div>
            </div>

            <div class="divider" style="margin-top: 20px;"></div>
            <div class="text-center" style="font-size: 8px; color: #666;">
                Dicetak otomatis saat buka kasir<br>
                {{ now()->format('d/m/Y H:i:s') }}
            </div>
        </div>
    </div>

    <script>
        function togglePaperSize() {
            const card = document.getElementById('receiptCard');
            const btn = document.getElementById('btnSize');
            if (card.classList.contains('paper-80mm')) {
                card.classList.remove('paper-80mm');
                btn.innerText = 'Ukuran: 58mm';
                localStorage.setItem('pos_receipt_size', '58mm');
            } else {
                card.classList.add('paper-80mm');
                btn.innerText = 'Ukuran: 80mm';
                localStorage.setItem('pos_receipt_size', '80mm');
            }
        }

        // Baca setting ukuran kertas yang tersimpan
        if (localStorage.getItem('pos_receipt_size') === '80mm') {
            document.getElementById('receiptCard').classList.add('paper-80mm');
            document.getElementById('btnSize').innerText = 'Ukuran: 80mm';
        }

        // Auto print jika dibuka pertama kali
        window.onload = function() {
            setTimeout(function() {
                window.print();
            }, 400);
        };
    </script>
</body>
</html>
