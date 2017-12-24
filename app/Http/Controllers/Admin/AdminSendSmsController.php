<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseAdminController;
use App\Http\Models\CarrierSetting;
use App\Http\Models\MenuSystem;
use App\Http\Models\SmsCustomer;
use App\Http\Models\SmsLog;
use App\Http\Models\SmsSendTo;
use App\Library\AdminFunction\FunctionLib;
use App\Library\AdminFunction\CGlobal;
use App\Library\AdminFunction\Define;
use App\Library\AdminFunction\Pagging;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;


use PHPExcel;
use PHPExcel_IOFactory;
use PHPExcel_Worksheet_PageSetup;
use PHPExcel_Style_Alignment;
use PHPExcel_Style_Fill;
use PHPExcel_Style_Border;
class AdminSendSmsController extends BaseAdminController
{
    private $permission_view = 'sendSms_view';
    private $permission_full = 'sendSms_full';
    private $permission_delete = 'sendSms_delete';
    private $permission_create = 'sendSms_create';
    private $permission_edit = 'sendSms_edit';
    private $arrStatus = array();
    private $error = array();
    private $arrMenuParent = array();
    private $viewPermission = array();//check quyen

    public function __construct()
    {
        parent::__construct();
        $this->arrMenuParent = MenuSystem::getAllParentMenu();
        CGlobal::$pageAdminTitle = 'Send SMS';
        FunctionLib::link_js(array(
            'lib/highcharts/highcharts.js',
            'lib/highcharts/highcharts-3d.js',
            'lib/highcharts/exporting.js',
        ));
    }

    public function getDataDefault(){
        $this->arrStatus = array(
            CGlobal::status_block => FunctionLib::controLanguage('status_choose',$this->languageSite),
            CGlobal::status_show => FunctionLib::controLanguage('status_show',$this->languageSite),
            CGlobal::status_hide => FunctionLib::controLanguage('status_hidden',$this->languageSite));
    }

    public function getPermissionPage(){
        return $this->viewPermission = [
            'is_root'=> $this->is_root ? 1:0,
            'permission_edit'=>in_array($this->permission_edit, $this->permission) ? 1 : 0,
            'permission_create'=>in_array($this->permission_create, $this->permission) ? 1 : 0,
            'permission_delete'=>in_array($this->permission_delete, $this->permission) ? 1 : 0,
            'permission_full'=>in_array($this->permission_full, $this->permission) ? 1 : 0,
        ];
    }

    public function getSendSms() {
        if(!$this->is_root && !in_array($this->permission_full,$this->permission) && !in_array($this->permission_edit,$this->permission) && !in_array($this->permission_create,$this->permission)){
            return Redirect::route('admin.dashboard',array('error'=>Define::ERROR_PERMISSION));
        }
        $data = array();
        $this->viewPermission = $this->getPermissionPage();
        return view('admin.AdminSendSms.add',array_merge([
            'data'=>$data,
            'id'=>0,
            'error'=>$this->error,
            'arrStatus'=>$this->arrStatus,
        ],$this->viewPermission));
    }

