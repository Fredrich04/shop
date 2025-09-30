@extends('layouts.auth')

@section('title', 'Mot de passe oublié')

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
                                <p class="fs-15 fst-italic mt-3">" Entrez votre email pour recevoir un lien de réinitialisation "</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Col droite -->
                <div class="col-lg-6">
                    <div class="p-lg-5 p-4">
                        <h5 class="text-primary">Mot de passe oublié ?</h5>
                        <p class="text-muted">Entrez votre adresse email et nous vous enverrons un lien de réinitialisation.</p>

                        <!-- Session Status -->
                        @if (session('status'))
                            <div class="alert alert-success">
                                {{ session('status') }}
                            </div>
                        @endif

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

                        <!-- Formulaire -->
                        <form method="POST" action="{{ route('password.email') }}">
                            @csrf
                            <div class="mb-4">
                                <label for="email" class="form-label">Adresse email</label>
                                <input type="email" id="email" name="email"
                                       class="form-control @error('email') is-invalid @enderror"
                                       placeholder="Entrez votre email" value="{{ old('email') }}" required autofocus>
                                @error('email')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="text-center mt-4">
                                <button class="btn btn-success w-100" type="submit">Envoyer le lien</button>
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
        </div>
    </div>
</div>
@endsection

