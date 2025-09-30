@extends('layouts.auth')

@section('title', 'Créer un nouveau mot de passe')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-12">
        <div class="card overflow-hidden">
            <div class="row justify-content-center g-0">

                <!-- Col gauche -->
                <div class="col-lg-6 d-none d-lg-block">
                    <div class="p-lg-5 p-4 auth-one-bg h-100">
                        <div class="bg-overlay"></div>
                        <div class="h-100 d-flex flex-column justify-content-between">
                            <div>
                                <a href="/" class="d-block mb-4">
                                    <img src="{{ asset('assets/images/logo-light.png') }}" alt="Logo" height="18">
                                </a>
                            </div>
                            <div class="text-white text-center pb-5">
                                <i class="ri-double-quotes-l display-4 text-success"></i>
                                <p class="fs-15 fst-italic mt-3">" Entrez un nouveau mot de passe pour continuer votre aventure "</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Col droite -->
                <div class="col-lg-6">
                    <div class="p-lg-5 p-4">
                        <h5 class="text-primary">Créer un nouveau mot de passe</h5>
                        <p class="text-muted">Votre mot de passe doit être différent des précédents.</p>

                        <!-- Affiche les erreurs -->
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('password.store') }}">
                            @csrf
                            <!-- Token -->
                            <input type="hidden" name="token" value="{{ $request->route('token') }}">

                            <!-- Email -->
                            <input type="hidden" name="email" value="{{ old('email', $request->email) }}">

                            <!-- Password -->
                            <div class="mb-3">
                                <label for="password" class="form-label">Nouveau mot de passe</label>
                                <div class="position-relative auth-pass-inputgroup">
                                    <input id="password" type="password" name="password"
                                           class="form-control pe-5 password-input @error('password') is-invalid @enderror"
                                           required autocomplete="new-password" placeholder="Entrer un mot de passe">
                                    <button class="btn btn-link position-absolute end-0 top-0 text-muted password-addon" type="button">
                                        <i class="ri-eye-fill align-middle"></i>
                                    </button>
                                </div>
                                @error('password')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Confirm Password -->
                            <div class="mb-3">
                                <label for="password_confirmation" class="form-label">Confirmer le mot de passe</label>
                                <div class="position-relative auth-pass-inputgroup">
                                    <input id="password_confirmation" type="password" name="password_confirmation"
                                           class="form-control pe-5 password-input @error('password_confirmation') is-invalid @enderror"
                                           required autocomplete="new-password" placeholder="Confirmer le mot de passe">
                                    <button class="btn btn-link position-absolute end-0 top-0 text-muted password-addon" type="button">
                                        <i class="ri-eye-fill align-middle"></i>
                                    </button>
                                </div>
                                @error('password_confirmation')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Indications -->
                            <div id="password-contain" class="p-3 bg-light mb-2 rounded">
                                <h5 class="fs-13">Le mot de passe doit contenir :</h5>
                                <p id="pass-length" class="fs-12 mb-1">✔ Minimum <b>8 caractères</b></p>
                                <p id="pass-lower" class="fs-12 mb-1">✔ Une lettre <b>minuscule</b> (a-z)</p>
                                <p id="pass-upper" class="fs-12 mb-1">✔ Une lettre <b>majuscule</b> (A-Z)</p>
                                <p id="pass-number" class="fs-12 mb-0">✔ Un <b>nombre</b> (0-9)</p>
                            </div>

                            <div class="mt-4">
                                <button type="submit" class="btn btn-success w-100">Réinitialiser le mot de passe</button>
                            </div>
                        </form>

                        <div class="mt-5 text-center">
                            <p class="mb-0">Je me souviens de mon mot de passe ?
                                <a href="{{ route('login') }}" class="fw-semibold text-primary text-decoration-underline"> Me connecter </a>
                            </p>
                        </div>
                    </div>
                </div>
                <!-- end col -->
            </div>
            <!-- end row -->
        </div>
    </div>
</div>
@endsection
