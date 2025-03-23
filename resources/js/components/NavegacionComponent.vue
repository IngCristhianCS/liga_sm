<template>
    <div class="menu-container">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <ul class="h-menu">
                        <li><router-link to="/" class="nav-link" active-class="active">
                                <i class="zmdi zmdi-home"></i> Clasificación
                            </router-link></li>
                        <li><router-link to="/resultados" class="nav-link" active-class="active">
                                <i class="zmdi zmdi-view-dashboard"></i> Resultados
                            </router-link></li>
                        <!--<router-link 
        to="/registro" 
        class="nav-link"
        active-class="active"
      >
        Registro
      </router-link>-->


                    </ul>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: 'AppNavigation'
}
</script>