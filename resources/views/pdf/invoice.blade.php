<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        .invoice-header {
            text-align: center;
            margin-bottom: 20px;
        }
        .invoice-details {
            margin-bottom: 20px;
        }
        .invoice-footer {
            text-align: center;
            margin-top: 40px;
        }
    </style>
</head>
<body>
    <div class="invoice-header">
        <h1>Invoice #{{ $invoice->id }}</h1>
        <p>Date: {{ $invoice->issue_date }}</p>
    </div>

    <div class="invoice-details">
        <p><strong>Total Amount:</strong> ${{ number_format($invoice->total_amount, 2) }}</p>
        <p><strong>Payment Status:</strong> {{ $invoice->payment_status }}</p>
    </div>

    <div class="invoice-footer">
        <p>Thank you for your payment!</p>
    </div>
</body>
</html>
