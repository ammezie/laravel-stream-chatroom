@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Dashboard</div>

                    <div class="card-body">
                        <p>You have left the channel!</p>

                        <form action="{{ route('join-channel') }}" method="post">
                          <button type="submit" class="btn btn-primary">Join Channel</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
