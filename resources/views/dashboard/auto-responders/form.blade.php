@extends('backpack::layout')

@section('header')
    <section class="content-header">
        <h1>
            Auto Responders
        </h1>
        <ol class="breadcrumb">
            <li><a href="{{ url(config('backpack.base.route_prefix', 'admin')) }}">{{ config('backpack.base.project_name') }}</a></li>
            <li><a href="{{ route('auto-responders.index') }}">Auto Responders</a></li>
            <li class="active">Edit Auto Responder</li>
        </ol>
    </section>
@endsection


@section('content')
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <a href="{{ route('auto-responders.index') }}"><i class="fa fa-angle-double-left"></i> Back to Auto Responders</a>
            <br>
            <br>
            <div class="box box-default">
                <div class="box-header with-border">
                    <div class="box-title">Edit Auto Responder</div>
                </div>

                <div class="box-body">
                    <form method="post" action="{{ route('auto-responders.update', $autoResponder->id) }}">
                            <input type="hidden" name="_method" value="patch">
                        {{ csrf_field() }}
                        <div class="form-group">
                            <label>Title *</label>
                            <input type="text" class="form-control" name="title" value="{{ $autoResponder->title }}">
                            @if($errors->has('title'))
                                <span class="text-danger">{{ $errors->first('title') }}</span>
                            @endif
                        </div>

                        <div class="form-group">
                            <label>Description *</label>
                            <textarea type="text" class="form-control" name="description">{{ $autoResponder->description }}</textarea>
                            @if($errors->has('description'))
                                <span class="text-danger">{{ $errors->first('description') }}</span>
                            @endif
                        </div>

                        <div class="form-group">
                            <label>Content *</label>
                            <input type="hidden" name="content" value="">
                            <textarea type="text" id="content" class="form-control">{{ $autoResponder->content }}</textarea>
                            @if($errors->has('content'))
                                <span class="text-danger">{{ $errors->first('content') }}</span>
                            @endif
                        </div>

                        <div class="form-group text-center">
                            <button class="btn btn-sm btn-primary" id="update">Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('after_vendor_scripts')
    <script>
        $(function() {
            $('#content').froalaEditor({
                key: 'wC6B5D5C4hB3D3A9A8C3B4A4F3D3G3cdsA-13ekwktxeD-16xF6nab=='
            })
            $('#content').froalaEditor('html.set', '{!! $autoResponder->content !!}');

            $('#update').on('click', function(e) {
                e.preventDefault()
                $('[name="content"]').val($('#content').froalaEditor('html.get'))

                $('form').submit()
            })
        });
    </script>
@endsection
