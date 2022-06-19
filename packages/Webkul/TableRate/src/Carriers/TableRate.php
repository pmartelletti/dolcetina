<?php

namespace Webkul\TableRate\Carriers;

use Webkul\Checkout\Models\CartItem;
use Webkul\Checkout\Models\CartShippingRate;
use Webkul\Shipping\Carriers\AbstractShipping;
use Webkul\Checkout\Facades\Cart;
use Webkul\TableRate\Models\SuperSet;

/**
 * Table Rate Shipping.
 *
 */
class TableRate extends AbstractShipping
{
    /**
     * Payment method code
     *
     * @var string
     */
    protected $code  = 'tablerate';

    /**
     * Returns rate for flatrate
     *
     * @return array
     */
    public function calculate()
    {
        if (! $this->isAvailable()) {
            return false;
        }

        $shippingMethods    = [];
        $rates = [];
        $shippingData       = app('Webkul\TableRate\Helpers\ShippingHelper');
        $totalWeight = Cart::getCart()->items->sum(fn(CartItem $item) => $item->total_weight);
        $shippingCost = $shippingData->getWeightShippingRates($totalWeight);

        if (empty($shippingCost)) {
            return [];
        }

        $superset_name = $shippingCost['superset_name'];
        $shippingTotalCost = $shippingCost['price'];

        $object                     = new CartShippingRate;
        $object->carrier            = 'tablerate';
        $object->carrier_title      = $this->getConfigData('title');
        $object->method             = 'tablerate_' . $shippingCost['superset_code'];
//        $object->method             = 'tablerate';// . $shippingCost['superset_code'];
        $object->method_title       = $superset_name;
        $object->method_description = $this->getConfigData('title') . ' - ' . $superset_name;
        $object->is_calculate_tax = $this->getConfigData('is_calculate_tax') ?: 0;
        $object->price              = core()->convertPrice($shippingTotalCost);

        $object->base_price         = $shippingCost['price'];

        $shipping_rates = session()->get('shipping_rates');

        $rates[$superset_name] = [
            'amount'        => core()->convertPrice($shippingTotalCost),
            'base_amount'   => $shippingTotalCost
        ];

        if (! is_array($shipping_rates)) {
            $shipment_rates['tablerate'] = $rates;

            session()->put('shipping_rates', $shipment_rates);
        } else {
            session()->put('shipping_rates.tablerate', $rates);
        }

        array_push($shippingMethods, $object);

        return $shippingMethods;
    }

    public function getServices()
    {
        return null;
    }

    public function getSuperSets($columns = ['*'])
    {
        return SuperSet::query()
            ->select($columns)
            ->where('status', true)
            ->get()
        ;
    }
}
