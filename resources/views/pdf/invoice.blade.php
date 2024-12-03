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
            width: 100%;
            padding: 20px;
            border: 1px solid #000;
            max-width: 800px;
            margin: auto;
        }

        .header {
            background-color: #003366;
            color: white;
            text-align: center;
            padding: 10px;
            position: relative;
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
        }

        .content table {
            width: 100%;
            margin-bottom: 20px;
        }

        .content td {
            padding: 5px 10px;
            border: 1px solid #000;
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
                    <td>{{ $invoice->issue_date }}</td>
                    <td><strong>Cantidad:</strong></td>
                    <td>${{ number_format($invoice->total_amount, 2) }}</td>
                </tr>
                <tr>
                    <td><strong>Recibí de:</strong></td>
                    <td colspan="3">[NOMBRE DEL INQUILINO]</td>
                </tr>
                <tr>
                    <td colspan="4">
                        <i>
                            Cantidad en letras PESOS
                        </i>
                    </td>
                </tr>
                <tr>
                    <td><strong>Por concepto de renta:</strong></td>
                    <td colspan="3">Renta de vivienda</td>
                </tr>
                <tr>
                    <td><strong>Fecha de pago desde:</strong></td>
                    <td>{{ $invoice->issue_date }}</td>
                    <td><strong>Hasta:</strong></td>
                    <td>{{ $invoice->issue_date }}</td>
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
                <p>Nombre y firma</p>
            </div>
        </div>
    </div>
</body>

</html>
