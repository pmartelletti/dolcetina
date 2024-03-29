<?php

namespace Webkul\Product\Type;

use Illuminate\Support\Collection;
use Webkul\Attribute\Repositories\AttributeRepository;
use Webkul\Product\Models\Product;
use Webkul\Product\Models\ProductFlat;
use Webkul\Product\Models\ProductGroupedProduct;
use Webkul\Product\Repositories\ProductAttributeValueRepository;
use Webkul\Product\Repositories\ProductFlatRepository;
use Webkul\Product\Repositories\ProductGroupedProductRepository;
use Webkul\Product\Repositories\ProductImageRepository;
use Webkul\Product\Repositories\ProductInventoryRepository;
use Webkul\Product\Repositories\ProductRepository;
use Webkul\Product\Repositories\ProductVideoRepository;

class Grouped extends AbstractType
{
    /**
     * Skip attribute for downloadable product type.
     *
     * @var array
     */
//    protected $skipAttributes = ['price', 'cost', 'special_price', 'special_price_from', 'special_price_to', 'length', 'width', 'height', 'weight', 'depth'];
    protected $skipAttributes = ['color', 'size', 'brand', 'cost', 'special_price', 'special_price_from', 'special_price_to', 'length', 'width', 'height', 'weight', 'depth'];

    /**
     * These blade files will be included in product edit page.
     *
     * @var array
     */
    protected $additionalViews = [
        'admin::catalog.products.accordians.images',
        'admin::catalog.products.accordians.videos',
        'admin::catalog.products.accordians.categories',
        'admin::catalog.products.accordians.grouped-products',
        'admin::catalog.products.accordians.channels',
        'admin::catalog.products.accordians.product-links',
    ];

    /**
     * Is a composite product type.
     *
     * @var boolean
     */
    protected $isComposite = true;

    protected $showQuantityBox = true;

    /**
     * Create a new product type instance.
     *
     * @param  \Webkul\Attribute\Repositories\AttributeRepository  $attributeRepository
     * @param  \Webkul\Product\Repositories\ProductRepository  $productRepository
     * @param  \Webkul\Product\Repositories\ProductAttributeValueRepository  $attributeValueRepository
     * @param  \Webkul\Product\Repositories\ProductInventoryRepository  $productInventoryRepository
     * @param  \Webkul\Product\Repositories\ProductImageRepository  $productImageRepository
     * @param  \Webkul\Product\Repositories\ProductGroupedProductRepository  $productGroupedProductRepository
     * @param  \Webkul\Product\Repositories\ProductVideoRepository  $productVideoRepository
     * @return void
     */
    public function __construct(
        AttributeRepository $attributeRepository,
        ProductRepository $productRepository,
        ProductAttributeValueRepository $attributeValueRepository,
        ProductInventoryRepository $productInventoryRepository,
        ProductImageRepository $productImageRepository,
        ProductVideoRepository $productVideoRepository,
        protected ProductGroupedProductRepository $productGroupedProductRepository
    )
    {
        parent::__construct(
            $attributeRepository,
            $productRepository,
            $attributeValueRepository,
            $productInventoryRepository,
            $productImageRepository,
            $productVideoRepository
        );
    }

    /**
     * Update.
     *
     * @param  array  $data
     * @param  int  $id
     * @param  string  $attribute
     * @return \Webkul\Product\Contracts\Product
     */
    public function update(array $data, $id, $attribute = 'id')
    {
        $product = parent::update($data, $id, $attribute);

        $route = request()->route() ? request()->route()->getName() : '';

        if ($route != 'admin.catalog.products.massupdate') {
            $this->productGroupedProductRepository->saveGroupedProducts($data, $product);
        }

        return $product;
    }

    /**
     * Returns children ids.
     *
     * @return array
     */
    public function getChildrenIds()
    {
        return array_unique($this->product->grouped_products()->pluck('associated_product_id')->toArray());
    }

