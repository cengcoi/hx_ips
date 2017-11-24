<?php
namespace TodChan;
use TodChan\lib\IpsPayNotifyVerify;
use TodChan\lib\IpsPaySubmit;
use TodChan\lib\log\CLogFileHandler;
use TodChan\lib\log\Log;

/**
 * 支付类，集中调用IPS的各种支付方式
 * Created by tod.
 * Date: 2017/11/24
 * Time: 15:25
 */

Class Pay{
    protected $config;

    public function __construct($config,$logFilePath)
    {
        $this->config = $config;
        Log::Init(new CLogFileHandler($logFilePath),15);
    }

    const WXPAY_POST_URL ='https://thumbpay.e-years.com/psfp-webscan/onlinePay.do';//微信网页支付网关

    /**
     * 微信网页支付
     * @param array $params
     * @return bool|string
     */
    public function wxWebPay($params=[]){
        if(empty($params))
            return false;
        $needKeys = ['MerBillNo','OrdAmt','OrdTime','GoodsName','GoodsCount','CurrencyType','BillExp'];

        // 构成支付订单必须的字段
        foreach ($needKeys as $need){
            if(!isset($params[$need]))
                return false;
        }

        $parameters = array(
            "MerCode"	=> $this->config['MerCode'],// 商户号，必填
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
//            "ReachAddress"	=> '',//收货人地址
//            "ReachBy"	=> '',//收货人姓名
//            "Attach"	=> //买家留言
        );

        if(isset($params['MerName']))
            $parameters['MerName'] = $params['MerName'];//商户名称


        $request = new IpsPaySubmit($this->config);
        $formText = $request->buildRequestForm($parameters);
        Log::INFO('支付请求 | 生成报文:',$request->getReqXml());
        return $formText;
    }

    /**
     * 查询订单
     */
    public function queryOrder(){

    }

    /**
     * 退款操作
     */
    public function refund(){

    }

    /**
     * 回调验证订单
     * @param string $xml   回调报文
     * @return array result表示处理状态，order_result表示订单状态（不一定有），msg表示处理信息（不一定有）
     */
    public function orderValidate($xml){
        $verify = new IpsPayNotifyVerify($this->config);
        if (($msg = $verify->verifyReturn($xml)) === true) { // 验证成功，必须要是true
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
}