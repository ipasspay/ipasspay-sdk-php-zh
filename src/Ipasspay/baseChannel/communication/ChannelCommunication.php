<?php
namespace Ipasspay\baseChannel\communication;
//该扩展包为编写特定渠道的扩展包提供基本的抽象类，便于统一标准和编写思路，也可将一些代码做好整合

abstract class ChannelCommunication
{
    //渠道通信类负责实际的数据请求和数据接收，同时负责数据的加解密、签名和验签加工，这些加解密、签名和验签对外应为黑盒，无感知
    //同时增加原始请求数据、原始返回数据、请求地址、是否进行网络请求等信息的获取，便于业务逻辑和保存日志使用
    //各渠道扩展包中的通信类继承该类，并实现自己的加解密、签名和验签，以及数据请求和数据接收

    protected $is_send=false;//有没有进行渠道通信，如果没有进行的话，平台可以根据情况进行下个渠道的尝试。

    protected $send=true;//在getResponse时是不是真实请求，这在跳转接口创建数据时有用

    //请求地址，提供对外方法输出
    protected $request_url = '';
    //请求数据，提供对外方法输出
    protected $request_data = [];

    //请求原始数据，提供对外方法输出，SOAP模式下有用
    protected $request_origin_data = [];

    //返回的原始数据，提供对外方法输出
    protected $response_origin_data = [];

    protected $error_code = 0;//错误编码。
    protected $error_msg = '';//错误信息。

    public function setRequestUrl($request_url) {
        $this->request_url=$request_url;
        return $this;
    }

    public function setRequestData($request_data) {
        $this->request_data=$request_data;
        return $this;
    }

    public function setSend($send) {
        $this->send=$send;
        return $this;
    }

    public function getRequestUrl() {
        return $this->request_url;
    }

    public function getRequestData() {
        return $this->request_data;
    }

    public function getRequestOriginData() {
        return $this->request_origin_data;
    }

    public function getResponseOriginData() {
        return $this->response_origin_data;
    }

    public function isSend() {
        return $this->is_send;
    }

    //交互
    abstract public function getResponse();

    public function getErrorCode() {
        return $this->error_code;
    }

    public function getErrorMsg() {
        return $this->error_msg;
    }
}