@extends('backpack::layout')

@section('header')
    <section class="content-header">
        <h1>
            Auto Responders
        </h1>
        <ol class="breadcrumb">
            <li><a href="{{ url(config('backpack.base.route_prefix', 'admin')) }}">{{ config('backpack.base.project_name') }}</a></li>
            <li class="active">Auto Responders</li>
        </ol>
    </section>
@endsection


@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="box box-default">
                <div class="box-header with-border">
                    <div class="box-title">Auto Responders ({{ $autoResponders->count() }})</div>
                </div>

                <div class="box-body">
                    <table class="table table-bordered table-hover">
                        <thead>
                        <tr>
                            <th>Title</th>
                            <th>Description</th>
                            <th>Content</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($autoResponders as $responder)
                            <tr>
                                <td>{{ $responder->title }}</td>
                                <td>{{ $responder->description }}</td>
                                <td>{!! str_limit($responder->content, 30) !!}</td>
                                <td>
                                    <a class="btn btn-xs btn-default pull-right" href="{{ route('auto-responders.edit', $responder->id) }}">
                                        <i class="fa fa-edit"></i> Edit
                                    </a>
                                    <a class="btn btn-xs btn-success pull-right" href="{{ route('auto-responders.preview', $responder->id) }}" target="_blank">
                                        <i class="fa fa-eye"></i> Preview
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
