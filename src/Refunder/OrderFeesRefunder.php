<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Refunder;

use Sylius\RefundPlugin\Creator\RefundCreatorInterface;
use Sylius\RefundPlugin\Event\FeeRefunded;
use Sylius\RefundPlugin\Model\FeeRefund;
use Sylius\RefundPlugin\Model\RefundType;
use Symfony\Component\Messenger\MessageBusInterface;

final class OrderFeesRefunder implements RefunderInterface
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

        /** @var FeeRefund $feeUnit */
        foreach ($units as $feeUnit) {
            $this->refundCreator->__invoke($orderNumber, $feeUnit->id(), $feeUnit->total(), RefundType::fee());

            $refundedTotal += $feeUnit->total();

            $this->eventBus->dispatch(new FeeRefunded($orderNumber, $feeUnit->id(), $feeUnit->total()));
        }

        return $refundedTotal;
    }
}
