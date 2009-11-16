<?php

/**
 * Description of GoogleOpenID
 *
 * @author andreyp
 */
class Google_OpenID
{
    private $_http;
    private $_start_url;
    private $_endpoint_url;

    private $_home_url;
    private $_return_url;

    private $_use_oauth = false;

    private $_response;

    public function __construct() {
        $this->_http = new Http();
    }

    public static function create_from_response(array $response = array()) {
        $google = new self();

        $_response = array();
        foreach ($response as $key=>$value) {
            if (preg_match('/^openid/', $key)) {
                $_response[$key] = $value;
            }
        }

        $google->set_response($_response);

        return $google;
    }

    function resolve_endpoint_url() {
        if (isset($this->_start_url)) {
            $response = $this->_http->execute($this->_start_url, null, 'GET');

            if (preg_match('/<URI>(.*?)<\/URI>/', $response, $matches)) {
                $this->_endpoint_url = $matches[1];
                return $this->_endpoint_url;
            } else {
                throw new Exception('Can\'t resolve endpoint url');
            }
        } else {
            throw new Exception('Start url is not set');
        }
    }

    function get_authorization_url() {

        if (!isset($this->_home_url)) {
            throw new Exception('Home url is not set');
        }
        if (!isset($this->_return_url)) {
            throw new Exception('Return url is not set');
        }

        if (!isset($this->_endpoint_url)) {
            $this->resolve_endpoint_url();
        }

        $params = array(
            'openid.ns' => 'http://specs.openid.net/auth/2.0',
            'openid.ns.pape' => 'http://specs.openid.net/extensions/pape/1.0',
            'openid.pape.max_auth_age' => 300,
            'openid.claimed_id' => 'http://specs.openid.net/auth/2.0/identifier_select',
            'openid.identity' => 'http://specs.openid.net/auth/2.0/identifier_select',
            'openid.return_to' => self::normalize_url($this->_return_url),
            'openid.realm' => self::normalize_url($this->_home_url),
//          'openid.assoc_handle=ABSmpf6DNMw',
            'openid.mode' => 'checkid_setup',
//            'openid.ns.ui' => 'http://specs.openid.net/extensions/ui/1.0',
//            'openid.ui.mode' => 'popup',
//            'openid.ui.icon' => 'true',
            'openid.ns.ax' => 'http://openid.net/srv/ax/1.0',
            'openid.ax.mode' => 'fetch_request',
            'openid.ax.required' => 'email,language,country,firstname,lastname',
            'openid.ax.type.email' => 'http://axschema.org/contact/email',
            'openid.ax.type.language' => 'http://axschema.org/pref/language',
            'openid.ax.type.country' => 'http://axschema.org/contact/country/home',
            'openid.ax.type.firstname' => 'http://axschema.org/namePerson/first',
            'openid.ax.type.lastname' => 'http://axschema.org/namePerson/last'
        );

        /**
         * @TODO using OAuth in the future
         */
        if ($this->_use_oauth) {
            $params['openid.ns.auth'] = 'http://specs.openid.net/extensions/oauth/1.0';
            $params['openid.ext2.consumer'] = self::normalize_url($this->_home_url);
        }

        $_params = array();
        foreach ($params as $key => $value) {
            array_push($_params, self::encode($key) . '=' . self::encode($value));
        }

        $url = $this->_endpoint_url;

        $url .= (preg_match('/\?/', $url) ? '&' : '?');

        $url .= implode('&', $_params);

        // var_dump

        foreach ($params as $key=>$value) {
            echo "<b>$key</b> = $value<br>\n\n";
        }
        echo "<br>";

        return $url;
    }

    public static function encode($string)
    {
        return rawurlencode(utf8_encode($string));
    }
    
    public static function normalize_url($url) {
        if (!preg_match('/^[\w]{3,5}\:\/\//', $url)) {
            $url = 'http://' . $url;
        }
        
        if (!$urlParts = parse_url($url)) {
            throw new Exception("can't parse");
        }

        $host = strtolower($urlParts['host']);
        $scheme = strtolower($urlParts['scheme']);

        if (isset($urlParts['port'])) {
            $port = intval($urlParts['port']);
        } else {
            $port = $scheme=='http'?80:443;
        }

        $retval = "{$scheme}://{$host}";

        if($port > 0 && ($scheme === 'http' && $port !== 80) || ($scheme === 'https' && $port !== 443)) {
            $retval .= ":{$port}";
        }

        if (isset($urlParts['path'])) {
            $retval .= $urlParts['path'];
        }

        if(!empty($urlParts['query'])) {
            $retval .= "?{$urlParts['query']}";
        }

        return $retval;
    }

    public function is_authorized() {
        if (!isset($this->_response)) {
            throw new Exception('Response is not set');
        }

        return($this->_response['openid.mode'] == 'id_res');
    }
    
    public function get_email() {
        return $this->get_response_attribute('openid.ext1.value.email');
    }

    public function get_firstname() {
        return $this->get_response_attribute('openid.ext1.value.firstname');
    }

    public function get_lastname() {
        return $this->get_response_attribute('openid.ext1.value.lastname');
    }

    public function get_response_attribute($attr) {
        if (!$this->is_authorized()) {
            throw new Exception('User is not authorized');
        }

        return $this->_response[$attr];
    }

    /**
     * GETTERS/SETTERS
     */

    public function get_start_url() {
        return $this->_start_url;
    }
    public function set_start_url($value) {
        $this->_start_url = $value;
    }

    public function get_return_url() {
        return $this->_return_url;
    }
    public function set_return_url($value) {
        $this->_return_url = $value;
    }

    public function get_home_url() {
        return $this->_home_url;
    }
    public function set_home_url($value) {
        $this->_home_url = $value;
    }

    public function get_response() {
        return $this->_response;
    }
    public function set_response($value) {
        $this->_response = $value;
    }
}
?>
