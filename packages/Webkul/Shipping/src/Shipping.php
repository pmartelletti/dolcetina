<?php

namespace Webkul\Shipping;

use Illuminate\Support\Facades\Config;
use Webkul\Checkout\Facades\Cart;
use Webkul\TableRate\Carriers\TableRate;

class Shipping
{
    /**
     * Rates.
     *
     * @var array
     */
    protected $rates = [];

    /**
     * Collects rate from available shipping methods.
     *
     * @return array
     */
    public function collectRates()
    {
        if (! $cart = Cart::getCart()) {
            return false;
        }

        $this->removeAllShippingRates();

        foreach (Config::get('carriers') as $shippingMethod) {
            $object = new $shippingMethod['class'];

            if ($rates = $object->calculate()) {
                if (is_array($rates)) {
                    $this->rates = array_merge($this->rates, $rates);
                } else {
                    $this->rates[] = $rates;
                }
            }
        }

        $this->saveAllShippingRates();

        return [
            'jump_to_section' => 'shipping',
            'shippingMethods' => $this->getGroupedAllShippingRates($cart),
            'html'            => view('shop::checkout.onepage.shipping', ['shippingRateGroups' => $this->getGroupedAllShippingRates($cart)])->render(),
        ];
    }

    /**
     * Remove all shipping rates.
     *
     * @return void
     */
    public function removeAllShippingRates()
    {
        if (! $cart = Cart::getCart()) {
            return;
        }

        foreach ($cart->shipping_rates()->get() as $rate) {
            $rate->delete();
        }

        $this->rates = [];
    }

    /**
     * Save all shipping rates.
     *
     * @return void
     */
    public function saveAllShippingRates()
    {
        if (! $cart = Cart::getCart()) {
            return;
        }

        $shippingAddress = $cart->shipping_address;

        if ($shippingAddress) {

            foreach ($this->rates as $rate) {
                $rate->cart_address_id = $shippingAddress->id;

                $rate->save();
            }
        }
    }

    /**
     * Returns shipping rates, grouped by shipping method.
     *
     * @return void
     */
    public function getGroupedAllShippingRates($cart)
    {
        $rates = [];

        foreach ($this->rates as $rate) {
            if (! isset($rates[$rate->carrier])) {
                $rates[$rate->carrier] = [
                    'carrier_title' => $rate->carrier_title,
                    'rates'         => []
                ];
            }
            $rate->applyDiscount($cart);

            $rates[$rate->carrier]['rates'][] = $rate;
        }

        return $rates;
    }

    /**
     * Returns active shipping methods.
     *
     * @return array
     */
    public function getShippingMethods()
    {
        $methods = [];

        foreach (Config::get('carriers') as $shippingMethod) {
            $object = new $shippingMethod['class'];

            if ($object instanceof TableRate) {
                $sets = $object->getSuperSets(['code', 'name']);
                foreach ($sets as $set) {
                    $methods[] = [
                        'code'         => $object->getCode(),
                        'method'       => $object->getCode() . "_" . $set->code,
                        'method_title' => $set->name,
                        'description'  => $object->getDescription()
                    ];
                }
            } else {
                if (! $object->isAvailable()) {
                    continue;
                }

                $methods[] = [
                    'code'         => $object->getCode(),
                    'method'       => $object->getMethod(),
                    'method_title' => $object->getTitle(),
                    'description'  => $object->getDescription()
                ];
            }
        }

        return $methods;
    }

    /**
     * Is method exist in active shipping methods.
     *
     * @param  string  $shippingMethodCode
     * @return boolean
     */
    public function isMethodCodeExists($shippingMethodCode)
    {
        $activeShippingMethods = collect($this->getShippingMethods());
//        dd($shippingMethodCode, $activeShippingMethods);

        return $activeShippingMethods->contains('method', $shippingMethodCode);
    }
}
