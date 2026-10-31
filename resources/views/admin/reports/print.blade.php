<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Penjualan - {{ $startDate }} s/d {{ $endDate }}</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&family=Space+Mono:wght@400;700&display=swap');
        
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 12px;
            color: #1e293b;
            background: #f1f5f9;
            padding: 30px;
        }

        .paper {
            max-width: 800px;
            margin: 0 auto;
            background: #fff;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        }

        .header {
            border-bottom: 2px solid #0f172a;
            padding-bottom: 15px;
            margin-bottom: 25px;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
        }

        .header h1 {
            font-size: 20px;
            font-weight: 800;
            color: #0f172a;
        }

        .header p {
            font-size: 11px;
            color: #64748b;
        }

        .summary-box {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
            margin-bottom: 30px;
        }

        .metric {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            padding: 12px;
            border-radius: 8px;
        }

        .metric span {
            font-size: 10px;
            color: #64748b;
            font-weight: bold;
            text-transform: uppercase;
            display: block;
        }

        .metric strong {
            font-size: 15px;
            font-weight: 800;
            color: #0f172a;
            display: block;
            margin-top: 4px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        th {
            background: #0f172a;
            color: #fff;
            text-align: left;
            padding: 8px 10px;
            font-size: 11px;
            text-transform: uppercase;
        }

        td {
            padding: 8px 10px;
            border-bottom: 1px solid #e2e8f0;
            font-size: 11px;
        }

        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }

        .signature-section {
            display: flex;
            justify-content: space-between;
            margin-top: 40px;
            padding-top: 20px;
        }

        .sign-box {
            text-align: center;
            width: 200px;
        }

        .sign-space {
            height: 60px;
        }

        .btn-bar {
            max-width: 800px;
            margin: 20px auto 0;
            display: flex;
            gap: 10px;
            justify-content: flex-end;
        }

        .btn {
            background: #0f172a;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: bold;
            cursor: pointer;
            text-decoration: none;
            font-size: 12px;
        }

        @media print {
            body { background: #fff; padding: 0; }
            .paper { box-shadow: none; padding: 10px; max-width: 100%; }
            .btn-bar { display: none !important; }
        }
    </style>
</head>
<body>

    <div class="paper">
        <!-- Header -->
        <div class="header">
            <div>
                <h1>☕ CAFE NUSA POS</h1>
                <p>LAPORAN PENJUALAN & REKAP MENU TERJUAL</p>
            </div>
            <div style="text-align: right;">
                <p><strong>Periode:</strong> {{ date('d M Y', strtotime($startDate)) }} s/d {{ date('d M Y', strtotime($endDate)) }}</p>
                <p>Dicetak pada: {{ date('d/m/Y H:i') }}</p>
            </div>
        </div>

        <!-- 4 Summary Metrics -->
        <div class="summary-box">
            <div class="metric">
                <span>Total Omzet</span>
                <strong>Rp {{ number_format($totalSales, 0, ',', '.') }}</strong>
            </div>
            <div class="metric">
                <span>Estimasi Profit</span>
                <strong style="color: #059669;">+Rp {{ number_format($totalProfit, 0, ',', '.') }}</strong>
            </div>
            <div class="metric">
                <span>Total Transaksi</span>
                <strong>{{ $totalOrders }} Pesanan</strong>
            </div>
            <div class="metric">
                <span>Total Menu Terjual</span>
                <strong style="color: #d97706;">{{ $totalItemsSold }} Porsi/Cup</strong>
            </div>
        </div>

        <!-- Tabel Rincian Menu Terjual -->
        <h3 style="font-size: 13px; font-weight: 800; margin-bottom: 10px;">RINCIAN MENU YANG TERJUAL</h3>
        <table>
            <thead>
                <tr>
                    <th class="text-center" style="width: 40px;">No</th>
                    <th>Nama Menu</th>
                    <th class="text-center">Qty Terjual</th>
                    <th class="text-right">Total Omzet</th>
                    <th class="text-right">Estimasi Laba</th>
                </tr>
            </thead>
            <tbody>
                @forelse($itemSales as $idx => $item)
                    <tr>
                        <td class="text-center font-bold">{{ $idx + 1 }}</td>
                        <td>
                            <strong style="color: #0f172a;">{{ $item->product_name }}</strong>
                            <span style="font-size: 9px; color: #64748b; font-family: monospace;"> ({{ $item->product_code }})</span>
                        </td>
                        <td class="text-center font-bold">{{ $item->total_qty }}</td>
                        <td class="text-right">Rp {{ number_format($item->total_omzet, 0, ',', '.') }}</td>
                        <td class="text-right font-bold" style="color: #059669;">+Rp {{ number_format($item->total_profit, 0, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center" style="padding: 20px; color: #94a3b8;">
                            Tidak ada transaksi penjualan pada rentang tanggal ini.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Signatures -->
        <div class="signature-section">
            <div class="sign-box">
                <p>Dibuat Oleh (Admin):</p>
                <div class="sign-space"></div>
                <p style="border-top: 1px solid #94a3b8; padding-top: 5px;"><strong>Manager Resto</strong></p>
            </div>
            <div class="sign-box">
                <p>Mengetahui (Owner):</p>
                <div class="sign-space"></div>
                <p style="border-top: 1px solid #94a3b8; padding-top: 5px;"><strong>Pemilik Cafe</strong></p>
            </div>
        </div>
    </div>

    <!-- Print Buttons -->
    <div class="btn-bar">
        <button class="btn" onclick="window.print()">🖨️ Cetak / Simpan PDF</button>
        <button class="btn" style="background: #64748b;" onclick="window.close(); if(!window.opener) history.back();">Kembali</button>
    </div>

</body>
</html>
