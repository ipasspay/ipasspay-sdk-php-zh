<?php
namespace Ipasspay\AirsecurecardChannel\logic;

use Ipasspay\AirsecurecardChannel\config\AirsecurecardConstant;

class QueryOrderLogic extends AirsecurecardChannelCommonLogic
{
    protected $request_url_key = "query_order_url";

    protected $request_data_field = [
        "merchant_id",
        "app_id",
        "order_no",
        "timestamp",
    ];

    //签名数组
    protected $sign_field=[
        "merchant_id",
        "app_id",
        "order_no",
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