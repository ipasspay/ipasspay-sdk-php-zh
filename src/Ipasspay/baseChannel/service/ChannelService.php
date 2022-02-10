<?php
namespace Ipasspay\baseChannel\service;
//该扩展包为编写特定渠道的扩展包提供基本的抽象类，便于统一标准和编写思路，也可将一些代码做好整合

use Ipasspay\baseChannel\communication\ChannelCommunication;
use Ipasspay\baseChannel\logic\ChannelCommonLogic;

abstract class ChannelService
{
    /* @var ChannelCommunication $handler */
    protected $handler;//通讯处理器对象
    protected $config;//渠道配置

    protected $error_code = 0;//错误编码。
    protected $error_msg = '';//错误信息。

    /* @var ChannelCommonLogic $logic_obj */
    protected $logic_obj;//业务逻辑对象

    public function setConfig($config) {
        if (is_array($config)) {
            if (is_array($this->config)) {
                $this->config=array_merge($this->config,$config);
            } else {
                $this->config=$config;
            }
        }
        return $this;
    }

    public function isSend() {
        return $this->handler->isSend();
    }

    public function getRequestUrl() {
        return $this->handler->getRequestUrl();
    }

    public function getRequestData() {
        return $this->handler->getRequestData();
    }

    public function getRequestOriginData() {
        return $this->handler->getRequestOriginData();
    }

    public function getResponseOriginData() {
        return $this->handler->getResponseOriginData();
    }

    public function getLogic() {
        return $this->logic_obj;
    }

    public function getErrorCode() {
        return $this->error_code;
    }

    public function getErrorMsg() {
        return $this->error_msg;
    }

    protected function deal($params,$send=true)
    {
        //创建data数据
        $request_data = $this->logic_obj->createData($params);
        if (!$request_data) {
            $this->error_code = $this->logic_obj->getErrorCode();
            $this->error_msg = $this->logic_obj->getErrorMsg();
            return false;
        }
        //数据组装成功，进行参数设置
        if (!$this->handler
            ->setRequestUrl($this->logic_obj->getRequestUrl())
            ->setRequestData($this->logic_obj->getRequestData())
            ->setSend($send)
            ->getResponse()) {
            $this->error_code = $this->handler->getErrorCode();
            $this->error_msg = $this->handler->getErrorMsg();
            return false;
        }
        return true;
    }

    //form表单形式的请求
    protected function htmlRequest($post_url,$params)
    {
        $html_content='<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8" /></head><body><form id="autoRedirectForm" name="autoRedirectForm" action="'.$post_url.'" method="post">';
        foreach ($params as $key=>$value) {
            $html_content.='<input type="hidden" name="'.$key.'" value=\''.$value.'\'>';
        }

        $html_content.='</form>
                        <script type="text/javascript">
                          function load_submit(){document.autoRedirectForm.submit()}
                          load_submit();
                        </script>
                      </body>';
        return $html_content;
    }
}