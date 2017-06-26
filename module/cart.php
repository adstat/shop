<?php
require_once(DIR_SYSTEM.'/db.php');
require_once(DIR_SYSTEM.'/log.php');
require_once(DIR_SYSTEM.'/redis.php');

class CART{
    private $cart;

    function __construct() {
        $this->cart = new MyRedis();
    }

    function getKey($id, $station_id=1){
        $keyPrefix = REDIS_CART_KEY_PREFIX ? REDIS_CART_KEY_PREFIX : 'customer';
        $keyName = REDIS_CART_KEY_NAME ? REDIS_CART_KEY_NAME : 'cart';

        $key = $keyPrefix.':'.$id.':'.$station_id.':'.$keyName; //customer:[customer_id[:[station_id]:cart

        return $key;
    }

    function getCartItem($key,$field){
        return $this->cart->hget($key,$field); //Qty, get null if not exist
    }

    function countCartItem($key){
        $itemVals = $this->cart->hvals($key);
        $result = sizeof($itemVals) ? array_sum($itemVals) : 0;

        return array_sum($itemVals);
    }

    public function getCart($data, $station_id=1, $language_id=2, $origin_id=1){
        /*
        * $data = array($customer_id, $uid);  //UID or Session id
        */

        $data = unserialize($data);
        $id = (isset($data['customer_id'])&&$data['customer_id']>0) ? $data['customer_id'] : (isset($data['uid']) ? $data['uid'] : false);
        if(!$id){
            return array();
        }

        $key = $this->getKey($id, $station_id);

        return  $this->cart->hgetall($key); //Array, get array() if not exist
    }

    public function delCart($data, $station_id=1, $language_id=2, $origin_id=1){
        /*
        * $data = array($customer_id, $uid);  //UID or Session id
        */

        $data = unserialize($data);

        $key_customer = $this->getKey($data['customer_id'], $station_id);

        $result = $this->cart->del($key_customer) ? true : false;

        if( !$result && isset($data['uid']) ){
            $key_uid = $this->getKey($data['uid'], $station_id);
            $result = $this->cart->del($key_uid) ? true : false;
        }

        return $result; //While delete cart, we only need true or false
    }


    public function addCart($data, $station_id=1, $language_id=2, $origin_id=1){
        /*
        * $data = array($customer_id, $uid, $product_id);
        * Here we do add to cart, it's a init. operation, always add(set) as qty=1
        */
        $data = unserialize($data);

        $id = (isset($data['customer_id'])&&$data['customer_id']>0) ? $data['customer_id'] : (isset($data['uid']) ? $data['uid'] : false);
        if( !$data['product_id'] || !$id){
            return false;
        }

        $key = $this->getKey($id, $station_id);
        $thisCart = $this->getCart($data['customer_id'],$station_id);

        $cartLimit = defined('REDIS_CART_ITEM_LIMIT')?REDIS_CART_ITEM_LIMIT:30;

        if( sizeof($thisCart) < $cartLimit ){
            $num = (int)$this->cart->hget($key, $data['product_id']);
            if($data['product_id'] > 1000){
                $result = $this->cart->hset($key,$data['product_id'],$num+1); //Redis will auto create hash key if not exist, set it anyway.

            }
        }

        if( !is_array($thisCart) || !sizeof($thisCart) ){
            //We just create a new cart/key
            $expire = defined("REDIS_CART_EXPIRE") ? REDIS_CART_EXPIRE : 608400; //Set expire time for new cart
            $result = $this->cart->expire($key, $expire);
        }


//        if($result == 0 || $result =1){ //Redis returns success with 0 or 1(new item) by default, while we need true or false
//            return true;
//        }
//
//        return false;
        return $this->countCartItem($key); //Maybe return cart count
    }

    public function updateCart($data, $station_id=1, $language_id=2, $origin_id=1){
        /*
        * $data = array($customer_id, $uid, $product_id, $option=1, $qty=1, $limit=99);
        * option: 0=set, 1=add, 2=minus, 3=delete
        * if option=0, set the cart product to $qty
         *
         * Add $data['newCartItems'] for multi cart item update
        */
        $data = unserialize($data);
        $id = (isset($data['customer_id'])&&$data['customer_id']>0) ? $data['customer_id'] : (isset($data['uid']) ? $data['uid'] : false);

        if( !$id ){
            return 'Invalid Customer Id';
        }


        $limit = isset($data['limit']) ? abs($data['limit']) : ( defined('REDIS_CART_ITEM_QTY_LIMIT')?REDIS_CART_ITEM_QTY_LIMIT:499 );
        $key = $this->getKey($id, $station_id);

        if( isset($data['mergeCartItems']) ){

            foreach($data['mergeCartItems'] as $prod=>$qty){
                if($qty > $limit){
                    $this->cart->hset($key, $prod, $limit);
                    continue;
                }
                if($qty < 1){
                    $this->cart->hdel($key, $prod);
                    continue;
                }

                $this->cart->hset($key,$prod,$qty);  //While set, give the qty to return result
            }
        }
        else{
            if(!$data['product_id'] || !$data['qty']){
                return 'ERR: Args incorrect.';
            }

            if( !is_numeric($data['option']) ){
                return 'ERR: Option id not int.';
            }

            //TODO product limit control
            $itemCount = $this->getCartItem($key, $data['product_id']);
            switch($data['option'] ){
                case 0: //Set
                    if($data['qty'] > $limit){
                        $data['qty']=$limit;
                    }
                    elseif($data['qty'] < 1){
                        $data['qty']=1;
                    }

                    if($data['product_id'] > 1000){

                        $this->cart->hset($key,$data['product_id'],$data['qty']); //While set, give the qty to return result
                    }
                    $result = $data['qty'];
                    break;

                case 1: //Add
                    if($itemCount >= $limit){
                        break;
                    }
                    if($itemCount<1){
                        $this->cart->hset($key,$data['product_id'],1); //Repair if needed
                        $result = 1;
                    }
                    else{
                        $result = $this->cart->hincrby($key,$data['product_id'],1);
                    }
                    break;

                case 2: //Minus
                    if($itemCount > 1){
                        $result = $this->cart->hincrby($key,$data['product_id'],-1);
                    }
                    else{
                        $result = $this->cart->hdel($key, $data['product_id']) ? true : false; //Remove item
                        //While delete, we only need true or false
                    }
                    break;

                case 3: //Delete
                    $result = $this->cart->hdel($key, $data['product_id']) ? true : false;
                    break;

                default:
                    return 'ERR: Not valid option id';
            }

        }

        //return $result; //Return INT, true or false

        return $this->countCartItem($key);
    }
}

$cart = new CART();
?>