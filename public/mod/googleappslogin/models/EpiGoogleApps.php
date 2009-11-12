<?php
class EpiGoogleApps extends EpiOAuth
{
  const EPIGOOGLEAPPS_SIGNATURE_METHOD = 'RSA-SHA1';

  protected $requestTokenUrl = 'https://www.google.com/accounts/OAuthGetRequestToken';
  protected $accessTokenUrl = 'https://www.google.com/accounts/OAuthGetAccessToken';
  protected $authorizeUrl = 'https://www.google.com/accounts/OAuthGetRequestToken';
  protected $authenticateUrl = 'http://google.com/apps/';
  protected $apiUrl = 'http://googleapps.com';

  public function __call($name, $params = null)
  {
    $parts  = explode('_', $name);
    $method = strtoupper(array_shift($parts));
    $parts  = implode('_', $parts);
    $url    = $this->apiUrl . '/' . preg_replace('/[A-Z]|[0-9]+/e', "'/'.strtolower('\\0')", $parts) . '.json';
    if(!empty($params))
      $args = array_shift($params);

    return new EpiGoogleAppsJson(call_user_func(array($this, 'httpRequest'), $method, $url, $args));
  }

  public function __construct($consumerKey = null, $consumerSecret = null, $oauthToken = null, $oauthTokenSecret = null)
  {
    parent::__construct($consumerKey, $consumerSecret, self::EPIGOOGLEAPPS_SIGNATURE_METHOD);
    $this->setToken($oauthToken, $oauthTokenSecret);
  }
}



class EpiGoogleAppsJson
{
  private $resp;

  public function __construct($resp)
  {
    $this->resp = $resp;
  }

  public function __get($name)
  {
    $this->responseText = $this->resp->data;
    $this->response = (array)json_decode($this->responseText, 1);
    foreach($this->response as $k => $v)
    {
      $this->$k = $v;
    }

    return $this->$name;
  }
}
