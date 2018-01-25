<?php
/**
 * Created by JetBrains PhpStorm.
 * User: QuynhTM
 */
namespace App\Library\AdminFunction;

class ArrayPermission{
    public static $arrPermit = array(
        'root' => array('name_permit'=>'Quản trị site','group_permit'=>'Quản trị site'),//admin site
        /*'is_boss' => array('name_permit'=>'Boss','group_permit'=>'Boss'),//tech dùng quyen cao nhat*/

        'user_view' => array('name_permit'=>'Xem danh sách','group_permit'=>'Quản trị người dùng'),
        'user_create' => array('name_permit'=>'Tạo user','group_permit'=>'Quản trị người dùng'),
        'user_edit' => array('name_permit'=>'Sửa user','group_permit'=>'Quản trị người dùng'),
        'user_change_pass' => array('name_permit'=>'Thay đổi passs','group_permit'=>'Quản trị người dùng'),
        'user_remove' => array('name_permit'=>'Xóa user','group_permit'=>'Quản trị người dùng'),

        'group_user_view' => array('name_permit'=>'Xem nhóm quyền','group_permit'=>'Nhóm quyền'),
        'group_user_create' => array('name_permit'=>'Tạo nhóm quyền','group_permit'=>'Nhóm quyền'),
        'group_user_edit' => array('name_permit'=>'Sửa nhóm quyền','group_permit'=>'Nhóm quyền'),

        'permission_full' => array('name_permit'=>'Full tạo quyền','group_permit'=>'Tạo quyền'),
        'permission_create' => array('name_permit'=>'Tạo tạo quyền','group_permit'=>'Tạo quyền'),
        'permission_edit' => array('name_permit'=>'Sửa tạo quyền','group_permit'=>'Tạo quyền'),

        'waittingSms_full' => array('name_permit'=>'Full waittingSms','group_permit'=>'Quyền waittingSms'),
        'waittingSms_view' => array('name_permit'=>'Xem waittingSms','group_permit'=>'Quyền waittingSms'),
        'waittingSms_delete' => array('name_permit'=>'Xóa waittingSms','group_permit'=>'Quyền waittingSms'),
        'waittingSms_create' => array('name_permit'=>'Tạo waittingSms','group_permit'=>'Quyền waittingSms'),
        'waittingSms_edit' => array('name_permit'=>'Sửa waittingSms','group_permit'=>'Quyền waittingSms'),

        'systemSetting_full' => array('name_permit'=>'Full quyền','group_permit'=>'Cài đặt hệ thống'),//done
        'systemSetting_view' => array('name_permit'=>'Xem ','group_permit'=>'Cài đặt hệ thống'),
        'systemSetting_delete' => array('name_permit'=>'Xóa ','group_permit'=>'Cài đặt hệ thống'),
        'systemSetting_create' => array('name_permit'=>'Tạo ','group_permit'=>'Cài đặt hệ thống'),
        'systemSetting_edit' => array('name_permit'=>'Sửa ','group_permit'=>'Cài đặt hệ thống'),
        'permission_client_api_edit' => array('name_permit'=>'Sửa Client Api','group_permit'=>'Quyền Api'),
        'permission_customer_api_edit' => array('name_permit'=>'Sửa Customer Api','group_permit'=>'Quyền Api'),

        'stationSetting_full' => array('name_permit'=>'Full quyền','group_permit'=>'Cài đặt trạm'),
        'stationSetting_view' => array('name_permit'=>'Xem','group_permit'=>'Cài đặt trạm'),
        'stationSetting_delete' => array('name_permit'=>'Xóa','group_permit'=>'Cài đặt trạm'),
        'stationSetting_create' => array('name_permit'=>'Tạo','group_permit'=>'Cài đặt trạm'),
        'stationSetting_edit' => array('name_permit'=>'Sửa','group_permit'=>'Cài đặt trạm'),

        'sendSmsHistory_full' => array('name_permit'=>'Full sendSmsHistory','group_permit'=>'Quyền sendSmsHistory'),
        'sendSmsHistory_view' => array('name_permit'=>'Xem sendSmsHistory','group_permit'=>'Quyền sendSmsHistory'),
        'sendSmsHistory_delete' => array('name_permit'=>'Xóa sendSmsHistory','group_permit'=>'Quyền sendSmsHistory'),
        'sendSmsHistory_create' => array('name_permit'=>'Tạo sendSmsHistory','group_permit'=>'Quyền sendSmsHistory'),
        'sendSmsHistory_edit' => array('name_permit'=>'Sửa sendSmsHistory','group_permit'=>'Quyền sendSmsHistory'),

        'sendSmsTemplate_full' => array('name_permit'=>'Full quyền','group_permit'=>'Tin nhắn mẫu'),//done
        'sendSmsTemplate_view' => array('name_permit'=>'Xem','group_permit'=>'Tin nhắn mẫu'),
        'sendSmsTemplate_delete' => array('name_permit'=>'Xóa','group_permit'=>'Tin nhắn mẫu'),
        'sendSmsTemplate_create' => array('name_permit'=>'Tạo','group_permit'=>'Tin nhắn mẫu'),
        'sendSmsTemplate_edit' => array('name_permit'=>'Sửa','group_permit'=>'Tin nhắn mẫu'),

        'sendSms_full' => array('name_permit'=>'Full quyền','group_permit'=>'Quyền gửi Sms'),//done
        'sendSms_delete' => array('name_permit'=>'Xóa','group_permit'=>'Quyền gửi Sms'),
        'sendSms_create' => array('name_permit'=>'Tạo','group_permit'=>'Quyền gửi Sms'),
        'sendSms_edit' => array('name_permit'=>'Sửa','group_permit'=>'Quyền gửi Sms'),

        'sendSmsClever_full' => array('name_permit'=>'Full SMS thông minh','group_permit'=>'Quyền SMS thông minh'),
        'sendSmsClever_delete' => array('name_permit'=>'Xóa SMS thông minh','group_permit'=>'Quyền SMS thông minh'),
        'sendSmsClever_create' => array('name_permit'=>'Tạo SMS thông minh','group_permit'=>'Quyền SMS thông minh'),
        'sendSmsClever_edit' => array('name_permit'=>'Sửa SMS thông minh','group_permit'=>'Quyền SMS thông minh'),

        'stationReport_full' => array('name_permit'=>'Full reportChart','group_permit'=>'Quyền reportChart'),
        'stationReport_view' => array('name_permit'=>'Xem reportChart','group_permit'=>'Quyền reportChart'),

        'reportChart_full' => array('name_permit'=>'Full stationSetting','group_permit'=>'Quyền stationSetting'),
        'reportChart_view' => array('name_permit'=>'Xem stationSetting','group_permit'=>'Quyền stationSetting'),

        'modem_full' => array('name_permit'=>'Full quyền','group_permit'=>'Thông kê trạm'),//done
        'modem_view' => array('name_permit'=>'Xem','group_permit'=>'Thông kê trạm'),
        'modem_delete' => array('name_permit'=>'Xóa','group_permit'=>'Thông kê trạm'),
        'modem_create' => array('name_permit'=>'Tạo','group_permit'=>'Thông kê trạm'),
        'modem_edit' => array('name_permit'=>'Sửa','group_permit'=>'Thông kê trạm'),

        'menu_full' => array('name_permit'=>'Full menu','group_permit'=>'Quyền menu'),
        'menu_view' => array('name_permit'=>'Xem menu','group_permit'=>'Quyền menu'),
        'menu_delete' => array('name_permit'=>'Xóa menu','group_permit'=>'Quyền menu'),
        'menu_create' => array('name_permit'=>'Tạo menu','group_permit'=>'Quyền menu'),
        'menu_edit' => array('name_permit'=>'Sửa menu','group_permit'=>'Quyền menu'),

        'deviceToken_full' => array('name_permit'=>'Full quyền','group_permit'=>'Cài đặt thiết bị'),//done
        'deviceToken_view' => array('name_permit'=>'Xem','group_permit'=>'Cài đặt thiết bị'),
        'deviceToken_delete' => array('name_permit'=>'Xóa','group_permit'=>'Cài đặt thiết bị'),
        'deviceToken_create' => array('name_permit'=>'Tạo','group_permit'=>'Cài đặt thiết bị'),
        'deviceToken_edit' => array('name_permit'=>'Sửa','group_permit'=>'Cài đặt thiết bị'),

        'carrierSetting_full' => array('name_permit'=>'Full','group_permit'=>'Quản lý nhà mạng'),//done
        'carrierSetting_view' => array('name_permit'=>'Xem','group_permit'=>'Quản lý nhà mạng'),
        'carrierSetting_delete' => array('name_permit'=>'Xóa','group_permit'=>'Quản lý nhà mạng'),
        'carrierSetting_create' => array('name_permit'=>'Tạo','group_permit'=>'Quản lý nhà mạng'),
        'carrierSetting_edit' => array('name_permit'=>'Sửa','group_permit'=>'Quản lý nhà mạng'),

        'stationList_view' => array('name_permit'=>'Xem','group_permit'=>'Danh sách trạm'),//done
        'stationList_full' => array('name_permit'=>'Full quyền','group_permit'=>'Danh sách trạm'),

    );

}