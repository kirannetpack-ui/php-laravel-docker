<!DOCTYPE html>
<html>
<head><title>Monthly Invoice</title></head>
<body>
    <h2>Monthly Warehouse Invoice</h2>
    <p>Dear {{ $invoice->warehouseRequest->client->name }},</p>
    <p>Your monthly invoice for warehouse space is <strong>Rs. {{ number_format($invoice->amount, 2) }}</strong>.</p>
    <p>Due date: {{ $invoice->due_date->format('d M Y') }}</p>
    <p>Please make the payment by the due date to avoid late fees.</p>
    <br>
    <p>KTM-WDC Team</p>
</body>
</html>