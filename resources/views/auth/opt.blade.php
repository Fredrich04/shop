<form method="POST" action="{{ route('otp.verify') }}">
    @csrf
    <label>Entrez le code reçu par email :</label>
    <input type="text" name="code" required>
    <button type="submit">Valider</button>
</form>