    public function postSendSms() {
        if(!$this->is_root && !in_array($this->permission_full,$this->permission) && !in_array($this->permission_edit,$this->permission) && !in_array($this->permission_create,$this->permission)){
            return Redirect::route('admin.dashboard',array('error'=>Define::ERROR_PERMISSION));
        }
        $data = $_POST;

        //check số hợp lệ
        $dataSend = array();
        $dataPhone = array();
        $dataCarriesInput = array();
        //nội dung tin nhắn
        $contenstSms = trim($data['sms_content']);
        $send_sms_deadline = (trim($data['send_sms_deadline']) != '')? $data['send_sms_deadline']:'';
        $arr_numberFone = (trim($data['phone_number']) != '')? explode(',',trim($data['phone_number'])): array();

        if(!empty($arr_numberFone)){
            foreach ($arr_numberFone as $k =>$number){
                $checkNumber = FunctionLib::checkNumberPhone($number);
                if($checkNumber > 0){
                    $dataPhone[] = trim($checkNumber);
                }else{
                    $this->error[] = trim($number).' not number phone';
                }
            }
        }else{
            $this->error[] = FunctionLib::controLanguage('phone_number',$this->languageSite).' null';
        }



        //đẩy dữ liệu theo nhà mạng
        if( empty($this->error)){
            $carrier = CarrierSetting::getInfoCarrier();
            $arrNumberCarries = array();
            foreach ($carrier as $kc=>$val_carr) {
                $number_firsr = (trim($val_carr['first_number']) != '') ? explode(',', $val_carr['first_number']) : array();
                if(!empty($number_firsr)){
                    foreach ($number_firsr as $kk =>$number_firsr_carr){
                        $arrNumberCarries[$number_firsr_carr] = array(
                            'first_number'=>$number_firsr_carr,
                            'carrier_id'=>$val_carr['carrier_setting_id'],
                            'carrier_name'=>$val_carr['carrier_name'],
                            'slipt_number'=>$val_carr['slipt_number'],
                            'min_number'=>$val_carr['min_number'],
                            'max_number'=>$val_carr['max_number']);
                    }
                }
            }


            //check số có phù hợp với nhà mạng
            if(!empty($carrier)){
                $arrMsg = array();
                $infoPhone = array();
                foreach ($dataPhone as $kkk=>$phone_number){
                    $lenghtNumber = strlen($phone_number);
                    foreach ($arrNumberCarries as $kk =>$dauso){
                        $pos = strpos(trim($phone_number), trim($dauso['first_number']));
                        if($pos === 0){
                            if($dauso['min_number'] >= $lenghtNumber || $lenghtNumber <= $dauso['max_number']){
                                $infoPhone[trim($phone_number)] = array(
                                    'phone_number'=>$phone_number,
                                    'lenght'=>strlen($phone_number),
                                    'slipt_number'=>$dauso['slipt_number'],
                                    'carrier_id'=>$dauso['carrier_id'],
                                    'carrier_name'=>$dauso['carrier_name']);
                                $arrMsg[$dauso['carrier_id']] = FunctionLib::splitStringSms($contenstSms,$dauso['slipt_number']);
                            }else{
                                $this->error[] = trim($phone_number).' not valiable';
                            }
                        }
                    }
                    if(!empty($infoPhone) && !in_array(trim($phone_number),array_keys($infoPhone))){
                        $this->error[] = trim($phone_number).' not number first';
                    }
                }
                //ghep data
                if(!empty($infoPhone) && !empty($arrMsg)){
                    foreach ($infoPhone as $k=>$phone){
                        foreach ($arrMsg[$phone['carrier_id']] as $kk =>$msgSms){
                            $dataSend[] = array(
                                'phone_number'=>$phone['phone_number'],
                                'content'=>$msgSms,
                                'carrier_id'=>$phone['carrier_id'],
                                'carrier_name'=>$phone['carrier_name']);

                            $dataCarriesInput[$phone['carrier_id']] = array(
                                'carrier_id'=>$phone['carrier_id'],
                                'carrier_name'=>$phone['carrier_name']);
                        }
                    }
                }

            }
        }
        //FunctionLib::debug($dataSend);
        if(!empty($dataSend) && empty($this->error)) {
            //get tổng send SMS theo nhà mạng
            foreach ($dataSend as $kkk=>$valu){
                if(isset($dataCarriesInput[$valu['carrier_id']]['tong_sms'])){
                    $dataCarriesInput[$valu['carrier_id']]['tong_sms'] = $dataCarriesInput[$valu['carrier_id']]['tong_sms']+1;
                }else{
                    $dataCarriesInput[$valu['carrier_id']]['tong_sms'] = 1;
                }
            }

            //web_sms_customer
            $dataInsertSmsCustomer = array(
                'user_customer_id'=>$this->user_id,
                'status'=>Define::SMS_STATUS_PROCESSING,
                'status_name'=>Define::$arrSmsStatus[Define::SMS_STATUS_PROCESSING],
                'correct_number'=>count($dataSend),
                'sms_deadline'=>FunctionLib::getDateTime($send_sms_deadline),
                'created_date'=>FunctionLib::getDateTime(),);
            if(trim($send_sms_deadline) != ''){
                $dataInsertSmsCustomer['sms_deadline'] = FunctionLib::getDateTime($send_sms_deadline);
            }
            $sms_customer_id = SmsCustomer::createItem($dataInsertSmsCustomer);

            //web_sms_log: bao nhiêu nhà mạng thì co bấy nhiêu bản ghi
            foreach ($dataCarriesInput as $carrier_id =>&$val_carr){
                $dataInsertSmsLog = array(
                    'user_customer_id'=>$this->user_id,
                    'user_manager_id'=>0,
                    'sms_customer_id'=>$sms_customer_id,
                    'carrier_id'=>$val_carr['carrier_id'],
                    'carrier_name'=>$val_carr['carrier_name'],
                    'total_sms'=>$val_carr['tong_sms'],
                    'status'=>Define::SMS_STATUS_PROCESSING,
                    'status_name'=>Define::$arrSmsStatus[Define::SMS_STATUS_PROCESSING],
                    'send_date'=>FunctionLib::getIntDate(),
                    'sms_deadline'=>FunctionLib::getDateTime($send_sms_deadline),
                    'created_date'=>FunctionLib::getDateTime(),);
                $sms_log_id = SmsLog::createItem($dataInsertSmsLog);
                $val_carr['sms_log_id'] = $sms_log_id;
            }

            //web_sms_sendTo
            $dataInsertSmsSendTo = array();
            foreach ($dataSend as $kk=>$val){
                $dataInsertSmsSendTo[] = array(
                    'sms_log_id'=>isset($dataCarriesInput[$val['carrier_id']]['sms_log_id']) ? $dataCarriesInput[$val['carrier_id']]['sms_log_id']: 0,
                    'sms_customer_id'=>$sms_customer_id,
                    'user_customer_id'=>$this->user_id,
                    'carrier_id'=>$val['carrier_id'],
                    'phone_receive'=>$val['phone_number'],
                    'status'=>Define::SMS_STATUS_PROCESSING,
                    'status_name'=>Define::$arrSmsStatus[Define::SMS_STATUS_PROCESSING],
                    'content'=>$val['content'],
                    'content_grafted'=>$val['content'],
                    'created_date'=>FunctionLib::getDateTime(),
                    );
            }
            if(!empty($dataInsertSmsSendTo)){
                SmsSendTo::insertMultiple($dataInsertSmsSendTo);
            }
            $this->error[] = 'Danh sách tin nhắn đang được chờ xử lý';
            $data = array();
        }

        $this->viewPermission = $this->getPermissionPage();
        return view('admin.AdminSendSms.add',array_merge([
            'data'=>$data,
            'id'=>0,
            'error'=>$this->error,
            'arrStatus'=>$this->arrStatus,
        ],$this->viewPermission));
    }

