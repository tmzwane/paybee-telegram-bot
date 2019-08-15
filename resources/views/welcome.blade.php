<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Paybee</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet">

        <!-- Styles -->
        <style>
            html, body {
                background-color: #fff;
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
                font-size: 84px;
            }

            .links > a {
                color: #636b6f;
                padding: 0 25px;
                font-size: 13px;
                font-weight: 600;
                letter-spacing: .1rem;
                text-decoration: none;
                text-transform: uppercase;
            }

            .m-b-md {
                margin-bottom: 30px;
            }
        </style>
    </head>
    <body>
        <div class="flex-center position-ref full-height">
            

            <div class="content">
                <img src="{{ asset('paybee.png') }}" width="250" height="250" />
                <div class="title m-b-md">
                    PayBee Telegram Bot
                </div>

                <p>Bitcoin Exchange Info at Your fingertips</p><br>

                @if (Route::has('login'))
                    <div class="links">
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
                <div class="links">
                    <a href="https://github.com/tmzwane/paybee-tel-bot">Project on GitHub</a>
                    <a href="https://t.me/paybeetelbot">Telegram @paybeetelbot</a>
                    <a href="https://tmzwane.com">TMZwane.com</a>
                    <a href="https://twitter.com/tm_zwane">Tweet @tm_zwane</a>
                    <a href="https://t.me/tmzwane">Telegram @tmzwane</a>
                    <a href="https://instagram.com/tmzwane">Instagram @tmzwane</a>
                </div>
            </div>
        </div>
    </body>
</html>
