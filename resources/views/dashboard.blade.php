<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
        <div class="container">
            <a class="navbar-brand" href="{{ route('dashboard') }}">Mon Shop</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a href="{{ route('dashboard') }}" class="nav-link">Produits</a></li>
                    <li class="nav-item"><a href="{{ route('cart.index') }}" class="nav-link">Mon Panier</a></li>
                    <li class="nav-item"><a href="{{ route('orders.index') }}" class="nav-link">Mes Commandes</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Messages de session -->
    <div class="container">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
    </div>

    <!-- Produits -->
    <div class="container">
        <h3 class="mb-4 text-center">Nos Produits</h3>
        <div class="row">
            @forelse ($products as $product)
                <div class="col-md-4 col-lg-3 mb-4">
                    <div class="card h-100 shadow-sm">
                        <img src="{{ $product->image ?? 'https://via.placeholder.com/300x200' }}"
                             class="card-img-top" alt="{{ $product->name }}">
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title">{{ $product->name }}</h5>
                            <p class="card-text text-muted">{{ Str::limit($product->description, 60) }}</p>
                            <p class="fw-bold text-success">{{ number_format($product->price, 0, ',', ' ') }} FCFA</p>
                            <form action="{{ route('cart.add', $product->id) }}" method="POST" class="mt-auto">
                                @csrf
                                <button type="submit" class="btn btn-primary w-100">Ajouter au Panier</button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <p class="text-center">Aucun produit disponible pour l’instant.</p>
            @endforelse
        </div>
    </div>
</x-app-layout>
