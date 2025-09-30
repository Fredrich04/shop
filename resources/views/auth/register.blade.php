@extends('layouts.auth')

@section('title', 'Inscription')

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card overflow-hidden m-0">
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
                                <p class="fs-15 fst-italic mt-3">" Rejoignez-nous et créez votre compte gratuitement "</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Col droite -->
                <div class="col-lg-6">
                    <div class="p-lg-5 p-4">
                        <div>
                            <h5 class="text-primary">Créer un compte</h5>
                            <p class="text-muted">Inscrivez-vous pour commencer.</p>
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
                            <form method="POST" action="{{ route('register') }}">
                                @csrf

                                <div class="mb-3">
                                    <label for="name" class="form-label">Nom complet</label>
                                    <input id="name" type="text" name="name"
                                           class="form-control @error('name') is-invalid @enderror"
                                           value="{{ old('name') }}" required autofocus>
                                    @error('name')
                                        <span class="text-danger small">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="email" class="form-label">Adresse Email</label>
                                    <input id="email" type="email" name="email"
                                           class="form-control @error('email') is-invalid @enderror"
                                           value="{{ old('email') }}" required>
                                    @error('email')
                                        <span class="text-danger small">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="password" class="form-label">Mot de passe</label>
                                    <div class="position-relative auth-pass-inputgroup">
                                        <input id="password" type="password" name="password"
                                               class="form-control pe-5 password-input @error('password') is-invalid @enderror"
                                               required autocomplete="new-password">
                                        <button class="btn btn-link position-absolute end-0 top-0 text-muted password-addon" type="button">
                                            <i class="ri-eye-fill align-middle"></i>
                                        </button>
                                    </div>
                                    @error('password')
                                        <span class="text-danger small">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="password_confirmation" class="form-label">Confirmer le mot de passe</label>
                                    <input id="password_confirmation" type="password" name="password_confirmation"
                                           class="form-control" required autocomplete="new-password">
                                </div>

                                <div id="password-contain" class="p-3 bg-light mb-2 rounded">
                                    <h5 class="fs-13">Votre mot de passe doit contenir :</h5>
                                    <p class="fs-12 mb-1">✔ Minimum <b>8 caractères</b></p>
                                    <p class="fs-12 mb-1">✔ Une lettre <b>minuscule</b></p>
                                    <p class="fs-12 mb-1">✔ Une lettre <b>majuscule</b></p>
                                    <p class="fs-12 mb-0">✔ Un <b>nombre</b></p>
                                </div>

                                <div class="mt-4">
                                    <button class="btn btn-success w-100" type="submit">S'inscrire</button>
                                </div>

                                <!-- Social login -->
                                <div class="mt-4 text-center">
                                    <div class="signin-other-title">
                                        <h6 class="fs-13 mb-3 text-muted">Ou inscrivez-vous avec</h6>
                                    </div>
                                    <div>
                                        <a href="{{ url('auth/facebook') }}" class="btn btn-primary btn-icon me-2">
                                            <i class="ri-facebook-fill fs-16"></i>
                                        </a>
                                        <a href="{{ url('auth/google') }}" class="btn btn-danger btn-icon">
                                            <i class="ri-google-fill fs-16"></i>
                                        </a>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <!-- Lien login -->
                        <div class="mt-5 text-center">
                            <p class="mb-0">Déjà inscrit ?
                                <a href="{{ route('login') }}" class="fw-semibold text-primary text-decoration-underline">Se connecter</a>
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
