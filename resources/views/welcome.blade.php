@extends('layouts.auth')

@section('title', 'Connexion')

@section('content')
<div class="row">
    <div class="col-lg-6 mx-auto">
        <div class="card">
            <div class="card-body p-4">
                <h5 class="text-primary">Se connecter</h5>
                <p class="text-muted">Entrez vos identifiants pour continuer.</p>

                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    <div class="mb-3">
                        <label for="email" class="form-label">Adresse Email</label>
                        <input id="email" type="email" class="form-control" name="email" required autofocus>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Mot de passe</label>
                        <input id="password" type="password" class="form-control" name="password" required>
                    </div>

                    <button type="submit" class="btn btn-success w-100">Connexion</button>
                </form>

                <div class="mt-4 text-center">
                    <h5 class="fs-13 mb-3">Ou connectez-vous avec</h5>
                    <a href="{{ url('auth/google') }}" class="btn btn-danger">Google</a>
                    <a href="{{ url('auth/facebook') }}" class="btn btn-primary">Facebook</a>
                </div>

                <div class="mt-4 text-center">
                    <p>Pas encore inscrit ? <a href="{{ route('register') }}" class="text-primary">Créer un compte</a></p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
