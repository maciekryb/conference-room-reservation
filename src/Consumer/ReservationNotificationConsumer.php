<?php

namespace App\Consumer;

use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;
use Psr\Log\LoggerInterface;

class ReservationNotificationConsumer implements ConsumerInterface
{
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function execute(AMQPMessage $msg): int
    {
        try {
            $data = json_decode($msg->getBody(), true);

            $this->logger->info('Reservation notification received', [
                'action' => $data['action'],
                'roomName' => $data['roomName'],
                'personName' => $data['personName'],
                'startDate' => $data['startDate'],
                'endDate' => $data['endDate'],
            ]);
            return ConsumerInterface::MSG_ACK;
        } catch (\Exception $e) {
            $this->logger->error('Notification processing error: ' . $e->getMessage());
            return ConsumerInterface::MSG_REJECT_REQUEUE;
        }
    }
}
