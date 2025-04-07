<?php

namespace App\Mail;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;

use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Mail\Mailable;

class BookingSuccessMail extends Mailable
{
    public $booking;
    
    public function __construct($booking)
    {
        $this->booking = $booking;
    }

    public function build()
    {
        // Generate QR code as a base64 string
        $qrCodeImage = base64_encode(QrCode::format('png')->size(300)->generate($this->booking->booking_id));
        
        return $this->view('emails.booking_success')
                    ->with([
                        'booking' => $this->booking,
                        'qrCodeImage' => $qrCodeImage,
                    ]);
    }
}
