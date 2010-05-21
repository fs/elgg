<?php

	/**
	 * Functions for use OAuth
	 *
	 * @package GoogleAppsLogin
	 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
	 * @author Alexander Ulitin <alexander.ulitin@flatsoft.com>
	 * @copyright FlatSourcing 2010
	 * @link http://elgg.org/
	 */

class OAuth_Data {
	
	var $client = null;
	var $user = null;
	var $ajax = null;
	var $access_token = null;
	var $token_secret = null;
	var $scopes = 'mail sites activity docs folders';
	
	public function OAuth_Data($ajax = false, $scopes = '') {
		
		$this->ajax = (bool) $ajax;
		$this->scopes = explode(' ', $scopes);
		
		$this->user =& $_SESSION['user'];
		$this->access_token =& $_SESSION['access_token'];
		$this->access_secret =& $_SESSION['token_secret'];
		
		$this->client = $this->authorized_client();
		if ($this->client) {
			$x = $this->googleapps_fetch_oauth_data();
			if ($this->ajax) {
				return $x;
			}
		}
		
		return false;
		
	}
	
	public function authorized_client() {
		
		require_once 'OAuth.php';
		require_once 'client.inc';

		$CONSUMER_KEY = get_plugin_setting('googleapps_domain', 'googleappslogin');
		$CONSUMER_SECRET = get_plugin_setting('login_secret', 'googleappslogin');

		if (!empty($this->user->access_token)) {
			$this->access_token = $this->user->access_token;
		}
		if (!empty($this->user->token_secret)) {
			$this->access_secret = $this->user->token_secret;
		}

		$this->client = new OAuth_Client($CONSUMER_KEY, $CONSUMER_SECRET, SIG_METHOD_HMAC);

		if (!empty($this->client->access_token)) {
			$this->access_token = $this->client->access_token;
		}
		if (!empty($this->client->access_secret)) {
			$this->access_secret = $this->client->access_secret;
		}

		if ($this->client->authorized()) {
			return $this->client;
		} else {
			if (!$this->ajax) {
				// Authorise user in google for access to data
				$url = $this->client->oauth_authorize();
				header('Location: ' . $url);
				exit;
			} else {
				return false;
			}
		}
	}

	function googleapps_fetch_oauth_data() {
		
		if (!is_object($this->client)) {
			return false;
		}
		
		$oauth_sync_email = get_plugin_setting('oauth_sync_email', 'googleappslogin');
		$oauth_sync_sites = get_plugin_setting('oauth_sync_sites', 'googleappslogin');
		$oauth_sync_docs = get_plugin_setting('oauth_sync_docs', 'googleappslogin');
		
		foreach ($this->scopes as $scope) {
			
			switch ($scope) {
				
				case 'mail':
					$this->fetch_email_data();
					break;
				
				case 'sites':
					$this->fetch_sites_data();
					break;
					
				case 'activity':
					$this->fetch_activity_data();
					break;
					
				case 'docs':
					$this->fetch_docs_data();
					break;
				
				case 'folders':
					$this->fetch_folders_data();
					break;
					
				default:
					
					break;
			}
			
		}
		
		$count = 0;
		$is_new_activity = false;
		$is_new_docs = false;
		
		$user = $_SESSION['user'];
		if ($oauth_sync_email != 'no') {
			
		}
		
		if ($oauth_sync_sites != 'no') {
			
			
			
		}
		
		if ($oauth_sync_docs != 'no') {
			
			// Get google docs folders
			$folders = googleapps_google_docs_folders($client);
			$oauth_docs_folders = serialize($folders);
			$_SESSION['oauth_google_folders'] = $oauth_docs_folders;
			
			// Get google docs
			$google_folder = !empty($_SESSION['oauth_google_folder']) ? $_SESSION['oauth_google_folder'] : null;
			$documents = googleapps_google_docs($client, $google_folder);
			$oauth_docs = serialize($documents);
			if (empty($_SESSION['oauth_google_docs']) || $_SESSION['oauth_google_docs'] != $oauth_docs) {
				$_SESSION['oauth_google_docs'] = $oauth_docs;
				$is_new_docs = true;
			}
			
		}
		
		if ($ajax) {
			$response = array();
			$response['mail_count'] = !empty($count) ? $count : 0;
			$response['new_activity'] = !empty($is_new_activity) ? 1 : 0;
			$response['new_docs'] = !empty($is_new_docs) ? 1 : 0;
			return json_encode($response);
		}
	}
	
