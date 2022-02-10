<?php

//require '../vendor/autoload.php';//如果是使用composer获取的sdk，并不使用任何框架的话，注意添加此句，路径应可访问到vendor下的autoload.php
//如果是直接获得的zip包，请自行将src下的文件放入项目，保证可以正确引用sdk包

use Ipasspay\IpasspayChannel\service\IpasspayService;

    //异步通知(订单状态改变，ipasspay都会发出通知)

    //异步通知的处理样例，因异步通知都是由服务器后台完成，如果需要测试，可以将结果记录在日志文件里
    //不同的系统记录日志的方式不同，这里使用类似error_log这样的php标准方法记录，在php相应日志文件里可以查看输出
    error_log('收到ipasspay异步通知');

    //可将结果数据交由sdk进行验签，如果对验签不在意，或准备自己做相应判断，也可跳过这步
    $ipasspay_service=new IpasspayService('sandbox');//env可以为live或sandbox对应相应的配置，缺省为live

    //注：如果需要通过程序动态进行商户信息的配置，可以在进行请求时使用setConfig($config)来改变IpasspayConfig.php文件里配置的数据
    /*$config['merchant_id']='111111';
    $config['app_id']='222222';
    $config['api_secret']='333333';
    if (!$ipasspay_service->setConfig($config)->verifyNotifyOrder($request_data)) {*/
    if (!$ipasspay_service->verifyNotifyOrder()) {
        //做相应验签失败的处理......
        error_log("验签失败");
    } else {
        error_log("验签成功");
    }
    //可以获得相应的验签字符串和签名结果，与ipasspay技术人员进行核对，也可用于日志记录
    error_log('验签字符串为'.$ipasspay_service->getVerifySignString());
    error_log('验签为'.$ipasspay_service->getVerifySign());

    //可以直接使用php获得请求参数的方法，获得参数进行处理......
    error_log('异步通知数据：'.json_encode($_REQUEST));
    //也可使用sdk中的方法，获得参数进行处理......
    error_log('sdk中数据为'.json_encode($ipasspay_service->getNotifyData()));
    //--------------------

    //如果确认收到通知处理成功，无需ipasspay再次通知本次数据请使用notifySuccess，或按文档返回相应数据
    //如果处理中发生异常，希望ipasspay再次通知本次数据请使用notifyFail，或按文档返回相应数据
    echo $ipasspay_service->notifySuccess();
    //echo $ipasspay_service->notifyFail();
    exit;
