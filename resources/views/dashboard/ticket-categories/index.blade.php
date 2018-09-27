@extends('backpack::layout')

@section('header')
    <section class="content-header">
        <h1>
            Ticket Categories
        </h1>
        <ol class="breadcrumb">
            <li><a href="{{ url(config('backpack.base.route_prefix', 'admin')) }}">{{ config('backpack.base.project_name') }}</a></li>
            <li class="active">Ticket Categories</li>
        </ol>
    </section>
@endsection


@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="box box-default">
                <div class="box-header with-border">
                    <div class="box-title">Ticket Categories ({{ count($ticketCategories) }})</div>
                    <a class="btn btn-xs btn-primary pull-right" href="{{ route('ticket_categories.create') }}">
                        <i class="fa fa-plus-circle"></i> Create new
                    </a>
                </div>

                <div class="box-body">
                    <table class="table table-bordered table-hover">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($ticketCategories as $ticketCategory)
                            <tr>
                                <td>{{ $ticketCategory->id }}</td>
                                <td>{{ $ticketCategory->name }}</td>
                                <td>
                                    <a class="btn btn-xs btn-danger pull-right remove" data-href="{{ route('ticket_categories.destroy', $ticketCategory->id) }}">
                                        <i class="fa fa-trash"></i> Delete
                                    </a>
                                    <a class="btn btn-xs btn-default pull-right" href="{{ route('ticket_categories.edit', $ticketCategory->id) }}">
                                        <i class="fa fa-edit"></i> Edit
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
