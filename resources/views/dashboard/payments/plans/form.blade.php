@extends('backpack::layout')

@section('header')
    <section class="content-header">
        <h1>
            Plans
        </h1>
        <ol class="breadcrumb">
            <li><a href="{{ url(config('backpack.base.route_prefix', 'admin')) }}">{{ config('backpack.base.project_name') }}</a></li>
            <li><a href="{{ route('plans.index') }}">Plans</a></li>
            <li class="active">{{ isset($plan) ? 'Edit' : 'Create' }} Plan</li>
        </ol>
    </section>
@endsection


@section('content')
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <a href="{{ route('plans.index') }}"><i class="fa fa-angle-double-left"></i> Back to plans</a>
            <br>
            <br>
            <div class="box box-default">
                <div class="box-header with-border">
                    <div class="box-title">{{ isset($plan) ? 'Edit' : 'Create' }} Plan</div>
                </div>

                <div class="box-body">
                    <form method="post"
                          action="@if(isset($plan)) {{ route('plans.update', $plan->id) }} @else {{ route('plans.store') }} @endif">
                        @isset($plan)
                            <input type="hidden" name="_method" value="patch">
                        @endisset
                        {{ csrf_field() }}
                        <div class="form-group">
                            <label>Name</label>
                            <input type="text" class="form-control" name="name" @isset($plan) value="{{ $plan->id }}" readonly @endisset>
                            @if($errors->has('name'))
                                <span class="text-danger">{{ $errors->first('name') }}</span>
                            @endif
                        </div>
                        <div class="form-group">
                            <label>Price (USD)</label>
                            <input type="number" class="form-control" name="amount" @isset($plan) value="{{ $plan->amount / 100 }}" readonly @endisset>
                            @if($errors->has('amount'))
                                <span class="text-danger">{{ $errors->first('amount') }}</span>
                            @endif
                        </div>
                        <div class="form-group">
                            <label>Trial period (days)</label>
                            <input type="number" class="form-control" name="trial_period_days" @isset($plan) value="{{ $plan->trial_period_days }}" @endisset>
                            @if($errors->has('trial_period_days'))
                                <span class="text-danger">{{ $errors->first('trial_period_days') }}</span>
                            @endif
                        </div>
                        <div class="form-group">
                            <label>Interval</label>
                            <select class="form-control" name="interval" @isset($plan) readonly @endisset>
                                <option value="">-- Please select --</option>
                                <option value="day" @if(isset($plan) && $plan->interval === 'day') selected @endif>Day</option>
                                <option value="week" @if(isset($plan) && $plan->interval === 'week') selected @endif>Week</option>
                                <option value="month" @if(isset($plan) && $plan->interval === 'month') selected @endif>Month</option>
                                <option value="year" @if(isset($plan) && $plan->interval === 'year') selected @endif>Year</option>
                            </select>
                            @if($errors->has('interval'))
                                <span class="text-danger">{{ $errors->first('interval') }}</span>
                            @endif
                        </div>
                        <div class="form-group text-center">
                            <button type="submit" class="btn btn-sm btn-primary">{{ isset($plan) ? 'Update' : 'Create' }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
