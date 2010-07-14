<?php


// Echo title
echo elgg_view_title(elgg_echo('googleappslogin:google_sync_settings'));


$user = $_SESSION['user'];
$user_sync_settings = unserialize( $user->sync_settings );

$enabled = array ();

if(!is_array($user_sync_settings)) {
	echo "<pre>SET DEF</pre>";
    $user_sync_settings['sync_name'] = 1;
	$user->sync_settings = serialize($user_sync_settings);
    $user->save();
}

foreach ($user_sync_settings as $setting => $v) {
    if ($v) $enabled[]=$setting;
}


?>
<div class="contentWrapper">
	<div class="notification_methods">

	<?php if ($user->google == 1 || $subtype == 'googleapps') { ?>
			
			<p><?php echo elgg_echo('googleappslogin:google_sync_settings_description'); ?></p>
					<?php
					$body = "<p>" . elgg_view('input/checkboxes', array('internalname' => "sync_settings", 'value' =>$enabled,  'options' => array('Syncing name upon login'=>'sync_name')) );
					$body .= '</p>';
					$body .= '<div class="clearfloat"></div><div class="friendspicker_savebuttons">	<input type="submit" value="' . elgg_echo('save') . '" /><br /></div>	';

					echo elgg_view('input/form',array(
						'body' => $body,
						'method' => 'post',
						'action' => $vars['url'] . 'action/googleappslogin/save_user_sync_settings',
					));

					echo elgg_view('googleappslogin/disconnect');

	} else {
		$googleapps_screen_name = $user->googleapps_screen_name;
	?>
			<h3><?php echo elgg_echo('googleappslogin:googleapps_login_title'); ?></h3>
			<p><?php echo elgg_echo('googleappslogin:googleapps_login_description'); ?></p>
	<?php
		echo elgg_view('googleappslogin/connect');
	}
	?>
			
	</div>
</div>