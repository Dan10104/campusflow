<div class="nav">
    <div class="nav__header">
        <img class="nav__header__image text-light" src="{{asset('assets/images/logo.jpg')}}" alt="mylogo.png">
        <span>{{ Auth::user()->nombre . ' ' . Auth::user()->app }}</span>
    </div>

    <div class="nav__body">
        <div class="nav__body__items">
            <a
                href="{{route('dashboard')}}"
                wire:navigate
                class="{{ Str::startsWith(request()->path(), 'home') ? 'nav__body__items__active' : '' }}">
                <i class="bi bi-house-door-fill"></i>
                <span>Home</span>
            </a>
            @foreach (Auth::user()->role->permisos()->get() as $permiso)
                <a
                    href="{{route($permiso->ruta)}}"
                    wire:navigate
                    class="{{ Str::startsWith(request()->path(), $permiso->ruta) ? 'nav__body__items__active' : '' }}">
                    <i class="{{ $permiso->icono }}"></i>
                    <span>{{ $permiso->nombre }}</span>
                </a>
            @endforeach
        </div>
    </div>

    <div class="nav__footer">
        {{-- <a
            class="dropdown-item" href="{{ route('logout') }}"
            onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
            <i class="bi bi-power"></i>
        </a>

        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
            @csrf
        </form> --}}
    </div>
</div>