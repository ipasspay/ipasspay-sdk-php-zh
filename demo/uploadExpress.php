<?php

//require '../vendor/autoload.php';//如果是使用composer获取的sdk，并不使用任何框架的话，注意添加此句，路径应可访问到vendor下的autoload.php
//如果是直接获得的zip包，请自行将src下的文件放入项目，保证可以正确引用sdk包

use Ipasspay\IpasspayChannel\config\IpasspayConfig;
use Ipasspay\IpasspayChannel\service\IpasspayService;

    //对接ipasspay的上传运单号

    //可调用sdk完成加密和通讯。具体参数说明请参见api文档
    //注意：merchant_id、app_id、api_secret都在IpasspayConfig中进行配置，这里无需填写
    //使用sdk的话，timestamp无需考虑
    //使用sdk的话，签名和验签过程无需考虑
    $request_data['gateway_order_no']='11024995227413376';//ipasspay订单号
    $request_data['express_company']='DHL';
    $request_data['express_no']='12345678';//物流公司运单号
    //------------------------------------

    //尝试发起上传运单号请求，只需进行如下调用
    $ipasspay_service=new IpasspayService('sandbox');//env可以为live或sandbox对应相应的配置，缺省为live
    if (!$ipasspay_service->uploadExpress($request_data)) {
        //请求异常，可以通过以下方法知道错误原因，请完成您系统中的相应处理......
        echo '错误编码为'.$ipasspay_service->getErrorCode()."\n";
        echo '错误原因为'.$ipasspay_service->getErrorMsg()."\n";
        exit;
    }

    //请求成功，可通过响应数据进行处理
    echo 'HTTP状态码为'.$ipasspay_service->getResponseHttpStatus()."\n";
    echo '结果状态码为'.$ipasspay_service->getResponseCode()."\n";
    echo '结果描述为'.$ipasspay_service->getResponseMsg()."\n";
    echo '结果数据为'.json_encode($ipasspay_service->getResponseData(),JSON_UNESCAPED_UNICODE+JSON_UNESCAPED_SLASHES)."\n";
    //建议先判断结果状态码，如果是成功再取结果数据做相应业务处理。HTTP状态码可以根据需要做更加严谨的判断或记录
    switch ($ipasspay_service->getResponseCode()) {
        case IpasspayConfig::RESPONSE_CODE['SUCCESS']:
            //说明请求返回结果正常（注：上传运单号结果无签名）
            $response_data=$ipasspay_service->getResponseData();//数组
            //做相应业务处理......
            break;
        case IpasspayConfig::RESPONSE_CODE['REQUEST FAIL']:
        case IpasspayConfig::RESPONSE_CODE['INVALID PARAMETER']:
        default:
            //理论上都是异常，按具体情况进行处理......
            //可以查看具体的出错信息
            echo $ipasspay_service->getResponseMsg();
            break;
    }
    //---------------------------------

    //如果出现问题，可以通过以下方法来获得相应数据，可与ipasspay技术人员进行核对，也可用于日志记录
    //echo '请求地址为'.$ipasspay_service->getRequestUrl()."\n";
    //echo '请求数据为'.json_encode($ipasspay_service->getRequestData(),JSON_UNESCAPED_UNICODE+JSON_UNESCAPED_SLASHES)."\n";
    //echo '响应原始数据为'.json_encode($ipasspay_service->getResponseOriginData(),JSON_UNESCAPED_UNICODE+JSON_UNESCAPED_SLASHES)."\n";
    //echo '签名字符串为'.$ipasspay_service->getSignString()."\n";
    //echo '签名为'.$ipasspay_service->getSign()."\n";
    exit;
