<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Refunder;

use Sylius\RefundPlugin\Creator\RefundCreatorInterface;
use Sylius\RefundPlugin\Event\ShipmentRefunded;
use Sylius\RefundPlugin\Model\PaymentRefund;
use Sylius\RefundPlugin\Model\RefundType;
use Sylius\RefundPlugin\Model\ShipmentRefund;
use Symfony\Component\Messenger\MessageBusInterface;

final class OrderPaymentsRefunder implements RefunderInterface
{
    /** @var RefundCreatorInterface */
    private $refundCreator;

    /** @var MessageBusInterface */
    private $eventBus;

    public function __construct(RefundCreatorInterface $refundCreator, MessageBusInterface $eventBus)
    {
        $this->refundCreator = $refundCreator;
        $this->eventBus = $eventBus;
    }

    public function refundFromOrder(array $units, string $orderNumber): int
    {
        $refundedTotal = 0;

        /** @var PaymentRefund $paymentUnit */
        foreach ($units as $paymentUnit) {
            $this->refundCreator->__invoke($orderNumber, $paymentUnit->id(), $paymentUnit->total(), RefundType::payment());

            $refundedTotal += $paymentUnit->total();

            $this->eventBus->dispatch(new ShipmentRefunded($orderNumber, $paymentUnit->id(), $paymentUnit->total()));
        }

        return $refundedTotal;
    }
}