    //ajax
    public function uploadFileExcelPhone(){
        $file_excel_phone = Request::file('file_excel_phone');
        FunctionLib::debug($file_excel_phone);
    }

    public function exportData($data) {
        if(empty($data)){
            return;
        }
        //FunctionLib::debug($data);

        $objPHPExcel = new PHPExcel();
        $objPHPExcel->setActiveSheetIndex(0);
        $sheet = $objPHPExcel->getActiveSheet();

        // Set Orientation, size and scaling
        $sheet->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_PORTRAIT);
        $sheet->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
        $sheet->getPageSetup()->setFitToPage(true);
        $sheet->getPageSetup()->setFitToWidth(1);
        $sheet->getPageSetup()->setFitToHeight(0);

        // Set font
        $sheet->getDefaultStyle()->getFont()->setName('Arial')->setSize(10);
        $sheet->getStyle('A1')->getFont()->setSize(16)->setBold(true)->getColor()->setRGB('000000');
        $sheet->mergeCells('A1:K1');
        $sheet->setCellValue("A1", "Danh sách các phiên làm việc ngày ".date('d-m-Y H:i'));
        $sheet->getRowDimension("1")->setRowHeight(32);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
            ->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

        // setting header
        $position_hearder = 3;
        $sheet->getRowDimension($position_hearder)->setRowHeight(30);
        $val10 = 10; $val18 = 18; $val35 = 35;$val45 = 45; $val25 = 25;$val55 = 55;
        $ary_cell = array(
            'A'=>array('w'=>$val10,'val'=>'STT','align'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
            'B'=>array('w'=>$val18,'val'=>'Case Number','align'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
            'C'=>array('w'=>$val35,'val'=>'Trạng thái','align'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER),

            'D'=>array('w'=>$val35,'val'=>'Tên quy trình','align'=>PHPExcel_Style_Alignment::HORIZONTAL_LEFT),
            'E'=>array('w'=>$val55,'val'=>'Task hiện tại','align'=>PHPExcel_Style_Alignment::HORIZONTAL_LEFT),

            'F'=>array('w'=>$val25,'val'=>'Assignee hiện tại','align'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
            'G'=>array('w'=>$val45,'val'=>'Group user','align'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
            'H'=>array('w'=>$val25,'val'=>'Thời điểm tạo case','align'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
            'I'=>array('w'=>$val45,'val'=>'Họ tên khách hàng','align'=>PHPExcel_Style_Alignment::HORIZONTAL_LEFT),

            'J'=>array('w'=>$val10,'val'=>'Mã CK','align'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
            'K'=>array('w'=>$val18,'val'=>'Số lượng','align'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
            /*'K'=>array('w'=>$val18,'val'=>'Hạn mức tiền hàng','align'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
            'L'=>array('w'=>$val35,'val'=>'Thông tin sale','align'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
            'M'=>array('w'=>$val45,'val'=>'Thông tin Chuyển khoản','align'=>PHPExcel_Style_Alignment::HORIZONTAL_CENTER),*/
            //'J'=>array('w'=>$val45,'val'=>'Thông tin thanh toán','align'=>PHPExcel_Style_Alignment::HORIZONTAL_RIGHT),
        );

        //build header title
        foreach($ary_cell as $col => $attr){
            $sheet->getColumnDimension($col)->setWidth($attr['w']);
            $sheet->setCellValue("$col{$position_hearder}",$attr['val']);
            $sheet->getStyle($col)->getAlignment()->setWrapText(true);
            $sheet->getStyle($col . $position_hearder)->applyFromArray(
                array(
                    'fill' => array(
                        'type' => PHPExcel_Style_Fill::FILL_SOLID,
                        'color' => array('rgb' => '05729C'),
                        'style' => array('font-weight' => 'bold')
                    ),
                    'font'  => array(
                        'bold'  => true,
                        'color' => array('rgb' => 'FFFFFF'),
                        'size'  => 10,
                        'name'  => 'Verdana'
                    ),
                    'borders' => array(
                        'allborders' => array(
                            'style' => PHPExcel_Style_Border::BORDER_THIN,
                            'color' => array('rgb' => '333333')
                        )
                    ),
                    'alignment' => array(
                        'horizontal' => $attr['align'],
                    )
                )
            );
        }
        //hien thị dũ liệu
        $rowCount = $position_hearder+1; // hang bat dau xuat du lieu
        $i = 1;
        $break="\r";

        foreach ($data as $k => $v) {
            $sheet->getRowDimension($rowCount)->setRowHeight(50);//chiều cao của row

            $sheet->getStyle('A' . $rowCount)->getAlignment()->applyFromArray(
                array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,));
            $sheet->SetCellValue('A' . $rowCount, $i);

            $sheet->getStyle('B' . $rowCount)->getAlignment()->applyFromArray(array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,));
            $sheet->SetCellValue('B' . $rowCount, $v['CaseNo']);

            $sheet->getStyle('C' . $rowCount)->getAlignment()->applyFromArray(array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,));
            $sheet->SetCellValue('C' . $rowCount, (isset($this->arrStatus[$v['State']]))? $this->arrStatus[$v['State']]:'');

            $sheet->getStyle('D' . $rowCount)->getAlignment()->applyFromArray(array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,));
            $sheet->SetCellValue('D' . $rowCount, $v['Workflow']);

            $sheet->getStyle('E' . $rowCount)->getAlignment()->applyFromArray(array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,));
            $sheet->SetCellValue('E' . $rowCount, $v['Description']);

            $sheet->getStyle('F' . $rowCount)->getAlignment()->applyFromArray(array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,));
            $sheet->SetCellValue('F' . $rowCount, $v['Author']);

            $sheet->getStyle('G' . $rowCount)->getAlignment()->applyFromArray(array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,));
            $sheet->SetCellValue('G' . $rowCount, $v['GRP_TITLE']);

            $sheet->getStyle('H' . $rowCount)->getAlignment()->applyFromArray(array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,));
            $sheet->SetCellValue('H' . $rowCount, $v['CreateDate']);

