<?php

//require '../vendor/autoload.php';//如果是使用composer获取的sdk，并不使用任何框架的话，注意添加此句，路径应可访问到vendor下的autoload.php
//如果是直接获得的zip包，请自行将src下的文件放入项目，保证可以正确引用sdk包

use Ipasspay\AirsecurecardChannel\config\AirsecurecardConstant;
use Ipasspay\AirsecurecardChannel\service\AirsecurecardService;

    //对接airsecurecard的订单查询

    //可调用sdk完成加密和通讯。具体参数说明请参见api文档
    //注意：merchant_id、app_id、api_secret都在AirsecurecardConfig中进行配置，这里无需填写
    //使用sdk的话，timestamp无需考虑
    //使用sdk的话，签名和验签过程无需考虑
    $request_data['order_no']='1643273266828';//请求时的订单号
    //------------------------------------

    //进行订单查询请求，只需进行如下调用
    $airsecurecard_service=new AirsecurecardService('sandbox');//env可以为live或sandbox对应相应的配置，缺省为live

    //注：如果需要通过程序动态进行商户信息的配置，可以在进行请求时使用setConfig($config)来改变AirsecurecardConfig.php文件里配置的数据
    /*$config['merchant_id']='111111';
    $config['app_id']='222222';
    $config['api_secret']='333333';
    if (!$airsecurecard_service->setConfig($config)->queryOrder($request_data)) {*/
    if (!$airsecurecard_service->queryOrder($request_data)) {
        //请求异常，可以通过以下方法知道错误原因，请完成您系统中的相应处理......
        echo '错误编码为'.$airsecurecard_service->getErrorCode()."\n";
        echo '错误原因为'.$airsecurecard_service->getErrorMsg()."\n";
        exit;
    }

    //请求成功，可通过响应数据进行处理
    echo 'HTTP状态码为'.$airsecurecard_service->getResponseHttpStatus()."\n";
    echo '结果状态码为'.$airsecurecard_service->getResponseCode()."\n";
    echo '结果描述为'.$airsecurecard_service->getResponseMsg()."\n";
    echo '结果数据为'.json_encode($airsecurecard_service->getResponseData(),JSON_UNESCAPED_UNICODE+JSON_UNESCAPED_SLASHES)."\n";
    //建议先判断结果状态码，如果是成功再取结果数据做相应业务处理。HTTP状态码可以根据需要做更加严谨的判断或记录
    switch ($airsecurecard_service->getResponseCode()) {
        case AirsecurecardConstant::RESPONSE_CODE['SUCCESS']:
            //说明请求返回结果正常（注：查询结果无签名）
            $response_data=$airsecurecard_service->getResponseData();//数组
            //做相应业务处理......
            break;
        case AirsecurecardConstant::RESPONSE_CODE['REQUEST FAIL']:
        case AirsecurecardConstant::RESPONSE_CODE['INVALID PARAMETER']:
        default:
            //理论上都是异常，按具体情况进行处理......
            //可以查看具体的出错信息
            echo $airsecurecard_service->getResponseMsg();
            break;
    }
    //---------------------------------

    //如果出现问题，可以通过以下方法来获得相应数据，可与airsecurecard技术人员进行核对，也可用于日志记录
    //echo '请求地址为'.$airsecurecard_service->getRequestUrl()."\n";
    //echo '请求数据为'.json_encode($airsecurecard_service->getRequestData(),JSON_UNESCAPED_UNICODE+JSON_UNESCAPED_SLASHES)."\n";
    //echo '响应原始数据为'.json_encode($airsecurecard_service->getResponseOriginData(),JSON_UNESCAPED_UNICODE+JSON_UNESCAPED_SLASHES)."\n";
    //echo '签名字符串为'.$airsecurecard_service->getSignString()."\n";
    //echo '签名为'.$airsecurecard_service->getSign()."\n";
    exit;
