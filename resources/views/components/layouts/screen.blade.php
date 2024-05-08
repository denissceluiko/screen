<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="refresh" content="86400">

        <title>{{ $title ?? 'Page Title' }}</title>
        <style>
            * {
                margin: 0;
                padding: 0;
            }

            body {
                overflow: hidden;
            }
        </style>
    </head>
    <body>
        {{ $slot }}
    </body>
</html>
