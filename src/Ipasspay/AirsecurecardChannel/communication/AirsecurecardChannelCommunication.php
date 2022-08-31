<?php
namespace Ipasspay\AirsecurecardChannel\communication;

use Ipasspay\baseChannel\communication\ChannelCommunication;
use Ipasspay\baseChannel\tools\Curl;
use Ipasspay\AirsecurecardChannel\config\AirsecurecardConstant;

class AirsecurecardChannelCommunication extends ChannelCommunication
{
    //交互
    public function getResponse()
    {
        if ($this->send) {
            //该步骤相当于Curl请求，在发起前应标明已和渠道方通信
            $this->is_send = true;

            $this->response_origin_data = Curl::to($this->request_url)
                ->setRetryTimes(0)
                ->withTimeout(180)
                ->withData($this->request_data)
                ->returnResponseArray()
                ->post();

            if (is_array($this->response_origin_data) && isset($this->response_origin_data['status']) && isset($this->response_origin_data['content'])) {
                return true;
            }

            $this->error_code=AirsecurecardConstant::ERROR_CODE['REQUEST INTERFACE EXCEPTION'];
            $this->error_msg='abnormal response data';
            return false;
        } else {
            return true;
        }
    }
}