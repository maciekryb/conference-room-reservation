<?php

namespace App\Service;

use OldSound\RabbitMqBundle\RabbitMq\ProducerInterface;
use App\Entity\Reservation;

class NotificationService
{
    private $producer;

    public function __construct(ProducerInterface $reservationNotificationProducer)
    {
        $this->producer = $reservationNotificationProducer;
    }

    public function sendReservationNotification(Reservation $reservation, string $action): void
    {
        $message = [
            'reservationId' => $reservation->getId(),
            'action' => $action,
            'roomId' => $reservation->getConferenceRoomId(),
            'personName' => $reservation->getReservedBy(),
            'startDate' => $reservation->getStartTime()->format('Y-m-d H:i:s'),
            'endDate' => $reservation->getEndTime()->format('Y-m-d H:i:s'),
        ];

        $this->producer->publish(json_encode($message));
    }
}