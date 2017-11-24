<?php 
namespace TodChan\lib\log;

/**
 * 日志文件操作类
 * Class CLogFileHandler
 * @package TodChan\lib\log
 */
class CLogFileHandler implements ILogHandler
{
	private $handle = null;
	
	public function __construct($file = '')
	{
		$this->handle = fopen($file,'a');
	}
	
	public function write($msg)
	{
		fwrite($this->handle, $msg, 4096);
	}
	
	public function __destruct()
	{
		fclose($this->handle);
	}
}