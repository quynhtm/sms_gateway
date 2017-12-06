<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseAdminController;
use App\Http\Models\User;
use App\Http\Models\SmsReport;
use App\Library\AdminFunction\FunctionLib;
use App\Library\AdminFunction\CGlobal;
use App\Library\AdminFunction\Define;
use App\Library\AdminFunction\Pagging;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Response;

class AdminSMSReportChartController extends BaseAdminController
{
    private $permission_view = 'stationReport_view';
    private $permission_full = 'stationReport_full';
//    private $permission_delete = 'carrierSetting_delete';
//    private $permission_create = 'carrierSetting_create';
//    private $permission_edit = 'carrierSetting_edit';

    private $arrManager = array();
    private $arrStatus = array();
    private $error = array();
    private $viewPermission = array();//check quyen

    public function __construct()
    {
        parent::__construct();
        CGlobal::$pageAdminTitle = 'Quản lý menu';
        $this->getDataDefault();

        FunctionLib::link_js(array(
            'lib/highcharts/highcharts.js',
            'lib/highcharts/highcharts-3d.js',
            'lib/highcharts/exporting.js',
        ));
    }

    public function getDataDefault()
    {
        $this->arrManager = User::getOptionUserFullNameAndMail();
        $this->arrStatus = array(
            CGlobal::active => FunctionLib::controLanguage('active',$this->languageSite),
            CGlobal::not_active => FunctionLib::controLanguage('not_active',$this->languageSite)
        );
    }

    public function getPermissionPage(){
        return $this->viewPermission = [
            'is_root'=> $this->is_root ? 1:0,
//            'permission_edit'=>in_array($this->permission_edit, $this->permission) ? 1 : 0,
//            'permission_create'=>in_array($this->permission_create, $this->permission) ? 1 : 0,
//            'permission_delete'=>in_array($this->permission_delete, $this->permission) ? 1 : 0,
            'permission_full'=>in_array($this->permission_full, $this->permission) ? 1 : 0,
        ];
    }

    public function view() {
        //Check phan quyen.
        if(!$this->is_root && !in_array($this->permission_full,$this->permission)&& !in_array($this->permission_view,$this->permission)){
            return Redirect::route('admin.dashboard',array('error'=>Define::ERROR_PERMISSION));
        }
        $number_day_of_month = cal_days_in_month(CAL_GREGORIAN,2,2017);
        $arrDay = array();
        for($i =1;$i<=$number_day_of_month;$i++){
            $arrDay[] = $i;
        }

        $sql = "SELECT wsr.sms_report_id,wsr.carrier_id,wsr.day,wsr.month,wsr.success_number,wsr.fail_number,(wsr.success_number + wsr.fail_number) as num_mess,wcs.carrier_name from web_sms_report wsr INNER JOIN web_carrier_setting wcs ON wsr.carrier_id = wcs.carrier_setting_id";
        $data = SmsReport::executesSQL($sql);
        foreach ($data as $k => $v){
            $data[$k] = (array)$v;
            
        }
        FunctionLib::debug($data);

        $page_no = (int) Request::get('page_no',1);
        $sbmValue = Request::get('submit', 1);
        $dataSearch['station_account'] = addslashes(Request::get('station_account',''));
        $total = 0;

        $optionUser = FunctionLib::getOption(array(''=>'---'.FunctionLib::controLanguage('select_user',$this->languageSite).'---')+$this->arrManager, (isset($dataSearch['station_account'])?$dataSearch['station_account']:0));
        $this->getDataDefault();
        $this->viewPermission = $this->getPermissionPage();
        return view('admin.AdminReportChart.view',array_merge([
            'data'=>array(),
            'search'=>$dataSearch,
            'size'=>$total,
            'optionUser'=>$optionUser,
        ],$this->viewPermission));
    }
}