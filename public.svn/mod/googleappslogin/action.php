<?php
/* Copyright (c) 2009 Google Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 * Author: Eric Bidelman <e.bidelman@google.com>
 */
dissss('2q');
session_start();
require_once 'models/OAuth.php';
require_once 'models/client.inc';
//require_once('models/common.inc.php');

$CONSUMER_KEY = 'elggnew.flatsourcing.com';
$CONSUMER_SECRET = '59mLT7eDYSJwtjUSmp7glE6f';


if ($_GET['model']) {
	
	$client = new OAuth_Client($CONSUMER_KEY, $CONSUMER_SECRET, SIG_METHOD_HMAC);
	
	if (!empty($client->access_token)) {
		$_SESSION['access_token'] = $client->access_token;
	}
	if (!empty($client->access_secret)) {
		$_SESSION['access_secret'] = $client->access_secret;
	}
	
	//$client->access_token = !empty($_SESSION['access_key']) ? $_SESSION['access_key'] : NULL;
	//$client->access_secret = !empty($_SESSION['access_secret']) ? $_SESSION['access_secret'] : NULL;
	
	if (!$client->authorized()) {
		
		if (empty($_GET['oauth_verifier'])) {
			$result = $client->oauth_authorize();
			header('Location: ' . $result);
			exit;
		} else {
			$result = $client->oauth_fetch_access_token($_GET['oauth_verifier'], $_SESSION['request_key'], $_SESSION['request_secret']);
		}
		
	} else {
		$response = $client->unread_messages();
		print_r($response);
	}
	
	$result = $client->execute('https://sites.google.com/feeds/site/' . $CONSUMER_KEY . '/', '1.1');
	$site_list = $client->fetch_sites($result);
	
	foreach ($site_list as $site) {
		$feed = $site['feed'];
		$title = $site['title'];
		
		echo'<b>' . $title . '</b><pre>';
		print_r($client->execute($feed, '1.1'));
		echo'</pre>';
	}
	
	echo 'Done.';
	exit;
}




//$CONSUMER_KEY = 'thinkglobalschool.com';
//$CONSUMER_SECRET = 'RXTYYNGV/+gWxnGC8LrGaWCI';

$PRIV_KEY_FILE = '/tmp/elggnew.rsakey.pem';

session_start();
require_once('models/common.inc.php');

$CONSUMER_KEY = !empty($_SESSION['consumer_key']) ? $_SESSION['consumer_key'] : $CONSUMER_KEY;
$CONSUMER_SECRET = !empty($_SESSION['consumer_secret']) ? $_SESSION['consumer_secret'] : $CONSUMER_SECRET;

$consumer = new OAuthConsumer($CONSUMER_KEY, $CONSUMER_SECRET);

// OAuth v1.0a contains the oauth_verifier parameter coming back from the SP
if($_GET['oauth_verifier'] && !isset($_SESSION['oauth_verifier'])) {
  $_SESSION['oauth_verifier'] = $_GET['oauth_verifier'];
}

$scope = 'https://mail.google.com/mail/feed/atom/ https://sites.google.com/feeds';
$http_method = @$_REQUEST['http_method'];
$feedUri = @$_REQUEST['feedUri'];
$postData = @$_REQUEST['postData'];
$privKey = '';
$token_endpoint = @$_REQUEST['token_endpoint'];

$callback_url = "https://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}";

// OAuth token from token/secret parameters
$token = @$_REQUEST['oauth_token'];
$token_secret = isset($_SESSION['token_secret']) ? $_SESSION['token_secret'] : @$_REQUEST['token_secret'];
$oauth_token = $token ? new OAuthToken($token, $token_secret) : NULL;

//$sig_method = isset($_REQUEST['sig_method']) ? $SIG_METHODS[$_REQUEST['sig_method']] : $hmac_method;
$sig_method = $hmac_method;

/**
 *  Main action controller
 */
