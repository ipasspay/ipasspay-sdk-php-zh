<?php

//require '../vendor/autoload.php';//如果是使用composer获取的sdk，并不使用任何框架的话，注意添加此句，路径应可访问到vendor下的autoload.php
//如果是直接获得的zip包，请自行将src下的文件放入项目，保证可以正确引用sdk包

use Ipasspay\AirsecurecardChannel\service\AirsecurecardService;

    //对接airsecurecard的跳转支付

    //通过各种方式获得相应的用户数据后，可调用sdk完成加密和通讯。具体参数说明请参见api文档
    //注意：merchant_id、app_id、version、api_secret都在AirsecurecardConfig中进行配置，这里无需填写
    //使用sdk的话，签名和验签过程无需考虑
    $order_no=time().mt_rand(100,999);//订单号，此处只是模拟
    $request_data['order_no']=$order_no;

    $request_data['order_amount']='12.00';//订单金额
    $request_data['order_currency'] = 'USD';//订单币种

    $order_items=[];
    $order_item['goods_name']='something';
    $order_item['quality']=2;
    $order_item['price']='6.00';
    $order_items[]=$order_item;
    $request_data['order_items'] = json_encode($order_items,JSON_UNESCAPED_UNICODE+JSON_UNESCAPED_SLASHES);//订单商品数据。Json字符串

    $request_data['bill_email'] = 'test1@gmail.com';//顾客邮箱

    $request_data['source_url'] = 'http://www.yourdomain.com/pay?shopping_cart=123';//来源网址，用于记录商品支付网页地址
    //同步通知地址，用户完成交易后将跳转到该页面，跳转时将携带交易结果数据。
    //此处数据可以用作对顾客展示内容的判断，但不建议用作业务逻辑操作，因为该页面是给用户浏览器访问使用，存在不被访问的可能性。
    //对交易结果的业务逻辑操作，建议用异步通知结果和订单查询的结果
    $request_data['syn_notify_url'] = 'https://www.yourdomain.com/airsecurecardReturn.php';
    //异步通知地址，当订单状态发生变化，airsecurecard服务器会将订单结果数据异步通知到该地址，请根据实际业务逻辑做进一步处理。
    $request_data['asyn_notify_url'] = 'https://www.yourdomain.com/airsecurecardNotify.php';
    //------------------------------------

    //进行跳转支付请求，只需进行如下调用
    $airsecurecard_service=new AirsecurecardService('sandbox');//env可以为live或sandbox对应相应的配置，缺省为live

    //注：如果需要通过程序动态进行商户信息的配置，可以在进行请求时使用setConfig($config)来改变AirsecurecardConfig.php文件里配置的数据
    /*$config['merchant_id']='111111';
    $config['app_id']='222222';
    $config['api_secret']='333333';
    if (!$airsecurecard_service->setConfig($config)->onlinePayRedirect($request_data)) {*/
    if (!$airsecurecard_service->onlinePayRedirect($request_data)) {
        //请求异常，可以通过以下方法知道错误原因，请完成您系统中的相应处理
        echo '错误编码为'.$airsecurecard_service->getErrorCode()."\n";
        echo '错误原因为'.$airsecurecard_service->getErrorMsg()."\n";
        exit;
    }

    //如果需要自行进行重定向，可以使用redirectByGet(false)来获得重定向的地址
    /*$redirect_url=$airsecurecard_service->redirectByGet(false);
    echo '重定向地址为'.$redirect_url;
    if ($redirect_url===false) {
        //获得重定向地址失败，可以通过以下方法知道错误原因，请完成您系统中的相应处理
        echo '错误编码为'.$airsecurecard_service->getErrorCode()."\n";
        echo '错误原因为'.$airsecurecard_service->getErrorMsg()."\n";
        exit;
    }*/

    //如果使用sdk进行重定向，则redirectByGet()使用get方法自动跳转，如果需要也可以使用redirectByPost()方法进行post跳转，自由选择
    if (!$airsecurecard_service->redirectByPost()) {
        //请求异常，可以通过以下方法知道错误原因，请完成您系统中的相应处理
        echo '错误编码为'.$airsecurecard_service->getErrorCode()."\n";
        echo '错误原因为'.$airsecurecard_service->getErrorMsg()."\n";
        exit;
    }

    //---------------------------------

    //如果出现问题，可以通过以下方法来获得相应数据，可与airsecurecard技术人员进行核对，也可用于日志记录
    //echo '请求地址为'.$airsecurecard_service->getRequestUrl()."\n";
    //echo '请求数据为'.json_encode($airsecurecard_service->getRequestData(),JSON_UNESCAPED_UNICODE+JSON_UNESCAPED_SLASHES)."\n";
    //echo '签名字符串为'.$airsecurecard_service->getSignString()."\n";
    //echo '签名为'.$airsecurecard_service->getSign()."\n";
    exit;
