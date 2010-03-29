<?php

     /**
	 * Elgg googleapps scripts
	 * 
	 * @package Elgg
	 * @subpackage googleappslogin
	 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
	 * @author Kevin Jardine, Radagast Solutions
	 * @copyright Curverider Ltd 2008-2009
	 * @link http://elgg.org/
	 */
	
	$interval = $GLOBALS['oauth_update_interval'] ? $GLOBALS['oauth_update_interval'] : 3;
	$url = $GLOBALS['oauth_update_url'];
	$user = $_SESSION['user'];
	if ($url && $user && ($user->googleapps_sync_email != 'no' || $user->googleapps_sync_sites != 'no')) {
		
		?>
		<script type="text/javascript" src="/mod/googleappslogin/views/default/googleappslogin/system_messages.js"></script>
		<script type="text/javascript">
			
			jQuery(function($) {
				
				function oauth_update() {
					$.getJSON("<?= $url ?>".replace(/http(s?):\/\/.*?\//, '/'), function (data) {
					<?php
					if ($user->googleapps_sync_email != 'no') {
					?>
						
						if (data.mail_count == 0) {
							data.mail_count = '';
						}
						$('#unreadmessagescountlink').html('<img src="/mod/googleappslogin/views/default/googleappslogin/newmail.gif" align="left" style="margin-right:4px;" alt="You' + (!data.mail_count ? 'dosn`t' : '') + ' have ' + data.mail_count + ' unread messages" />' + data.mail_count);
					<?php
					}
					
					if ($user->googleapps_sync_sites != 'no') {
					?>
						
						if (data.new_activity == 1) {
							elgg.system_message('Please click here for look at <a href="/pg/dashboard/">new activity</a>!');
						} else {
							//elgg.register_error('You has not new activity!');
						}
					<?php
					}
					?>
					
					});
				}
				
				oauth_update();
				setInterval(oauth_update, (<?= $interval ?> * 60 * 1000));
				
			});
			
		</script>
		
		<div id="custom-messages" class="hidden">
			
			<div class="messages radius8">
				<p></p>
			</div>
			
			<div class="errors radius8">
				<p></p>
			</div>
			
		</div>
		
		<?
	}
?>
