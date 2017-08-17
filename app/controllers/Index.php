<?php

class IndexController extends ApplicationController {
    // protected $layout = 'frontend';

    public function indexAction() {
        $signature = \WR\Input::request("signature");
        $timestamp = \WR\Input::request("timestamp");
        $nonce = \WR\Input::request("nonce");
        $echostr = \WR\Input::request("echostr");
        $token = $this->_config['token'];

        $params = array($token, $timestamp, $nonce);
        sort($params);
        $sha1 = sha1(implode($params));
        if($sha1 == $signature) {
            echo $echostr;
        } else {
            echo "";
        }
        exit();
    }
}
