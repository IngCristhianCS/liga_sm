<template>
    <div class="menu-container">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <ul class="h-menu">
                        <template v-if="!authStore.user">
                            <li>
                                <router-link to="/" class="nav-link" @click="closeMobileMenu" active-class="open active">
                                    <i class="zmdi zmdi-view-list"></i> Clasificación
                                </router-link>
                            </li>
                            <li>
                                <router-link to="/resultados" class="nav-link" @click="closeMobileMenu" active-class="open active">
                                    <i class="zmdi zmdi-chart"></i> Resultados
                                </router-link>
                            </li>
                        </template>

                        <template v-else>
                            <li>
                                <router-link to="/" class="nav-link" @click="closeMobileMenu" active-class="open active">
                                    <i class="zmdi zmdi-view-list"></i> Clasificación
                                </router-link>
                            </li>
                            <li>
                                <router-link to="/resultados" class="nav-link" @click="closeMobileMenu" active-class="open active">
                                    <i class="zmdi zmdi-chart"></i> Resultados
                                </router-link>
                            </li>
                            <li v-if="isAdmin" class="dropdown">
                                <a href="javascript:void(0)">
                                    <i class="zmdi zmdi-settings"></i> Administración
                                    <i class="zmdi zmdi-chevron-down"></i>
                                </a>
                                <ul class="sub-menu">
                                    <li><router-link to="/usuarios" class="nav-link" @click="closeMobileMenu"  active-class="open active">Usuarios</router-link></li>
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
import { computed, ref } from 'vue';
import { useAuthStore } from '../../stores/auth';

export default {
    name: 'AppNavigation',
    setup() {
        const authStore = useAuthStore();
        const mobileMenuOpen = ref(false);

        return {
            authStore,
            mobileMenuOpen,
            isAdmin: computed(() => authStore.isAdmin),
            isEntrenador: computed(() => authStore.isEntrenador),
            isJugador: computed(() => authStore.isJugador),
            userName: computed(() => authStore.userName),
        };
    },
    methods: {
        closeMobileMenu() {
            $(".h-menu").toggleClass("show-on-mobile");
        }
    },
};
</script>