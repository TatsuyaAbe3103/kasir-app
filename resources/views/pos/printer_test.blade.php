<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Print Printer Thermal</title>
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
            width: 58mm;
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
        .divider {
            border-top: 1px dashed #000;
            margin: 8px 0;
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
            body { background: none; padding: 0; }
            .receipt { box-shadow: none; padding: 4px; width: 100% !important; }
            .no-print { display: none !important; }
        }
    </style>
</head>
<body>

    <div>
        <div class="no-print">
            <button onclick="window.print()" class="btn btn-amber">🖨️ Cetak Kertas Tes</button>
            <button onclick="togglePaperSize()" id="btnSize" class="btn">Ukuran: 58mm</button>
            <button onclick="window.close()" class="btn" style="background:#e2e8f0; color:#334155;">Tutup</button>
        </div>

        <div class="receipt" id="receiptCard">
            <div class="text-center">
                <div style="font-size: 14px; font-weight: bold; text-transform: uppercase;">{{ $storeName }}</div>
                <div style="font-size: 9px; color: #333;">{{ $storeTagline }}</div>
                <div class="divider"></div>
                <div style="font-weight: bold; font-size: 12px;">TEST PRINT THERMAL</div>
                <div style="font-size: 9px;">Koneksi Printer Berhasil!</div>
            </div>

            <div class="divider"></div>

            <div style="font-size: 10px; line-height: 1.5;">
                Tanggal : {{ now()->format('d/m/Y') }}<br>
                Waktu   : {{ now()->format('H:i:s') }}<br>
                Printer : Thermal POS OK<br>
                Karakter: 1234567890 ABCDEFGH
            </div>

            <div class="divider"></div>

            <div class="text-center" style="font-size: 10px; margin: 10px 0;">
                <div>--------------------------------</div>
                <div style="font-weight: bold;">PRINTER SIAP JUALAN!</div>
                <div>--------------------------------</div>
            </div>

            <div class="divider"></div>
            <div class="text-center" style="font-size: 8px; color: #666;">
                Auto Cutter / Feed Spacing Test OK<br>
                *** SELESAI ***
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
        if (localStorage.getItem('pos_receipt_size') === '80mm') {
            document.getElementById('receiptCard').classList.add('paper-80mm');
            document.getElementById('btnSize').innerText = 'Ukuran: 80mm';
        }
        window.onload = function() {
            setTimeout(function() { window.print(); }, 400);
        };
    </script>
</body>
</html>
