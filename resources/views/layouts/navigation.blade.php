<nav class="navbar">
    <div class="container">
        <ul class="nav navbar-nav">
            <li>
                <div class="navbar-header">
                    <a href="javascript:void(0);" class="h-bars"></a>
                    <a class="navbar-brand" href="/"><img src="assets/images/logo.png" width="50"
                            alt="San Miguel"><span class="m-l-10">San Miguel</span></a>
                </div>
            </li>

            @auth
                <li class="float-right dropdown"><a href="javascript:void(0);" class="dropdown-toggle"
                        data-toggle="dropdown" ><i class="zmdi zmdi-account"></i> {{auth()->user()->name}} <i class="zmdi zmdi-chevron-down"></i></a>
                    <ul class="dropdown-menu pullDown">
                        <li><a href="{{ route('profile.edit') }}"><i
                                    class="zmdi zmdi-email m-r-10"></i><span>Perfil</span></a></li>
                        <li><a href="javascript:void(0);"><span>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit"
                                            style="background: none;
                                                    color: inherit;
                                                    border: none;
                                                    padding: 0;
                                                    font: inherit;
                                                    cursor: pointer;
                                                    outline: inherit;">
                                            <i class="zmdi zmdi-power m-r-10"></i> Cerrar Sesión
                                        </button>
                                    </form>
                                </span></a></li>
                    </ul>
                </li>
            @else
                <li class="float-right">
                    <a href="/login" class="js-right-sidebar"><i class="zmdi zmdi-settings zmdi-hc-spin"></i> Ingresar</a>
                </li>
            @endauth
        </ul>
    </div>
</nav>
