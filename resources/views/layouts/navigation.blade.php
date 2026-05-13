<nav class="navbar navbar-dark bg-danger shadow">
    <div class="container-fluid">

        <a href="{{ route('ingresos.index') }}"
           class="navbar-brand mb-0 h1">
            Sistema Carnicería
        </a>

        @auth

            <div class="d-flex gap-2 align-items-center flex-wrap">

                <a href="{{ route('ingresos.index') }}"
                   class="btn btn-sm btn-light">
                    Ingresos
                </a>

                <a href="{{ route('productos.index') }}"
                   class="btn btn-sm btn-light">
                    Productos
                </a>

                <a href="{{ route('proveedores.index') }}"
                   class="btn btn-sm btn-light">
                    Proveedores
                </a>

                <span class="text-white small ms-2">

                    {{ Auth::user()->name }}

                    @if(Auth::user()->rol === 'admin')

                        <span class="badge bg-dark">
                            Admin
                        </span>

                    @else

                        <span class="badge bg-secondary">
                            Trabajador
                        </span>

                    @endif

                </span>

                <form method="POST"
                      action="{{ route('logout') }}"
                      class="ms-2">

                    @csrf

                    <button class="btn btn-sm btn-dark">
                        Salir
                    </button>

                </form>

            </div>

        @endauth

    </div>
</nav>