@extends('backpack::layout')

@section('header')
    <section class="content-header">
        <h1>
            Create Ticket Category
        </h1>
        <ol class="breadcrumb">
            <li><a href="{{ url(config('backpack.base.route_prefix', 'admin')) }}">{{ config('backpack.base.project_name') }}</a></li>
            <li><a href="{{ route('ticket_categories.index') }}">Categories</a></li>
            <li class="active">{{ $type }} Category</li>
        </ol>
    </section>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <a href="{{ route('ticket_categories.index') }}"><i class="fa fa-angle-double-left"></i> Back to Categories</a>
            <br>
            <br>
            <div class="box box-default">
                <div class="box-header with-border">
                    <div class="box-title">{{ $type }} Category</div>
                </div>

                <div class="box-body">
                    <form method="post"
                          action="@if($type=='Edit') {{ route('ticket_categories.update', $category->id) }} @else {{ route('ticket_categories.store') }} @endif">
                        @if($type=='Edit')
                            <input type="hidden" name="_method" value="patch">
                        @endisset
                        {{ csrf_field() }}
                        <div class="form-group">
                            <label>Name *</label>
                            <input type="text" class="form-control" name="name" value="@if($type=='Edit') {{$category->name}} @endif">
                            @if($errors->has('name'))
                                <span class="text-danger">{{ $errors->first('name') }}</span>
                            @endif
                        </div>

                        <div class="form-group text-center">
                            <button type="submit" class="btn btn-sm btn-primary">{{ $type }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
