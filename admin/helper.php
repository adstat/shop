<?php

/**
 * Created by PhpStorm.
 * User: user
 * Date: 2016/9/14
 * Time: 15:02
 */
class Helper extends Controller
{
    public $in_url = '';

    public function UrlSort(array $array)
    {
        $return = [];
        foreach ($array as $key => $value) {
            $arr = explode('_', $key);
            $arr0 = $arr[0];
            unset($arr[0]);
            $arr1 = join('_',$arr);
            $return[$key] = $this->url->link($value, 'token=' . $this->session->data['token'] . '&' . $arr0 . '=' . $arr1 . $this->in_url, 'SSL');
        }
        return $return;
    }
}