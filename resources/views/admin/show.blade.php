@extends('dashboard')

@section('content')
<div class="container mt-4">
    <div class="row">
        <div class="col-md-6">
            @if($product->image)
                <img src="{{ asset('storage/'.$product->image) }}" class="img-fluid" alt="{{ $product->name }}">
            @else
                <img src="https://via.placeholder.com/400x300" class="img-fluid" alt="{{ $product->name }}">
            @endif
        </div>
        <div class="col-md-6">
            <h2>{{ $product->name }}</h2>
            <p class="text-muted">{{ $product->description }}</p>
            <h4 class="fw-bold">{{ number_format($product->price, 2) }} €</h4>
            <p>Stock disponible : <strong>{{ $product->stock }}</strong></p>

            <form action="{{ route('cart.add', $product->id) }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-success">Ajouter au Panier</button>
            </form>
        </div>
    </div>
</div>
@endsection
