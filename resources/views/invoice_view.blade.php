<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice Pembelian</title>
    <style>
        :root {
            --primary-blue: #2B7FBB;
            --light-blue: #E6F2FF;
            --dark-blue: #1A5276;
        }

        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background-color: var(--light-blue);
            color: #333;
            line-height: 1.6;
        }

        .invoice-container {
            width: 90%;
            max-width: 800px;
            margin: 30px auto;
            padding: 30px;
            border-radius: 15px;
            background-color: white;
            box-shadow: 0 10px 25px rgba(43, 127, 187, 0.1);
            border: 1px solid var(--light-blue);
        }

        .header {
            text-align: center;
            padding-bottom: 20px;
            border-bottom: 3px solid var(--primary-blue);
        }

        .header h1 {
            color: var(--primary-blue);
            margin: 0;
            font-size: 2.5em;
        }

        .header p {
            color: var(--dark-blue);
            margin: 10px 0 0;
        }

        .details, .order-items {
            margin: 25px 0;
        }

        .details h2, .order-items h2 {
            color: var(--primary-blue);
            border-bottom: 2px solid var(--light-blue);
            padding-bottom: 10px;
        }

        .details table, .order-items table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        .details th, .order-items th {
            background-color: var(--light-blue);
            color: var(--dark-blue);
            padding: 12px;
            text-align: left;
            border: 1px solid var(--primary-blue);
        }

        .details td, .order-items td {
            padding: 12px;
            border: 1px solid var(--light-blue);
        }

        .footer {
            text-align: center;
            padding-top: 20px;
            border-top: 3px solid var(--primary-blue);
            background-color: var(--light-blue);
            border-radius: 0 0 15px 15px;
        }

        .footer p {
            color: var(--dark-blue);
            margin: 10px 0;
        }

        .total-price {
            font-size: 1.5em;
            color: var(--primary-blue);
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="invoice-container">
        <div class="header">
            <h1>Invoice Pembelian</h1>
            <p>Pembeli atas nama : {{ $user->name }}</p>
            <p>Tanggal : {{ $order->created_at->format('d-m-Y') }}</p>
        </div>

        <div class="details">
            <h2>Data Pembeli</h2>
            <table>
                <tr>
                    <th>Nama</th>
                    <td>{{ $user->name }}</td>
                </tr>
                <tr>
                    <th>Email</th>
                    <td>{{ $user->email }}</td>
                </tr>
                <tr>
                    <th>Nomor Handphone</th>
                    <td>{{ $user->phone_number }}</td>
                </tr>
            </table>
        </div>

        <div class="order-items">
            <h2>Detail Pemesanan Kost</h2>
            @if($order->kost_id)
            <table>
                <tr>
                    <th>Gambar Kost</th>
                    <td>{{ $kost->image }}</td>
                </tr>
                <tr>
                    <th>Nama Kost</th>
                    <td>{{ $kost->name }}</td>
                </tr>
                <tr>
                    <th>Pemilik Kost</th>
                    <td>{{ $kost->owner }}</td>
                </tr>
                <tr>
                    <th>Deskripsi Kost</th>
                    <td>{{ $kost->description }}</td>
                </tr>
                <tr>
                    <th>Tipe Kost</th>
                    <td>{{ $kost->kost_type }}</td>
                </tr>
                <tr>
                    <th>Harga</th>
                    <td>Rp {{ number_format($kost->price, 0, ',', '.') }}</td>
                </tr>
            </table>
        </div>

        <div class="footer">
            <p class="total-price">Total Harga Pembelian: Rp {{ number_format($order->total_price, 0, ',', '.') }}</p>
            <p>Terimakasih telah memesan kost di website kami!</p>
        </div>
    </div>
</body>
</html>