<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Budget - {{ $presupuesto->id_presupuesto }}</title>
    <style>
        :root {
            --primary-color: #3498db;
            --secondary-color: #2c3e50;
            --accent-color: #e74c3c;
            --light-gray: #f8f9fa;
            --dark-gray: #343a40;
            --border-color: #dee2e6;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 15px;
            color: var(--dark-gray);
            line-height: 1.4;
            font-size: 12px;
            background-color: #fff;
        }
        
        .invoice-container {
            max-width: 800px;
            margin: 0 auto;
            border: 1px solid var(--border-color);
            padding: 15px;
            position: relative;
        }
        
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid var(--primary-color);
        }
        
        .logo {
            max-height: 150px;
        }
        
        .invoice-title {
            color: var(--secondary-color);
            font-size: 18px;
            font-weight: 700;
            margin: 0;
        }
        
        .invoice-number {
            color: var(--primary-color);
            font-weight: bold;
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            margin-bottom: 15px;
        }
        
        .info-box {
            border: 1px solid var(--border-color);
            border-radius: 3px;
            padding: 8px;
            background-color: var(--light-gray);
        }
        
        .info-title {
            font-size: 12px;
            font-weight: 600;
            color: var(--secondary-color);
            margin-top: 0;
            margin-bottom: 8px;
            padding-bottom: 3px;
            border-bottom: 1px solid var(--border-color);
        }
        
        .info-item {
            margin-bottom: 5px;
        }
        
        .info-label {
            font-weight: 600;
            display: inline-block;
            width: 60px;
        }
        
        .dates-container {
            display: flex;
            justify-content: space-between;
            margin-bottom: 12px;
            font-size: 11px;
        }
        
        .date-item {
            background-color: var(--light-gray);
            padding: 5px 10px;
            border-radius: 3px;
        }
        
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
            font-size: 11px;
        }
        
        .items-table th {
            background-color: var(--primary-color);
            color: white;
            text-align: left;
            padding: 6px 8px;
            font-weight: 600;
        }
        
        .items-table td {
            padding: 6px 8px;
            border-bottom: 1px solid var(--border-color);
        }
        
        .items-table tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        
        .totals-table {
            width: 250px;
            margin-left: auto;
            border-collapse: collapse;
            font-size: 11px;
        }
        
        .totals-table th, 
        .totals-table td {
            padding: 5px 8px;
            text-align: right;
        }
        
        .totals-table th {
            background-color: var(--light-gray);
            font-weight: 600;
        }
        
        .grand-total {
            font-size: 13px;
            font-weight: 700;
            color: var(--accent-color);
            border-top: 2px solid var(--primary-color);
            border-bottom: 2px solid var(--primary-color);
        }
        
        .footer {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px solid var(--border-color);
            font-size: 11px;
        }
        
        .signature-box {
            text-align: center;
        }
        
        .signature-title {
            font-weight: 600;
            margin-bottom: 20px;
        }
        
        .signature-line {
            border-top: 1px solid var(--dark-gray);
            width: 70%;
            margin: 0 auto;
            padding-top: 15px;
        }
        
        .status-badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 10px;
            text-transform: uppercase;
            margin-left: 10px;
        }
        
        .status-pending {
            background-color: #fff3cd;
            color: #856404;
        }
        
        .status-approved {
            background-color: #d4edda;
            color: #155724;
        }
        
        .status-rejected {
            background-color: #f8d7da;
            color: #721c24;
        }
        
        .text-right {
            text-align: right;
        }
        
        .text-center {
            text-align: center;
        }
        
        @media print {
            body {
                padding: 5px;
                font-size: 11px;
            }
            .invoice-container {
                border: none;
                padding: 5px;
            }
        }
    </style>
