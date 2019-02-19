<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\CommandHandler;

use Doctrine\Common\Persistence\ObjectManager;
use Sylius\RefundPlugin\Command\GenerateCreditMemo;
use Sylius\RefundPlugin\Event\CreditMemoGenerated;
use Sylius\RefundPlugin\Generator\CreditMemoGeneratorInterface;
use Symfony\Component\Messenger\MessageBusInterface;

final class GenerateCreditMemoHandler
{
    /** @var CreditMemoGeneratorInterface */
    private $creditMemoGenerator;

    /** @var ObjectManager */
    private $creditMemoManager;

    /** @var MessageBusInterface */
    private $eventBus;

    public function __construct(
        CreditMemoGeneratorInterface $creditMemoGenerator,
        ObjectManager $creditMemoManager,
        MessageBusInterface $eventBus
    ) {
        $this->creditMemoGenerator = $creditMemoGenerator;
        $this->creditMemoManager = $creditMemoManager;
        $this->eventBus = $eventBus;
    }

    public function __invoke(GenerateCreditMemo $command): void
    {
        $orderNumber = $command->orderNumber();

        $creditMemo = $this->creditMemoGenerator->generate(
            $command->token(),
            $orderNumber,
            $command->total(),
            $command->units(),
            $command->shipments(),
            $command->payments(),
            $command->fees(),
            $command->comment(),
            $command->paymentMethodId()
        );

        $this->creditMemoManager->persist($creditMemo);
        $this->creditMemoManager->flush();

        $this->eventBus->dispatch(new CreditMemoGenerated($creditMemo->getNumber(), $orderNumber));
    }
}
