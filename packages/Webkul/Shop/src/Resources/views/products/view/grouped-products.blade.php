@if ($product->type == 'grouped')
    {!! view_render_event('bagisto.shop.products.view.grouped_products.before', ['product' => $product]) !!}

{{--    <div class="grouped-product-container">--}}
{{--        @if ($product->groupedProductsBySortOrder->count())--}}
{{--            <div class="grouped-product-list">--}}
{{--                <ul>--}}
{{--                    <li>--}}
{{--                        <span>{{ __('shop::app.products.name') }}</span>--}}
{{--                        <span>{{ __('shop::app.products.qty') }}</span>--}}
{{--                    </li>--}}
{{--                    @foreach ($product->groupedProductsBySortOrder as $groupedProduct)--}}
{{--                        @if($groupedProduct->associated_product->getTypeInstance()->isSaleable())--}}
{{--                            <li>--}}
{{--                                <span class="name">--}}
{{--                                    {{ $groupedProduct->associated_product->name }}--}}

{{--                                    @include ('shop::products.price', ['product' => $groupedProduct->associated_product])--}}
{{--                                </span>--}}

{{--                                <span class="qty">--}}
{{--                                    <quantity-changer--}}
{{--                                        :control-name="'qty[{{$groupedProduct->associated_product_id}}]'"--}}
{{--                                        :validations="'required|numeric|min_value:0'"--}}
{{--                                        quantity="{{ $groupedProduct->qty }}"--}}
{{--                                        min-quantity="0">--}}
{{--                                    </quantity-changer>--}}
{{--                                </span>--}}
{{--                            </li>--}}
{{--                        @endif--}}
{{--                    @endforeach--}}
{{--                </ul>--}}
{{--            </div>--}}
{{--        @endif--}}
{{--    </div>--}}

    {!! view_render_event('bagisto.shop.products.view.grouped_products.before', ['product' => $product]) !!}
@endif