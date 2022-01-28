<?php
namespace Ipasspay\IpasspayChannel\logic;

use Ipasspay\IpasspayChannel\config\IpasspayConfig;

class QueryOrderListLogic extends IpasspayChannelCommonLogic
{
    protected $request_url_key = "query_order_list_url";

    protected $request_data_field = [
        "merchant_id",
        "app_id",
        "start_datetime",
        "end_datetime",
        "order_status",
        "timestamp",
    ];

    protected $optional_data_field = [
        "page",
    ];

    //签名数组
    protected $sign_field=[
        "merchant_id",
        "app_id",
        "start_datetime",
        "end_datetime",
        "order_status",
        "timestamp",
    ];

    public function createData($params)
    {
        $this->request_data['timestamp']=time();
        //初始化请求数据
        if (!$this->setRequestUrl()->createCommonData($params)->appendData()->validateData()) {
            $this->error_code=IpasspayConfig::ERROR_CODE['REQUEST PARAM ERROR'];
            $this->error_msg=$this->validate_obj->getError();
            return false;
        }

        //进行签名
        $this->signData();
        return true;
    }
}