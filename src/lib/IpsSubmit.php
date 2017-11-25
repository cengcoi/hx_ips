<?php
/**
 * 请求报文基类
 * Created by tod chan.
 * Date: 2017/11/25
 * Time: 下午12:36
 */

namespace TodChan\HxIps\lib;


class IpsSubmit implements IpsSubmitInterface
{
    protected $reqXml;
    protected $ipsPayConfig;

    public function __construct($ipsPayConfig){
        $this->ipsPayConfig = $ipsPayConfig;
    }

    /**
     * 获取请求xml数据
     * @return mixed
     */
    public function getReqXml()
    {
        return $this->reqXml;
    }

    /**
     * 生成要请求给IPS的参数XMl
     * @param $paraTemp 请求前的参数数组
     * @return string 要请求的参数XMl
     */
    public function buildRequestPara($paraTemp) {
        $sReqXml = "<Ips>";
        $sReqXml .= "<WxPayReq>";
        $sReqXml .= $this->buildHead($paraTemp);
        $sReqXml .= $this->buildBody($paraTemp);
        $sReqXml .= "</WxPayReq>";
        $sReqXml .= "</Ips>";
        // 记录生成的请求xml数据
        $this->reqXml = $sReqXml;
        return $sReqXml;
    }

    /**
     * 构造请求报文头
     * @param  array $paraTemp 请求前的参数数组
     * @return string 要请求的报文头
     */
    public function buildHead($paraTemp){
        $sReqXmlHead = "<head>";
        $sReqXmlHead .= "<Version>".$this->ipsPayConfig["Version"]."</Version>";
        $sReqXmlHead .= "<MerCode>".$this->ipsPayConfig["MerCode"]."</MerCode>";
        $sReqXmlHead .= "<MerName>".$paraTemp["MerName"]."</MerName>";
        $sReqXmlHead .= "<Account>".$this->ipsPayConfig["Account"]."</Account>";
        $sReqXmlHead .= "<MsgId>".$paraTemp["MsgId"]."</MsgId>";
        $sReqXmlHead .= "<ReqDate>".$paraTemp["ReqDate"]."</ReqDate>";
        $sReqXmlHead .= "<Signature>".SecretMd5Helper::md5Sign($this->buildBody($paraTemp),$paraTemp["MerCode"],$this->ipsPayConfig['MerCert'])."</Signature>";
        $sReqXmlHead .= "</head>";
        return $sReqXmlHead;
    }

    /**
     * 构造请求报文体（无层级）
     * @param  array $params 请求前的参数数组
     * @return string 要请求的报文体
     */
    public function buildBody($params){
        $sReqXmlBody = "<body>";
        foreach ($params as $key=>$v){
            $sReqXmlBody .= "<{$key}>{$v}</{$key}>";
        }
        $sReqXmlBody .= "</body>";
        return $sReqXmlBody;
    }

}