<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Semua Stand QR Meja - {{ $storeName }}</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800;900&display=swap');

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: #f1f5f9;
            color: #1e293b;
            padding: 30px;
        }

        .container {
            max-width: 900px;
            margin: 0 auto;
        }

        .grid-cards {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 25px;
        }

        .tent-card {
            background: #ffffff;
            border-radius: 20px;
            padding: 25px 20px;
            text-align: center;
            border: 2px solid #0f172a;
            page-break-inside: avoid;
            position: relative;
        }

        .store-badge {
            font-size: 9px;
            font-weight: 800;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            color: #d97706;
            margin-bottom: 2px;
        }

        .store-title {
            font-size: 15px;
            font-weight: 900;
            color: #0f172a;
        }

        .table-display {
            margin: 12px 0 10px;
            background: #f8fafc;
            border: 1px solid #cbd5e1;
            padding: 6px 14px;
            border-radius: 12px;
            display: inline-block;
        }

        .table-number {
            font-size: 20px;
            font-weight: 900;
            color: #0f172a;
        }

        .qr-wrapper img {
            width: 140px;
            height: 140px;
            display: inline-block;
            border: 1px solid #0f172a;
            padding: 6px;
            border-radius: 12px;
        }

        .instruction-title {
            font-size: 11px;
            font-weight: 800;
            color: #0f172a;
            margin-top: 8px;
        }

        .instruction-desc {
            font-size: 9px;
            color: #64748b;
            line-height: 1.4;
            margin-top: 2px;
        }

        .btn-bar {
            margin-bottom: 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .btn {
            background: #0f172a;
            color: #fff;
            padding: 10px 20px;
            border-radius: 10px;
            text-decoration: none;
            font-size: 12px;
            font-weight: bold;
            border: none;
            cursor: pointer;
        }

        @media print {
            body { background: #fff; padding: 0; }
            .container { max-width: 100%; }
            .btn-bar { display: none !important; }
            .tent-card { border: 1.5px dashed #000; box-shadow: none; margin-bottom: 20px; }
        }
    </style>
</head>
<body>

    <div class="container">
        <div class="btn-bar">
            <div>
                <h2 style="font-size: 18px; font-weight: 900;">Lembar Cetak Stand QR Semua Meja</h2>
                <p style="font-size: 11px; color: #64748b;">Potong sesuai garis luar kartu dan tempatkan di masing-masing meja tamu.</p>
            </div>
            <div style="display: flex; gap: 10px;">
                <button class="btn" onclick="window.print()">🖨️ Cetak Semua Halaman</button>
                <a href="{{ route('pos.tables') }}" class="btn" style="background: #64748b;">Kembali</a>
            </div>
        </div>

        <div class="grid-cards">
            @foreach($tables as $table)
                <div class="tent-card">
                    <p class="store-badge">DIGITAL SELF-ORDER</p>
                    <h3 class="store-title">☕ {{ $storeName }}</h3>

                    <div class="table-display">
                        <span class="table-number">{{ $table->name }}</span>
                    </div>

                    <div class="qr-wrapper">
                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data={{ urlencode($table->qr_url ?? route('guest.menu', $table->id)) }}" alt="QR {{ $table->name }}">
                    </div>

                    <h4 class="instruction-title">📱 Scan QR untuk Memesan</h4>
                    <p class="instruction-desc">
                        Arahkan kamera HP Anda ke kode QR untuk melihat buku menu & langsung memesan dari tempat duduk Anda.
                    </p>
                </div>
            @endforeach
        </div>
    </div>

</body>
</html>
