<nav class="bg-gray-800 shadow-lg" x-data="{ isOpen: false, adminOpen: false, userOpen: false }">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
            <!-- Logo -->
            <div class="flex-shrink-0">
                <a href="{{ route('dashboard') }}" class="text-white text-xl font-bold flex items-center">
                    <x-application-logo class="h-8 w-8 mr-2 fill-current text-white" />
                    Liga SM
                </a>
            </div>

            <!-- Desktop Menu -->
            <div class="hidden md:flex items-center space-x-4">
                @auth
                    <!-- Admin Menu -->
                    @can('admin-access')
                    <div class="relative">
                        <button @click="adminOpen = !adminOpen" class="text-gray-300 hover:bg-gray-700 px-3 py-2 rounded-md flex items-center">
                            Administración
                            <svg class="w-4 h-4 ml-1" :class="{ 'transform rotate-180': adminOpen }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        
                        <div x-show="adminOpen" @click.away="adminOpen = false" class="absolute z-10 mt-2 w-48 bg-white rounded-md shadow-lg py-1">
                            <a href="{{ route('temporadas.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Temporadas</a>
                            <a href="{{ route('categorias.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Categorías</a>
                            <a href="{{ route('torneos.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Torneos</a>
                            <a href="{{ route('equipos.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Equipos</a>
                            <a href="{{ route('partidos.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Partidos</a>
                            <a href="{{ route('finanzas.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Finanzas</a>
                        </div>
                    </div>
                    @endcan

                    <!-- Entrenador Menu -->
                    @can('entrenador-access')
                    <div class="flex space-x-4">
                        <a href="{{ route('mi-equipo') }}" class="text-gray-300 hover:bg-gray-700 px-3 py-2 rounded-md">Mi Equipo</a>
                        <a href="{{ route('calendario') }}" class="text-gray-300 hover:bg-gray-700 px-3 py-2 rounded-md">Calendario</a>
                        <a href="{{ route('estadisticas') }}" class="text-gray-300 hover:bg-gray-700 px-3 py-2 rounded-md">Estadísticas</a>
                    </div>
                    @endcan

                    <!-- Jugador Menu -->
                    @can('jugador-access')
                    <div class="flex space-x-4">
                        <a href="{{ route('mis-estadisticas') }}" class="text-gray-300 hover:bg-gray-700 px-3 py-2 rounded-md">Mis Stats</a>
                        <a href="{{ route('calendario') }}" class="text-gray-300 hover:bg-gray-700 px-3 py-2 rounded-md">Calendario</a>
                    </div>
                    @endcan

                    <!-- User Dropdown -->
                    <div class="relative ml-4">
                        <button @click="userOpen = !userOpen" class="flex items-center text-gray-300 hover:bg-gray-700 px-3 py-2 rounded-md">
                            <span class="mr-2">{{ Auth::user()->name }}</span>
                            <svg class="w-4 h-4" :class="{ 'transform rotate-180': userOpen }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        
                        <div x-show="userOpen" @click.away="userOpen = false" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-20">
                            <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Perfil</a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    Cerrar Sesión
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <!-- Public Menu -->
                    <a href="{{ route('dashboard') }}" class="text-gray-300 hover:bg-gray-700 px-3 py-2 rounded-md">Dashboard</a>
                    <a href="{{ route('clasificacion') }}" class="text-gray-300 hover:bg-gray-700 px-3 py-2 rounded-md">Clasificación</a>
                    <a href="{{ route('partidos') }}" class="text-gray-300 hover:bg-gray-700 px-3 py-2 rounded-md">Resultados</a>
                    <a href="{{ route('login') }}" class="text-gray-300 hover:bg-gray-700 px-3 py-2 rounded-md">Ingresar</a>
                    <a href="{{ route('register') }}" class="text-gray-300 hover:bg-gray-700 px-3 py-2 rounded-md">Registrarse</a>
                @endauth
            </div>

            <!-- Mobile Menu Button -->
            <div class="md:hidden">
                <button @click="isOpen = !isOpen" class="text-gray-300 hover:text-white focus:outline-none">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="isOpen ? 'M6 18L18 6M6 6l12 12' : 'M4 6h16M4 12h16M4 18h16'"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Menu -->
    <div class="md:hidden" x-show="isOpen" @click.away="isOpen = false">
        <div class="px-2 pt-2 pb-3 space-y-1">
            @auth
                <!-- Admin Mobile Menu -->
                @can('admin-access')
                <div class="space-y-2">
                    <a href="{{ route('temporadas.index') }}" class="text-gray-300 block px-3 py-2 rounded-md">Temporadas</a>
                    <a href="{{ route('categorias.index') }}" class="text-gray-300 block px-3 py-2 rounded-md">Categorías</a>
                    <a href="{{ route('torneos.index') }}" class="text-gray-300 block px-3 py-2 rounded-md">Torneos</a>
                    <a href="{{ route('equipos.index') }}" class="text-gray-300 block px-3 py-2 rounded-md">Equipos</a>
                    <a href="{{ route('partidos.index') }}" class="text-gray-300 block px-3 py-2 rounded-md">Partidos</a>
                    <a href="{{ route('finanzas.index') }}" class="text-gray-300 block px-3 py-2 rounded-md">Finanzas</a>
                </div>
                @endcan

                <!-- Entrenador Mobile Menu -->
                @can('entrenador-access')
                <div class="space-y-2">
                    <a href="{{ route('mi-equipo') }}" class="text-gray-300 block px-3 py-2 rounded-md">Mi Equipo</a>
                    <a href="{{ route('calendario') }}" class="text-gray-300 block px-3 py-2 rounded-md">Calendario</a>
                    <a href="{{ route('estadisticas') }}" class="text-gray-300 block px-3 py-2 rounded-md">Estadísticas</a>
                </div>
                @endcan

                <!-- Jugador Mobile Menu -->
                @can('jugador-access')
                <div class="space-y-2">
                    <a href="{{ route('mis-estadisticas') }}" class="text-gray-300 block px-3 py-2 rounded-md">Mis Stats</a>
                    <a href="{{ route('calendario') }}" class="text-gray-300 block px-3 py-2 rounded-md">Calendario</a>
                </div>
                @endcan

                <!-- User Mobile Menu -->
                <div class="pt-4 border-t border-gray-700">
                    <a href="{{ route('profile.edit') }}" class="block px-3 py-2 text-gray-300 rounded-md">Perfil</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="block w-full text-left px-3 py-2 text-gray-300 rounded-md hover:text-white">
                            Cerrar Sesión
                        </button>
                    </form>
                </div>
                
            @else
                <!-- Public Mobile Menu -->
                <a href="{{ route('dashboard') }}" class="text-gray-300 block px-3 py-2 rounded-md">Dashboard</a>
                <a href="{{ route('clasificacion') }}" class="text-gray-300 block px-3 py-2 rounded-md">Clasificación</a>
                <a href="{{ route('partidos') }}" class="text-gray-300 block px-3 py-2 rounded-md">Resultados</a>
                <a href="{{ route('login') }}" class="text-gray-300 block px-3 py-2 rounded-md">Ingresar</a>
                <a href="{{ route('register') }}" class="text-gray-300 block px-3 py-2 rounded-md">Registrarse</a>
            @endauth
        </div>
    </div>
</nav>