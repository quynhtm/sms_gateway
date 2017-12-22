{{--<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">--}}
{{--<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap-theme.min.css">--}}
{{--<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>--}}
{{--<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>--}}
<script src="{{URL::asset('assets/admin/js/jquery.min.js')}}"></script>
<script src="{{URL::asset('assets/admin/js/admin.js')}}"></script>
<?php use App\Library\AdminFunction\FunctionLib; ?>
<?php use App\Library\AdminFunction\Define; ?>
@extends('admin.AdminLayouts.index')
@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">
<div class="main-content-inner">
    <div class="breadcrumbs breadcrumbs-fixed" id="breadcrumbs">
        <ul class="breadcrumb">
            <li>
                <i class="ace-icon fa fa-home home-icon"></i>
                <a href="{{URL::route('admin.dashboard')}}">{{FunctionLib::viewLanguage('home')}}</a>
            </li>
            <li class="active">{{FunctionLib::viewLanguage('sent_sms_history')}}</li>
        </ul><!-- /.breadcrumb -->
    </div>

    <div class="page-content">
        <div class="col-md-8 panel-content">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <h4><i class="fa fa-list" aria-hidden="true"></i> {{\App\Library\AdminFunction\FunctionLib::viewLanguage('web_sms_template_list')}}</h4>
                </div> <!-- /widget-header -->
                {{ Form::open(array('method' => 'GET', 'role'=>'form')) }}
                <div style="margin-top: 10px">
                    <div class="col-sm-4" >
                        <input @if(isset($search['template_name'])) value="{{$search['template_name']}}" @endif placeholder="{{FunctionLib::viewLanguage('sms_template_name')}}" name="name_template_s" class="form-control" id="name_template_s">
                        {{--<select style="height: 34px" name="name_template" id="name_template" class="form-control input-sm">--}}
                            {{--{!! $optionUser !!}--}}
                        {{--</select>--}}
                    </div>
                    <div style="float: left" class="form-group">
                        <button class="btn btn-primary btn-sm" type="submit" name="submit" value="1"><i
                                    class="fa fa-search"></i> {{FunctionLib::viewLanguage('search')}}</button>
                    </div>
                </div>
                {{ Form::close() }}
                <div class="panel-body" id="element">
                    @if(sizeof($data) > 0)
                        <table class="table table-bordered">
                            <thead class="thin-border-bottom">
                            <tr class="">
                                <th class="text-center w10 center">{{FunctionLib::viewLanguage('no')}}</th>
                                <th class="center w50">{{FunctionLib::viewLanguage('template_name')}}</th>
                                <th class="center w200">{{FunctionLib::viewLanguage('content')}}</th>
                                <th class="center w150">{{FunctionLib::viewLanguage('update')}}</th>
                                <th class="center w100">{{FunctionLib::viewLanguage('action')}}</th>
                            </tr>
                            </thead>
                            <tbody id="list_sms_template">
                            @foreach ($data as $key => $item)
                                <td class="text-center middle">{{$key+1 }}</td>
                                <td>{{$item['template_name']}}</td>
                                <td>{{ $item['content']}}</td>
                                <td>{{ $item['updated_date'] }}
                                </td>
                                <td class="center">
                                    <a onclick="edit_sms_template('{{FunctionLib::inputId($item['sms_template_id'])}}','{{$item['template_name']}}','{{$item['content']}}')"><i class="fa fa-pencil blue" aria-hidden="true"></i></a>
                                    <a onclick="delete_item('{{FunctionLib::inputId($item['sms_template_id'])}}')"><i class="fa fa-trash red" aria-hidden="true"></i></a>
                                </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    @else
                        <div class="alert">
                            {{FunctionLib::viewLanguage('no_data')}}
                        </div>
                    @endif
                </div> <!-- /widget-content -->
            </div> <!-- /widget -->
        </div>
        <div class="col-md-4 panel-content">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <h4><i class="fa fa-plus-square" aria-hidden="true"></i> {{\App\Library\AdminFunction\FunctionLib::viewLanguage('add_template_sms')}}</h4>
                </div> <!-- /widget-header -->
                <div class="panel-body">
                    <form id="form" method="post">
                        <input type="hidden" name="id" value="{{\App\Library\AdminFunction\FunctionLib::inputId(0)}}" class="form-control" id="id">
                        <div class="form-group">
                            <label for="name_template">{{\App\Library\AdminFunction\FunctionLib::viewLanguage('sms_template_name')}}</label>
                            <input type="" name="name_template" title="{{\App\Library\AdminFunction\FunctionLib::viewLanguage('sms_template_name')}}" class="form-control input-required" id="name_template">
                        </div>
                        <div class="form-group">
                            <label for="content">{{\App\Library\AdminFunction\FunctionLib::viewLanguage('sms_content_grafted')}}</label>
                            <textarea onkeyup="count_character(this)" name="content" style="resize: none" title="{{FunctionLib::viewLanguage('sms_content_grafted')}}" class="form-control input-required" rows="5" id="content"></textarea>
                        </div>
                        <span style="float: right" class="right">{{\App\Library\AdminFunction\FunctionLib::viewLanguage('sms_length')}}:<strong id="num_character" >0</strong></span>
                        <a class="btn btn-success" id="submit" onclick="add_sms_template()">Submit</a>
                        <a class="btn btn-default" id="cancel" onclick="reset()">Reset</a>
                    </form>
                </div> <!-- /widget-content -->
            </div>
        </div>
        <div class="row">
        </div>
    </div>
</div>
@stop
<script>
    $(document).ready(function () {
        setLang()
    })
    function reset() {
        $("#name_template").val("");
        $("#content").val("");
        $("#id").val('{{\App\Library\AdminFunction\FunctionLib::inputId(0)}}');
        $("#num_character").html(0)
    }
    function delete_item(id) {
        var a = confirm(lng['txt_mss_confirm_delete']);
        if (a){
            $.ajax({
                type: 'get',
                url: '/manager/smsTeplate/deleteTemplate',
                data: {
                    'id':id
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(data) {
                    if ((data.errors)) {
                        alert(data.errors)
                    }else {
                        $("#element").html(data.view)
                        reset();
                    }
                },
            });
        }
    }
    function add_sms_template() {
        var is_error = false;
        var msg = {};

        $("form#form :input").each(function(){
            var input = $(this); // This is the jquery object of the input, do what you will
            if ($(this).hasClass("input-required") && $(this).val() == "") {
                msg[$(this).attr("name")] = "※" + $(this).attr("title") + lng['is_required'];
                is_error = true;
            }
        });

        if (is_error == true) {
            var error_msg = "";
            $.each(msg, function (key, value) {
                error_msg = error_msg + value + "\n";
            });
//            error_msg += (str_is_sms !="")?str_is_sms:"";
            alert(error_msg);
            return false;
        }else {
            $("#submit").attr("disabled","true");
            var name_template = $("#name_template").val()
            var content = $("#content").val()
            var id = $("#id").val()
            $.ajax({
                type: 'post',
            url: '/manager/smsTeplate/addTemplate',
                data: {
                    'name_template':name_template,
                    'content':content,
                    'id':id
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(data) {
                    $('#submit').removeAttr("disabled")
                    if ((data.errors)) {
                        alert(data.errors)
                    }else {
                        $("#element").html(data.view)
                        reset();
                    }
                },
            });
        }
    }
    function count_character(event) {
        var length = $(event).val().length;
        $("#num_character").html(length)
    }
    function edit_sms_template(id,name,content) {
        $("#name_template").val(name);
        $("#content").val(content);
        $("#id").val(id);
    }
    $(document).ready(function(){
        $(".date-picker").datepicker({
            format: "yyyy-mm-dd",
            language: "vi",
            autoclose: true,
            keyboardNavigation:true
        })});

    var lng
    function setLang() {
        $.ajaxSetup({async: false});//同期通信(json取ってくるまで待つ)
        var lang = $("body").attr("lang");
        //alert(lang);
        $.getJSON("../../../../storage/language/" + lang + ".json", function (data) {
            lng = data;
        });
        $.ajaxSetup({async: true});
    }
</script>
<style>
    a:hover {
        cursor:pointer;
    }
</style>