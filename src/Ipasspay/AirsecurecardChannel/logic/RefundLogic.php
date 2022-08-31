<?php
namespace Ipasspay\AirsecurecardChannel\logic;

use Ipasspay\AirsecurecardChannel\config\AirsecurecardConstant;

class RefundLogic extends AirsecurecardChannelCommonLogic
{
    protected $request_url_key = "refund_url";

    protected $request_data_field = [
        "merchant_id",
        "app_id",
        "gateway_order_no",
        "refund_no",
        "refund_amount",
        "timestamp",
    ];

    protected $optional_data_field = [
        "refund_desc",
    ];

    //签名数组
    protected $sign_field=[
        "merchant_id",
        "app_id",
        "gateway_order_no",
        "refund_no",
        "refund_amount",
        "timestamp",
    ];

    public function createData($params)
    {
        $this->request_data['timestamp']=time();
        //初始化请求数据
        if (!$this->setRequestUrl()->createCommonData($params)->appendData()->validateData()) {
            $this->error_code=AirsecurecardConstant::ERROR_CODE['REQUEST PARAM ERROR'];
            $this->error_msg=$this->validate_obj->getError();
            return false;
        }

        //进行签名
        $this->signData();
        return true;
    }
}