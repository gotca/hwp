<html>
<head>
    <title>@yield('title')Hudsonville Water Polo</title>

    <base href="@route('home')" />

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="apple-touch-icon" sizes="180x180" href="/icons/apple-touch-icon.png?v=69k3Ao4nqq">
    <link rel="icon" type="image/png" href="/icons/favicon-32x32.png?v=69k3Ao4nqq" sizes="32x32">
    <link rel="icon" type="image/png" href="/icons/favicon-16x16.png?v=69k3Ao4nqq" sizes="16x16">
    <link rel="manifest" href="/icons/manifest.json?v=69k3Ao4nqq">
    <link rel="mask-icon" href="/icons/safari-pinned-tab.svg?v=69k3Ao4nqq" color="#32345d">
    <link rel="shortcut icon" href="/icons/favicon.ico?v=69k3Ao4nqq">
    <meta name="msapplication-config" content="/icons/browserconfig.xml?v=69k3Ao4nqq">
    <meta name="theme-color" content="#ffffff">

    <?php if (getenv('APP_ENV') == 'local'): ?>
    <link rel="stylesheet" href="{{ asset('css/main.css') }}">
    <?php else: ?>
    <link rel="stylesheet" href="{{ elixir('css/main.css') }}">
    <?php endif ?>

    <link rel="stylesheet" href="/css/scratch.css" />

    <script src="js/modernizr.custom.js"></script>
</head>
<body>


<canvas id="c"></canvas>
<p>hi</p>
<img id="o" />


<script src="js/playerlist/{{ app('App\Models\ActiveSite')->domain }}.js"></script>
<script src="{{ asset('js/components.js') }}"></script>
{{--<script src="{{ elixir('js/main.js') }}"></script>--}}

<script src="{{ asset('js/shareables.js') }}"></script>

</body>
</html>