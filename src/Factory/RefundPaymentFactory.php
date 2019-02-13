<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Factory;

use Sylius\Component\Core\Model\PaymentMethodInterface;
use Sylius\Component\Core\Repository\PaymentMethodRepositoryInterface;
use Sylius\RefundPlugin\Entity\RefundPayment;
use Sylius\RefundPlugin\Entity\RefundPaymentInterface;

final class RefundPaymentFactory implements RefundPaymentFactoryInterface
{
    /** @var PaymentMethodRepositoryInterface */
    private $paymentMethodRepository;

    public function __construct(PaymentMethodRepositoryInterface $paymentMethodRepository)
    {
        $this->paymentMethodRepository = $paymentMethodRepository;
    }

    public function createWithData(
        string $token,
        string $orderNumber,
        int $amount,
        int $feeAmount,
        string $currencyCode,
        string $state,
        int $paymentMethodId,
        ?\DateTime $payedAt = null,
        ?string $reference = null,
        ?string $comment = null
    ): RefundPaymentInterface {
        /** @var PaymentMethodInterface $paymentMethod */
        $paymentMethod = $this->paymentMethodRepository->find($paymentMethodId);

        return new RefundPayment($token, $orderNumber, $amount, $feeAmount, $currencyCode, $state, $paymentMethod, $payedAt, $reference, $comment);
    }
}
