<?php

declare(strict_types=1);

namespace Sylius\RefundPlugin\Creator;

use Sylius\RefundPlugin\Command\RefundUnits;
use Sylius\RefundPlugin\Model\FeeRefund;
use Sylius\RefundPlugin\Model\OrderItemUnitRefund;
use Sylius\RefundPlugin\Model\PaymentRefund;
use Sylius\RefundPlugin\Model\RefundType;
use Sylius\RefundPlugin\Model\ShipmentRefund;
use Sylius\RefundPlugin\Model\UnitRefundInterface;
use Sylius\RefundPlugin\Provider\RemainingTotalProviderInterface;
use Symfony\Component\HttpFoundation\Request;

final class RefundUnitsCommandCreator implements RefundUnitsCommandCreatorInterface
{
    /** @var RemainingTotalProviderInterface */
    private $remainingTotalProvider;

    public function __construct(RemainingTotalProviderInterface $remainingTotalProvider)
    {
        $this->remainingTotalProvider = $remainingTotalProvider;
    }

    public function fromRequest(Request $request): RefundUnits
    {
        if (!$request->attributes->has('orderNumber')) {
            throw new \InvalidArgumentException('Refunded order number not provided');
        }

        $orderNumber = $request->attributes->get('orderNumber');

        $units = $this->filterEmptyRefundUnits($request->request->get('sylius_refund_units', []));
        $shipments = $this->filterEmptyRefundUnits($request->request->get('sylius_refund_shipments', []));
        $payments = $this->filterEmptyRefundUnits($request->request->get('sylius_refund_payments', []));
        $fees = $this->parseIdsToFeeRefunds($this->filterEmptyRefundUnits($request->request->get('sylius_refund_fees', [])), $orderNumber);

        if (count($units) === 0 && count($shipments) === 0 && count($payments) === 0 && count($this->getRefundFees($fees)) === 0) {
            throw new \InvalidArgumentException('sylius_refund.at_least_one_unit_should_be_selected_to_refund');
        }

        return new RefundUnits(
            $orderNumber,
            $this->parseIdsToUnitRefunds($units, $orderNumber),
            $this->parseIdsToShipmentRefunds($shipments, $orderNumber),
            $this->parseIdsToPaymentRefunds($payments, $orderNumber),
            $fees,
            (int) $request->request->get('sylius_refund_payment_method'),
            $request->request->get('sylius_refund_date'),
            $request->request->get('sylius_refund_reference'),
            $request->request->get('sylius_refund_comment'),
            $request->request->get('sylius_refund_token', null)
        );
    }

    private function parseIdsToUnitRefunds(array $units, string $orderNumber): array
    {
        return array_map(function (array $refundUnit) use ($orderNumber) : UnitRefundInterface {
            if (isset($refundUnit['amount']) && $refundUnit['amount'] !== '') {
                $id = (int) $refundUnit['partial-id'];
                $total = (int) (((float) $refundUnit['amount']) * 100);

                return new OrderItemUnitRefund($id, $total);
            }

            $id = (int) $refundUnit['id'];
            $total = $this->remainingTotalProvider->getTotalLeftToRefund($id, RefundType::orderItemUnit(), $orderNumber);

            return new OrderItemUnitRefund($id, $total);
        }, $units);
    }

    private function parseIdsToShipmentRefunds(array $units, string $orderNumber): array
    {
        return array_map(function (array $refundShipment) use ($orderNumber) : UnitRefundInterface {
            if (isset($refundShipment['amount']) && $refundShipment['amount'] !== '') {
                $id = (int) $refundShipment['partial-id'];
                $total = (int) (((float) $refundShipment['amount']) * 100);

                return new ShipmentRefund($id, $total);
            }

            $id = (int) $refundShipment['id'];
            $total = $this->remainingTotalProvider->getTotalLeftToRefund($id, RefundType::shipment(), $orderNumber);

            return new ShipmentRefund($id, $total);
        }, $units);
    }

    private function parseIdsToPaymentRefunds(array $units, string $orderNumber): array
    {
        return array_map(function (array $refundPayment) use ($orderNumber) : UnitRefundInterface {
            if (isset($refundPayment['amount']) && $refundPayment['amount'] !== '') {
                $id = (int) $refundPayment['partial-id'];
                $total = (int) (((float) $refundPayment['amount']) * 100);

                return new PaymentRefund($id, $total);
            }

            $id = (int) $refundPayment['id'];
            $total = $this->remainingTotalProvider->getTotalLeftToRefund($id, RefundType::payment(), $orderNumber);

            return new PaymentRefund($id, $total);
        }, $units);
    }

    private function parseIdsToFeeRefunds(array $units, string $orderNumber): array
    {
        return array_map(function (array $refundFee) use ($orderNumber) : UnitRefundInterface {
            if (isset($refundFee['amount']) && $refundFee['amount'] !== '') {
                $id = (int) $refundFee['partial-id'];
                $total = (int) (((float) $refundFee['amount']) * 100);

                if ($id < 1000) {
                    $total *= -1;
                }

                return new FeeRefund($id, $total);
            }

            $id = (int) $refundFee['id'];
            $total = $this->remainingTotalProvider->getTotalLeftToRefund($id, RefundType::fee(), $orderNumber);

            return new FeeRefund($id, $total);
        }, $units);
    }

    private function filterEmptyRefundUnits(array $units): array
    {
        return array_filter($units, function (array $refundUnit): bool {
            return (isset($refundUnit['amount']) && $refundUnit['amount'] !== '') || isset($refundUnit['id']);
        });
    }

    private function getRefundFees(array $fees): array {
        $result = [];

        /** @var FeeRefund $fee */
        foreach ($fees as $fee) {
            if ($fee->id() >= 1000) {
                $result[] = $fee;
            }
        }

        return $result;
    }
}
