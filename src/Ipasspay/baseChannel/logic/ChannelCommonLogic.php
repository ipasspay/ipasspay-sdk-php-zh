<?php
namespace Ipasspay\baseChannel\logic;
//该扩展包为编写特定渠道的扩展包提供基本的抽象类，便于统一标准和编写思路，也可将一些代码做好整合

use Ipasspay\baseChannel\validate\Validate;

abstract class ChannelCommonLogic
{
    //各渠道扩展包中的业务逻辑类或业务逻辑基类继承该类

    protected $request_data_field=[];//必填字段
    protected $optional_data_field=[];//可选字段

    /* @var Validate */
    protected $validate_obj=null;//校验类

    protected $request_url = '';
    protected $request_data = [];
    protected $config=[];

    protected $notify_data = [];//通知数据

    protected $sign_field= [];
    protected $sign_string= '';
    protected $sign= '';

    protected $verify_sign_field= [];
    protected $verify_sign_string= '';
    protected $verify_sign= '';

    protected $error_code = 0;//错误编码。
    protected $error_msg = '';//错误信息。

    public function __construct($config = [])
    {
        $this->config = $config;
    }

    public function getRequestUrl() {
        return $this->request_url;
    }

    public function getRequestData() {
        return $this->request_data;
    }

    public function getErrorCode() {
        return $this->error_code;
    }

    public function getErrorMsg() {
        return $this->error_msg;
    }

    abstract public function createData($params);//请求数据组装，由子类实现

    //常规生成请求数据的方法，使用request_data_field对传过来的数据构造request_data，目前是最基本的处理
    public function createCommonData($params)
    {
        //过滤$request_data_field和$optional_data_field以外的数据，且对字符串类型数据做trim处理
        if (is_array($params)) {
            foreach ($params as $k=>$v) {
                if (in_array($k,$this->request_data_field) || in_array($k,$this->optional_data_field)) {
                    if (is_string($v)) {
                        $param_value=trim($v);
                    } else {
                        $param_value=$v;
                    }
                    if ($param_value!==null && $param_value!=='') {
                        $this->request_data[$k]=$param_value;
                    }
                }
            }
        }
        return $this;
    }

    abstract public function signData();//签名，由子类实现

    public function getSignString() {
        return $this->sign_string;
    }

    public function getSign() {
        return $this->sign;
    }

    abstract public function verifySign($data);//验签

    public function getVerifySignString() {
        return $this->verify_sign_string;
    }

    public function getVerifySign() {
        return $this->verify_sign;
    }

    public function getNotifyData() {
        return $this->notify_data;
    }
}