switch(@$_REQUEST['action']) {


	case 'access_token':

	if (empty($_GET['oauth_verifier']) && empty($token)) {
		
		// Cleanup: started the process to get a new access token
		unset($_SESSION['access_token']);
		unset($_SESSION['token_secret']);
		unset($_SESSION['oauth_verifier']);
		unset($_SESSION['consumer_key']);
		unset($_SESSION['consumer_secret']);
		
		// STEP 1: Fetch request token
		
		// Use the user's own private key if set. Defaults to the Playground's.
		if ($privKey) {
		  $_SESSION['privKey'] = $privKey;
		} else {
		  unset($_SESSION['privKey']);
		}
		
		// Use the user's own consumer key ifset. Defaults to the Playground's.
		if ($_REQUEST['consumer_key']) {
		  $_SESSION['consumer_key'] = $_REQUEST['consumer_key'];
		} else {
		  unset($_SESSION['consumer_key']);
		}
		
		// Use the user's own consumer secret if set. Defaults to the Playground's.
		if ($_REQUEST['consumer_secret']) {
		  $_SESSION['consumer_secret'] = $_REQUEST['consumer_secret'];
		} else {
		  unset($_SESSION['consumer_secret']);
		}
		
		$token_endpoint = 'https://www.google.com/accounts/OAuthGetRequestToken';
		
		// Handle certain Google Data scopes that have their own approval pages.
		if ($scope) {
		  // Health still uses OAuth v1.0
		  if (preg_match('/health/', $scope) || preg_match('/h9/', $scope)) {
			$params = array('scope' => $scope);
		  } else {
			// Use the OAuth v1.0a flow (callback in the request token step)
			$params = array('scope' => $scope, 'oauth_callback' => $callback_url);
		  }
		  $url = $token_endpoint . '?scope=' . urlencode($scope);
		} else {
		  $params = array('oauth_callback' => $callback_url);
		  $url = $token_endpoint;
		}
		
		// Installed app use case
		if ($_REQUEST['xoauth_displayname'] != NULL) {
		  $params['xoauth_displayname'] = $_REQUEST['xoauth_displayname'];
		  $params['oauth_callback'] = 'oob';
		  $url .= (preg_match('/\?/', $url, $matches) == 0) ? '?' : '&';
		  $url .= 'xoauth_displayname=' . urlencode($_REQUEST['xoauth_displayname']);
		}
		
		// GET https://www.google.com/accounts/OAuthGetRequestToken?scope=<SCOPEs>
		$req = OAuthRequest::from_consumer_and_token($consumer, $oauth_token, 'GET',
													 $url, $params);
		$req->sign_request($sig_method, $consumer, $oauth_token, $privKey);
		
		// Fill response
		//echo json_encode();
		/*
		echo '<pre>';
		print_r(send_signed_request('GET', $url, array($req->to_header()), null, false));
		echo '</pre>';exit;
		*/
		
		/*
		exit;  // ajax request, just stop exec
		
		break;
		
		// STEP 2: Authorize the request token.  Redirect user to approval page.
		case 'authorize':
		
		
		echo '<pre>';
		print_r($url);
		echo '</pre>';
		*/
		$response = send_signed_request('GET', $url, array($req->to_header()), null, false);
		//echo '<pre>';
		//print_r($response);exit;
		if ('oauth' != substr($response, 0, 5)) {
			exit;
		}
		$tokens = explode('&', $response);
		$result = array();
		foreach ($tokens as $token) {
			$keys = explode('=', $token);
			$result[$keys[0]] = $keys[1];
		}
		
		preg_match('/oauth_token=(.*)&oauth_token_secret=(.*)/', $response, $matches);
		$_SESSION['access_token'] = urldecode($result['oauth_token']);
		$_SESSION['token_secret'] = urldecode($result['oauth_token_secret']);
		
		$oauth_token = !empty($result) ? new OAuthToken($result['oauth_token'], $result['oauth_token_secret']) : NULL;
		//print_r($oauth_token);exit;	
		$token_endpoint = 'https://www.google.com/accounts/OAuthAuthorizeToken';
		// OAuth v1.0a - no callback URL provided to the authorization page.
		$auth_url = $token_endpoint . '?oauth_token=' . $oauth_token->key . '&hd=' . urlencode($CONSUMER_KEY);
		
		// Cover special cases for Google Health and YouTube approval pages
		if (preg_match('/health/', $scope) || preg_match('/h9/', $scope)) {
		  // Google Health - append permission=1 parameter to read profiles
		  // and callback URL for v1.0 flow.
		  $auth_url .= '&permission=1&oauth_callback=' . urlencode($callback_url);
		}
		//print_r($_SESSION);
		// Redirect to https://www.google.com/accounts/OAuthAuthorizeToken
		/*
		echo json_encode(array(
			'html_link' => '',
			'base_string' => '',
			'response' => "<script>window.location='$auth_url';</script>"
		));
		*/
		header('Location: ' . $auth_url);
		echo '<a href="' . $auth_url . '&hd=' . urlencode($CONSUMER_KEY) . '">' . $auth_url . '</a>';
		exit;
	} else {

		$token_endpoint = 'https://www.google.com/accounts/OAuthGetAccessToken';
		
		// GET https://www.google.com/accounts/OAuthGetAccessToken
		$req = OAuthRequest::from_consumer_and_token($consumer, $oauth_token, 'GET',
			$token_endpoint, array('oauth_verifier' => $_SESSION['oauth_verifier']));
		$req->sign_request($sig_method, $consumer, $oauth_token, $privKey);
	
	
		$response = send_signed_request('GET', $token_endpoint, array($req->to_header()), null, false);
		
		// Extract and remember oauth_token (access token) and oauth_token_secret
		preg_match('/oauth_token=(.*)&oauth_token_secret=(.*)/', $response, $matches);
		$_SESSION['access_token'] = urldecode(@$matches[1]);
		$_SESSION['token_secret'] = urldecode(@$matches[2]);
		//print_r($_SESSION);exit;
		$url = $_SERVER['PHP_SELF'] . '?action=sites';
		//$url = 'http://elggnew.flatsourcing.com/mod/googleappslogin/action.php?action=execute&access_token=' . urlencode($_SESSION['access_token']) . '&token_secret=' . urlencode($_SESSION['token_secret']);
		//$url = 'http://elggnew.flatsourcing.com/mod/googleappslogin/action.php?action=sites';
		header('Location: ' . $url);
		echo '<a href="' . $url .'">' . $url . '</a>';
		exit;  // ajax request, just stop exec
	}
    break;

	// Fetch data.  User has valid access token.
  case 'mail':
    $feedUri = 'https://mail.google.com/mail/feed/atom/';
	
	$oauth_token = new OAuthToken($_SESSION['access_token'], $_SESSION['token_secret']);
	$req = OAuthRequest::from_consumer_and_token($consumer, $oauth_token,
                                                 'GET', $feedUri, array());
    $req->sign_request($sig_method, $consumer, $oauth_token);
	$content_type = 'Content-Type: application/atom+xml';
    $gdataVersion = 'GData-Version: 2.0';
	
	$result = send_signed_request('GET', $feedUri,
                                  array($req->to_header(), $content_type, $gdataVersion), null, false);
	print_r($result);
	
	exit;
	break;
	
	case 'sites':
		
		//$feedUri = 'http://sites.google.com/feeds/activity/elggnew.flatsourcing.com/test';
		//$feedUri = 'http://sites.google.com/feeds/site/elggnew.flatsourcing.com/';
		$feedUri = 'https://sites.google.com/feeds/site/' . $CONSUMER_KEY . '/';
		
		$oauth_token = new OAuthToken($_SESSION['access_token'], $_SESSION['token_secret']);
		$req = OAuthRequest::from_consumer_and_token($consumer, $oauth_token,
													 'GET', $feedUri, array());
		$req->sign_request($sig_method, $consumer, $oauth_token);
		$content_type = 'Content-Type: application/atom+xml';
		$gdataVersion = 'GData-Version: 1.1';
		
		$result = send_signed_request('GET', $feedUri,
									  array($req->to_header(), $content_type, $gdataVersion), null, false);
		//die('2');
		$rss = simplexml_load_string($result);
		if ($rss->entry) {
		foreach ($rss->entry as $item) {
				echo '<h1>' . $item->title . '</h1>';
				$feedUri = preg_replace('!(.*)feeds/site/(.*)!', '$1feeds/activity/$2', $item->id);
				
				$oauth_token = new OAuthToken($_SESSION['access_token'], $_SESSION['token_secret']);
				$req = OAuthRequest::from_consumer_and_token($consumer, $oauth_token,
															 'GET', $feedUri, array());
				$req->sign_request($sig_method, $consumer, $oauth_token);
				$content_type = 'Content-Type: application/atom+xml';
				$gdataVersion = 'GData-Version: 1.1';
				
				$result = send_signed_request('GET', $feedUri,
											  array($req->to_header(), $content_type, $gdataVersion), null, false);
				$rss = simplexml_load_string($result);
				foreach ($rss->entry as $item) {
					echo '<p>' . $item->id . '</p>';
					echo '<p>' . $item->updated . '</p>';
					echo '<b>' . $item->summary->div->asXML() . '</b>';
				}
				
		}
		} else {
			echo'<pre>';
			print_r($_SESSION);
			echo'</pre>';
			echo'<pre>';
			print_r($result);
			echo'</pre>';
			echo'<pre>';
			print_r($req);
			echo'</pre>';
			exit;
		}
		
		//print_r($result);
		
		exit;
		
	break;
  // Fetch data.  User has valid access token.
  case 'execute':
    
	$feedUri = 'https://mail.google.com/mail/feed/atom/';
	
	$oauth_token = new OAuthToken($_REQUEST['access_token'], $_REQUEST['token_secret']);
	$req = OAuthRequest::from_consumer_and_token($consumer, $oauth_token,
                                                 'GET', $feedUri, array());
    $req->sign_request($sig_method, $consumer, $oauth_token);
	$content_type = 'Content-Type: application/atom+xml';
    $gdataVersion = 'GData-Version: 2.0';
	
    $result = send_signed_request('GET', $feedUri,
                                  array($req->to_header(), $content_type, $gdataVersion), null, false);
	print_r($result);
	exit;
	
    break;

  // 'available feeds' button. Uses AuthSubTokenInfo to parse out feeds the token can access.
  case 'discovery':
    $url = 'https://www.google.com/accounts/AuthSubTokenInfo';
    $req = OAuthRequest::from_consumer_and_token($consumer, $oauth_token, 'GET',
                                                 $url);
    $req->sign_request($sig_method, $consumer, $oauth_token, $privKey);

    $response = send_signed_request('GET', $req);

    // Parse out valid scopes returned for this access token
    preg_match_all('/Scope.*=(.*)/', $response, $matches);

    echo json_encode(array(
        'html_link' => $req->to_url(),
        'base_string' => $req->get_signature_base_string(),
        'authorization_header' => $req->to_header(),
        'args' => json_encode($matches[1]),
        'callback' => 'getAvailableFeeds'
    ));

    exit;  // ajax request, just stop execution

    break;
}
?>
