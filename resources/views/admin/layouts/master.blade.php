<!doctype html>
<html lang="en">
@include('admin.layouts.head')
<body>
    <div class="container-scroller">
        @if (Auth::guard('admin')->check())
            @include('admin.layouts.sidebar')
            <div class="container-fluid page-body-wrapper">
                @include('admin.layouts.header')
                <div class="main-panel">
                    @yield('content')   
                    @include('admin.layouts.footer')
                </div>
            </div>
        @else
            @yield('content')    
        @endif 
    </div>
    @include('admin.layouts.script')
    @yield('scripts')
</body>
</html>
