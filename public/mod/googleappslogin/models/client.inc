<?php

	/**
	 * Class for use OAuth
	 * 
	 * @package GoogleAppsLogin
	 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
	 * @author Alexander Ulitin <alexander.ulitin@flatsoft.com>
	 * @copyright FlatSourcing 2010
	 * @link http://elgg.org/
	 */

DEFINE('SIG_METHOD_PLAINTEXT', 'plaintext');
DEFINE('SIG_METHOD_HMAC', 'hmac_sha1');
DEFINE('SIG_METHOD_RSA', 'rsa');

class OAuth_Client {
	var $key = null;
	var $secret = null;
	var $consumer = null;
	var $signature_method = null;
	var $callback_url = null;
	var $access_token = null;
	var $access_secret = null;
	var $scope = 'https://mail.google.com/mail/feed/atom/ https://sites.google.com/feeds';

	public function OAuth_Client($consumer_key, $consumer_secret, 
								$signature_method_name = SIG_METHOD_HMAC, $priv_key = '') {
		
		$this->callback_url = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
		$this->key = $consumer_key;
		$this->secret = $consumer_secret;
		$this->priv_key = $priv_key;
		
		$this->consumer = new OAuthConsumer($this->key, $this->secret, null);
		
		$this->access_token = $_SESSION['access_token'];
		$this->access_secret = $_SESSION['token_secret'];
		
		$user = $_SESSION['user'];
		$scopes = array();
		//if (isset($user->googleapps_sync_email) && $user->googleapps_sync_email != 'no') {
		$scopes[] = 'https://mail.google.com/mail/feed/atom/';
		//}
		//if (isset($user->googleapps_sync_sites) && $user->googleapps_sync_sites != 'no') {
		$scopes[] = 'https://sites.google.com/feeds';
		
		$scopes[] = 'http://docs.google.com/feeds/';
		$scopes[] = 'http://spreadsheets.google.com/feeds/';
		//}
		if ($scopes) {
			$this->scope = implode(' ', $scopes);
		}
		
		switch ($signature_method_name) {
		case SIG_METHOD_PLAINTEXT:
			$this->signature_method = new OAuthSignatureMethod_PLAINTEXT();
			break;
		case SIG_METHOD_RSA:
			$this->signature_method = new OAuthSignatureMethod_RSA_SHA1();
			break;
		case SIG_METHOD_HMAC:
		default:
			$this->signature_method = new OAuthSignatureMethod_HMAC_SHA1();
			break;
		}
	}

	public function oauth_fetch_request_token() {
		
		$endpoint = 'https://www.google.com/accounts/OAuthGetRequestToken';
		
		// Handle certain Google Data scopes that have their own approval pages.
		if ($this->scope) {
			// Health still uses OAuth v1.0
			if (preg_match('/health/', $this->scope) || preg_match('/h9/', $this->scope)) {
				$params = array('scope' => $this->scope);
			} else {
				// Use the OAuth v1.0a flow (callback in the request token step)
				$params = array('scope' => $this->scope, 'oauth_callback' => $this->callback_url);
			}
			$url = $endpoint . '?scope=' . urlencode($this->scope);
		} else {
			$params = array('oauth_callback' => $this->callback_url);
			$url = $endpoint;
		}
		
		$url = $endpoint . '?scope=' . urlencode($this->scope);
		$req_req = OAuthRequest::from_consumer_and_token($this->consumer, NULL, "GET", $endpoint, $params);
		$req_req->sign_request($this->signature_method, $this->consumer, NULL);
		
		$service_response = $this->send_signed_request('GET', $url, array($req_req->to_header()), null, false);
		
		parse_str($service_response, $result);
		
		if (empty($result['oauth_token']) || empty($result['oauth_token_secret'])) {
			die('Can not fetch request token. Server\'s response:' . "\n" . $service_response);
		}
		$request_key = $result['oauth_token'];
		$request_secret = $result['oauth_token_secret'];
		$_SESSION['request_key'] = $request_key;
		$_SESSION['request_secret'] = $request_secret;
		return new OAuthToken($request_key, $request_secret);
	}

