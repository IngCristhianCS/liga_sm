<template>
    <nav class="navbar">
        <div class="container">
            <ul class="nav navbar-nav">
                <li>
                    <div class="navbar-header">
                        <a href="javascript:void(0);" class="h-bars"></a>
                        <a class="navbar-brand" href="/"><img src="/assets/images/logo.webp" width="50"
                                alt="San Miguel"><span class="m-l-10">San Miguel</span></a>
                    </div>
                </li>

                <template v-if="!authStore.user">
                    <li class="float-right">
                        <a href="/login" class="js-right-sidebar"><i class="zmdi zmdi-settings zmdi-hc-spin"></i>
                            Ingresar</a>
                    </li>
                </template>

                <template v-else>
                    <li class="float-right dropdown"><a href="javascript:void(0);" class="dropdown-toggle"
                            data-toggle="dropdown"><i class="zmdi zmdi-account"></i> {{ authStore.user.name }} <i
                                class="zmdi zmdi-chevron-down"></i></a>
                        <ul class="dropdown-menu pullDown">
                            <li><router-link to="/mi-perfil" class="nav-link" @click="closeMobileMenu"
                                    active-class="open active"><i
                                        class="zmdi zmdi-email m-r-10"></i>Perfil</router-link></li>
                            <li><a href="javascript:void(0);" @click="logout">
                                    <span>
                                        <i class="zmdi zmdi-power m-r-10"></i> Cerrar Sesión
                                    </span></a></li>
                        </ul>
                    </li>
                </template>
            </ul>
        </div>
    </nav>
    <div class="menu-container">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <ul class="h-menu">
                        <template v-if="!authStore.user">
                            <li>
                                <router-link to="/" class="nav-link" @click="closeMobileMenu"
                                    active-class="open active">
                                    <i class="zmdi zmdi-view-list"></i> Clasificación
                                </router-link>
                            </li>
                            <li>
                                <router-link to="/resultados" class="nav-link" @click="closeMobileMenu"
                                    active-class="open active">
                                    <i class="zmdi zmdi-chart"></i> Resultados
                                </router-link>
                            </li>
                        </template>

                        <template v-else>
                            <li>
                                <router-link to="/" class="nav-link" @click="closeMobileMenu"
                                    active-class="open active">
                                    <i class="zmdi zmdi-view-list"></i> Clasificación
                                </router-link>
                            </li>
                            <li>
                                <router-link to="/resultados" class="nav-link" @click="closeMobileMenu"
                                    active-class="open active">
                                    <i class="zmdi zmdi-chart"></i> Resultados
                                </router-link>
                            </li>
                            <li v-if="isAdmin" class="dropdown">
                                <a href="javascript:void(0)">
                                    <i class="zmdi zmdi-settings"></i> Administración
                                    <i class="zmdi zmdi-chevron-down"></i>
                                </a>
                                <ul class="sub-menu">
                                    <li><router-link to="/usuarios" class="nav-link" @click="closeMobileMenu"
                                            active-class="open active">Usuarios</router-link></li>
                                    <li><router-link to="/equipos" class="nav-link" @click="closeMobileMenu"
                                            active-class="open active">Equipos</router-link></li>
                                    <li><router-link to="/partidos" class="nav-link" @click="closeMobileMenu"
                                            active-class="open active">Partidos</router-link></li>
                                    <li><router-link to="/jornadas" class="nav-link" @click="closeMobileMenu"
                                            active-class="open active">Jornadas</router-link></li>
                                    <li><router-link to="/ingresos" class="nav-link" @click="closeMobileMenu"
                                            active-class="open active">Ingresos</router-link></li>
                                    <li><router-link to="/egresos" class="nav-link" @click="closeMobileMenu"
                                            active-class="open active">Egresos</router-link></li>
                                    <li><router-link to="/torneos" class="nav-link" @click="closeMobileMenu"
                                            active-class="open active">Torneos</router-link></li>
                                    <li><router-link to="/temporadas" class="nav-link" @click="closeMobileMenu"
                                            active-class="open active">Temporadas</router-link></li>
                                    <li><router-link to="/categorias" class="nav-link" @click="closeMobileMenu"
                                            active-class="open active">Categorías</router-link></li>
                                </ul>
                            </li>

                            <template v-if="isEntrenador">
                                <li>
                                    <router-link to="/mi-equipo" @click="closeMobileMenu" class="nav-link">
                                        <i class="zmdi zmdi-accounts"></i> Mi Equipo
                                    </router-link>
                                </li>
                                <li>
                                    <router-link to="/calendario" @click="closeMobileMenu" class="nav-link">
                                        <i class="zmdi zmdi-calendar"></i> Calendario
                                    </router-link>
                                </li>
                                <li><router-link to="/ingresos" class="nav-link" @click="closeMobileMenu"
                                        active-class="open active"><i class="zmdi zmdi-money"></i> Mis
                                        Pagos</router-link></li>
                            </template>

                            <template v-if="isJugador">
                                <li>
                                    <router-link to="/mis-estadisticas" @click="closeMobileMenu" class="nav-link">
                                        <i class="zmdi zmdi-chart-donut"></i> Mis Stats
                                    </router-link>
                                </li>
                            </template>
                        </template>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { computed } from 'vue';
import { useAuthStore } from '../../stores/auth';
import axios from 'axios';
export default {
    name: 'AppNavigation',
    setup() {
        const authStore = useAuthStore();

        const logout = async () => {
            try {
                await axios.post('/logout');
                authStore.user = null;
                authStore.token = null;
                window.location.href = '/';
            } catch (error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Error al cerrar sesión.',
                });
            }
        };

        return {
            authStore,
            isAdmin: computed(() => authStore.isAdmin),
            isEntrenador: computed(() => authStore.isEntrenador),
            isJugador: computed(() => authStore.isJugador),
            userName: computed(() => authStore.userName),
            logout,
        };
    },
    methods: {
        closeMobileMenu() {
            $(".h-menu").toggleClass("show-on-mobile");
        },
    },
};
</script>