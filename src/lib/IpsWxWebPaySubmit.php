<?php
namespace TodChan\HxIps\lib;

/**
 * IPS微信网页支付提交类
 * Class IpsWxWebPaySubmit
 * @package TodChan\lib
 */
class IpsWxWebPaySubmit extends IpsSubmit implements IpsSubmitInterface
{

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
