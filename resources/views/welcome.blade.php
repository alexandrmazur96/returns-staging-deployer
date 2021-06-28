@extends('layouts.app')

@section('content')
    <div class="container" style="margin-top:15%">
        <div class="row">
            @foreach($tools as $tool)
                <div class="col-4" style="margin-bottom: 20px">
                    <a href="{{ $tool['url'] }}" style="text-decoration: none;">
                        <div class="card text-white bg-dark h-100 tool-card">
                            <h5 class="card-header"
                                style="border-bottom:1px solid red">{!! $tool['icon'] !!} {{ $tool['name'] }}</h5>
                            <div class="card-body">
                                <p class="card-text">{{ $tool['description'] }}</p>
                            </div>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
    </div>
@endsection
