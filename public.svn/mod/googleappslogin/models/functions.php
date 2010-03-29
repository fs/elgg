<?php

	function googleappslogin_get_oauth_data($ajax = false) {
		
		$user = $_SESSION['user'];
		
		if (!empty($user->access_token)) {
			$_SESSION['access_token'] = $user->access_token;
		}
		if (!empty($user->token_secret)) {
			$_SESSION['token_secret'] = $user->token_secret;
		}
		
		require_once 'OAuth.php';
		require_once 'client.inc';
		
		$CONSUMER_KEY = get_plugin_setting('googleapps_domain', 'googleappslogin');
		$CONSUMER_SECRET = get_plugin_setting('login_secret', 'googleappslogin');
		
		$client = new OAuth_Client($CONSUMER_KEY, $CONSUMER_SECRET, SIG_METHOD_HMAC);
		
		if (!empty($client->access_token)) {
			$_SESSION['access_token'] = $client->access_token;
		}
		if (!empty($client->access_secret)) {
			$_SESSION['access_secret'] = $client->access_secret;
		}
		if ($client->authorized()) {

			$x = googleapps_fetch_oauth_data($client, $ajax);
			if ($ajax) {
				return $x;
			}
			
			// end of parsing server response
			
		} else {
			
			if (!$ajax) {
				// Authorise user in google for access to data
				$url = $client->oauth_authorize();
				header('Location: ' . $url);
				exit;
				
			} else {
				return false;
			}
			
		}
		
	}
	
	function googleapps_fetch_oauth_data($client, $ajax = false) {
		
		if (!is_object($client)) {
			return false;
		}
		
		$count = 0;
		$is_new_activity = false;
		
		$user = $_SESSION['user'];
		if ($user->googleapps_sync_email != 'no') {
			// Get count unread messages of gmail
			$count = $client->unread_messages();
			$_SESSION['new_google_mess'] = $count;
		}
		
		if ($user->googleapps_sync_sites != 'no') {
			// Get google site feeds list
			$result = $client->execute('https://sites.google.com/feeds/site/' . 
										$client->key . '/', '1.1');
			$response_list = $client->fetch_sites($result);
			
			$max_time = null;
			$times = array();
			
			$site_list = empty($user->site_list) ? array() : unserialize($user->site_list);
			
			if (empty($user->last_site_activity)) {
				$user->last_site_activity = 0;
			}
			
			// Parse server response for google sites activity stream
			foreach ($response_list as $site) {
				
				$title = $site['title'];
				$feed = $site['feed'];
				$site_exist = null;
				
				$site_access = $site_list[$title];
				if (empty($site_access)) {
					$site_list[$title] = 2;
					$site_access = 2;
				}
				
				// Get google sites activity stream
				$activity_xml = $client->execute($feed, '1.1');
				
				$rss = simplexml_load_string($activity_xml);
				$times[] = strtotime($rss->updated);
				//echo '<pre>';print_r($rss->entry);echo '</pre>';
				// Parse entries for each google site
				foreach ($rss->entry as $item) {
					
					// Get entry data
					$title = 'Changes on ' . $title . ' site';
					$text = $item->summary->div->asXML();
					$date = $item->updated;
					$time = strtotime($date);
					$access = !empty($site_access) ? $site_access : 2;
					
					$times[] = $time;
					
					if ($user->last_site_activity <= $time) {
						
						// Initialise a new ElggObject (entity)
						$site_activity = new ElggObject();
						$site_activity->subtype = "site_activity";
						$site_activity->owner_guid = get_loggedin_userid();
						$site_activity->container_guid = (int)get_input('container_guid', get_loggedin_userid());
						
						$site_activity->access_id = $access;
						$site_activity->title = $title;
						$site_activity->updated = $date;
						$site_activity->text = $text;
						
						// not working
						//$site_activity->posted = strtotime($date);
						
						// Now save the object
						if (!$site_activity->save()) {
							register_error('Site activity has not saves.');
							//forward($_SERVER['HTTP_REFERER']);
						}
						
						// add to river
						if (add_to_river('river/object/site_activity/create', 'create', get_loggedin_userid(), $site_activity->guid)) {
							$is_new_activity = true;
						}
						
					}
				}
				
			}
			
			$max_time = max($times);
			$user->last_site_activity = $max_time;
			$user->save();
			if (!empty($site_list)) {
				$user->site_list = serialize($site_list);
				$user->save();
			}
			
			
		}
		if ($ajax) {
			$response = array();
			$response['mail_count'] = !empty($count) ? $count : 0;
			$response['new_activity'] = !empty($is_new_activity) ? 1 : 0;
			return json_encode($response);
		}
	}

?>
