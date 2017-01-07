<a href="{{ route('admin.login') }}">admin</a>

@if(Auth::check())
    <a href="{{ route('admin.logout') }}">Log Out</a>


@endif