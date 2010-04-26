<?php


// Echo title
echo elgg_view_title(elgg_echo('googleappslogin:google_sites_settings'));

if ($user->google == 1 || $subtype == 'googleapps') {
	$site_list = unserialize($user->site_list);

	if (!empty($site_list)) {
		?>
<h3><?php echo elgg_echo('googleappslogin:google_sites_settings'); ?></h3>

<p><?php echo elgg_echo('googleappslogin:google_sites_settings_description'); ?></p>
		<?php
		foreach ($site_list as $title => $access) {
			if (!empty($title)) {
				if (is_null($access) || $access != 0 && $access != 22) {
					$access = 1;
				}
				?><p><b><?php echo $title;?></b><br /><?
					echo elgg_view('input/radio',array('internalname' => "googleapps_sites_settings[" . $title . "]", 'options' => $access_types, 'value' => $access));
					?></p><?
			}
		}
	}
	echo elgg_view('googleappslogin/disconnect');
	
} else {
	$googleapps_screen_name = $user->googleapps_screen_name;
	?>
<h3><?php echo elgg_echo('googleappslogin:googleapps_login_title'); ?></h3>

<p><?php echo elgg_echo('googleappslogin:googleapps_login_description'); ?></p>

	<?php
	echo elgg_view('googleappslogin/connect');
}