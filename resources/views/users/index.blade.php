@extends('layouts.default')
@section('title', '用户列表')

@section('content')
    @foreach($users as $user)
        @include('users._user',$user)
    @endforeach
    {{ $users->links() }}
@stop
