<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Menu;

use Knp\Menu\ItemInterface;
use Sylius\Bundle\UiBundle\Menu\Event\MenuBuilderEvent;

final class AdminMainMenuListener
{
    public function addCreditMemosSection(MenuBuilderEvent $event): void
    {
        /** @var ItemInterface $salesMenu */
        $salesMenu = $event->getMenu()->getChild('sales');

        $salesMenu
            ->addChild('refund_payments', [
                'route' => 'sylius_refund_admin_refund_payment_index',
            ])
            ->setLabel('sylius_refund.ui.refund_payments')
            ->setLabelAttribute('icon', 'payment');

        $salesMenu
            ->addChild('credit_memos', [
                'route' => 'sylius_refund_admin_credit_memo_index',
            ])
            ->setLabel('sylius_refund.ui.credit_memos')
            ->setLabelAttribute('icon', 'file');
    }
}
