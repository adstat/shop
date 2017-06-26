<?php
class LOG {
    private $filename;

    public function __construct($filename) {
        $this->filename = $filename;
    }
    
    public function write($message) {
        $file = DIR_LOGS . $this->filename;
        $handle = fopen($file, 'a+'); 

        fwrite($handle, date('Y-m-d H:i:s',time()) . ' - ' . $message . "\n");
        fclose($handle);
    }
}

$log = new LOG("api" . date("Ymd", time()) . ".txt");
?>