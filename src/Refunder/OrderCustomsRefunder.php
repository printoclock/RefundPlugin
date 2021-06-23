<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Refunder;

use Sylius\RefundPlugin\Creator\RefundCreatorInterface;
use Sylius\RefundPlugin\Event\CustomRefunded;
use Sylius\RefundPlugin\Event\FeeRefunded;
use Sylius\RefundPlugin\Model\CustomRefund;
use Sylius\RefundPlugin\Model\FeeRefund;
use Sylius\RefundPlugin\Model\RefundType;
use Symfony\Component\Messenger\MessageBusInterface;

final class OrderCustomsRefunder implements RefunderInterface
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

        /** @var CustomRefund $customUnit */
        foreach ($units as $customUnit) {
            $this->refundCreator->__invoke($orderNumber, $customUnit->id(), $customUnit->total(), RefundType::custom());

            $refundedTotal += $customUnit->total();

            $this->eventBus->dispatch(new CustomRefunded($orderNumber, $customUnit->id(), $customUnit->total()));
        }

        return $refundedTotal;
    }
}
