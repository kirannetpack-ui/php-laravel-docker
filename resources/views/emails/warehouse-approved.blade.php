<!DOCTYPE html>
<html>
<head><title>Warehouse Approved</title></head>
<body>
    <h2>Dear {{ $warehouse->owner->name }},</h2>
    <p>Your warehouse <strong>{{ $warehouse->name }}</strong> has been approved by KTM-WDC.</p>
    <p>It is now visible to clients for allocation.</p>
    <p>Thank you for partnering with us.</p>
    <br>
    <p>KTM-WDC Team</p>
</body>
</html>