@extends('layouts.default')
@section('title', $user->name)

@section('content')
    <div class="row">
        <div class="offset-md-2 col-md-8">
            <div class="col-md-12">
                    <section class="user_info">
                        @include('shared._user_info', ['user' => $user])
                    </section>

                    @if (Auth::check())
                        @include('users._follow_form')
                    @endif

                    <section class="stats mt-2">
                        @include('shared._stats', ['user' => $user])
                    </section>

                    <hr>
                    @include('shared._feed', ['feed_items' => $statuses])

                </div>
        </div>
    </div>
@stop
