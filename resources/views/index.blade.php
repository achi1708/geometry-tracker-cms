<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Geometry</title>

        <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.1/css/all.css" integrity="sha384-gfdkjb5BdAXd+lj+gudLWI+BXq4IuLW5IT+brZEZsLFm++aCMlF1V92rMkPaX4PP" crossorigin="anonymous">

        <link href="{{ asset('css/app.css') }}" rel="stylesheet">
        
    </head>
    <body>
        <script>
            window.fbAsyncInit = function() {
                FB.init({
                appId            : '{{env("FB_APP_ID")}}',
                autoLogAppEvents : true,
                xfbml            : true,
                version          : 'v10.0'
                });
            };
        </script>
        <script async defer crossorigin="anonymous" src="https://connect.facebook.net/en_US/sdk.js"></script>
        <div id="index"></div>

        <script src="{{ asset('js/app.js') }}"></script>
    </body>
</html>