	public function oauth_authorize() {
		
		$endpoint = 'https://www.google.com/accounts/OAuthAuthorizeToken';
		$rt = $this->oauth_fetch_request_token();
		//$url = $endpoint . '?oauth_callback=' . urlencode(($_SERVER['HTTPS'] ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . '/?action=callback') . '&oauth_token=' . urlencode($rt->key);
		$url = $endpoint . '?oauth_token=' . $rt->key . '&hd=' . urlencode($this->key);
		
		// Cover special cases for Google Health and YouTube approval pages
		if (preg_match('/health/', $this->scope) || preg_match('/h9/', $this->scope)) {
			// Google Health - append permission=1 parameter to read profiles
			// and callback URL for v1.0 flow.
			$url .= '&permission=1&oauth_callback=' . urlencode($this->callback_url);
		}
		
		return $url;
		
	}

	public function authorized() {
		
		if (!empty($_SESSION['access_token']) && !empty($_SESSION['access_secret'])){
			//empty($this->access_token) && empty($this->access_secret)) {
			$this->access_token = $_SESSION['access_token'];
			$this->access_secret = $_SESSION['access_secret'];
		}
		
		//return !empty($this->access_token) && !empty($this->access_secret);
		
		return (!empty($_SESSION['access_token']) && !empty($_SESSION['access_secret']));
		
	}

	public function unauthorize() {
		
		unset($_SESSION['access_token']);
		unset($_SESSION['access_secret']);
		$this->access_token = NULL;
		$this->access_secret = NULL;
		
	}

	public function oauth_fetch_access_token($verifier, $request_key, $request_secret) {
		
		$endpoint = 'https://www.google.com/accounts/OAuthGetAccessToken';
		
		$request_token = new OAuthToken($request_key, $request_secret);
		
		$acc_req = OAuthRequest::from_consumer_and_token($this->consumer, $request_token, 
														'GET', $endpoint, 
														array('oauth_verifier' => $verifier));
		$acc_req->sign_request($this->signature_method, $this->consumer, $request_token);
		$url = $endpoint;
		
		$service_response = $this->send_signed_request('GET', $url, array($acc_req->to_header()), null, false);
		
		$access = array();
		parse_str($service_response, $access);
		
		$this->access_token = $access['oauth_token'];
		$this->access_secret = $access['oauth_token_secret'];
		
		$token = new OAuthToken($this->access_token, $this->access_secret);
		return $token;
	}

	public function execute($endpoint, $version = '2.0', $params = null) {
		
		if (empty($this->access_token) || empty($this->access_secret)) {
			return false;
		}
		
		if (empty($params)) {
			$params = array();
		}
		$access_token = new OAuthToken($this->access_token, $this->access_secret);
		
		$echo_req = OAuthRequest::from_consumer_and_token($this->consumer, $access_token, 
														'GET', $endpoint, $params);
		$echo_req->sign_request($this->signature_method, $this->consumer, $access_token);
		$content_type = 'Content-Type: application/atom+xml';
		$gdataVersion = 'GData-Version: ' . $version;
		
		return $this->send_signed_request('GET', $endpoint,
											array($echo_req->to_header(), $content_type, $gdataVersion),
											null, false);
		
	}
	
