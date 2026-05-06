{{--<!DOCTYPE html>--}}
{{--<html lang="en" dir="ltr">--}}

{{--<head>--}}
{{--    <meta charset="UTF-8"/>--}}
{{--    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>--}}
{{--    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no"/>--}}

{{--    <title>E-Facilitation Center AJK</title>--}}

{{--    <link rel="icon" href="/favicon.ico">--}}
{{--    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">--}}
{{--    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">--}}

{{--    <link rel="preconnect" href="https://fonts.googleapis.com"/>--}}
{{--    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin/>--}}
{{--    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css"--}}
{{--          integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw=="--}}
{{--          crossorigin="anonymous" referrerpolicy="no-referrer"/>--}}
{{--    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;500;600;700;800&display=swap"--}}
{{--          rel="stylesheet"/>--}}

{{--    <link rel="manifest" href="/manifest.json">--}}

{{--    <!-- DigitalPersona SDK -->--}}
{{--    <script src="/biometrics/scripts/es6-shim.js"></script>--}}
{{--    <script src="/biometrics/scripts/websdk.client.bundle.min.js"></script>--}}
{{--    <script src="/biometrics/scripts/fingerprint.sdk.min.js"></script>--}}
{{--</head>--}}

{{--<body>--}}
{{--    <noscript>--}}
{{--        <strong>We're sorry but this app doesn't work properly without JavaScript enabled. Please enable it to continue.</strong>--}}
{{--    </noscript>--}}
{{--    <div id="app"></div>--}}
{{--    @vite('resources/js/main.js')--}}
{{--</body>--}}

{{--</html>--}}

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">


    <!-- PWA meta tags -->
    <link rel="manifest" href="{{ asset('build/manifest.webmanifest') }}">
    <meta name="theme-color" content="#ffffff">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com"/>
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css"
          integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw=="
          crossorigin="anonymous" referrerpolicy="no-referrer"/>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;500;600;700;800&display=swap"
          rel="stylesheet"/>

    <!-- DigitalPersona SDK -->
    <script src="/biometrics/scripts/es6-shim.js"></script>
    <script src="/biometrics/scripts/websdk.client.bundle.min.js"></script>
    <script src="/biometrics/scripts/fingerprint.sdk.min.js"></script>

    <!-- Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/main.ts'])
</head>
<body>
<div id="app"></div>

<!-- PWA Service Worker Registration -->
@if(config('app.env') === 'production')
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('{{ asset('build/sw.js') }}')
                    .then(registration => {
                        console.log('SW registered: ', registration);
                    })
                    .catch(registrationError => {
                        console.log('SW registration failed: ', registrationError);
                    });
            });
        }
    </script>
@endif
</body>
</html>
