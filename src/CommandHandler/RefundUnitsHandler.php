<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\CommandHandler;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\RefundPlugin\Command\RefundUnits;
use Sylius\RefundPlugin\Event\UnitsRefunded;
use Sylius\RefundPlugin\Model\FeeRefund;
use Sylius\RefundPlugin\Refunder\RefunderInterface;
use Sylius\RefundPlugin\Validator\RefundUnitsCommandValidatorInterface;
use Symfony\Component\Messenger\MessageBusInterface;

final class RefundUnitsHandler
{
    /** @var RefunderInterface */
    private $orderUnitsRefunder;

    /** @var RefunderInterface */
    private $orderShipmentsRefunder;

    /** @var RefunderInterface */
    private $orderPaymentsRefunder;

    /** @var RefunderInterface */
    private $orderFeesRefunder;

    /** @var MessageBusInterface */
    private $eventBus;

    /** @var OrderRepositoryInterface */
    private $orderRepository;

    /** @var RefundUnitsCommandValidatorInterface */
    private $refundUnitsCommandValidator;

    public function __construct(
        RefunderInterface $orderUnitsRefunder,
        RefunderInterface $orderShipmentsRefunder,
        RefunderInterface $orderPaymentsRefunder,
        RefunderInterface $orderFeesRefunder,
        MessageBusInterface $eventBus,
        OrderRepositoryInterface $orderRepository,
        RefundUnitsCommandValidatorInterface $refundUnitsCommandValidator
    ) {
        $this->orderUnitsRefunder = $orderUnitsRefunder;
        $this->orderShipmentsRefunder = $orderShipmentsRefunder;
        $this->orderPaymentsRefunder = $orderPaymentsRefunder;
        $this->orderFeesRefunder = $orderFeesRefunder;
        $this->eventBus = $eventBus;
        $this->orderRepository = $orderRepository;
        $this->refundUnitsCommandValidator = $refundUnitsCommandValidator;
    }

    public function __invoke(RefundUnits $command): void
    {
        $this->refundUnitsCommandValidator->validate($command);

        $orderNumber = $command->orderNumber();

        /** @var OrderInterface $order */
        $order = $this->orderRepository->findOneByNumber($orderNumber);

        $refundedExtraFeeTotal = $this->orderFeesRefunder->refundFromOrder($this->getExtraFees($command->fees()), $orderNumber);

        $refundedTotal = 0;
        $refundedTotal += $this->orderUnitsRefunder->refundFromOrder($command->units(), $orderNumber);
        $refundedTotal += $this->orderShipmentsRefunder->refundFromOrder($command->shipments(), $orderNumber);
        $refundedTotal += $this->orderPaymentsRefunder->refundFromOrder($command->payments(), $orderNumber);
        $refundedTotal += $this->orderFeesRefunder->refundFromOrder($this->getRefundFees($command->fees()), $orderNumber);
        $refundedTotal += $refundedExtraFeeTotal;

        $this->eventBus->dispatch(new UnitsRefunded(
            $command->token() ?? bin2hex(random_bytes(16)),
            $orderNumber,
            $command->units(),
            $command->shipments(),
            $command->payments(),
            $command->fees(),
            $command->paymentMethodId(),
            $refundedTotal,
            $refundedExtraFeeTotal,
            $order->getCurrencyCode(),
            $command->payedAt(),
            $command->reference(),
            $command->comment()
        ));
    }

    protected function getRefundFees(array $fees): array {
        $result = [];

        /** @var FeeRefund $fee */
        foreach ($fees as $fee) {
            if ($fee->id() >= 1000) {
                $result[] = $fee;
            }
        }

        return $result;
    }

    protected function getExtraFees(array $fees): array {
        $result = [];

        /** @var FeeRefund $fee */
        foreach ($fees as $fee) {
            if ($fee->id() < 1000) {
                $result[] = $fee;
            }
        }

        return $result;
    }
}
