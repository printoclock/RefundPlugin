<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Sender;

use Sylius\Component\Mailer\Sender\SenderInterface;
use Sylius\RefundPlugin\Entity\RefundPaymentInterface;
use Sylius\RefundPlugin\File\FileManagerInterface;

final class RefundPaymentEmailSender implements RefundPaymentEmailSenderInterface
{
    /** @var SenderInterface */
    private $emailSender;

    public function __construct(
        SenderInterface $emailSender
    ) {
        $this->emailSender = $emailSender;
    }

    public function send(
        RefundPaymentInterface $refundPayment,
        string $recipient
    ): void {
        $this->sender->send(
            'refund_payment_generated',
            [$recipient],
            ['refundPayment' => $refundPayment]
        );
    }
}
