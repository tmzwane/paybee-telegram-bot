<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Paybee</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet">

        <!-- Styles -->
        <link href="{{ asset('css/app.css') }}" rel="stylesheet"/>
        <style>
            html, body {
                background-color: #e9ecef;
                color: #636b6f;
                font-family: 'Nunito', sans-serif;
                font-weight: 200;
                height: 100vh;
                margin: 0;
            }

            .full-height {
                height: 100vh;
            }

            .flex-center {
                align-items: center;
                display: flex;
                justify-content: center;
            }

            .position-ref {
                position: relative;
            }

            .top-right {
                position: absolute;
                right: 10px;
                top: 18px;
            }

            .content {
                text-align: center;
            }

            .title {
                font-size: 60px;
            }

            .links > a {
                color: #636b6f;
                padding: 0 25px;
                font-size: 13px;
                font-weight: 600;
                letter-spacing: .1rem;
                text-transform: uppercase;
            }

            @media screen and (max-width: 768px) {
                .content-hide-mobile {display: none;}
            }

            .m-b-md {
                margin-bottom: 30px;
            }
        </style>
    </head>
    <body class="jumbotron text-center">
        <div class="container">
            <br><img src="{{ asset('paybee.png') }}" width="250" height="250" />
            <div class="title m-b-md">
                PayBee Telegram Bot
            </div>

            <p>Bitcoin Exchange Info at Your fingertips</p><br>

            @if (Route::has('login'))
                <div class="top-center links">
                    @auth
                        <a href="{{ url('/bot-config') }}">Configure Bot Settings</a>
                    @else
                        <a href="{{ route('login') }}">Login</a>

                        @if (Route::has('register'))
                            <a href="{{ route('register') }}">Register</a>
                        @endif
                    @endauth
                </div>
            @endif
            <br><br>
            <div class="links content-hide-mobile">
                <a href="https://github.com/tmzwane/paybee-telegram-bot">Project on GitHub</a>
                <a href="https://t.me/paybeetelbot">Telegram @paybeetelbot</a>
                <a href="https://tmzwane.com">TMZwane.com</a>
                <a href="https://twitter.com/tm_zwane">Tweet @tm_zwane</a>
            </div>
        </div>
    </body>
</html>
