<?php


// Echo title
echo elgg_view_title(elgg_echo('googleappslogin:google_sites_settings'));


$options = array(elgg_echo('googleappslogin:settings:yes')=>'yes',
		elgg_echo('googleappslogin:settings:no')=>'no'
);

$access_types = array(
		'private' => '0',
		'logged-in' => '1',
		'public' => '22'
);

$user = $_SESSION['user'];
$subtype = $user->getSubtype();

if ($user->connect == 1) {
	$subtype = 'googleapps';
	$user->google = 1;
}

googleapps_sync_sites();

?>
<div class="contentWrapper">
	<div class="notification_methods">
		<?php



		if ($user->google == 1 || $subtype == 'googleapps') {
			$site_list = unserialize($user->site_list);

			if (!empty($site_list)) {
				?>
		<h3><?php echo elgg_echo('googleappslogin:google_sites_settings'); ?></h3>

		<p><?php echo elgg_echo('googleappslogin:google_sites_settings_description'); ?></p>
				<?php
				$body = '';
				foreach ($site_list as $title => $access) {
					if (!empty($title)) {
						if (is_null($access) || $access != 0 && $access != 22) {
							$access = 1;
						}

						$body .= '<p><b>'. $title . '</b><br />' . elgg_view('input/radio',array('internalname' => "googleapps_sites_settings[" . $title . "]", 'options' => $access_types, 'value' => $access)) . '</p>';
					}
				}

				$body .= '<div class="clearfloat"></div><div class="friendspicker_savebuttons">	<input type="submit" value="' . elgg_echo('save') . '" /><br /></div>	';

				echo elgg_view('input/form',array(
				'body' => $body,
				'method' => 'post',
				'action' => $vars['url'] . 'action/googleappslogin/save',
				));
			}
			echo elgg_view('googleappslogin/disconnect');

		} else {
			$googleapps_screen_name = $user->googleapps_screen_name;
			?>
		<h3><?php echo elgg_echo('googleappslogin:googleapps_login_title'); ?></h3>
			<?php
			echo elgg_echo('googleappslogin:googleapps_login_description');
			echo elgg_view('googleappslogin/connect');
		}

		?>
	</div>
</div>