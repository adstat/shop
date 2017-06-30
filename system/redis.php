<?php
class MyRedis {

    private $redis;

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

        $this->options =  $options;
        $func = $options['persistent'] ? 'pconnect' : 'connect';

        $this->redis  = new Redis();
        $this->connected = $options['timeout'] === false ?
            $this->redis->$func($options['host'], $options['port']) :
            $this->redis->$func($options['host'], $options['port'], $options['timeout']);
    }

    public function selectdb($dbindex){
        return $this->redis->select($dbindex);
    }

    public function hgetall($tableName){
        return $this->redis->hgetall($tableName);
    }

    public function hget($tableName,$field){
        return $this->redis->hget($tableName,$field);
    }

    public function hset($tableName,$field,$value){
        return $this->redis->hset($tableName,$field,$value);
    }

    public function hincrby($tableName,$field,$value){
        return $this->redis->hincrby($tableName,$field,$value);
    }

    public function hdel($tableName,$field){
        return $this->redis->hdel($tableName,$field);
    }

    public function set($key,$value){
        return $this->redis->set($key,$value);
    }

    public function get($key){
        return $this->redis->get($key);
    }

    public function del($key){
        return $this->redis->del($key);
    }

    public function expire($key,$expire){
        return $this->redis->expire($key,$expire);
    }

    public function hvals($key){
        return $this->redis->hvals($key);
    }

    public function rpush($key, $value){
        return $this->redis->rPush($key, $value);
    }

    public function lrange($key, $start, $end){
        return $this->redis->lRange($key, $start, $end);
    }

    public function llen($key){
        return $this->redis->lLen($key);
    }

    public function exists($key){
        return $this->redis->exists($key);
    }

    public function setex($key, $ttl, $value){
        return $this->redis->setex($key, $ttl, $value);
    }

    public function hlen($key){
        return $this->redis->hLen($key);
    }

    public function hexists($key, $hashKey){
        return $this->redis->hExists($key, $hashKey);
    }

    public function hsetnx($key, $hashKey, $value){
        return $this->redis->hSetNx($key, $hashKey, $value);
    }
}
?>