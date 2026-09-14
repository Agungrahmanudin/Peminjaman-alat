<!DOCTYPE html>
<html lang="en" dir="ltr">
@include('layouts.head')
<body class="nk-body bg-lighter npc-default has-sidebar no-touch nk-nio-theme">
     
    @include('partials.sidebar')

     @include('partials.navbar')
   
    @include('partials.flash')

    @yield('content')
    
    @include('layouts.footer')
    @stack('scripts')
</body>
</html>