<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Sender;

use Sylius\RefundPlugin\Entity\RefundPaymentInterface;

interface RefundPaymentEmailSenderInterface
{
    public function send(RefundPaymentInterface $refundPayment, string $recipient): void;
}
