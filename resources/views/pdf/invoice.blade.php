<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recibo de Renta</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        .container {
            width: 90%;
            padding: 20px;
            border: 1px solid #000;
            max-width: 800px;
            margin: 20px auto;
            background-color: #fff; /* Fondo blanco */
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .header {
            background-color: #1F4529;
            color: white;
            text-align: center;
            padding: 10px;
            position: relative;
            width: 100%;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .header .number {
            position: absolute;
            top: 10px;
            right: 20px;
            font-size: 18px;
            color: yellow;
            font-weight: bold;
            border: 2px solid yellow;
            padding: 5px 10px;
        }
        .content {
            padding: 10px 20px;
            width: 100%;
        }
        .content table {
            width: 100%;
            margin-bottom: 20px;
            border-collapse: collapse;
        }
        .content td {
            padding: 10px;
            border: 1px solid #000;
            text-align: center;
        }
        .footer {
            margin-top: 20px;
        }
        .payment-method {
            display: flex;
            justify-content: flex-start;
            align-items: center;
            gap: 10px;
            margin-top: 20px;
        }
        .signature {
            margin-top: 40px;
            text-align: right;
        }
        .signature-line {
            border-top: 1px solid #000;
            width: 200px;
            margin-left: auto;
        }
        .signature-label {
            margin-top: 5px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>RECIBO DE RENTA</h1>
            <span class="number">N° {{ str_pad($invoice->id, 4, '0', STR_PAD_LEFT) }}</span>
        </div>
        <div class="content">
            <table>
                <tr>
                    <td><strong>Fecha:</strong></td>
                    <td>{{ date('d/m/Y', strtotime($invoice->issue_date)) }}</td>
                    <td><strong>Cantidad:</strong></td>
                    <td>${{ number_format($invoice->total_amount, 2) }}</td>
                </tr>
                <tr>
                    <td><strong>Recibí de:</strong></td>
                    <td colspan="3"></td>
                </tr>
                <tr>
                    <td colspan="4">
                        <i>
                            Cantidad en letras: {{ convertToLetters($invoice->total_amount) }} PESOS
                        </i>
                    </td>
                </tr>
                <tr>
                    <td><strong>Por concepto de renta:</strong></td>
                    <td colspan="3">Renta de vivienda</td>
                </tr>
                <tr>
                    <td><strong>Fecha de pago desde:</strong></td>
                    <td>{{ date('d/m/Y', strtotime($invoice->issue_date)) }}</td>
                    <td><strong>Hasta:</strong></td>
                    <td>{{ date('d/m/Y', strtotime($invoice->issue_date)) }}</td>
                </tr>
            </table>
            <div class="payment-method">
                <span><strong>Pago:</strong></span>
                <label><input type="checkbox" checked> Efectivo</label>
                <label><input type="checkbox" disabled> Cheque</label>
                <label><input type="checkbox" disabled> Transferencia</label>
            </div>
            <div class="signature">
                <div class="signature-line"></div>
                <p class="signature-label">Nombre y firma</p>
            </div>
        </div>
    </div>
</body>
</html>

<?php
function convertToLetters($number) {
    // Definición de números en palabras
    $units = [
        '', 'uno', 'dos', 'tres', 'cuatro', 'cinco', 'seis', 'siete', 'ocho', 'nueve',
        'diez', 'once', 'doce', 'trece', 'catorce', 'quince', 'dieciséis', 'diecisiete',
        'dieciocho', 'diecinueve', 'veinte', 'veintiuno', 'veintidós', 'veintitrés',
        'veinticuatro', 'veinticinco', 'veintiséis', 'veintisiete', 'veintiocho', 
        'veintinueve', 'treinta', 'cuarenta', 'cincuenta', 'sesenta', 'setenta', 
        'ochenta', 'noventa', 'cien', 'ciento'
    ];
    $tens = [
        '', '', 'veinte', 'treinta', 'cuarenta', 'cincuenta', 'sesenta', 'setenta', 
        'ochenta', 'noventa'
    ];
    
    if ($number < 100) {
        return $units[$number];
    } elseif ($number < 1000) {
        $hundreds = floor($number / 100);
        $remainder = $number % 100;
        return ($hundreds === 1 && $remainder === 0) ? 'cien' : $units[$hundreds] . 'cientos ' . ($remainder ? convertToLetters($remainder) : '');
    } elseif ($number < 1000000) {
        $thousands = floor($number / 1000);
        $remainder = $number % 1000;
        return convertToLetters($thousands) . ' mil ' . ($remainder ? convertToLetters($remainder) : '');
    } elseif ($number < 1000000000) {
        $millions = floor($number / 1000000);
        $remainder = $number % 1000000;
        return convertToLetters($millions) . ' millones ' . ($remainder ? convertToLetters($remainder) : '');
    }
    
    return 'Número demasiado grande';
}
?>
