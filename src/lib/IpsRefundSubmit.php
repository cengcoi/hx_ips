<?php
/**
 * 退款请求报文构建
 * Created by tod chan.
 * Date: 2017/11/25
 * Time: 下午1:09
 */

namespace TodChan\HxIps\lib;


class IpsRefundSubmit extends IpsSubmit implements IpsSubmitInterface
{
    /**
     * 构造退款请求报文体
     * @param  array $paraTemp 请求前的参数数组
     * @return string 要请求的报文体
     */
    public function buildBody($paraTemp){
        $sReqXmlBody = "<body>";
        $sReqXmlBody .= "<MerBillNo>".$paraTemp["MerBillNo"]."</MerBillNo>";//商户退款订单号，必填
        $sReqXmlBody .= "<OrgMerBillNo>".$paraTemp["OrgMerBillNo"]."</OrgMerBillNo>";//原订单的商户订单号，必填
        $sReqXmlBody .= "<OrgMerTime>".$paraTemp["OrgMerTime"]."</OrgMerTime>";//原订单商户订单提交时间，必填
        $sReqXmlBody .= "<BillAmount>".$paraTemp["BillAmount"]."</BillAmount>";//原订单总金额，必填
        $sReqXmlBody .= "<RefundAmount>".$paraTemp["RefundAmount"]."</RefundAmount>";//退款金额，必填
        $sReqXmlBody .= "<RefundMemo>".$paraTemp["RefundMemo"]."</RefundMemo>";//退款备注
        $sReqXmlBody .= "</body>";
        return $sReqXmlBody;
    }
}