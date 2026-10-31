<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stand QR Meja - {{ $table->name }}</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800;900&family=Playfair+Display:ital,wght@0,700;0,900;1,700&display=swap');

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: #0f172a;
            color: #1e293b;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 30px 15px;
        }

        .tent-card {
            width: 340px;
            background: #ffffff;
            border-radius: 24px;
            padding: 36px 28px;
            text-align: center;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.4);
            border: 2px solid #e2e8f0;
            position: relative;
            overflow: hidden;
        }

        .tent-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 8px;
            background: linear-gradient(90deg, #d97706, #f59e0b, #d97706);
        }

        .store-badge {
            font-size: 11px;
            font-weight: 800;
            letter-spacing: 2px;
            text-transform: uppercase;
            color: #d97706;
            margin-bottom: 4px;
        }

        .store-title {
            font-size: 18px;
            font-weight: 900;
            color: #0f172a;
            letter-spacing: -0.5px;
        }

        .table-display {
            margin: 20px 0 16px;
            background: #f8fafc;
            border: 1.5px solid #e2e8f0;
            padding: 10px 16px;
            border-radius: 16px;
            display: inline-block;
        }

        .table-number {
            font-size: 26px;
            font-weight: 900;
            color: #0f172a;
            letter-spacing: -0.5px;
        }

        .qr-wrapper {
            background: #ffffff;
            padding: 16px;
            border-radius: 20px;
            display: inline-block;
            box-shadow: 0 10px 25px -5px rgba(0,0,0,0.06);
            border: 2px solid #0f172a;
            margin: 10px 0;
        }

        .qr-wrapper img {
            width: 180px;
            height: 180px;
            display: block;
        }

        .instruction-title {
            font-size: 13px;
            font-weight: 800;
            color: #0f172a;
            margin-top: 14px;
        }

        .instruction-desc {
            font-size: 11px;
            color: #64748b;
            line-height: 1.5;
            margin-top: 4px;
            padding: 0 10px;
        }

        .btn-bar {
            margin-top: 25px;
            display: flex;
            gap: 10px;
        }

        .btn {
            background: #f59e0b;
            color: #0f172a;
            font-weight: 800;
            font-size: 12px;
            padding: 12px 24px;
            border-radius: 12px;
            border: none;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            box-shadow: 0 10px 20px -5px rgba(245, 158, 11, 0.4);
            transition: all 0.2s;
        }

        .btn:hover { background: #d97706; color: #fff; }

        .btn-secondary {
            background: #334155;
            color: #fff;
            box-shadow: none;
        }

        .btn-secondary:hover { background: #475569; color: #fff; }

        @media print {
            body { background: #fff; padding: 0; }
            .tent-card { box-shadow: none; border: 1.5px solid #000; margin: 20px auto; }
            .btn-bar { display: none !important; }
        }
    </style>
</head>
<body>

    <div class="tent-card">
        <p class="store-badge">DIGITAL SELF-ORDER</p>
        <h1 class="store-title">☕ {{ $storeName }}</h1>

        <div class="table-display">
            <span class="table-number">{{ $table->name }}</span>
        </div>

        <div class="qr-wrapper">
            <!-- QR Code beresolusi tinggi langsung mengarah ke halaman menu meja tamu -->
            <img src="https://api.qrserver.com/v1/create-qr-code/?size=250x250&data={{ urlencode($orderUrl) }}" alt="QR Meja {{ $table->name }}">
        </div>

        <h3 class="instruction-title">📱 Scan QR untuk Memesan</h3>
        <p class="instruction-desc">
            Arahkan kamera smartphone Anda ke kode QR di atas untuk melihat buku menu & langsung memesan dari tempat duduk Anda.
        </p>
    </div>

    <div class="btn-bar">
        <button class="btn" onclick="window.print()">
            🖨️ Cetak Stand QR Meja Ini
        </button>
        <a href="{{ route('tables.qr.all') }}" class="btn btn-secondary">
            📑 Cetak Semua Meja Sekaligus
        </a>
        <a href="{{ route('pos.tables') }}" class="btn btn-secondary">
            Kembali ke Denah Meja
        </a>
    </div>

</body>
</html>
