<?php

	$CONSUMER_KEY = get_plugin_setting('googleapps_domain', 'googleappslogin');
	
	require_once $_SERVER['DOCUMENT_ROOT'] . '/mod/googleappslogin/models/OAuth.php';
	require_once $_SERVER['DOCUMENT_ROOT'] . '/mod/googleappslogin/models/client.inc';
	
	$google_folder = !empty($vars['entity']->google_folder) ? $vars['entity']->google_folder : '';
	$max_entry = !empty($vars['entity']->max_entry) ? (int) $vars['entity']->max_entry : 15;
	
	$_SESSION['oauth_google_folder'] = $google_folder;
	
	$client = authorized_client(true);
	googleapps_fetch_oauth_data($client, false, 'folders docs');
	//print_r($_SESSION);exit;
	
	
	// Get google docs folders
	$folders = unserialize($_SESSION['oauth_google_folders']);
	//print_r($google_folder);exit;
	$google_folder = $folders[$google_folder];
	$main_folders = child_folders('', $folders);
	
	$oauth_google_docs = unserialize($_SESSION['oauth_google_docs']);
	$_SESSION['oauth_google_folder'] = $google_folder->id;
	?>
	
	<div id="google_docs_widget" class="google_docs_widget">
	<script type="text/javascript">
		
		/**
		 * function for search elgg widget id
		 * 
		 * @param object $this jquery object
		 * @return object
		 */
		function search_widget_id($this) {
			
			if (!$this) {
				return false;
			}
			
			if (!$this[0]) {
				return false;
			}
			
			// DOM object
			var current = $this[0];
			
			var reg = /^widgetcontent(\d+)$/;
			var arr = Array();
			
			if (reg.test(current.id)) {
				arr = reg.exec(current.id);
				if (arr[1]) {
					return arr[1];
				}
			} else {
				return search_widget_id($this.parent());
			}
			
		}
		
		function update_select() {
			
			var select = $('select#google_folders');
			
			select.empty();
			select.append('<option value="">All folders</option>');
			select.append('<?= walk_folders($main_folders, $folders, $vars['entity']->google_folder, true); ?>');
			
			return true;
			
		}
		
		function update_widget(id, username) {
			
			if (!id || !username) {
				return false;
			}
			
			$("#widgetcontent" + id).html('<div align=\"center\" class=\"ajax_loader\"></div>');
			$("#widgetcontent" + id).load("/pg/view/" + id + "?shell=no&username=" + username + "&context=dashboard&callback=true");
			
			return true;
			
		}
		
		update_select();
		
	</script>
	<div class="contentWrapper">
	<?
	if (!empty($google_folder)) {
		echo '<div><b>' . $google_folder->title . '</b></div>';
	}
    if (!empty($oauth_google_docs)) {
		?>
		<div class="river_item_list">
		<?
		$i = 0;
		foreach ($oauth_google_docs as $doc) {
			if ($i >= $max_entry) {
				break;
			}
			
			if (!empty($doc['type'])) {
				switch ($doc['type']) {
					
					case 'folder':
						$doc['type'] = 'folder';
						// folder links should link to parent folder
						$doc['href'] = preg_replace('/(.*)folder\.0\.(.*)/', '$1folder.0.' . substr($google_folder->id, 9), $doc['href']);
						break;
						
					case 'spreadsheet':
						$doc['type'] = 'spread';
						break;
						
					case 'presentation':
						$doc['type'] = 'pres';
						break;
						
					case 'document':
						$doc['type'] = 'doc';
						break;
						
					default:
						$doc['type'] = 'doc';
						break;
				}
			} else {
				$doc['type'] = 'doc';
			}
			?>
			<div class="river_item">
				<span class="document-icon <?= $doc['type'] ?>"></span>
				<a href="<?= $doc['href'] ?>" target="_blank" title="<?= $doc['title'] ?>"><?= $doc['trunc_title'] ?></a>
			</div>
			<?
			$i++;
		}
		?>
		</div>
		<?
    } else {
		
		echo "You Do not have a Google Document.";
		
	}
	
	?>
			<div class="view-all">
				<a href="https://docs.google.com/a/<?= $CONSUMER_KEY ?>/#all" target="_blank">All docs &raquo;</a>
			</div>
		</div>
	</div>
</div>
