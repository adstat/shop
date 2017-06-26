<?php
class Cache { 
    private $handler;
    private $options;
    private $cache_used;

	public function __construct($options = null) {
	    if ( !extension_loaded('redis') ) {
            exit('PHP Redis extension is not installed!');
        }
        
        if(empty($options)) {
            $options = array (
                'host'  => REDIS_HOST ? REDIS_HOST : '127.0.0.1',
                'port'  => REDIS_PORT ? REDIS_PORT : 6379,
                'timeout' => REDIS_CACHE_TIMEOUT ? REDIS_CACHE_TIMEOUT : false,
                'persistent' => false,
                'expire'   => REDIS_CACHE_TIME ? REDIS_CACHE_TIME : 3600,
                'length'   => 0,
            );
        }
        
        if(defined('CAHCE_USED') && CAHCE_USED){
            $this->cache_used = true;
        }else{
            $this->cache_used = false;
        }
        
        $this->options =  $options;
        $func = $options['persistent'] ? 'pconnect' : 'connect';
        
        if($this->cache_used){
            $this->handler  = new Redis;
            $this->connected = $options['timeout'] === false ?
                $this->handler->$func($options['host'], $options['port']) :
                $this->handler->$func($options['host'], $options['port'], $options['timeout']);
        }
	}

	public function get($key) {
	    if($this->cache_used){
    		$value = $this->handler->get(getCacheKey($key));
    		return unserialize($value);
	    }else{
	        return false;
	    }
	}

  	public function set($key, $value, $expire = null) {
  	    if($this->cache_used){
      	    if(is_null($expire)) {
      	        $expire  = $this->options['expire'];
      	    }
      	    $name = getCacheKey($key);
      	    $value = serialize($value);
      	    if($expire > 0) {
      	    	
      	        return $this->handler->setex($name, $expire, $value);
      	    }else{
      	        return $this->handler->set($name, $value);
      	    }
  	    }else{
  	        return false;
  	    }
  	}
	
  	public function delete($key) {
  	    if($this->cache_used){
  	        return $result = $this->handler->delete(getCacheKey($key));
  	    }
  	}
  	
  	public function getHandler(){
  	    return $this->handler;
  	}
}
?>