<?php
/**
 * IPS 查询请求报文构造
 * Created by tod chan.
 * Date: 2017/11/25
 * Time: 下午12:34
 */

namespace TodChan\HxIps\lib;


class IpsQuerySubmit extends IpsSubmit implements IpsSubmitInterface
{

    /**
     * 构造查询订单请求报文体
     * @param  array $params 请求前的参数数组
     * @return string 要请求的报文体
     */
    public function buildBody($params){
        $sReqXmlBody = "<body>";
        $sReqXmlBody .= "<MerBillNo>".$params["MerBillNo"]."</MerBillNo>";
        $sReqXmlBody .= "<Date>".$params["Date"]."</Date>";
        $sReqXmlBody .= "<Amount>".$params["Amount"]."</Amount>";
        $sReqXmlBody .= "</body>";
        return $sReqXmlBody;
    }
}