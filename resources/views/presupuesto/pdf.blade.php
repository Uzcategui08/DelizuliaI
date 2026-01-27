<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Presupuesto - {{ $presupuesto->id_presupuesto }}</title>
    <style>
        :root {
            --color-primario: #3498db;
            --color-secundario: #2c3e50;
            --color-destacado: #e74c3c;
            --gris-claro: #f8f9fa;
            --gris-oscuro: #343a40;
            --borde: #dee2e6;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 15px;
            color: var(--gris-oscuro);
            line-height: 1.4;
            font-size: 12px;
            background-color: #fff;
        }
        
        .contenedor-presupuesto {
            max-width: 800px;
            margin: 0 auto;
            border: 1px solid var(--borde);
            padding: 15px;
            position: relative;
        }
        
        .encabezado {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid var(--color-primario);
        }
        
        .logo {
            max-height: 50px;
        }
        
        .titulo-presupuesto {
            color: var(--color-secundario);
            font-size: 18px;
            font-weight: 700;
            margin: 0;
        }
        
        .numero-presupuesto {
            color: var(--color-primario);
            font-weight: bold;
        }
        
        .grid-informacion {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            margin-bottom: 15px;
        }
        
        .caja-informacion {
            border: 1px solid var(--borde);
            border-radius: 3px;
            padding: 8px;
            background-color: var(--gris-claro);
        }
        
        .titulo-informacion {
            font-size: 12px;
            font-weight: 600;
            color: var(--color-secundario);
            margin-top: 0;
            margin-bottom: 8px;
            padding-bottom: 3px;
            border-bottom: 1px solid var(--borde);
        }
        
        .item-informacion {
            margin-bottom: 5px;
        }
        
        .etiqueta-informacion {
            font-weight: 600;
            display: inline-block;
            width: 60px;
        }
        
        .contenedor-fechas {
            display: flex;
            justify-content: space-between;
            margin-bottom: 12px;
            font-size: 11px;
        }
        
        .item-fecha {
            background-color: var(--gris-claro);
            padding: 5px 10px;
            border-radius: 3px;
        }
        
        .tabla-items {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
            font-size: 11px;
        }
        
        .tabla-items th {
            background-color: var(--color-primario);
            color: white;
            text-align: left;
            padding: 6px 8px;
            font-weight: 600;
        }
        
        .tabla-items td {
            padding: 6px 8px;
            border-bottom: 1px solid var(--borde);
        }
        
        .tabla-items tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        
        .tabla-totales {
            width: 250px;
            margin-left: auto;
            border-collapse: collapse;
            font-size: 11px;
        }
        
        .tabla-totales th, 
        .tabla-totales td {
            padding: 5px 8px;
            text-align: right;
        }
        
        .tabla-totales th {
            background-color: var(--gris-claro);
            font-weight: 600;
        }
        
        .total-general {
            font-size: 13px;
            font-weight: 700;
            color: var(--color-destacado);
            border-top: 2px solid var(--color-primario);
            border-bottom: 2px solid var(--color-primario);
        }
        
        .pie-pagina {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px solid var(--borde);
            font-size: 11px;
        }
        
        .caja-firma {
            text-align: center;
        }
        
        .titulo-firma {
            font-weight: 600;
            margin-bottom: 20px;
        }
        
        .linea-firma {
            border-top: 1px solid var(--gris-oscuro);
            width: 70%;
            margin: 0 auto;
            padding-top: 15px;
        }
        
        .etiqueta-estado {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 10px;
            text-transform: uppercase;
            margin-left: 10px;
        }
        
        .estado-pendiente {
            background-color: #fff3cd;
            color: #856404;
        }
        
        .estado-aprobado {
            background-color: #d4edda;
            color: #155724;
        }
        
        .estado-rechazado {
            background-color: #f8d7da;
            color: #721c24;
        }
        
        .texto-derecha {
            text-align: right;
        }
        
        .texto-centro {
            text-align: center;
        }
        
        @media print {
            body {
                padding: 5px;
                font-size: 11px;
            }
            .contenedor-presupuesto {
                border: none;
                padding: 5px;
            }
        }
    </style>
