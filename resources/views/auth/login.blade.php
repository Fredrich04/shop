@extends('layouts.auth')

@section('title', 'Connexion')

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card overflow-hidden">
            <div class="row g-0">

                <!-- Col gauche -->
                <div class="col-lg-6 d-none d-lg-block">
                    <div class="p-lg-5 p-4 auth-one-bg h-100">
                        <div class="bg-overlay"></div>
                        <div class="h-100 d-flex flex-column">
                            <div class="mb-4">
                                <a href="/" class="d-block">
                                    <img src="{{ asset('assets/images/logo-light.png') }}" alt="Logo" height="18">
                                </a>
                            </div>
                            <div class="mt-auto text-white text-center pb-5">
                                <i class="ri-double-quotes-l display-4 text-success"></i>
                                <p class="fs-15 fst-italic mt-3">" Bienvenue sur notre plateforme, connectez-vous pour continuer "</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Col droite -->
                <div class="col-lg-6">
                    <div class="p-lg-5 p-4">
                        <div>
                            <h5 class="text-primary">Heureux de vous revoir !</h5>
                            <p class="text-muted">Connectez-vous pour continuer.</p>
                        </div>

                        <!-- Erreurs -->
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <!-- Formulaire -->
                        <div class="mt-4">
                            <form method="POST" action="{{ route('login') }}">
                                @csrf

                                <div class="mb-3">
                                    <label for="email" class="form-label">Adresse Email</label>
                                    <input id="email" type="email" name="email"
                                           class="form-control @error('email') is-invalid @enderror"
                                           value="{{ old('email') }}" required autofocus>
                                    @error('email')
                                        <span class="text-danger small">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <div class="float-end">
                                        @if (Route::has('password.request'))
                                            <a href="{{ route('password.request') }}" class="text-muted small">Mot de passe oublié ?</a>
                                        @endif
                                    </div>
                                    <label for="password" class="form-label">Mot de passe</label>
                                    <div class="position-relative auth-pass-inputgroup mb-3">
                                        <input id="password" type="password" name="password"
                                               class="form-control pe-5 password-input @error('password') is-invalid @enderror"
                                               required>
                                        <button class="btn btn-link position-absolute end-0 top-0 text-muted password-addon" type="button">
                                            <i class="ri-eye-fill align-middle"></i>
                                        </button>
                                    </div>
                                    @error('password')
                                        <span class="text-danger small">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" name="remember" id="remember_me">
                                    <label class="form-check-label" for="remember_me">Se souvenir de moi</label>
                                </div>

                                <div class="mt-4">
                                    <button class="btn btn-success w-100" type="submit">Connexion</button>
                                </div>

                                <!-- Social login -->
                                <div class="mt-4 text-center">
                                    <div class="signin-other-title">
                                        <h5 class="fs-13 mb-4 title">Ou connectez-vous avec</h5>
                                    </div>
                                    <div>
                                        <a href="{{ url('auth/facebook') }}" class="btn btn-primary btn-icon waves-effect waves-light me-2">
                                            <i class="ri-facebook-fill fs-16"></i>
                                        </a>
                                        <a href="{{ url('auth/google') }}" class="btn btn-danger btn-icon waves-effect waves-light">
                                            <i class="ri-google-fill fs-16"></i>
                                        </a>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <!-- Lien inscription -->
                        <div class="mt-5 text-center">
                            <p class="mb-0">Pas encore de compte ?
                                <a href="{{ route('register') }}" class="fw-semibold text-primary text-decoration-underline">S'inscrire</a>
                            </p>
                        </div>
                    </div>
                </div>
                <!-- end col -->
            </div>
        </div>
    </div>
</div>
@endsection


