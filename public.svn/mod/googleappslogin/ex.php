<?php
/**
 * Makes an HTTP request to the specified URL
 * @param string $http_method The HTTP method (GET, POST, PUT, DELETE)
 * @param string $url Full URL of the resource to access
 * @param string $auth_header (optional) Authorization header
 * @param string $postData (optional) POST/PUT request body
 * @return string Response body from the server
 */
function send_request($http_method, $url, $auth_header=null, $postData=null) {
  $curl = curl_init($url);
  curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($curl, CURLOPT_FAILONERROR, false);
  curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
  
  switch($http_method) {
    case 'GET':
      if ($auth_header) {
        curl_setopt($curl, CURLOPT_HTTPHEADER, array($auth_header));
      }
      break;
    case 'POST':
      curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/atom+xml', 
                                                   $auth_header)); 
      curl_setopt($curl, CURLOPT_POST, 1);                                       
      curl_setopt($curl, CURLOPT_POSTFIELDS, $postData);
      break;
    case 'PUT':
      curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/atom+xml', 
                                                   $auth_header)); 
      curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $http_method);
      curl_setopt($curl, CURLOPT_POSTFIELDS, $postData);
      break;
    case 'DELETE':
      curl_setopt($curl, CURLOPT_HTTPHEADER, array($auth_header)); 
      curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $http_method); 
      break;
  }
  $response = curl_exec($curl);
  if (!$response) {
    $response = curl_error($curl);
  }
  curl_close($curl);
  return $response;
}


ini_set("display_errors", "1");
ini_set("display_startup_errors", "1");
ini_set('error_reporting', E_ALL);
ini_set('pcre.backtrack_limit', 10000000);
die(dirname(dirname(__FILE__)) . "/oauth/OAuth.php");
require_once (dirname(dirname(__FILE__)) . "/oauth/OAuth.php");

//global $CONFIG;

//if (empty($CONFIG->input['openid_ns'])){
//	$CONFIG->input = array_merge($CONFIG->input, $_POST);
//}
//$google = Google_OpenID::create_from_response($CONFIG->input);
//$google->set_home_url($googleapps_domain);
//$response = $google->get_response();
//$request_token = $response['openid_ext2_request_token'];
//$user= $google->get_email();

$CONSUMER_KEY = 'elggnew.flatsourcing.com';
$CONSUMER_SECRET = '59mLT7eDYSJwtjUSmp7glE6f';
$consumer = new OAuthConsumer($CONSUMER_KEY, $CONSUMER_SECRET, NULL);
echo '<pre>';print_r($consumer);
$scope = 'http://www.google.com/calendar/feeds/ http://docs.google.com/feeds/ https://mail.google.com/mail/feed/atom';
$base_feed = 'https://www.google.com/accounts/OAuthGetRequestToken';
$sig_method = new OAuthSignatureMethod_HMAC_SHA1();
$req_req = OAuthRequest::from_consumer_and_token($consumer, NULL, "GET", $base_feed, $params);
$req_req->set_parameter('scope', $scope);
$req_req->sign_request($sig_method, $consumer, NULL);
echo '<pre>';print_r($req_req);
$url = $req_req->to_url();
$response = send_request($req_req->get_normalized_http_method(), $url, $req_req->to_header());
echo '<pre>';print_r($response);exit;



$base_feed = 'https://www.google.com/accounts/OAuthGetAccessToken';
$endpoint = $base_feed;
$parsed = parse_url($endpoint);
parse_str($parsed['query'], $params);

$token = new OAuthToken($request_token, NULL);
echo '<pre>';print_r($token);

$sig_method = new OAuthSignatureMethod_HMAC_SHA1();
$req_req = OAuthRequest::from_consumer_and_token($consumer, $token, "GET", $base_feed, $params);
//$req_req->set_parameter('scope', $scope);
$req_req->sign_request($sig_method, $consumer, $token);
echo '<pre>';print_r($req_req);
$url = $req_req->to_url();
$response = send_request($req_req->get_normalized_http_method(), $url, $req_req->to_header());
echo '<pre>';print_r($response);
$tokens = explode('&', $response);
$result = array();
foreach ($tokens as $token) {
	$keys = explode('=', $token);
	$result[$keys[0]] = $keys[1];
}
$token = new OAuthToken($result['oauth_token'], $result['oauth_token_secret']);
echo '<pre>';print_r($token);
$req_req = OAuthRequest::from_consumer_and_token($consumer, $token, "GET", 'http://www.google.com/calendar/feeds/default/owncalendars/full' . urlencode($user), $params);
//$req_req->set_parameter('scope', $scope);
$req_req->sign_request($sig_method, $consumer, $token);
echo '<pre>';print_r($req_req);
$url = $req_req->to_url();
echo '<pre>';print_r($req_req->get_normalized_http_method());
echo '<pre>';print_r($url);
echo '<pre>';print_r($req_req->to_header());
//echo '<pre>Headers:</pre>';
//echo '<pre>';print_r(get_headers($url));
$response = send_request($req_req->get_normalized_http_method(), $url, $req_req->to_header());

echo '<pre>';print_r($response);exit;

/*
$httpConfig['method']     = 'GET';
$httpConfig['target']     = 'http://www.somedomain.com/index.html';
$httpConfig['referrer']   = 'http://www.somedomain.com';
$httpConfig['user_agent'] = 'My Crawler';
$httpConfig['timeout']    = '30';
$httpConfig['params']     = array('var1' => 'testvalue', 'var2' => 'somevalue');

$http = new Http($url);
$http->initialize($httpConfig);
$response = $http->getResult();
*/

//echo '<pre>';print_r($response);exit;
//forward('/mod/googleappslogin/ex.php?token=' . $request_token);

exit;
?>
