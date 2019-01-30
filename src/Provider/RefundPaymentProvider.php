<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Provider;

use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\RefundPlugin\Entity\RefundInterface;
use Sylius\RefundPlugin\Entity\RefundPayment;
use Sylius\RefundPlugin\Entity\RefundPaymentInterface;
use Sylius\RefundPlugin\StateResolver\RefundPaymentTransitions;

final class RefundPaymentProvider implements RefundPaymentProviderInterface
{
    /** @var RepositoryInterface */
    private $paymentRepository;

    public function __construct(RepositoryInterface $paymentRepository)
    {
        $this->paymentRepository = $paymentRepository;
    }

    public function __invoke(string $orderNumber, bool $excludeFees = true, ?RefundPayment $payment = null): int
    {
        $total = 0;
        $payments = $this->paymentRepository->findBy([
            'orderNumber' => $orderNumber,
            'state' => RefundPaymentInterface::STATE_COMPLETED
        ]);

        if ($payment !== null) {
            $payments[] = $payment;
        }

        /** @var RefundPaymentInterface $payment */
        foreach ($payments as $payment) {
            $total += $payment->getAmount();

            if ($excludeFees) {
                $total -= $payment->getFeeAmount();
            }
        }

        return $total;
    }
}
