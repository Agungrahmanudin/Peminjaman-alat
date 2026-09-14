<!DOCTYPE html>
<html lang="en">

<head>
    @include('layouts.head')
</head>

<body class="nk-body bg-lighter npc-default has-sidebar no-touch nk-nio-theme">
    <div class="main-wrapper">
      @include('partials.navbar')
        @include('partials.sidebar')

        @include('partials.flash')

        <div class="page-wrapper">
            <div class="content container-fluid">
                @yield('content')
            </div>
        </div>
    </div>
   

    @include('layouts.footer')
    @stack('scripts')
</body>

</html>