	public function fetch_email_data() {
		
		// Get count unread messages of gmail
		$count = $this->client->unread_messages();
		return $count;
		
	}
	
	public function fetch_sites_data() {
		
		$response_list = $this->googleapps_sync_sites();
			
		$max_time = null;
		$times = array();
		
		$site_list = empty($this->user->site_list) ? array() : unserialize($user->site_list);
		
		if (empty($this->user->last_site_activity)) {
			$this->user->last_site_activity = 0;
		}
		
		// Parse server response for google sites activity stream
		foreach ($response_list as $site) {

			$title = $site['title'];
			$feed = $site['feed'];
			$site_exist = null;

			$site_access = $site_list[$title];
			if (!isset($site_access)) {
				// todo: use constants
				$site_list[$title] = 2;
				$site_access = 2;
			}

			// Get google sites activity stream
			$activity_xml = $client->execute($feed, '1.1');

			$rss = simplexml_load_string($activity_xml);
			$times[] = strtotime($rss->updated);

			$site_title = $title;
			$title = 'Changes on ' . $title . ' site';

			//echo '<pre>';print_r($rss->entry);echo '</pre>';exit;
			// Parse entries for each google site
			foreach ($rss->entry as $item) {
				
				// Get entry data
				$text = $item->summary->div->asXML();
				$author_email = @$item->author->email[0];
				$date = $item->updated;
				$time = strtotime($date);
				$access = !empty($site_access) ? $site_access : 2;
				$times[] = $time;
				if ($user->last_site_activity <= $time && $author_email == $user->email) {
					// Initialise a new ElggObject (entity)
					$site_activity = new ElggObject();
					$site_activity->subtype = "site_activity";
					$site_activity->owner_guid = get_loggedin_userid();
					$site_activity->container_guid = (int)get_input('container_guid', get_loggedin_userid());
					
					$site_activity->access_id = $access;
					$site_activity->title = $title;
					$site_activity->updated = $date;
					$site_activity->text = str_replace('<a href', '<a target="_blank" href', $text) . ' on the <a target="_blank" href="' . $site['url'] . '">' . $site_title . '</a> site';
					$site_activity->site_name = $site_title;
					
					// Now save the object
					if (!$site_activity->save()) {
						register_error('Site activity has not saves.');
						//forward($_SERVER['HTTP_REFERER']);
					}
					
					// add to river
					if (add_to_river('river/object/site_activity/create', 'create',
						get_loggedin_userid(), $site_activity->guid, "", strtotime($date))) {
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
	
	public function fetch_activity_data() {
		
		// Get count unread messages of gmail
		$count = $this->client->unread_messages();
		return $count;
		
	}
	
	public function fetch_docs_data() {
		
		// Get count unread messages of gmail
		$count = $this->client->unread_messages();
		return $count;
		
	}
	
	public function fetch_folders_data() {
		
		// Get count unread messages of gmail
		$count = $this->client->unread_messages();
		return $count;
		
	}

	function googleapps_sync_sites($do_not_redirect = true) {
		// 0. Check settings
		if (get_plugin_setting('oauth_sync_sites', 'googleappslogin') == 'no') {
			return false;
		}
		
		$client = authorized_client($do_not_redirect);
		if (!$client) {
			return false;
		}
		
		// 1. Get google site feeds list
		$result = $client->execute('https://sites.google.com/feeds/site/' . $client->key . '/', '1.1');
		$response_list = $client->fetch_sites($result);
		// 2. Get local site list
		$user =& $_SESSION['user'];
		$site_list = empty($user->site_list) ? array() : unserialize($user->site_list);
		$normalized_sites = $user->getObjects('site');
		// 3. Join lists
		$merged = array();
		foreach ($response_list as $site) {
			$title = $site['title'];
			$merged[$title] = isset($site_list[$title]) ? $site_list[$title] : ACCESS_PUBLIC;
		}
		// 4. Update user
		$user->site_list = serialize($merged);
		$user->save();
		// 4.1 Update normalized sites: destroy deleted sites
		foreach ($normalized_sites as $site_entity) {
			$found = false;
			foreach ($response_list as $site) {
				if (empty($site_entity->site_id)) {
					continue;
				}
				if ($site['site_id'] == $site_entity->site_id) {
					$found = $site;
					break;
				}
			}
			if (!$found) {
				$site_entity->delete();
			} else {
				$modified = false;
				if ($site['url'] != $site_entity->url) {
					$site_entity->url = $found['url'];
					$modified = true;
				}
				if ($site['title'] != $site_entity->title) {
					$site_entity->title = $found['title'];
					$modified = true;
				}
				if ($site['modified'] != $site_entity->modified) {
					$site_entity->modified = $found['modified'];
					$modified = true;
				}
				if ($modified) {
					$site_entity->save();
				}
			}
		}
		// 4.2 Update normalized sites: create new sites
		foreach ($response_list as $site) {
			$found = false;
			foreach ($normalized_sites as $site_entity) {
				if ($site['site_id'] == $site_entity->site_id) {
					$found = true;
					break;
				}
			}
			if (!$found) {
				// create entity
				$new_site = new ElggObject();
				$new_site->owner_guid = $user->guid;
				$new_site->site_id = $site['site_id'];
				$new_site->title = $site['title'];
				$new_site->subtype = "site";
				$new_site->url = $site['url'];
				$new_site->modified = $site['modified'];
				$new_site->access_id = $merged[$site['title']];
				$new_site->save();
			}
		}
		
		// 5. Profit
		return $response_list;
	}

	function googleapps_google_docs_folders($client) {
		
		$feed = 'http://docs.google.com/feeds/default/private/full/-/folder';
		$result = $client->execute($feed, '3.0');
		$folders_rss = simplexml_load_string($result);
		
		$folders = folders_from_rss($folders_rss);
		
		return $folders;
		
	}

	function googleapps_google_docs($client, $folder = null) {
		
		// Get google docs feeds list
		
		if (empty($folder)) {
			$feed = 'http://docs.google.com/feeds/default/private/full';
		} else {
			$feed = 'http://docs.google.com/feeds/default/private/full/' . $folder . '/contents';
		}
		
		$result = $client->execute($feed, '3.0');
		$rss = simplexml_load_string($result);
		$documents = array();
		//echo '<pre>';print_r($rss);exit;
		
		// Parse entries for each google document
		foreach ($rss->entry as $item) {
			
			if (count($documents) >= $max_entry) {
				//break;
			}
			$title = $item['title'];

			$links = $item->link;
			$src = '';
			$is_folder = false;
			$type = '';

			foreach ($item->category as $category) {
				$attrs = array();
				foreach ($category->attributes() as $a => $value) {
					$attrs[$a] = $value[0];
				}
				if (!empty ($attrs['scheme']) && $attrs['scheme'] == 'http://schemas.google.com/g/2005#kind') {
					$type = preg_replace('/\ label\=\"(.*)\"/', '$1', $attrs['label']->asXML());;
					$is_folder = ($type == 'folder');
				}
			}

			foreach ($item->link as $link) {
				$attrs = array();
				foreach ($link->attributes() as $a => $value) {
					$attrs[$a] = $value[0];
				}
				if (!empty ($attrs['rel']) && $attrs['rel'] == 'alternate') {
					$src = $attrs['href'];
					break;
				}
			}

			if (!empty($src)) {
				$doc['title'] = preg_replace('/\<title\>(.*)\<\/title\>/', '$1', $item->title->asXML());
				$doc['trunc_title'] = trunc_name($doc['title']);
				$doc['href'] = preg_replace('/href=\"(.*)\"/', '$1', $src->asXML());
				$doc['type'] = $type;
				$doc['updated'] = strtotime($item->updated);
				$documents[] = $doc;
			}
		}
		return $documents;
		
	}

	function folders_from_rss($folders) {
		
		$folds = array();
		
		foreach ($folders->entry as $item) {
			
			$id = preg_replace('/http\:\/\/docs\.google\.com\/feeds\/id\/(.*)/', '$1', $item->id);
			$title = preg_replace('/\<title\>(.*)\<\/title\>/', '$1', $item->title->asXML());
			$parent_id = null;
			
			foreach ($item->link as $link) {
				$attrs = array();
				foreach ($link->attributes() as $a => $value) {
					$attrs[$a] = $value[0];
				}
				if ($attrs['rel'] == 'http://schemas.google.com/docs/2007#parent') {
					$parent_id = preg_replace('/http\:\/\/docs\.google\.com\/feeds\/default\/private\/full\/(.*)/', '$1', $attrs['href']);
					break;
				}
			}
			
			$folder = new stdClass;
			$folder->id = $id;
			$folder->title = $title;
			$folder->parent_id = $parent_id;
			
			$folds[$folder->id] = $folder;
			
		}
		
		return $folds;
	}

	function child_folders($parent_id, $folders) {
		
		$folds = array();
		
		foreach ($folders as $folder) {
			if ($parent_id == $folder->parent_id) {
				$folds[] = $folder;
			}
		}
		
		return $folds;
		
	}

	function walk_folders($folders, $global_folders, $default_folder = '', $without_n = false) {
		
		foreach ($folders as $folder) {
			if (!$without_n) {
			echo '
			';
			}
			echo '<option value="' . $folder->id . '"';
			if ($default_folder == $folder->id) {
				echo ' selected';
			}
			echo '>' . echo_breadcrumbs(get_breadcrumbs($folder->id, $global_folders)) . '</option>';
			
			$folds = child_folders($folder->id, $global_folders);
			walk_folders($folds, $global_folders, $default_folder, $without_n);
			
		}
	}

	function get_breadcrumbs($folder_id, $folders, $path = null) {
		
		if (!$path) {
			$path = array();
		}
		
		foreach ($folders as $folder) {
			if ($folder_id == $folder->id) {
				$path[] = $folder->title;
				return get_breadcrumbs($folder->parent_id, $folders, $path);
				break;
			}
		}
		return $path;
	}

	function echo_breadcrumbs($path = null) {
		
		if (!$path) {
			return false;
		}
		$i = 0;
		$result = '';
		
		if (count($path) > 2) {
			$newpath = array();
			$newpath[] = $path[0];
			$newpath[] = '...';
			$newpath[] = end($path);
			$path = $newpath;
		}
		
		foreach ($path as $folder) {
			
			if ($i > 0) {
				$result = ' > ' . $result;
			}
			
			$result = trunc_name($folder) . $result;
			
			$i++;
			
		}
		return $result;
	}

	function trunc_name($string = '') {
		
		if (empty($string)) {
			return false;
		}
		
		$i = 0;
		$result = '';
		
		$path = explode(' ', $string);
		
		if (count($path) > 2) {
			if (count($path) == 3 && strlen($path[1]) < 4) {
				return $string;
			}
			$newpath = array();
			$newpath[] = $path[0];
			$newpath[] = '...';
			$newpath[] = end($path);
			$path = $newpath;
			
			$result = implode(' ', $path);
			
			return $result;
		}
		
		return $string;
	}
	
}
?>
