<x-app-layout>
    <div id="app">
        <section class="content">
            <div class="container">
                <div class="block-header">
                    <div class="row clearfix">
                        <div class="col-lg-5 col-md-5 col-sm-12">
                            <h2>Mi Perfil <small>Información de Cuenta</small></h2>
                        </div>
                        <div class="col-lg-7 col-md-7 col-sm-12">
                            <ul class="breadcrumb float-md-right padding-0">
                                <li class="breadcrumb-item"><a href="/"><i class="zmdi zmdi-home"></i></a></li>
                                <li class="breadcrumb-item"><a href="javascript:void(0);">Mi Perfil</a></li>
                                <li class="breadcrumb-item active">Información de Cuenta</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="row clearfix">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="header">
                            </div>
                            <div class="body">
                                <div class="row clearfix">
                                    <div class="col-sm-12">
                                        @include('profile.partials.update-profile-information-form')
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row clearfix">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="header">
                            </div>
                            <div class="body">
                                <div class="row clearfix">
                                    <div class="col-sm-12">
                                        @include('profile.partials.update-password-form')
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</x-app-layout>
