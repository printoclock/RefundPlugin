<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\CommandHandler;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\RefundPlugin\Event\RefundPaymentGenerated;
use Sylius\RefundPlugin\Exception\OrderNotFound;
use Sylius\RefundPlugin\Sender\RefundPaymentEmailSenderInterface;

final class RefundPaymentHandler
{
    /** @var OrderRepositoryInterface */
    private $orderRepository;

    /** @var RepositoryInterface */
    private $refundPaymentRepository;

    /** @var RefundPaymentEmailSenderInterface */
    private $refundPaymentEmailSender;

    public function __construct(
        OrderRepositoryInterface $orderRepository,
        RepositoryInterface $refundPaymentRepository,
        RefundPaymentEmailSenderInterface $refundPaymentEmailSender
    ) {
        $this->orderRepository = $orderRepository;
        $this->refundPaymentRepository = $refundPaymentRepository;
        $this->refundPaymentEmailSender = $refundPaymentEmailSender;
    }

    public function __invoke(RefundPaymentGenerated $command): void
    {
        /** @var OrderInterface|null $order */
        $order = $this->orderRepository->findOneByNumber($command->orderNumber());
        if ($order === null) {
            throw OrderNotFound::withNumber($command->orderNumber());
        }

        $refundPayment = (!empty($token = $command->token())) ? $this->refundPaymentRepository->findOneBy([
            'token' => $token
        ]) : null;

        $this->refundPaymentEmailSender->send($refundPayment, $order->getCustomer()->getEmail());
    }
}
