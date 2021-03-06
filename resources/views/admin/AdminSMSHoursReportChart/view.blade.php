{{--<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>--}}
{{--<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>--}}
<?php use App\Library\AdminFunction\FunctionLib; ?>
<?php use App\Library\AdminFunction\Define; ?>
@extends('admin.AdminLayouts.index')
@section('content')
    <div class="main-content-inner">
        <div class="breadcrumbs breadcrumbs-fixed" id="breadcrumbs">
            <ul class="breadcrumb">
                <li>
                    <i class="ace-icon fa fa-home home-icon"></i>
                    <a href="{{URL::route('admin.dashboard')}}">{{FunctionLib::viewLanguage('home')}}</a>
                </li>
                <li class="active">{{FunctionLib::viewLanguage('send_sms_chart')}}</li>
                <li class="active">{{FunctionLib::viewLanguage('SMS_quality_by_hour')}}</li>
            </ul><!-- /.breadcrumb -->
        </div>

        <div class="page-content">
            <div class="row">
                <div class="col-xs-12">
                    <!-- PAGE CONTENT BEGINS -->
                    @if($is_root || $permission_full ==1)
                        <div class="panel panel-info">
                            {{ Form::open(array('method' => 'GET', 'role'=>'form')) }}
                            <div class="panel-body">
                                @if($user_role_type==\App\Library\AdminFunction\Define::ROLE_TYPE_SUPER_ADMIN)
                                    <div class="col-sm-2">
                                        <label for="type_report">{{FunctionLib::viewLanguage('report_type')}}</label>
                                        <select onchange="show_opt_user()" name="type_report" id="type_report"
                                                class="form-control input-sm">
                                            {!! $optionTypeReort !!}
                                        </select>
                                    </div>
                                    <div class="col-sm-2">
                                        <label for="station_account">{{FunctionLib::viewLanguage('user_name')}}</label>
                                        <select name="station_account1" id="station_account1"
                                                class="form-control input-sm">
                                            {!! $optionUser_station !!}
                                        </select>
                                        <select name="station_account2" id="station_account2"
                                                class="form-control input-sm hide">
                                            {!! $optionUser_customer !!}
                                        </select>
                                    </div>
                                @endif
                                <div class="col-sm-2">
                                    <label for="carrier_id">{{FunctionLib::viewLanguage('choose_carrier')}}</label>
                                    <select name="carrier_id" id="carrier_id" class="form-control input-sm">
                                        {!! $optionCarrier !!}
                                    </select>
                                </div>
                                <div class="form-group col-lg-3">
                                    <label for="day">{{FunctionLib::viewLanguage('choose_date')}}</label>
                                    <input type="text" class="form-control input-sm date-picker" name="day"
                                           autocomplete="off"
                                           @if(isset($search['day']))value="{{$search['day']}}"@endif>
                                </div>
                                <div class="col-sm-2">
                                    <label for="hours">{{FunctionLib::viewLanguage('divide_by_hour')}}</label>
                                    <select name="hours" id="hours" class="form-control input-sm">
                                        {!! $optionHours !!}
                                    </select>
                                </div>
                                <div class="form-group col-lg-12 text-right">
                                    <button class="btn btn-primary btn-sm" type="submit" name="submit" value="1"><i
                                                class="fa fa-search"></i> {{FunctionLib::viewLanguage('search')}}
                                    </button>
                                </div>
                            </div>
                            {{ Form::close() }}
                        </div>
                    @endif
                    @if(!empty($data))
                        <div id="container"
                             style="min-width: 310px; height: 400px; max-width: 800px; margin: 0 auto">
                        </div>
                    @else
                        <div class="alert">
                            {{FunctionLib::viewLanguage('no_data')}}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        $(document).ready(function () {
            $(".date-picker").datepicker({
                format: "dd-mm-YYYY",
                language: "vi",
                autoclose: true,
                keyboardNavigation: true
            })
            show_opt_user();
        });

        function show_opt_user(){
            if($("#type_report").val() == "2"){
                $("#station_account2").removeClass( 'hide' );
                $("#station_account1").addClass( 'hide' );
            }else{
                $("#station_account1").removeClass( 'hide' );
                $("#station_account2").addClass( 'hide' );
            }
        }

        $(function () {

            $('#container').highcharts({
                chart: {
                    type: 'column'
                },
                title: {
                    text: '{{FunctionLib::viewLanguage('report_by_hour')}}'
                },
                xAxis: {
                    type: 'category'
                },
                yAxis: {
                    title: {
                        text: 'Values'
                    }

                },
                legend: {
                    enabled: false
                },
                plotOptions: {
                    series: {
                        borderWidth: 0,
                        dataLabels: {
                            enabled: true,
                            format: '{point.y}'
                        }
                    }
                },

                tooltip: {
                    pointFormat: '<b>{point.y}</b> of total<br/>' +
                    '<b>{point.success}</b> of success <br/>' +
                    '<b>{point.success_per:.1f}%</b> success <br/>' +
                    '<b>{point.total_cost:.1f}</b> total cost <br/>'
                },
                series: [
                    {
                        name: 'Brands',
                        colorByPoint: true,
                        data: [
                            <?php
                            foreach ($data as $v) {
                                echo "{
                            name:'" . $v['range_time'] . "',
                            y:{$v['total_sms_hour']},
                            success:{$v['total_sms_success']},
                            success_per:{$v['success_percent']},
                            total_cost:{$v['total_cost']}
                            },";
                            }
                            ?>
                        ]
                    }
                ]
            });
        });
    </script>
@stop