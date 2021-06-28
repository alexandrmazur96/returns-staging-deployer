@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="card bg-dark">
            <div class="card-header text-white-50">
                <h4>{{ __('Versions') }}</h4>
            </div>
            <div class="card-body text-white-50">
                <div class="row">
                    <div class="col-4">
                        <div class="list-group" id="list-tab" role="tablist">
                            <a class="list-group-item list-group-item-action list-group-item-dark active"
                               data-toggle="list"
                               href="#ir-content"
                               role="tab">
                                IntelligentReturns
                            </a>

                            <a class="list-group-item list-group-item-action list-group-item-dark"
                               data-toggle="list"
                               href="#rp-content"
                               role="tab">
                                ReturnsPlatform
                            </a>

                            <a class="list-group-item list-group-item-action list-group-item-dark"
                               data-toggle="list"
                               href="#rq-content"
                               role="tab">
                                ReturnsQuick
                            </a>

                            <a class="list-group-item list-group-item-action list-group-item-dark"
                               data-toggle="list"
                               href="#air-content"
                               role="tab">
                                AIR
                            </a>
                        </div>
                    </div>
                    <div class="col-8">
                        <div class="tab-content">
                            <div class="tab-pane fade show active" id="ir-content" role="tabpanel">
                                <p>Current version: {{ $irCurrentVersion }} ({{ $irCurrentVersionReadable }})</p>
                                <ul class="list-group list-group-flush">
                                    @foreach($irVersions as $irVersion)
                                        <li class="list-group-item bg-secondary text-white">
                                            <p>Version: {{ $irVersion['raw'] }}</p>
                                            <p>Readable: {{ $irVersion['readable'] }}</p>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                            <div class="tab-pane fade" id="rp-content" role="tabpanel">
                                RP data
                            </div>
                            <div class="tab-pane fade" id="rq-content" role="tabpanel">
                                RQ data
                            </div>
                            <div class="tab-pane fade" id="air-content" role="tabpanel">
                                AIR data
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
