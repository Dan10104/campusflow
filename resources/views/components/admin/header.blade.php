<header class="app__header">
    <div class="app__header__left">
        @livewire("admin.funcionalidad.search")
    </div>
    <div class="app__header__right">
        @livewire("config.theme")
        @livewire("config.light-dark-theme")
        
        <form action="{{ route('logout') }}" method="post">
            @csrf
            <button
                data-bs-toggle="tooltip"
                data-bs-placement="left"
                title="Salir"
                type="submit"
                class="btn btn-outline-secondary me-2 d-flex justify-content-center align-items-center"
                style="width: 30px; height: 30px;">
                <i class="bi bi-box-arrow-in-left fs-4" style="position: relative; top: 2px; left: 2px;"></i>
            </button>
        </form>
    </div>
</header>