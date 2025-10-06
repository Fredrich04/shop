@extends('layouts.app')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-200">
    <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-8">
        <div class="text-center mb-6">
            <div class="flex justify-center mb-2">
                <x-application-logo class="w-12 h-12 text-gray-900" />
            </div>
            <h1 class="text-2xl font-bold text-gray-900">TechStore</h1>
            <p class="text-gray-500 text-sm mt-2">Connectez-vous à votre compte</p>
        </div>

        <form method="POST" action="{{ route('login') }}" class="space-y-4">
            @csrf

            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                    class="mt-1 block w-full rounded-md border-gray-300 focus:border-black focus:ring-black">
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-gray-700">Mot de passe</label>
                <input id="password" type="password" name="password" required
                    class="mt-1 block w-full rounded-md border-gray-300 focus:border-black focus:ring-black">
            </div>

            <div class="flex items-center justify-between">
                <label class="flex items-center">
                    <input type="checkbox" name="remember" class="rounded border-gray-300 text-black focus:ring-black">
                    <span class="ml-2 text-sm text-gray-600">Se souvenir de moi</span>
                </label>

                @if (Route::has('password.request'))
                    <a class="text-sm text-black hover:underline" href="{{ route('password.request') }}">
                        Mot de passe oublié ?
                    </a>
                @endif
            </div>

            <button type="submit"
                class="w-full bg-black text-white py-2 rounded-md hover:bg-gray-900 transition">
                Se connecter
            </button>
        </form>

        <div class="mt-6 text-center text-sm text-gray-500">
            <p>Pas encore de compte ?
                <a href="{{ route('register') }}" class="text-black hover:underline">S'inscrire</a>
            </p>
        </div>
    </div>
</div>
@endsection
