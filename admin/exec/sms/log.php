<?php
class LOG {
    private $filename;

    public function __construct($filename) {
        $this->filename = $filename;
    }

    public function write($message) {

        $file = 'logs/' . $this->filename;
        $handle = fopen($file, 'a+');

        fwrite( $handle, date('Y-m-d H:i:s',time () ) . ' - ' . $message . "\n");

        fclose($handle);
    }
}

date_default_timezone_set('PRC');
$log = new LOG( date ("Ymd", time () ) . ".log");
?>