# hx_ips
huanxun ips payment.


```
配置基本内容
<?php 
$config = [
    'Version'=>'v1.0.0',
    'MerCode'=>'',//商户号--申请
    'Account'=>'',//交易账户号--申请
    'MerCert'=>'',//商户证书
    'PostUrl'=>'https://thumbpay.e-years.com/psfp-webscan/onlinePay.do',//请求地址
    'S2Snotify_url'=>'',//回调通知地址
    'return_url'=>'',// 同步回调地址
];
```
