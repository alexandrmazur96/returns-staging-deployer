@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card bg-dark">
                    <div class="card-header text-white-50">
                        <h5>{{ __('Branches') }}</h5>
                    </div>

                    <div class="card-body text-white-50">
                        <div class="row" style="flex-direction: column">
                            <div class="deploying-spinner-block">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <h5>Projects</h5>
                                <hr>
                                @foreach($projects as $projectKey => $projectName)
                                    <div class="form-check">
                                        <label class="form-check-label">
                                            <input type="radio" class="form-check-input" name="project-radio"
                                                   value="{{ $projectKey }}"
                                                   @if($defaultProject === $projectKey) checked @endif>
                                            {{ $projectName }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                            <div class="col-md-9">
                                <div class="search-div">
                                    <div class="md-form mt-0" style="margin-bottom: 20px">
                                        <input class="form-control bg-dark text-white" type="text" placeholder="Search"
                                               aria-label="Search" id="branchSearch">
                                    </div>
                                </div>
                                <div style="overflow:scroll; height:350px;">
                                    <ul class="list-group branch-ul">
                                        @foreach($branches as $branch)
                                            <li class="list-group-item bg-secondary text-white" style="display: flex">
                                                @if($branch['user'] !== null)
                                                    <span class="badge badge-primary"
                                                          style="height: 16px;margin-top: 13px;margin-right: 5px;">
                                                        {{ $branch['user'] }}
                                                    </span>
                                                @endif
                                                <span class="branch-name"
                                                      style="flex-grow: 1;padding-top:10px">{{ $branch['name'] }}</span>
                                                <div class="buttons-action">
                                                    @if($branch['pull_link'] !== null)
                                                        <a href="{{ $branch['pull_link'] }}" target="_blank"
                                                           class="btn btn-info" style="margin: 5px;">Link to PR</a>
                                                    @endif
                                                    <button class="btn btn-success deploy-btn"
                                                            data-pr="{{ $branch['name'] }}" style="margin: 5px;">Deploy
                                                    </button>
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
