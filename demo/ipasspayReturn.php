<?php

//require '../vendor/autoload.php';//如果是使用composer获取的sdk，并不使用任何框架的话，注意添加此句，路径应可访问到vendor下的autoload.php
//如果是直接获得的zip包，请自行将src下的文件放入项目，保证可以正确引用sdk包

use Ipasspay\IpasspayChannel\service\IpasspayService;

    //同步通知(返回页面，跳转或3DS认证时会使用)

    //同步通知的处理样例，此返回页供支付用户浏览使用，如需要可根据订单结果在此页面做简单信息展示，提高用户体验。
    //但不建议在此页面做实际的交易业务数据处理，因为该页面是ipasspay直接提供给支付用户浏览器做跳转的，用户不一定会被访问，且数据安全性和时效性都不能保证。
    echo "ipasspay同步结果返回\n";

    //注意，对验签和数据获取实际和异步通知的方法调用是一样的
    //可将结果数据交由sdk进行验签，如果对验签不在意，或准备自己做相应判断，也可跳过这步
    $ipasspay_service=new IpasspayService('sandbox');//env可以为live或sandbox对应相应的配置，缺省为live
    if (!$ipasspay_service->verifyNotifyOrder()) {
        //做相应验签失败的处理......
        echo "验签失败\n";
    } else {
        echo "验签成功\n";
    }
    //可以获得相应的验签字符串和签名结果，与ipasspay技术人员进行核对，也可用于日志记录
    echo '验签字符串为'.$ipasspay_service->getVerifySignString()."\n";
    echo '验签为'.$ipasspay_service->getVerifySign()."\n";

    //可以直接使用php获得请求参数的方法，获得参数进行处理......
    echo '同步通知数据：'.json_encode($_REQUEST)."\n";
    //也可使用sdk中的方法，获得参数进行处理......
    echo 'sdk中数据为'.json_encode($ipasspay_service->getNotifyData())."\n";
    //--------------------
    exit;