    /**
     * Check if catalog rule can be applied.
     *
     * @return bool
     */
    public function priceRuleCanBeApplied()
    {
        return false;
    }

    /**
     * Get product minimal price.
     *
     * @param  int  $qty
     * @return float
     */
    public function getMinimalPrice($qty = null)
    {
        $groupPrice = $this->product->price;
        // we check if the sum of the products is less than the groupPrice, just in case one item is reduced

        $minPrices = [];

        foreach ($this->product->grouped_products as $groupOptionProduct) {
            $groupOptionProductTypeInstance = $groupOptionProduct->associated_product->getTypeInstance();

            $groupOptionProductMinimalPrice = $groupOptionProductTypeInstance->getMinimalPrice();

            $minPrices[] = $groupOptionProductTypeInstance->evaluatePrice($groupOptionProductMinimalPrice) * $groupOptionProduct->qty;
        }
        $sumPrices = array_sum($minPrices);

        return min($groupPrice, $sumPrices);
    }

    /**
     * Is saleable.
     *
     * @return bool
     */
    public function isSaleable()
    {
        if (! $this->product->status) {
            return false;
        }

        if (ProductFlat::query()->select('id')->whereIn('product_id', $this->getChildrenIds())->where('status', 0)->first()) {
            return false;
        }

        return true;
    }

    /**
     * Check whether group product have special price.
     *
     * @param  int  $qty
     * @return bool
     */
    public function haveSpecialPrice($qty = null)
    {
        return false;
        $haveSpecialPrice = false;

        foreach ($this->product->grouped_products as $groupOptionProduct) {
            if ($groupOptionProduct->associated_product->getTypeInstance()->haveSpecialPrice()) {
                $haveSpecialPrice = true;

                break;
            }
        }

        return $haveSpecialPrice;
    }

    /**
     * Get product minimal price.
     *
     * @return string
     */
    public function getPriceHtml()
    {
        $html = '';

        if ($this->haveSpecialPrice()) {
            $html .= '<div class="sticker sale">' . trans('shop::app.products.sale') . '</div>';
        }

        $html .= '<span class="final-price">' . core()->currency($this->getMinimalPrice()) . '</span>';

        return $html;
    }

    /**
     * Add product. Returns error message if can't prepare product.
     *
     * @param  array  $data
     * @return array
     */
    public function prepareForCart($data)
    {
        $data['quantity'] = (int) $data['quantity'] ?? 1;

        $data = $this->getQtyRequest($data);

        /** @var Collection $products */
        $products = $this->productGroupedProductRepository->findWhere(['product_id' => $this->product->id]);

        // check if all the products are in stock
        /** @var ProductGroupedProduct $product */
        foreach ($products as $product) {
            if (! $product->associated_product->haveSufficientQuantity($data['quantity'] * $product['qty'])) {
                return trans('shop::app.checkout.cart.quantity.inventory_warning');
            }
        }

        // this is the sum of the weight of all ordered products
        // eg, 6 chocolinas + 1 ddl
        $productsWeight = $products->reduce(fn($sum, $product) => ($product->associated_product->weight * $product->qty ?? 0) + $sum, 0);

        $price = $this->getFinalPrice();

        $name = app(ProductFlatRepository::class)->findOneWhere(['sku' => $this->product->sku], ['name'])
            ?->name;

        return [
            [
                'product_id'        => $this->product->id,
                'sku'               => $this->product->sku,
                'quantity'          => $data['quantity'],
                'name'              => $name,
                'price'             => $convertedPrice = core()->convertPrice($price),
                'base_price'        => $price,
                'total'             => $convertedPrice * $data['quantity'],
                'base_total'        => $price * $data['quantity'],
                'weight'            => $productsWeight,
                'total_weight'      => $productsWeight * $data['quantity'],
                'base_total_weight' => $productsWeight * $data['quantity'],
                'type'              => $this->product->type,
                'additional'        => $this->getAdditionalOptions($data),
            ],
        ];
    }
}
