<?php

namespace HiEvents\Mail\Attendee;

use HiEvents\DomainObjects\AttendeeDomainObject;
use HiEvents\DomainObjects\EventDomainObject;
use HiEvents\DomainObjects\EventSettingDomainObject;
use HiEvents\Helper\Url;
use HiEvents\Mail\BaseMail;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Support\Str;

/**
 * @uses /backend/resources/views/emails/orders/attendee-ticket.blade.php
 */
class AttendeeTicketMail extends BaseMail
{
    public function __construct(
        private readonly AttendeeDomainObject     $attendee,
        private readonly EventDomainObject        $event,
        private readonly EventSettingDomainObject $eventSettings,
    )
    {
        parent::__construct();
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            replyTo: $this->eventSettings->getSupportEmail(),
            subject: __('🎟️ Your Ticket for :event', [
                'event' => Str::limit($this->event->getTitle(), 50)
            ]),
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.orders.attendee-ticket',
            with: [
                'event' => $this->event,
                'attendee' => $this->attendee,
                'eventSettings' => $this->eventSettings,
                'ticketUrl' => sprintf(
                    Url::getFrontEndUrlFromConfig(Url::ATTENDEE_TICKET),
                    $this->event->getId(),
                    $this->attendee->getShortId(),
                )
            ]
        );
    }
}