	public function fetch_sites($xml) {
		
		$rss = simplexml_load_string($xml);
    
    $list = array();
		foreach ($rss->entry as $item) {
			$feedUri = preg_replace('!(.*)feeds/site/(.*)!', '$1feeds/activity/$2', $item->id);
			
			$namespaces = array_merge(array('' => ''), $rss->getDocNamespaces(true));
			$item_array = array();
			$item_array = $this->xml2phpArray($item, $namespaces, $item_array);
      
      $site_url = '';
			foreach ($item->link[0]->attributes() as $key => $val) {
				if ($key == 'href') {
					$site_url = $val;
				}
      }

      foreach ($item->link[1]->attributes() as $key => $val) {
          if ($key == 'href') {
            $acl_url = $val;
          }
      }
        
      
      
      $site = array();
			$site['site_id'] = $item_array['id'];
			$site['title'] = $item_array['title'];
			$site['feed'] = $feedUri;
      $site['url'] = $site_url;
      $site['acl'] = $acl_url;
			$site['modified'] = strtotime($item_array['updated']);
			
			$list[] = $site;
    }

		return $list;
		
  }

	
	public function unread_messages($list = false) {
		
		if ($this->authorized()) {
			
			$response = $this->execute('https://mail.google.com/mail/feed/atom/');
			
			if (!$list) {
				preg_match('/<fullcount>(.*)<\/fullcount>/', $response, $count);
				return !empty($count) && !empty($count[1]) ? $count[1] : 0;
			} else {
				return simplexml_load_string($response);
			}
		}
		
		return false;
	}

	/**
	 * Makes an HTTP request to the specified URL
	 *
	 * @param string $http_method The HTTP method (GET, POST, PUT, DELETE)
	 * @param string $url Full URL of the resource to access
	 * @param array $extraHeaders (optional) Additional headers to include in each
	 *     request. Elements are header/value pair strings ('Host: example.com')
	 * @param string $postData (optional) POST/PUT request body
	 * @param bool $returnResponseHeaders True if resp. headers should be returned.
	 * @return string Response body from the server
	 */
	private function send_signed_request($http_method, $url, $extraHeaders=null,
									 $postData=null, $returnResponseHeaders=true) {
		$curl = curl_init($url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_FAILONERROR, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		
		// Return request headers in the reponse
		curl_setopt($curl, CURLINFO_HEADER_OUT, true);
		
		// Return response headers ni the response?
		if ($returnResponseHeaders) {
			curl_setopt($curl, CURLOPT_HEADER, true);
		}
		
		$headers = array();
		//$headers[] = 'GData-Version: 2.0';  // use GData v2 by default
		if (is_array($extraHeaders)) {
			$headers = array_merge($headers, $extraHeaders);
		}
		
		// Setup default curl options for each type of HTTP request.
		// This is also a great place to add additional headers for each request.
		switch($http_method) {
			case 'GET':
				curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
				break;
			case 'POST':
				curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
				curl_setopt($curl, CURLOPT_POST, 1);
				curl_setopt($curl, CURLOPT_POSTFIELDS, $postData);
				break;
			case 'PUT':
				$headers[] = 'If-Match: *';
				curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
				curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $http_method);
				curl_setopt($curl, CURLOPT_POSTFIELDS, $postData);
				break;
			case 'DELETE':
				$headers[] = 'If-Match: *';
				curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
				curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $http_method);
				break;
			default:
				curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
		}
		
		// Execute the request.  If an error occures, fill the response body with it.
		$response = curl_exec($curl);
		if (!$response) {
			$response = curl_error($curl);
		}
		
		if ($returnResponseHeaders) {
			// Add server's response headers to our response body
			$response = curl_getinfo($curl, CURLINFO_HEADER_OUT) . $response;
		}
		
		curl_close($curl);
		
		return $response;
	}
	
	public function xml2phpArray($xml, $namespaces, $arr) {
		
		$iter = 0;
		
		foreach ($namespaces as $namespace => $namespaceUrl) {
			foreach ($xml->children($namespaceUrl) as $b) {
				$a = $b->getName();
				
				if ($b->children($namespaceUrl)) {
					$arr[$a][$iter] = array();
					$arr[$a][$iter] = $this->xml2phpArray($b, $namespaces, $arr[$a][$iter]);
				} else {
					$arr[$a] = trim($b[0]);
				}
				
				$iter++;
			}
		}
		
		return $arr;
	}

}


?>
