<?php
class Log {
	private $filename;
	
	public function __construct($filename) {
		$this->filename = $filename;
	}
	
	public function write($message) {
		$file = DIR_LOGS . $this->filename;
		$current = file_get_contents($file);
		$handle = fopen($file, 'w+'); 
		
		fwrite($handle, date('Y-m-d H:i:s') . ' - ' . $message . "\n");
        fwrite($handle, $current);
			
		fclose($handle); 
	}
}
?>