</head>
<body>
    <div class="contenedor-presupuesto">
        <div class="encabezado">
            <div>
                <h1 class="titulo-presupuesto">PRESUPUESTO <span class="numero-presupuesto">#{{ $presupuesto->id_presupuesto }}</span></h1>
            </div>
        </div>

        <div class="grid-informacion">
            <div class="caja-informacion">
                <h3 class="titulo-informacion">INFORMACIÓN DE LA EMPRESA</h3>
                <div class="item-informacion">
                    <span class="etiqueta-informacion">Nombre:</span> Autokeys Locksmith
                </div>
                <div class="item-informacion">
                    <span class="etiqueta-informacion">Dirección:</span> 1989 Covington Pike, Memphis TN 38128
                </div>
                <div class="item-informacion">
                    <span class="etiqueta-informacion">Teléfono:</span> +1 (901) 513-9541
                </div>
                <div class="item-informacion">
                    <span class="etiqueta-informacion">Email:</span> usa.autokeyslocksmith@gmail.com
                </div>
            </div>

            <div class="caja-informacion">
                <h3 class="titulo-informacion">INFORMACIÓN DEL CLIENTE</h3>
                <div class="item-informacion">
                    <span class="etiqueta-informacion">Nombre:</span> {{ $presupuesto->cliente->nombre ?? 'N/A' }}
                </div>
                <div class="item-informacion">
                    <span class="etiqueta-informacion">Dirección:</span> {{ $presupuesto->cliente->direccion ?? 'N/A' }}
                </div>
                <div class="item-informacion">
                    <span class="etiqueta-informacion">Teléfono:</span> {{ $presupuesto->cliente->telefono ?? 'N/A' }}
                </div>
            </div>
        </div>

        <div class="contenedor-fechas">
            <div class="item-fecha">
                <strong>Fecha presupuesto:</strong> {{ \Carbon\Carbon::parse($presupuesto->f_presupuesto)->format('d/m/Y') }}
            </div>
            <div class="item-fecha">
                @php
                    $f_presupuesto = \Carbon\Carbon::parse($presupuesto->f_presupuesto);
                    $fechaValidez = \Carbon\Carbon::parse($presupuesto->validez);
                    $diferenciaDias = $f_presupuesto->diffInDays($fechaValidez);
                @endphp
                <strong>Validez:</strong> Hasta {{ $fechaValidez->format('d/m/Y') }} ({{ $diferenciaDias }} días)
            </div>
        </div>

        @if (is_array($presupuesto->items) && count($presupuesto->items) > 0)
            <table class="tabla-items">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>DESCRIPCIÓN</th>
                        <th class="texto-derecha">SUBTOTAL</th>
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
                            <td>{{ $item['descripcion'] ?? 'Sin descripción' }}</td>
                            <td class="texto-derecha">${{ number_format($item['precio'] ?? 0, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p class="texto-centro">No hay items en este presupuesto</p>
        @endif

        @php
            $descuentoAmount = $subtotal * ($presupuesto->descuento / 100);
            $subtotalConDescuento = $subtotal - $descuentoAmount;
            $ivaAmount = $subtotalConDescuento * ($presupuesto->iva / 100);
            $total = $subtotalConDescuento + $ivaAmount;
        @endphp

        <table class="tabla-totales">
            <tr>
                <th>Subtotal:</th>
                <td>${{ number_format($subtotal, 2) }}</td>
            </tr>
            @if($presupuesto->descuento > 0)
            <tr>
                <th>Descuento ({{ $presupuesto->descuento }}%):</th>
                <td>-${{ number_format($descuentoAmount, 2) }}</td>
            </tr>
            <tr>
                <th>Subtotal con descuento:</th>
                <td>${{ number_format($subtotalConDescuento, 2) }}</td>
            </tr>
            @endif
            @if($presupuesto->iva > 0)
            <tr>
                <th>IVA ({{ $presupuesto->iva }}%):</th>
                <td>${{ number_format($ivaAmount, 2) }}</td>
            </tr>
            @endif
            <tr class="total-general">
                <th>TOTAL:</th>
                <td>${{ number_format($total, 2) }}</td>
            </tr>
        </table>

        <div class="pie-pagina">
            <div class="caja-firma">
                <div class="titulo-firma">Preparado por</div>
                <div class="linea-firma"></div>
            </div>
            <div class="caja-firma">
                <div class="titulo-firma">Aceptación del cliente</div>
                <div class="linea-firma"></div>
            </div>
        </div>
    </div>
</body>
</html>