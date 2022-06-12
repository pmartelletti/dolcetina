<?php

namespace Webkul\TableRate\Helpers;

use Webkul\Checkout\Models\CartItem;
use Webkul\TableRate\Repositories\SuperSetRateRepository;
use Webkul\TableRate\Repositories\ShippingRateRepository;
use Webkul\TableRate\Repositories\SuperSetRepository;
use Webkul\Product\Repositories\ProductRepository;
use Webkul\Checkout\Facades\Cart;

class ShippingHelper
{
    /**
     * Contains route related configuration
     *
     * @var array
     */
    protected $_config;

    /**
     * SuperSetRateRepository Object
     *
     * @var array
    */
    protected $superSetRateRepository;

    /**
     * ShippingRateRepository Object
     *
     * @var array
    */
    protected $shippingRateRepository;

    /**
     * SupersetRepository Object
     *
     * @var array
    */
    protected $supersetRepository;

    /**
     * ProductRepository Object
     *
     * @var array
    */
    protected $productRepository;

    /**
     * Create a new controller instance.
     *
     * @param  Webkul\TableRate\Repositories\SuperSetRateRepository $superSetRateRepository
     * @param  Webkul\TableRate\Repositories\ShippingRateRepository $shippingRateRepository
     * @param  Webkul\TableRate\Repositories\SupersetRepository $supersetRepository
     * @param  Webkul\Product\Repositories\ProductRepository $productRepository
     * @return void
     */
    public function __construct(
        SuperSetRateRepository $superSetRateRepository,
        ShippingRateRepository $shippingRateRepository,
        SupersetRepository $supersetRepository,
        ProductRepository $productRepository
    )   {
        $this->_config = request('_config');

        $this->superSetRateRepository = $superSetRateRepository;

        $this->shippingRateRepository = $shippingRateRepository;

        $this->supersetRepository = $supersetRepository;

        $this->productRepository = $productRepository;
    }

    /**
     * Find Appropriate TableRate Methods
     *
     * @return $shippingData
     */
    public function findAppropriateTableRateMethods()
    {
        $shippingMethods = [];

        $totalWeight = Cart::getCart()->items->sum(fn(CartItem $item) => $item->total_weight);

        //Get CartProduct wise available rates
        foreach (Cart::getCart()->items as $item) {
            $shippingRates = $this->getAvailableTableRates($item);
            if ( count($shippingRates) > 0 ) {
                $shippingMethods[$item->product_id] = $shippingRates;
            }
        }

        return $shippingMethods;
    }

    /**
     * Get All The Available ShippingRates
     *
     * @param $cartItem
     * @return $available_rates
     */
    public function getAvailableTableRates($cartItem)
    {
        $available_rates    = [];
        $supesetRates       = $this->superSetRateRepository->getModel()
            ->leftJoin('tablerate_supersets', 'tablerate_superset_rates.tablerate_superset_id', 'tablerate_supersets.id')
            ->addSelect('tablerate_superset_rates.*')
            ->addSelect(
                'tablerate_supersets.name',
                'tablerate_supersets.code'
                )
            ->where('tablerate_supersets.status', 1)
            ->where('tablerate_superset_rates.price_from', '<=', core()->convertToBasePrice($cartItem['price']))
            ->where('tablerate_superset_rates.price_to', '>=', core()->convertToBasePrice($cartItem['price']))
            ->orderBy('tablerate_superset_rates.created_at')
            ->get();

        if ( count($supesetRates) > 0 ) {
            foreach ($supesetRates as $supesetRate) {
                $available_rates[$supesetRate->code] = $this->getFormattedArray($cartItem, $supesetRate);
            }
        } else {
            $available_rates = $this->getShippingRates($cartItem);
        }

        return $available_rates;
    }

    /**
     * Get NeededData in ArrayFormat
     *
     * @param $cartItem, $superSetRate
     * @return $shipping_rate
     */
    public function getFormattedArray($cartItem, $superSetRate)
    {
        $product = $this->productRepository->find($cartItem['product_id']);
        if (! $product->getTypeInstance()->isStockable() && core()->getConfigData('sales.carriers.tablerate.type') == 'per_unit' ) {
            $superSetRate->price = 0;
        }

        $shipping_rate = [
            'price'         => $cartItem['price'],
            'base_price'    => $cartItem['base_price'],
            'weight'        => $cartItem['weight'],
            'shipping_cost' => $superSetRate->price,
            'superset_name' => $superSetRate->name,
            'superset_code' => $superSetRate->code,
            'quantity'      => $cartItem['quantity']
        ];

        return $shipping_rate;
    }

