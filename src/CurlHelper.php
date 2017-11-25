<?php
/**
 * CURL 请求助手
 * Created by tod chan.
 * Date: 2017/11/25
 * Time: 下午1:34
 */

namespace TodChan\HxIps;


class CurlHelper
{
    /**
     * 使用POST方式获取数据
     * @param string $url 提交地址
     * @param array $data 提交的post数据
     * @return mixed
     */
    public static function curlPost($url,$data){
        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
        curl_setopt($ch,CURLOPT_POST,true);
        curl_setopt($ch,CURLOPT_POSTFIELDS,$data);
        $return = curl_exec($ch);
        curl_close($ch);
        return $return;
    }

    /**
     * CURL使用GET方式获取数据
     * @param string $url 提交地址
     * @return mixed
     */
    public static function curlGet($url){
        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
        $return = curl_exec($ch);
        curl_close($ch);
        return $return;
    }
}