<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Creator;

use Doctrine\Common\Persistence\ObjectManager;
use Sylius\RefundPlugin\Exception\UnitAlreadyRefundedException;
use Sylius\RefundPlugin\Factory\RefundFactoryInterface;
use Sylius\RefundPlugin\Model\RefundType;
use Sylius\RefundPlugin\Provider\RemainingTotalProviderInterface;

final class RefundCreator implements RefundCreatorInterface
{
    /** @var RefundFactoryInterface */
    private $refundFactory;

    /** @var RemainingTotalProviderInterface */
    private $remainingTotalProvider;

    /** @var ObjectManager */
    private $refundManager;

    public function __construct(
        RefundFactoryInterface $refundFactory,
        RemainingTotalProviderInterface $remainingTotalProvider,
        ObjectManager $refundManager
    ) {
        $this->refundFactory = $refundFactory;
        $this->remainingTotalProvider = $remainingTotalProvider;
        $this->refundManager = $refundManager;
    }

    public function __invoke(string $orderNumber, int $unitId, int $amount, RefundType $refundType): void
    {
        if (!$refundType->equals(RefundType::fee()) || $unitId >= 1000) {
            $refundUnitId = $refundType->equals(RefundType::fee()) ? $unitId / 1000 : $unitId;
            $remainingTotal = $this->remainingTotalProvider->getTotalLeftToRefund($refundUnitId, $refundType, $orderNumber);

            if ($remainingTotal === 0) {
                throw UnitAlreadyRefundedException::withIdAndOrderNumber($refundUnitId, $orderNumber);
            }
        }

        $refund = $this->refundFactory->createWithData($orderNumber, $unitId, $amount, $refundType);

        $this->refundManager->persist($refund);
        $this->refundManager->flush();
    }
}
