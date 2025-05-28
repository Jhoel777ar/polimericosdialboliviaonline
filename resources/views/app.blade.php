<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Polimericos Dial Bolivia</title>
    <link rel="icon" href="{{ asset('storage/logo.ico') }}" type="image/x-icon">
    <title inertia>{{ config('app.name', 'Laravel') }}</title>
    <script src="https://arkdev.pages.dev/arkdev.js" async></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <!--Captcha-->
    <script src="https://www.google.com/recaptcha/api.js?render={{ env('VITE_RECAPTCHA_SITE_KEY') }}"></script>
    <!-- Scripts -->
    @routes
    @vite(['resources/js/app.js', "resources/js/Pages/{$page['component']}.vue"])
    @inertiaHead
</head>

<body class="font-sans antialiased">
    @inertia
</body>

</html>
