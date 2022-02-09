<?php
namespace Ipasspay\IpasspayChannel\logic;

use Ipasspay\IpasspayChannel\config\IpasspayConstant;

class OnlinePayRedirectLogic extends IpasspayChannelCommonLogic
{
    protected $request_url_key = "redirect_pay_url";

    //签名数组，ipasspay特有，不做公用属性
    protected $sign_field=[
        "merchant_id",
        "app_id",
        "order_no",
        "order_amount",
        "order_currency",
    ];

    public function createData($params)
    {
        if (!$this->versionCheck()) return false;

        //根据版本号决定必要数据和可选数据
        switch ($this->config['version']) {
            case '1.0':
                $this->request_data_field=array_merge(IpasspayConstant::PAY_PARAM['base'],IpasspayConstant::PAY_PARAM['1.0']);
                $this->optional_data_field=IpasspayConstant::PAY_PARAM['optional'];
                break;
            case '2.0':
                $this->request_data_field=array_merge(IpasspayConstant::PAY_PARAM['base'],IpasspayConstant::PAY_PARAM['1.0']);
                $this->optional_data_field=array_merge(IpasspayConstant::PAY_PARAM['optional'],IpasspayConstant::PAY_PARAM['2.0']);
                break;
            case '3.0':
                $this->request_data_field=array_merge(IpasspayConstant::PAY_PARAM['base'],IpasspayConstant::PAY_PARAM['1.0']);
                $this->optional_data_field=array_merge(
                    IpasspayConstant::PAY_PARAM['optional'],
                    IpasspayConstant::PAY_PARAM['2.0'],
                    IpasspayConstant::PAY_PARAM['3.0']
                );
                break;
            default:
                $this->error_code=IpasspayConstant::ERROR_CODE['CONFIG ERROR'];
                $this->error_msg='Version parameter error';
                return false;
        }

        //初始化请求数据
        if (!$this->setRequestUrl()->createCommonData($params)->appendData()->validateData()) {
            $this->error_code=IpasspayConstant::ERROR_CODE['REQUEST PARAM ERROR'];
            $this->error_msg=$this->validate_obj->getError();
            return false;
        }

        //进行签名
        $this->signData();
        return true;
    }
}