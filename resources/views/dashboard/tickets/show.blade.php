@extends('backpack::layout')

@section('header')
    <section class="content-header">
        <h1>
            Tickets
        </h1>
        <ol class="breadcrumb">
            <li><a href="{{ url(config('backpack.base.route_prefix', 'admin')) }}">{{ config('backpack.base.project_name') }}</a></li>
            <li><a href="{{ url(config('backpack.base.route_prefix', 'admin'). '/tickets/list') }}">All Tickets</a></li>
            <li class="active">#{{ $ticket->ticket_id }}</li>
        </ol>
    </section>
@endsection


@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="box box-default">
                <div class="box-header with-border">
                    <div class="box-title">#{{ $ticket->ticket_id }} - {{ $ticket->title }}</div>
                </div>
                <div class="box-body">
                    @include('backpack::inc.flash')

                    <div class="ticket-info">
                        <p>{!! $ticket->message !!}</p>
                        <p>Categry: {{ $category->name }}</p>
                        <p>
                        @if ($ticket->status === 'Open')
                            Status: <span class="label label-success">{{ $ticket->status }}</span>
                        @else
                            Status: <span class="label label-danger">{{ $ticket->status }}</span>
                        @endif
                        </p>
                        <p>Created on: {{ $ticket->created_at->diffForHumans() }}</p>
                    </div>

                    <hr>
                    <div class="comments">
                        @foreach ($comments as $comment)
                            <div class="panel panel-@if($ticket->user->id === $comment->user_id){{"default"}}@else{{"success"}}@endif">
                                <div class="panel panel-heading">
                                    {{ $comment->user->first_name. " ".  $comment->user->last_name}}
                                    <span class="pull-right">{{ $comment->created_at->format('Y-m-d') }}</span>
                                </div>

                                <div class="panel panel-body">
                                    {!! $comment->comment !!}     
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="comment-form">
                        <form action="{{ url(config('backpack.base.route_prefix', 'admin'). '/comment_ticket') }}" method="POST" class="form">
                            {!! csrf_field() !!}

                            <input type="hidden" name="ticket_id" value="{{ $ticket->id }}">

                            <div class="form-group{{ $errors->has('comment') ? ' has-error' : '' }}">
                                <textarea rows="10" id="comment" class="form-control" name="comment"></textarea>

                                @if ($errors->has('comment'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('comment') }}</strong>
                                    </span>
                                @endif
                            </div>

                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">Submit</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('after_styles')
<style>
    .panel-default {
        border-color: #ddd;
    }

    .panel-default .panel-heading {
        color: #333;
        background-color: #f5f5f5;
        border-color: #ddd;
    }

    .panel-heading {
        padding: 10px 15px;
        border-bottom: 1px solid transparent;
        border-top-left-radius: 3px;
        border-top-right-radius: 3px;
    }

    .panel-body {
        padding: 15px;
    }
</style>
@endsection