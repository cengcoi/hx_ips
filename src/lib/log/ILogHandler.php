<?php 
namespace TodChan\lib\log;

/**
* 日志列表
*/
interface ILogHandler
{
	public function write($msg);
	
}