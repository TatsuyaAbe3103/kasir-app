<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk Closing Kasir - Shift #{{ $shift->id }}</title>
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
            margin: 6px 0;
        }
        .double-divider {
            border-top: 1px double #000;
            margin: 8px 0;
        }
        .row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 2px;
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
            <button onclick="window.print()" class="btn btn-amber">🖨️ Cetak Struk Closing</button>
            <button onclick="togglePaperSize()" id="btnSize" class="btn">Ukuran: 58mm</button>
            <button onclick="window.close()" class="btn" style="background:#e2e8f0; color:#334155;">Tutup</button>
        </div>

        <div class="receipt" id="receiptCard">
            <!-- ============================================== -->
            <!-- BAGIAN 1: BUKTI CLOSING (PERSIS SEPERTI CODC.TXT) -->
            <!-- ============================================== -->
            <div class="text-center">
                <div class="font-bold" style="font-size: 13px; text-transform: uppercase;">{{ $storeName }}</div>
                <div style="font-size: 9px; color: #333;">{{ $storeTagline }}</div>
                <div class="divider"></div>
                <div class="font-bold" style="font-size: 12px; letter-spacing: 1px;">** BUKTI CLOSING **</div>
            </div>

            <div class="divider"></div>

            <div class="row">
                <span>TANGGAL</span>
                <span>{{ $shift->closed_at ? $shift->closed_at->format('d-m-Y') : now()->format('d-m-Y') }}</span>
            </div>
            <div class="row">
                <span>JAM CLOSING</span>
                <span>{{ $shift->closed_at ? $shift->closed_at->format('H:i:s') : now()->format('H:i:s') }}</span>
            </div>
            <div class="row">
                <span>NO. RESET</span>
                <span class="font-bold">#{{ str_pad($shift->id, 5, '0', STR_PAD_LEFT) }}</span>
            </div>
            <div class="row">
                <span>STATION NO</span>
                <span>POS-01</span>
            </div>

            <div class="divider"></div>

            <div class="row">
                <span>KASIR</span>
                <span class="font-bold">{{ strtoupper($shift->cashier_name) }}</span>
            </div>
            <div class="row">
                <span>CLOSING OLEH</span>
                <span>{{ strtoupper($shift->closed_by_name ?? $shift->cashier_name) }}</span>
            </div>
            <div class="row font-bold" style="padding-top: 3px;">
                <span>UANG FISIK LACI</span>
                <span>Rp {{ number_format($shift->actual_cash, 0, ',', '.') }}</span>
            </div>

            <div class="divider" style="margin-top: 15px; margin-bottom: 15px;"></div>

            <!-- ============================================== -->
            <!-- BAGIAN 2: STRUK RESET KASIR (SEPERTI CODT.TXT) -->
            <!-- ============================================== -->
            <div class="text-center">
                <div class="font-bold" style="font-size: 11px;">** STRUK RESET KASIR **</div>
                <div style="font-size: 9px;">Laporan Rekapitulasi Shift</div>
            </div>

            <div class="divider"></div>

            <div class="font-bold" style="font-size: 10px; margin-bottom: 4px; text-align: center;">
                *-- Informasi Cash Drawer --*
            </div>

            <div class="row">
                <span>KAS AWAL</span>
                <span>Rp {{ number_format($shift->starting_cash, 0, ',', '.') }}</span>
            </div>
            <div class="row">
                <span>RP. TUNAI MASUK</span>
                <span>Rp {{ number_format($shift->total_cash_sales, 0, ',', '.') }}</span>
            </div>
            <div class="divider"></div>
            <div class="row font-bold">
                <span>TOTAL TARGET LACI</span>
                <span>Rp {{ number_format($shift->expected_cash, 0, ',', '.') }}</span>
            </div>
            <div class="row font-bold">
                <span>ACTUAL CASH DRAWER</span>
                <span>Rp {{ number_format($shift->actual_cash, 0, ',', '.') }}</span>
            </div>

            <div class="divider"></div>

            <div class="row font-bold" style="font-size: 11px; {{ $shift->difference < 0 ? 'color: red;' : '' }}">
                <span>SELISIH (ACT - TTL)</span>
                <span>
                    @if($shift->difference == 0)
                        Rp 0 (PAS)
                    @elseif($shift->difference > 0)
                        +Rp {{ number_format($shift->difference, 0, ',', '.') }} (LEBIH)
                    @else
                        -Rp {{ number_format(abs($shift->difference), 0, ',', '.') }} (KURANG)
                    @endif
                </span>
            </div>

            <div class="divider"></div>

            <!-- METODE PEMBAYARAN -->
            <div class="font-bold" style="font-size: 10px; margin-bottom: 4px; text-align: center;">
                *-- Ringkasan Non-Tunai --*
            </div>
            <div class="row">
                <span>QRIS INSTAN</span>
                <span>Rp {{ number_format($shift->total_qris_sales, 0, ',', '.') }}</span>
            </div>
            <div class="row">
                <span>DEBET / TRANSFER</span>
                <span>Rp {{ number_format($shift->total_transfer_sales, 0, ',', '.') }}</span>
            </div>
            <div class="row">
                <span>TOTAL DISKON/KUPON</span>
                <span>Rp {{ number_format($shift->total_discount, 0, ',', '.') }}</span>
            </div>

            <div class="divider"></div>

            <!-- INFORMASI TRANSAKSI -->
            <div class="font-bold" style="font-size: 10px; margin-bottom: 4px; text-align: center;">
                *-- INFORMASI TRANSAKSI --*
            </div>
            <div class="row font-bold" style="font-size: 11px;">
                <span>TOTAL OMZET SHIFT</span>
                <span>Rp {{ number_format($shift->total_sales, 0, ',', '.') }}</span>
            </div>
            <div class="row">
                <span>JUMLAH TRANSAKSI</span>
                <span>{{ $shift->total_transactions }} Bill</span>
            </div>
            <div class="row">
                <span>JUMLAH VOID / BATAL</span>
                <span>{{ $shift->total_void_transactions }} Transaksi</span>
            </div>

            @if($shift->notes)
                <div class="divider"></div>
                <div style="font-size: 9px; color: #333;">
                    Catatan Kasir: {{ $shift->notes }}
                </div>
            @endif

            <div class="divider" style="margin-top: 15px;"></div>

            <!-- Area Tanda Tangan -->
            <div style="display: flex; justify-content: space-between; text-align: center; margin-top: 20px; font-size: 9px;">
                <div>
                    <div>Kasir Bertugas,</div>
                    <div style="height: 35px;"></div>
                    <div class="font-bold">( {{ strtoupper($shift->cashier_name) }} )</div>
                </div>
                <div>
                    <div>Supervisor / Owner,</div>
                    <div style="height: 35px;"></div>
                    <div class="font-bold">( {{ strtoupper($shift->closed_by_name ?? 'MANAGER') }} )</div>
                </div>
            </div>

            <div class="divider" style="margin-top: 15px;"></div>
            <div class="text-center" style="font-size: 8px; color: #666;">
                Dicetak: {{ now()->format('d/m/Y H:i:s') }}<br>
                Sistem Kasir {{ $storeName }}
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
            setTimeout(function() {
                window.print();
            }, 400);
        };
    </script>
</body>
</html>
