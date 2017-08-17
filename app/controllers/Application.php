<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * The main application controller.
 *
 * All application controllers may inherit from this controller.
 * This controller uses Layout class (@see lib/Layout.php)
 */
class ApplicationController extends Yaf\Controller_Abstract {
    const RET_OK = 200;
    const RET_NOT_MODIFIED = 304;
    const RET_INVALID_PARAM = 400;
    const RET_NOT_AUTH = 401;
    const RET_NOT_FOUND = 404;
    const RET_FORBIDDEN = 403;
    const RET_UNKNOWN = 500;

    /**
     * The name of layout file.
     *
     * The name of layout file to be used for this controller ommiting extension.
     * Layout class will use extension from application config ini. 
     *
     * @var string
     */
    protected $layout;

    /**
     * The session instance.
     *
     * Yaf\Session instance to be used for this application.
     *
     */
    protected $session;

    /**
     * A Yaf\Config\Ini object that contains application configuration data.
     * 
     * @var Yaf\Config\Ini
     */
    private $config;

    protected $_column_names = array(

    );

    /**
     * Initialize layout and session.
     *
     * In this method can be initialized anything that could be usefull for 
     * the controller.
     *
     * @return void
     */
    public function init() {
        // Set the layout.
        $this->getView()->setLayout($this->layout);

        // Assign application config file to this controller
        $this->config = Yaf\Application::app()->getConfig();

        // Assign config file to views
        $this->getView()->config = $this->config;

        $this->_init();
    }

    /**
     * When assign a public property to controller, this property will be 
     * available to action view template too.
     *
     * @param string $name  the name of the property
     * @param mixed  $value the value of the property
     *
     * @return void 
     */
    public function __set($name, $value) {
        $this->$name = $value;
        $this->getView()->assignRef($name, $value);
    }

    public function getConfig() {
        return $this->config;
    }

    /**
     * Cancel current action proccess and forward to {@link notFound()} method.
     *
     * @return false
     */
    public function forwardTo404() {
        $this->forward('Index', 'application', 'notFound');
        $this->getView()->setScriptPath($this->getConfig()->application->directory 
            . "/views");
        header('HTTP/1.0 404 Not Found');
        return false;       
    }

    /**
     * Renders a 404 Not Found template view
     *
     * @return void
     */
    public function notFoundAction() {

    }

    protected $_config;
    protected $_crumbKey = "DATA";

    protected $_referrerUrl = "";
    protected $_city_list = array();
    protected static $_modelList = null;

    private function _init() {
        $config = \Yaf\Registry::get("config")->toArray();
        $this->_config = $config;

        $this->_referrerUrl = isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : "";

        $this->getView()->assignRef("referrerUrl", $this->_referrerUrl);
        $this->view = $this->getView();
        $this->view->assign('viewPath', APP_PATH . 'views');

    }

    protected function _getFlagValue($fieldName) {
        $data = isset($_POST[$fieldName]) ? $_POST[$fieldName] : array();
        if(!is_array($data)) {
            $data = array($data);
        }

        $ret = 0;
        foreach($data as $r) {
            $ret |= $r;
        }
        return $ret;
    }

    protected function _getJoinValue($field, $splitter = ',') {
        $data = isset($_POST[$field]) ? $_POST[$field] : array();
        if(!is_array($data)) {
            $data = array($data);
        }
        return join($splitter, $data);
    }

    protected function _getClientIp() {
        $ip = self::getIp();
        $localip = isset($_COOKIE['I']) ? $_COOKIE['I'] : '';
        $localip = join(".", array_map("hexdec", str_split($localip, 2)));
        return $localip ? (($localip . ',') .  $ip) : $ip;
    }

    /**
     * @return null
     */
    static function getIp() {
        foreach(array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR') as $v) {
            if(isset($_SERVER[$v]) and !empty($_SERVER[$v]))
                return $_SERVER[$v];
        }
        return "";
    }

    protected function renderJsonEx($result, $code = 200, $msg = 'ok', $jsonp = false) {
        $this->renderJson(array("code" => $code, "msg" => $msg, "result" => $result), $jsonp);
    }

    /**
     * @param $class
     * @param $method
     * @param \Exception $e
     */
    protected function _processException($class, $method, $e) {
        $code = $e->getCode();
        if($code <= 0) {
            $code = self::RET_INVALID_PARAM;
        }
        $msg = $e->getMessage();
        if($code != 200) {
            foreach($this->_column_names as $key => $value) {
                $msg = str_replace($key, $value, $msg);
            }
        }
        $this->renderJsonEx("", $code, $msg);
    }

    protected function renderJson(array $data, $jsonp = false) {
        header("Content-Type: application/json; charset=utf8");

        if($jsonp) {
            $jsoncallback = $_REQUEST["jsoncallback"];
            echo $jsoncallback . '(' . json_encode($data, JSON_UNESCAPED_UNICODE) . ')';
        } else {
            echo json_encode($data, JSON_UNESCAPED_UNICODE);
        }
        exit;
    }

}
