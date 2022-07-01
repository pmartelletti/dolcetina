<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>

    <title>@yield('page_title')</title>

    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="base-url" content="{{ url()->to('/') }}">
    <meta http-equiv="content-language" content="{{ app()->getLocale() }}">
    <link rel="stylesheet" href="{{ asset('vendor/webkul/ui/assets/css/ui.css') }}">

    <link rel="stylesheet" href="{{ bagisto_asset('css/shop.css') }}">

    @if ($favicon = core()->getCurrentChannel()->favicon_url)
        <link rel="icon" sizes="16x16" href="{{ $favicon }}" />
    @else
        <link rel="icon" sizes="16x16" href="{{ bagisto_asset('images/favicon.ico') }}" />
    @endif

    @yield('head')

    @section('seo')
        @if (! request()->is('/'))
            <meta name="description" content="{{ core()->getCurrentChannel()->description }}"/>
        @endif
    @show

    @stack('css')

    {!! view_render_event('bagisto.shop.layout.head') !!}

    <style>
        {!! core()->getConfigData('general.content.custom_scripts.custom_css') !!}
    </style>

</head>


<body @if (core()->getCurrentLocale() && core()->getCurrentLocale()->direction == 'rtl') class="rtl" @endif style="scroll-behavior: smooth;">

    {!! view_render_event('bagisto.shop.layout.body.before') !!}

    <div id="app">
        <flash-wrapper ref='flashes'></flash-wrapper>

        <div class="main-container-wrapper">

            {!! view_render_event('bagisto.shop.layout.header.before') !!}

            @include('shop::layouts.header.index')

            {!! view_render_event('bagisto.shop.layout.header.after') !!}

            @yield('slider')

            <main class="content-container">

                {!! view_render_event('bagisto.shop.layout.content.before') !!}

                @yield('content-wrapper')

                {!! view_render_event('bagisto.shop.layout.content.after') !!}

            </main>

        </div>

        {!! view_render_event('bagisto.shop.layout.footer.before') !!}

        @include('shop::layouts.footer.footer')

        {!! view_render_event('bagisto.shop.layout.footer.after') !!}

        @if (core()->getConfigData('general.content.footer.footer_toggle'))
            <div class="footer">
                <p style="text-align: center;">
                    @if (core()->getConfigData('general.content.footer.footer_content'))
                        {{ core()->getConfigData('general.content.footer.footer_content') }}
                    @else
                        {!! trans('admin::app.footer.copy-right') !!}
                    @endif
                </p>
            </div>
        @endif

        <overlay-loader :is-open="show_loader"></overlay-loader>

