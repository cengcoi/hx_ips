<?php
namespace TodChan\HxIps\lib;

/**
 * 回调验证类
 * Class IpsPayNotifyVerify
 * @package TodChan\lib
 */
class IpsPayNotifyVerify
{
    private $ipsPayConfig;

    public function __construct($ipsPayConfig){
        $this->ipsPayConfig = $ipsPayConfig;
    }

    /**
     * 验证回调结果的有效性
     * @param string $paymentResult 回调报文（xml格式）
     * @return bool|string
     */
    public function verifyReturn($paymentResult){
        try {
            $xmlResult = new \SimpleXMLElement($paymentResult);
            $strSignature = $xmlResult->WxPayRsp->head->Signature; //签名
            $rspCode = $xmlResult->WxPayRsp->head->RspCode; //响应码
            if($rspCode == "000000")
            {
                $strBody = SecretMd5Helper::subStrXml("<body>","</body>",$paymentResult); //响应信息体
                if(SecretMd5Helper::md5Verify($strBody,$strSignature,$this->ipspay_config["MerCode"],$this->ipspay_config["MerCert"])){
                    return true;
                }else{
                    return 'verify fail because signature is not correct.';//签名错误。
                }
            }
        } catch (\Exception $e) {
            return 'verify fail.message:'.$e->getMessage();
        }

        return 'verify fail.unknow reason';
    }
}
