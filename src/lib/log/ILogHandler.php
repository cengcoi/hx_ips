<?php 
namespace TodChan\HxIps\lib\log;

/**
* 日志列表
*/
interface ILogHandler
{
	public function write($msg);
	
}