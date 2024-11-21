<x-backend.dashboard> 

    Welcome to your account.


{{-- Logout --}}

<form method="POST" action="{{ route('logout') }}">
    @csrf

    <button type="submit">Logout</button>
</form>


</x-backend.dashboard>