<?php
namespace TodChan\HxIps\lib;
/**
 * Created by tod chan.
 * Date: 2017/11/25
 * Time: 下午12:38
 */

interface IpsSubmitInterface{
    public function buildBody($params);
    public function getReqXml();
}