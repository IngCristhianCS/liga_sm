<x-guest-layout>
    <div class="authentication">
        <div class="container">
            <div class="col-md-12 content-center">
                <div class="row">
                    <div class="col-lg-6 col-md-12">
                        <div class="company_detail">
                            <h3>Liga de fútbol rápido <strong>San Miguel</strong></h3>
                            <p></p>
                            <div class="footer">


                            </div>
                        </div>
                    </div>
                    <div class="col-lg-5 col-md-12 offset-lg-1">
                        <div class="card-plain">
                            <div class="header">
                                <h5>Log in</h5>
                                <!-- Session Status -->
                                <x-auth-session-status class="alert alert-danger" :status="session('status')" />
                            </div>
                            <form class="form" method="POST" action="{{ route('login') }}">
                                @csrf
                                <div class="input-group">
                                    <x-text-input class="form-control" placeholder="Correo Electronico" id="email"
                                        type="email" name="email" :value="old('email')" required autofocus
                                        autocomplete="username" /><span class="input-group-addon"><i
                                            class="zmdi zmdi-account-circle"></i></span>
                                </div>
                                <div class="input-group">
                                    <x-text-input id="password" placeholder="Password" class="form-control"
                                        type="password" name="password" required autocomplete="current-password" /><span
                                        class="input-group-addon"><i class="zmdi zmdi-lock"></i></span>
                                </div>

                                <div class="footer">
                                    <x-primary-button class="btn btn-primary btn-round btn-block">
                                        {{ __('Log in') }}
                                    </x-primary-button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
</x-guest-layout>
