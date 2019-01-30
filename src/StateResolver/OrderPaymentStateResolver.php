<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\StateResolver;

use Doctrine\Common\Persistence\ObjectManager;
use SM\Factory\FactoryInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\RefundPlugin\Checker\OrderFullyRefundedTotalCheckerInterface;
use Sylius\RefundPlugin\Entity\RefundPayment;
use Sylius\RefundPlugin\Provider\RefundPaymentProviderInterface;
use Webmozart\Assert\Assert;

final class OrderPaymentStateResolver implements OrderPaymentStateResolverInterface
{
    /** @var FactoryInterface */
    private $stateMachineFactory;

    /** @var ObjectManager */
    private $orderManager;

    /** @var RefundPaymentProviderInterface */
    private $refundPaymentProvider;

    /** @var OrderRepositoryInterface */
    private $orderRepository;

    public function __construct(
        FactoryInterface $stateMachineFactory,
        ObjectManager $orderManager,
        RefundPaymentProviderInterface $refundPaymentProvider,
        OrderRepositoryInterface $orderRepository
    ) {
        $this->stateMachineFactory = $stateMachineFactory;
        $this->orderManager = $orderManager;
        $this->refundPaymentProvider = $refundPaymentProvider;
        $this->orderRepository = $orderRepository;
    }

    public function resolve(string $orderNumber, ?RefundPayment $payment = null): void
    {
        /** @var OrderInterface $order */
        $order = $this->orderRepository->findOneByNumber($orderNumber);
        Assert::notNull($order);

        $refundedTotal = $this->refundPaymentProvider->__invoke($orderNumber, true, $payment);

        if ($refundedTotal === 0) return;

        $stateMachine = $this->stateMachineFactory->get($order, OrderPaymentTransitions::GRAPH);

        if ($refundedTotal < $order->getTotal()) {
            $stateMachine->apply(OrderPaymentTransitions::TRANSITION_PARTIALLY_REFUND);
        } else {
            $stateMachine->apply(OrderPaymentTransitions::TRANSITION_REFUND);
        }

        $this->orderManager->flush();
    }
}
