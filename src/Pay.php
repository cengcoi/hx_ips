<?php
namespace TodChan\HxIps;

use TodChan\HxIps\lib\IpsXmlVerify;
use TodChan\HxIps\lib\IpsPayNotifyVerify;
use TodChan\HxIps\lib\IpsQuerySubmit;
use TodChan\HxIps\lib\IpsRefundSubmit;
use TodChan\HxIps\lib\IpsWxWebPaySubmit;
use TodChan\HxIps\lib\log\CLogFileHandler;
use TodChan\HxIps\lib\log\Log;
use TodChan\HxIps\lib\SecretMd5Helper;

/**
 * 支付类，集中调用IPS的各种支付方式
 * Created by tod.
 * Date: 2017/11/24
 * Time: 15:25
 */

Class Pay{
    protected $config;
    protected $verify;

    public function __construct($config,$logFilePath)
    {
        $this->config = $config;
        $this->verify = new IpsXmlVerify($this->config);
        Log::Init(new CLogFileHandler($logFilePath),15);
    }

    const WXPAY_POST_URL ='https://thumbpay.e-years.com/psfp-webscan/onlinePay.do';//微信网页支付网关
    const QUERY_URL = 'https://newpay.ips.com.cn/psfp-entry/services/order?wsdl';// 查询订单网关
    const REFUND_URL = 'https://newpay.ips.com.cn/psfp-entry/services/refund?wsdl';// 订单退款网关

    /**
     * 微信网页支付
     * @param array $params
     * @return bool|string
     */
    public function wxWebPay($params=[]){
        if(empty($params))
            return false;
        $needKeys = ['MerName','MerBillNo','OrdAmt','OrdTime','GoodsName','GoodsCount','CurrencyType','BillExp'];

        // 构成支付订单必须的字段
        foreach ($needKeys as $need){
            if(!isset($params[$need]))
                return false;
        }

        $parameters = array(
            "MerCode"	=> $this->config['MerCode'],// 商户号，必填
            "MerName" => $params['MerName'] ? $params['MerName'] : '',//商户名称
            "Account"	=> $this->config['Account'],//商户账户号，必填
            "MerBillno"	=> $params['MerBillNo'],//商户订单号，必填
            "OrdAmt"   => $params['OrdAmt'],//订单金额，必填
            "OrdTime"	=> $params['OrdTime'],//订单时间，必填，格式:yyyy-MM-dd HH:ii:ss
            "ReqDate"	=> date("YmdHis"),
            "GoodsName"	=> $params['GoodsName'],//商品名称，必填
            "GoodsCount"	=> $params['GoodsCount'],//商品数量，必填
            "CurrencyType"	=> $params['CurrencyType'],//支付币种，必填，人民币：156
            "MerchantUrl"	=> $this->config['return_url'],//商户返回地址，使用配置里面的同步地址
            "ServerUrl"	=> $this->config['S2Snotify_url'],//商户S2S返回地址，使用配置里面的回调地址
            "BillExp"	=> $params['BillExp'],//超时时间，接口不必填，可以通过业务要求必填，最大2小时，格式:yyyy-MM-dd HH:ii:ss
            "RetEncodeType"	=> 17,//签名方式，暂时固定为MD5方式，值为17
            "MsgId" => uniqid(),
            "ReachAddress"	=> '',//收货人地址
            "ReachBy"	=> '',//收货人姓名
            "Attach"	=> ''//买家留言
        );


        $request = new IpsWxWebPaySubmit($this->config);
        $formText = $request->buildRequestForm($parameters);
        Log::INFO('支付请求 | 生成报文:'.$request->getReqXml());
        return $formText;
    }

    /**
     * 支付后回调验证订单
     * @param string $xml   回调报文
     * @return array result表示处理状态，order_result表示订单状态（不一定有），msg表示处理信息（不一定有）
     */
    public function orderValidate($xml){
        if (($msg = $this->verify->verifyReturn($xml)) === true) { // 验证成功，必须要是true
            Log::INFO('支付完成进行回调 | 报文：'.$xml);

            $xmlResult = new SimpleXMLElement($xml);
            $status = $xmlResult->WxPayRsp->body->Status;
            if($status == "Y") {
                return ['result'=>true,'order_result'=>true];
            }elseif($status == "N") {
                Log::WARN('订单交易失败 | 报文：'.$xml);
                return ['result'=>true,'order_result'=>true,'msg'=>'交易失败'];
            }else {
                return ['result'=>true,'order_result'=>false,'msg'=>'交易处理中'];
            }
        } else {
            Log::WARN('回调验证失败 | 原因：'.$msg.' | 报文：'.$xml);
            return ['result'=>false];
        }
    }

    /**
     * 查询订单
     * @param array $params 提交参数
     * @return bool
     */
    public function queryOrder(array $params){
        if(empty($params))
            return false;
        $needKeys = ['MerBillNo','Date','Amount'];

        // 构成查询报文必须的字段
        foreach ($needKeys as $need){
            if(!isset($params[$need]))
                return false;
        }

        $parameters = [
            'MerBillNo'=>$params['MerBillNo'],
            'Date'=>$params['Date'],
            'Amount'=>$params['Amount']
        ];

        $request = new IpsQuerySubmit($this->config);
        $xml = $request->buildRequestPara($parameters);
        Log::INFO('订单查询 | 生成报文:'.$request->getReqXml());
        $result = CurlHelper::curlPost(self::QUERY_URL,$xml);

        if (($msg = $this->verify->verifyReturn($result)) === true) { // 验证成功，必须要是true
            $body = SecretMd5Helper::subStrXml("<body>","</body>",$result);
            return simplexml_load_string($body);
        } else {
            Log::WARN('查询返回验证失败 | 原因：'.$msg.' | 报文：'.$xml);
            return false;
        }
    }

    /**
     * 退款操作
     * @param array $params 提交参数
     * @return bool
     */
    public function refund(array $params){
        if(empty($params))
            return false;
        $needKeys = ['MerBillNo','OrgMerBillNo','OrgMerTime','BillAmount','RefundAmount'];

        // 构成退款报文必须的字段
        foreach ($needKeys as $need){
            if(!isset($params[$need]))
                return false;
        }

        $parameters = [
            'MerBillNo'=>$params['MerBillNo'],// 退款订单号
            'OrgMerBillNo'=>$params['OrgMerBillNo'],// 原订单号
            'OrgMerTime'=>$params['OrgMerTime'],// 原订单提交时间，格式：yyyyMMdd
            'BillAmount'=>$params['BillAmount'],// 订单金额
            'RefundAmount'=>$params['RefundAmount'],// 退款金额
            'RefundMemo'=>$params['RefundMemo'] ? $params['RefundMemo'] : '',// 备注信息
        ];

        $request = new IpsRefundSubmit($this->config);
        $xml = $request->buildRequestPara($parameters);
        Log::INFO('订单退款 | 生成报文:'.$request->getReqXml());
        $result = CurlHelper::curlPost(self::REFUND_URL,$xml);

        if (($msg = $this->verify->verifyReturn($result)) === true) { // 验证成功，必须要是true
            $xmlResult = new SimpleXMLElement($result);
            $status = $xmlResult->WxPayRsp->body->Status;
            if($status == "Y") {
                return ['result'=>true,'return_msg'=>simplexml_load_string($result)];
            }elseif($status == "N") {
                Log::WARN('处理交易失败 | 返回报文：'.$result);
                return ['result'=>false,'return_msg'=>simplexml_load_string($result)];
            }elseif($status == "P") {
                Log::INFO('退款正在处理中 | 返回报文:'.$result);
                return ['result'=>true,'return_msg'=>simplexml_load_string($result)];
            }
        } else {
            Log::WARN('退款请求失败 | 原因：'.$msg.' | 请求报文：'.$xml);
            return false;
        }





    }
}