    /**
     * Get ShippingRate
     *
     * @param $cartItem
     * @return $shipping_rates
     */
    public function getShippingRates($cartItem)
    {
        $shipping_rates     = [];
        $cart               = Cart::getCart();
        $shippingAddress    = $cart->shipping_address;

        $shippingRates = $this->shippingRateRepository->getModel()
            ->addSelect('tablerate_shipping_rates.*')
            ->addSelect('tablerate_supersets.name', 'tablerate_supersets.code')
            ->leftJoin('tablerate_supersets', 'tablerate_shipping_rates.tablerate_superset_id', 'tablerate_supersets.id')
            ->where('tablerate_supersets.status', 1)
            ->orderBy('tablerate_shipping_rates.created_at')
            ->get();

        if ( count($shippingRates) > 0 ) {
            foreach ($shippingRates as $shippingRate) {
                //Numeric Range
                if ($shippingRate->is_zip_range == 0) {
                    if ( $shippingRate->zip_from <= $shippingAddress->postcode
                    && $shippingRate->zip_to >= $shippingAddress->postcode ) {

                        if ($shippingRate->weight_from <= $cartItem['weight']
                        && $shippingRate->weight_to >= $cartItem['weight']) {
                            $shipping_rates[$shippingRate->code] = $this->getFormattedArray($cartItem, $shippingRate);
                        }
                    }
                } else {
                    //Alphanumeric Zip
                    if ( $shippingRate->zip_code == '*'
                    || $shippingRate->zip_code == $shippingAddress->postcode) {
                        if ($shippingRate->weight_from <= $cartItem['weight']
                        && $shippingRate->weight_to >= $cartItem['weight']) {
                            $shipping_rates[$shippingRate->code] = $this->getFormattedArray($cartItem, $shippingRate);
                        }
                    }
                }
            }
        }

        return $shipping_rates;
    }

    public function getWeightShippingRates($weight) : array
    {
        $cart               = Cart::getCart();
        $shippingAddress    = $cart->shipping_address;

        $shippingRates = $this->shippingRateRepository->getModel()
            ->addSelect('tablerate_shipping_rates.*')
            ->addSelect('tablerate_supersets.name', 'tablerate_supersets.code')
            ->leftJoin('tablerate_supersets', 'tablerate_shipping_rates.tablerate_superset_id', 'tablerate_supersets.id')
            ->where('tablerate_supersets.status', 1)
            ->orderBy('tablerate_shipping_rates.created_at')
            ->get();


        if ( count($shippingRates) > 0 ) {
            foreach ($shippingRates as $shippingRate) {
                //Numeric Range
                if ($shippingRate->is_zip_range == 0) {
                    if ( $shippingRate->zip_from <= $shippingAddress->postcode
                        && $shippingRate->zip_to >= $shippingAddress->postcode ) {

                        if ($shippingRate->weight_from <= $weight
                            && $shippingRate->weight_to >= $weight) {
                            return [
                                'superset_name' => $shippingRate->name,
                                'superset_code' => $shippingRate->code,
                                'price' => $shippingRate->price
                            ];
                        }
                    }
                } else {
                    //Alphanumeric Zip
                    if ( $shippingRate->zip_code == '*'
                        || $shippingRate->zip_code == $shippingAddress->postcode) {
                        if ($shippingRate->weight_from <= $weight
                            && $shippingRate->weight_to >= $weight) {
                            return [
                                'superset_name' => $shippingRate->name,
                                'superset_code' => $shippingRate->code,
                                'price' => $shippingRate->price
                            ];
                        }
                    }
                }
            }
        }
        return [];
    }


    /**
     * Comman ShippingRates
     *
     * @param $shippingMethods, $itemCount
     * @return $shippingMethodRates
     */
    public function getCommanRates($shippingMethods, $itemCount)
    {
        $shippingMethodRates    = [];
        $codeWiseMethods        = [];

        if ( !empty($shippingMethods) ) {
            foreach ($shippingMethods as $productId => $shipping_rates) {
                if ( !empty($shipping_rates) ) {
                    foreach($shipping_rates as $superset_code => $rate_detail) {
                        $codeWiseMethods[$superset_code][] = $rate_detail;
                    }
                } else {
                    return $shippingMethods = [];
                }
            }

            foreach ($codeWiseMethods as $superset_code => $commonMethods) {
                if ( count($commonMethods) == $itemCount ) {
                    $shippingMethodRates[$superset_code] = $commonMethods;
                }
            }

            return $shippingMethodRates;
        }

        return $shippingMethodRates;
    }
}