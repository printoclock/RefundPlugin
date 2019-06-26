<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\CommandHandler;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\RefundPlugin\Event\RefundPaymentGenerated;

final class RefundPaymentHandler
{
    /** @var OrderRepositoryInterface */
    private $orderRepository;

    /** @var RepositoryInterface */
    private $refundPaymentRepository;

    public function __construct(
        OrderRepositoryInterface $orderRepository,
        RepositoryInterface $refundPaymentRepository
    ) {
        $this->orderRepository = $orderRepository;
        $this->refundPaymentRepository = $refundPaymentRepository;
    }

    public function __invoke(RefundPaymentGenerated $command): void
    {

    }
}
