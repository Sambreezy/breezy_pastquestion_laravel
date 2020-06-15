<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{env('APP_NAME')}}</title>

        {{-- Styles --}}
        <link href="{{ asset('css/material-kit.css') }}" rel="stylesheet">
        <link href="{{ asset('css/material-kit.min.css?v=2.0.5') }}" rel="stylesheet">
        <link href="{{ asset('css/custom-kit.css') }}" rel="stylesheet">
        <link href="{{ asset('css/app.css') }}" rel="stylesheet">

        <!--  Fonts and icons  -->
        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700|Roboto+Slab:400,700|Material+Icons"/>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/latest/css/font-awesome.min.css" />
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.1.1/css/all.css" integrity="sha384-O8whS3fhG2OnA5Kas0Y9l3cfpmYjapjI0E4theH4iuMD+pLhbf6JI0jIMfYcK3yZ" crossorigin="anonymous" />
    </head>
    <body>
        <div id="root"></div>

        {{-- Script --}}
        <script src="https://widget.cloudinary.com/v2.0/global/all.js" type="text/javascript"></script>
        <script src="{{asset('js/core/jquery.min.js')}}" type="text/javascript"></script>
        <script src="{{asset('js/core/popper.min.js')}}" type="text/javascript"></script>
        <script src="{{asset('js/plugins/moment.min.js')}}" type="text/javascript"></script>
        <script src="{{asset('js/core/bootstrap-material-design.min.js')}}" type="text/javascript"></script>

        <!--  Plugin for the Datepicker, full documentation here: https://github.com/Eonasdan/bootstrap-datetimepicker -->
        <script src="{{asset('js/plugins/bootstrap-datetimepicker.js')}}" type="text/javascript"> </script>

        <!--  Plugin for the Sliders, full documentation here: http://refreshless.com/nouislider/ -->
        <script src="{{asset('js/plugins/nouislider.min.js')}}" type="text/javascript"> </script>

        <!--  Google Maps Plugin  -->
        {{-- <script src="https://maps.googleapis.com/maps/api/js?key=YOUR_KEY_HERE"></script> --}}

        <!-- Control Center for Material Kit: parallax effects, scripts for the example pages etc -->
        {{-- <script src="{{asset('js/material-kit.js?v=2.0.5')}}" type="text/javascript"></script> --}}
        {{-- <script src="{{asset('js/material-kit.min.js')}}" type="text/javascript"></script> --}}

        <script>
            // var nav = document.getElementsByClassName('navbar'); // Identify target
            setTimeout(function() {
                if (window.screen.width > 600) {
                document.getElementById('openNav').classList.remove('collapse');
                }
                window.scrollTo(0, 0);
            }, 500);
        
            window.addEventListener('scroll', function(event) {
                // To listen for event
                event.preventDefault();
                if (window.scrollY < 55) {
                document
                    .getElementById('myNavbar')
                    .classList.add('navbar-transparent');
                document.getElementById('myNavbar').classList.remove('breezy');
                // Just an example
                } else {
                document
                    .getElementById('myNavbar')
                    .classList.remove('navbar-transparent');
                document.getElementById('myNavbar').classList.add('breezy');
                }
            });
        </script>

        <script src="{{ asset('js/app.js') }}"></script>

    </body>
</html>