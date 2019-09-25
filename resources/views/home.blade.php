@extends('layouts.app')

@section('content')
    <chat-room :auth-user="{{ auth()->user() }}"></chat-room>
@endsection