</head>
<body>
    <div class="invoice-container">
        <div class="invoice-container">
            <div class="header" style="display: flex; align-items: center;">
                <h1 class="invoice-title" style="margin-left: 20px;">
                    BUDGET <span class="invoice-number">#{{ $presupuesto->id_presupuesto }}</span>
                </h1>
            </div>
        </div>
        
        <div class="info-grid">
            <div class="info-box">
                <h3 class="info-title">COMPANY INFORMATION</h3>
                <div class="info-item">
                    <span class="info-label">Name:</span> Autokeys Locksmith
                </div>
                <div class="info-item">
                    <span class="info-label">Address:</span> 1989 Covington Pike, Memphis TN 38128
                </div>
                <div class="info-item">
                    <span class="info-label">Phone:</span> +1 (901) 513-9541
                </div>
                <div class="info-item">
                    <span class="info-label">Email:</span> usa.autokeyslocksmith@gmail.com
                </div>
            </div>

            <div class="info-box">
                <h3 class="info-title">CUSTOMER INFORMATION</h3>
                <div class="info-item">
                    <span class="info-label">Name:</span> {{ $presupuesto->cliente->nombre ?? 'N/A' }}
                </div>
                <div class="info-item">
                    <span class="info-label">Address:</span> {{ $presupuesto->cliente->direccion ?? 'N/A' }}
                </div>
                <div class="info-item">
                    <span class="info-label">Phone:</span> {{ $presupuesto->cliente->telefono ?? 'N/A' }}
                </div>
            </div>
        </div>

        <div class="dates-container">
            <div class="date-item">
                <strong>Budget Date:</strong> {{ \Carbon\Carbon::parse($presupuesto->f_presupuesto)->format('M d, Y') }}
            </div>
            <div class="date-item">
                @php
                    $f_presupuesto = \Carbon\Carbon::parse($presupuesto->f_presupuesto);
                    $fechaValidez = \Carbon\Carbon::parse($presupuesto->validez);
                    $diferenciaDias = $f_presupuesto->diffInDays($fechaValidez);
                @endphp
                <strong>Valid Until:</strong> {{ $fechaValidez->format('M d, Y') }} ({{ $diferenciaDias }} days)
            </div>
        </div>

        @if (is_array($presupuesto->items) && count($presupuesto->items) > 0)
            <table class="items-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>DESCRIPTION</th>
                        <th class="text-right">SUBTOTAL</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $subtotal = 0;
                    @endphp
                    @foreach ($presupuesto->items as $index => $item)
                        @php
                            $subtotal += $item['precio'] ?? 0;
                        @endphp
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $item['descripcion'] ?? 'No description' }}</td>
                            <td class="text-right">${{ number_format($item['precio'] ?? 0, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p class="text-center">No items in this budget</p>
        @endif

        @php
            $descuentoAmount = $subtotal * ($presupuesto->descuento / 100);
            $subtotalConDescuento = $subtotal - $descuentoAmount;
            $ivaAmount = $subtotalConDescuento * ($presupuesto->iva / 100);
            $total = $subtotalConDescuento + $ivaAmount;
        @endphp

        <table class="totals-table">
            <tr>
                <th>Subtotal:</th>
                <td>${{ number_format($subtotal, 2) }}</td>
            </tr>
            @if($presupuesto->descuento > 0)
            <tr>
                <th>Discount ({{ $presupuesto->descuento }}%):</th>
                <td>-${{ number_format($descuentoAmount, 2) }}</td>
            </tr>
            <tr>
                <th>Subtotal with discount:</th>
                <td>${{ number_format($subtotalConDescuento, 2) }}</td>
            </tr>
            @endif
            @if($presupuesto->iva > 0)
            <tr>
                <th>Tax ({{ $presupuesto->iva }}%):</th>
                <td>${{ number_format($ivaAmount, 2) }}</td>
            </tr>
            @endif
            <tr class="grand-total">
                <th>TOTAL:</th>
                <td>${{ number_format($total, 2) }}</td>
            </tr>
        </table>

        <div class="footer">
            <div class="signature-box">
                <div class="signature-title">Prepared by</div>
                <div class="signature-line"></div>
            </div>
            <div class="signature-box">
                <div class="signature-title">Customer acceptance</div>
                <div class="signature-line"></div>
            </div>
        </div>
    </div>
</body>
</html>