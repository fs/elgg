<?php
$user = $_SESSION['user'];

$oauth_sync_email = get_plugin_setting('oauth_sync_email', 'googleappslogin');

if (isset($_SESSION['new_google_mess']) && !empty($user) && ($oauth_sync_email != 'no')) {
	$count = $_SESSION['new_google_mess'];
	$domain = get_plugin_setting('googleapps_domain', 'googleappslogin');
	if ($count > 0) {
		$title = 'You have ' . $count . ' unread message' . (($count > 1) ? 's' : '');
	} else {
		$title = 'You don`t have unread messages';
	}
	?>
	<a id="unreadmessagescountlink" href="https://mail.google.com/a/<?= $domain ?>" class="usersettings" target="_blank" title="<?= $title ?>">
	<img src="/mod/googleappslogin/graphics/gmail.gif" align="left" alt="<?= $title ?>" />
	<?php 
	if ($count > 0) {
		echo $count;
	}
	?>
	</a>
<?php
}
?>
