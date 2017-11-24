<?php
namespace TodChan\lib;

/**
 * IPS支付提交类
 * Class IpsPaySubmit
 * @package TodChan\lib
 */
class IpsPaySubmit
{
    private $ipsPayConfig;
    private $reqXml;
     
    public function __construct($ipsPayConfig){
        $this->ipsPayConfig = $ipsPayConfig;
    }

    public function getReqXml(){
        return $this->reqXml;
    }

    /**
     * 建立请求，以表单HTML形式构造（默认）
     * @param $paraTemp 请求参数数组
     * @return string 提交表单HTML文本
     */
    public function buildRequestForm($paraTemp) {
        //待请求参数xml
        $para = $this->buildRequestPara($paraTemp);

        $sHtml = '<form id="ipspaysubmit" name="ipspaysubmit" method="post" action="'.$this->ipsPayConfig['PostUrl'].'">';
        $sHtml .= '<input type="hidden" name="wxPayReq" value="'.$para.'"/>';
        $sHtml .= '<input type="submit" style="display:none;"></form>';
        $sHtml .= "<script>document.forms['ipspaysubmit'].submit();</script>";
    
        return $sHtml;
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
        $sReqXmlHead .= "<MerCode>".$paraTemp["MerCode"]."</MerCode>";
        $sReqXmlHead .= "<MerName>".$paraTemp["MerName"]."</MerName>";
        $sReqXmlHead .= "<Account>".$paraTemp["Account"]."</Account>";
        $sReqXmlHead .= "<MsgId>".$this->ipsPayConfig["MsgId"]."</MsgId>";
        $sReqXmlHead .= "<ReqDate>".$paraTemp["ReqDate"]."</ReqDate>";
        $sReqXmlHead .= "<Signature>".SecretMd5Helper::md5Sign($this->buildBody($paraTemp),$paraTemp["MerCode"],$this->ipsPayConfig['MerCert'])."</Signature>";
        $sReqXmlHead .= "</head>";
        return $sReqXmlHead;
    }
    /**
     * 构造请求报文体
     * @param  array $paraTemp 请求前的参数数组
     * @return string 要请求的报文体
     */
    public function buildBody($paraTemp){
        $sReqXmlBody = "<body>";
        $sReqXmlBody .= "<MerBillno>".$paraTemp["MerBillno"]."</MerBillno>";
        $sReqXmlBody .= "<GoodsInfo>";
        $sReqXmlBody .= "<GoodsName>".$paraTemp["GoodsName"]."</GoodsName>";
        $sReqXmlBody .= "<GoodsCount>".$paraTemp["GoodsCount"]."</GoodsCount>";
        $sReqXmlBody .= "</GoodsInfo>";
        $sReqXmlBody .= "<OrdAmt>".$paraTemp["OrdAmt"]."</OrdAmt>";
        $sReqXmlBody .= "<OrdTime>".$paraTemp["OrdTime"]."</OrdTime>";
        $sReqXmlBody .= "<MerchantUrl>".$paraTemp["MerchantUrl"]."</MerchantUrl>";
        $sReqXmlBody .= "<ServerUrl>".$paraTemp["ServerUrl"]."</ServerUrl>";
        $sReqXmlBody .= "<BillEXP>".$paraTemp["BillExp"]."</BillEXP>";
        $sReqXmlBody .= "<ReachBy>".$paraTemp["ReachBy"]."</ReachBy>";
        $sReqXmlBody .= "<ReachAddress>".$paraTemp["ReachAddress"]."</ReachAddress>";
        $sReqXmlBody .= "<CurrencyType>".$paraTemp["CurrencyType"]."</CurrencyType>";
        $sReqXmlBody .= "<Attach>".$paraTemp["Attach"]."</Attach>";
        $sReqXmlBody .= "<RetEncodeType>".$paraTemp["RetEncodeType"]."</RetEncodeType>";
        $sReqXmlBody .= "</body>";
        return $sReqXmlBody;
    }
}

?>