<?php


// Echo title
echo elgg_view_title(elgg_echo('googleappslogin:google_sites_settings'));


$user = $_SESSION['user'];
$user_sync_settings = unserialize( $user->sync_settings );

$enabled = array ();

// Set defaults
if(!is_array($user_sync_settings)) {
    $user_sync_settings['sync_name'] = 1;
    $user->save();
}

foreach ($user_sync_settings as $setting => $v) {
    if ($v) $enabled[]=$setting;
}


?>
<div class="contentWrapper">
	<div class="notification_methods">

		<h3><?php echo elgg_echo('googleappslogin:google_sync_settings'); ?></h3>

		<p><?php echo elgg_echo('googleappslogin:google_sync_settings_description'); ?></p>
				<?php
				$body = '';
				
                               $body .= "<p>" . elgg_view('input/checkboxes', array('internalname' => "sync_settings", 'value' =>$enabled,  'options' => array('Syncing name upon login'=>'sync_name')) );

//                               $body .= '<p><b>'. $title . '</b><br />' . elgg_view('input/radio',array('internalname' => "googleapps_sites_settings[" . $site_id . "]", 'options' => $access_types, 'value' => $access)) . '</p>';
//
//
                                $body .= '</p>';
				$body .= '<div class="clearfloat"></div><div class="friendspicker_savebuttons">	<input type="submit" value="' . elgg_echo('save') . '" /><br /></div>	';

				echo elgg_view('input/form',array(
				'body' => $body,
				'method' => 'post',
				'action' => $vars['url'] . 'action/googleappslogin/save_user_sync_settings',
				));
			
		?>
	</div>
</div>