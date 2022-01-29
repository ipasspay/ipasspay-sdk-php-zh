<?php

//require '../vendor/autoload.php';//如果是使用composer获取的sdk，并不使用任何框架的话，注意添加此句，路径应可访问到vendor下的autoload.php
//如果是直接获得的zip包，请自行将src下的文件放入项目，保证可以正确引用sdk包

use Ipasspay\IpasspayChannel\config\IpasspayConfig;
use Ipasspay\IpasspayChannel\service\IpasspayService;

    //对接ipasspay的直连支付

    //通过各种方式获得相应的用户数据后，可调用sdk完成加密和通讯。具体参数说明请参见api文档
    //注意：merchant_id、app_id、version、api_secret都在IpasspayConfig中进行配置，这里无需填写
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

    $request_data['bill_email'] = 'test1@ipasspay.com';//顾客邮箱

    $request_data['source_url'] = 'https://www.yourdomain.com/pay?shopping_cart=123';//来源网址，用于记录商品支付网页地址
    //同步通知地址，用户完成交易后将跳转到该页面，跳转时将携带交易结果数据。
    //此处数据可以用作对顾客展示内容的判断，但不建议用作业务逻辑操作，因为该页面是给用户浏览器访问使用，存在不被访问的可能性。
    //对交易结果的业务逻辑操作，建议用异步通知结果和订单查询的结果
    $request_data['syn_notify_url'] = 'https://www.yourdomain.com/ipasspayReturn.php';
    //异步通知地址，当订单状态发生变化，ipasspay服务器会将订单结果数据异步通知到该地址，请根据实际业务逻辑做进一步处理。
    $request_data['asyn_notify_url'] = 'https://www.yourdomain.com/ipasspayNotify.php';

    //直连需要上传卡信息部分
    $request_data['card_no'] = '5105105105105100';//Non-3DS
    //$request_data['card_no'] = '4048411801551156';//3DS
    $request_data['card_ex_year'] = '25';
    $request_data['card_ex_month'] = '12';
    $request_data['card_cvv'] = '123';
    //$request_data['source_ip'] = '127.0.0.1';//注意获得实际用户IP，支持IPV4和IPV6
    $request_data['source_ip'] = '2600:1700:e00:b0c0::41';//IPV6
    $request_data['bill_firstname'] = 'Pay';
    $request_data['bill_lastname'] = 'Ipass';

    //version2.0需要上传账单信息。具体要求参考文档
    $request_data['bill_phone'] = '13800138000';
    $request_data['bill_country'] = 'US';
    $request_data['bill_state'] = 'AL';
    $request_data['bill_city'] = 'Birmingham';
    $request_data['bill_street'] = 'somewhere';
    $request_data['bill_zip'] = '35201';
    //------------------------------------

    //进行直连支付请求，只需进行如下调用
    $ipasspay_service=new IpasspayService('sandbox');//env可以为live或sandbox对应相应的配置，缺省为live
    if (!$ipasspay_service->onlinePay($request_data)) {
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
            //说明请求返回结果正常
            //对于有签名的返回，可以先进行验签判断。如果对验签不在意，或准备自己做相应判断，也可跳过这步
            if (!$ipasspay_service->verifyResponseData()) {
                //做相应验签失败的处理......
                echo "验签失败\n";
                //可以获得相应的验签字符串和签名结果，与ipasspay技术人员进行核对，也可用于日志记录
                echo '验签字符串为'.$ipasspay_service->getVerifySignString()."\n";
                echo '验签为'.$ipasspay_service->getVerifySign()."\n";
                echo '数据签名为'.$ipasspay_service->getResponseSign()."\n";
                break;
            }
            //-------------

            $response_data=$ipasspay_service->getResponseData();//数组
            //直连接口中，需判断是否需要跳转(比如收单行需要3DS校验或访问中转页面)，如果需要跳转，则应将跳转地址交由顾客浏览器访问。
            if ($ipasspay_service->needRedirect()) {
                //需要跳转的情况下，交易最终结果需靠异步通知或订单查询进行确认
                //toPayUrl方法会使用Header进行跳转
                //如果希望自行处理跳转，可以使用getPayUrl获得跳转地址
                $ipasspay_service->toPayUrl();
                //echo $ipasspay_service->getPayUrl();
                break;
            } else{
                //可按返回的交易状态做相应业务处理......
                break;
            }
        case IpasspayConfig::RESPONSE_CODE['REQUEST FAIL']:
        case IpasspayConfig::RESPONSE_CODE['REQUEST ERROR']:
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
