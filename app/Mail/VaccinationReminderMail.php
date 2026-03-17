<?php

namespace App\Mail;

use App\Models\PetVaccination;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class VaccinationReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $petName;
    public string $vaccineName;
    public string $dueDate;

    public function __construct(PetVaccination $vaccination)
    {
        $this->petName = $vaccination->pet->name ?? 'your pet';
        $this->vaccineName = $vaccination->vaccine_name ?? 'Vaccination';
        $this->dueDate = Carbon::parse($vaccination->next_due_date)->format('M d, Y');
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Vaccination Reminder: {$this->vaccineName} for {$this->petName}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.vaccination-reminder',
        );
    }
}
