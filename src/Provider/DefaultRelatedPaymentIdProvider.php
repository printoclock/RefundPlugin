<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Provider;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Payment\Model\PaymentInterface as BasePaymentInterface;
use Sylius\RefundPlugin\Entity\RefundPaymentInterface;
use Sylius\RefundPlugin\Exception\CompletedPaymentNotFound;
use Sylius\RefundPlugin\Exception\OrderNotFound;

final class DefaultRelatedPaymentIdProvider implements RelatedPaymentIdProviderInterface
{
    /** @var OrderRepositoryInterface */
    private $orderRepository;

    public function __construct(OrderRepositoryInterface $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }

    public function getForRefundPayment(RefundPaymentInterface $refundPayment): int
    {
        $orderNumber = $refundPayment->getOrderNumber();
        /** @var OrderInterface|null $order */
        $order = $this->orderRepository->findOneByNumber($orderNumber);

        if ($order === null) {
            throw OrderNotFound::withNumber($orderNumber);
        }

        $payment = $this->getFirstCompletedOrProcessingPayment($order);

        if ($payment === null) {
            throw CompletedPaymentNotFound::withNumber($orderNumber);
        }

        return $payment->getId();
    }

    public function getFirstCompletedOrProcessingPayment(OrderInterface $order): ?PaymentInterface
    {
        if ($order->getPayments()->isEmpty()) {
            return null;
        }

        $payment = $order->getPayments()->filter(function (BasePaymentInterface $payment): bool {
            return in_array($payment->getState(), [PaymentInterface::STATE_PROCESSING, PaymentInterface::STATE_COMPLETED]);
        })->first();

        return ($payment !== false) ? $payment : null;
    }
}