            $sheet->getStyle('I' . $rowCount)->getAlignment()->applyFromArray(array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,));
            $stringCustomer = $v['FullName'];
            if(isset($v['TransactionAccounts']) && $v['TransactionAccounts'] != ''){
                $stringCustomer .= $break."Số TK: ".$v['TransactionAccounts'];
            }
            if(isset($v['SecuritiesDepositoryAcc']) && $v['SecuritiesDepositoryAcc'] != ''){
                $stringCustomer .= $break."Số LK: ".$v['SecuritiesDepositoryAcc'];
            }
            if(isset($v['IdNumber']) && $v['IdNumber'] != ''){
                $stringCustomer .= $break."Số CMT: ".$v['IdNumber'];
            }
            $sheet->SetCellValue('I' . $rowCount, $stringCustomer);

            $sheet->getStyle('J' . $rowCount)->getAlignment()->applyFromArray(array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,));
            $sheet->SetCellValue('J' . $rowCount, $v['SecuritiesCode']);

            $sheet->getStyle('K' . $rowCount)->getAlignment()->applyFromArray(array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,));
            $sheet->SetCellValue('K' . $rowCount, $v['TotalSecuritiesDepository']);

            $rowCount++;
            $i++;
        }

        // output file
        ob_clean();
        $filename = "Danh_sach_NCC" . "_" . date("_d/m_") . '.xls';
        @header("Cache-Control: ");
        @header("Pragma: ");
        @header("Content-type: application/octet-stream");
        @header("Content-Disposition: attachment; filename=\"{$filename}\"");

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save("php://output");
        exit();
    }

}
