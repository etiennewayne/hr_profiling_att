@extends('layouts.no-navbar')

@section('content')
    <div class="section">
        <div class="has-text-centered is-size-4 has-text-weight-bold">
            ACCOUNT STATUS IS PENDING AND NEED TO ACTIVATE BY THE ADMINISTRATOR.

            <form action="/logout" method="post">
                @csrf

                <div class="buttons is-centered">
                    <button class="button is-link">LOGOUT</button>
                </div>
            </form>
        </div>
    </div>
@endsection
