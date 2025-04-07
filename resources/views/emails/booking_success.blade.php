<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Confirmation</title>
</head>
<body>
    <h1>Your Booking is Confirmed!</h1>
    <p>Booking ID: {{ $booking->booking_id }}</p>
    <p>Scan the QR code below for check-in:</p>
    <img src="data:image/png;base64,{{ $qrCodeImage }}" alt="QR Code">
</body>
</html>