{{--        <go-top bg-color="#0041ff"></go-top>--}}
    </div>

    <div style="position: relative; z-index: 100000000000">
        <a href="https://wa.link/dsu5fe" target="_blank">
            <div style="bottom: 25px; right: 25px; position: fixed; height: 60px; width: 60px; background-color: rgb(48, 191, 57); border-radius: 50%; display: flex; align-items: center;">
                <img style="height: 100%; width: 100%; " alt="" src="data:image/svg+xml;charset=utf-8,%0A%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%2250%22%20height%3D%2250%22%20viewBox%3D%220%200%2050%2050%22%3E%0A%20%20%3Cg%3E%0A%20%20%20%20%3Ccircle%20cx%3D%2225%22%20cy%3D%2225%22%20r%3D%2225%22%20style%3D%22fill%3A%20%2330bf39%22%2F%3E%0A%20%20%20%20%3Cpath%20d%3D%22M39.8%2C23.4A14.64%2C14.64%2C0%2C0%2C1%2C25.1%2C38%2C15.25%2C15.25%2C0%2C0%2C1%2C18%2C36.2L9.8%2C38.8%2C12.5%2C31a14.84%2C14.84%2C0%2C0%2C1-2.1-7.5%2C14.7%2C14.7%2C0%2C0%2C1%2C29.4-.1ZM25.1%2C11.2A12.38%2C12.38%2C0%2C0%2C0%2C12.7%2C23.5a12%2C12%2C0%2C0%2C0%2C2.4%2C7.2l-1.5%2C4.6%2C4.8-1.5A12.44%2C12.44%2C0%2C0%2C0%2C37.6%2C23.5%2C12.53%2C12.53%2C0%2C0%2C0%2C25.1%2C11.2Zm7.4%2C15.6a3.22%2C3.22%2C0%2C0%2C0-.7-.4l-2.5-1.2c-.3-.1-.6-.2-.8.2a8.54%2C8.54%2C0%2C0%2C1-1.1%2C1.4.59.59%2C0%2C0%2C1-.8.1%2C11%2C11%2C0%2C0%2C1-2.9-1.8%2C9.88%2C9.88%2C0%2C0%2C1-2-2.5.46.46%2C0%2C0%2C1%2C.2-.7%2C2.65%2C2.65%2C0%2C0%2C0%2C.5-.6c.2-.2.2-.4.4-.6a.64.64%2C0%2C0%2C0%2C0-.6c-.1-.2-.8-1.9-1.1-2.7s-.6-.6-.8-.6h-.7a1.85%2C1.85%2C0%2C0%2C0-1%2C.4%2C4.16%2C4.16%2C0%2C0%2C0-1.3%2C3%2C6.45%2C6.45%2C0%2C0%2C0%2C1.5%2C3.7c.2.2%2C2.5%2C4%2C6.2%2C5.4s3.7%2C1%2C4.3.9a3.74%2C3.74%2C0%2C0%2C0%2C2.4-1.7A2.82%2C2.82%2C0%2C0%2C0%2C32.5%2C26.8Z%22%20style%3D%22fill%3A%20%23fff%22%2F%3E%0A%20%20%3C%2Fg%3E%0A%3C%2Fsvg%3E%0A">
            </div>
        </a>
    </div>

    <script type="text/javascript">
        window.flashMessages = [];

        @if ($success = session('success'))
            window.flashMessages = [{'type': 'alert-success', 'message': "{{ $success }}" }];
        @elseif ($warning = session('warning'))
            window.flashMessages = [{'type': 'alert-warning', 'message': "{{ $warning }}" }];
        @elseif ($error = session('error'))
            window.flashMessages = [{'type': 'alert-error', 'message': "{{ $error }}" }];
        @elseif ($info = session('info'))
            window.flashMessages = [{'type': 'alert-info', 'message': "{{ $info }}" }];
        @endif

        window.serverErrors = [];

        @if (isset($errors))
            @if (count($errors))
                window.serverErrors = @json($errors->getMessages());
            @endif
        @endif
    </script>

    <script type="text/javascript" src="{{ bagisto_asset('js/shop.js') }}" ></script>
    <script type="text/javascript" src="{{ asset('vendor/webkul/ui/assets/js/ui.js') }}"></script>

    @stack('scripts')

    {!! view_render_event('bagisto.shop.layout.body.after') !!}

    <div class="modal-overlay"></div>

    <script>
        {!! core()->getConfigData('general.content.custom_scripts.custom_javascript') !!}
    </script>

    <!-- Meta Pixel Code -->
    <script>
        !function(f,b,e,v,n,t,s)
        {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
            n.callMethod.apply(n,arguments):n.queue.push(arguments)};
            if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
            n.queue=[];t=b.createElement(e);t.async=!0;
            t.src=v;s=b.getElementsByTagName(e)[0];
            s.parentNode.insertBefore(t,s)}(window, document,'script',
            'https://connect.facebook.net/en_US/fbevents.js');
        fbq('init', '578285553686263');
        fbq('track', 'PageView');
    </script>
    <noscript><img height="1" width="1" style="display:none"
                   src="https://www.facebook.com/tr?id=578285553686263&ev=PageView&noscript=1"
        /></noscript>
    <!-- End Meta Pixel Code -->

    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-Y7C6KZ3GRT"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());

        gtag('config', 'G-Y7C6KZ3GRT');
    </script>
    <!-- End Google Analytics -->

</body>

</html>