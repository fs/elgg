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

$response=googleapps_sync_sites();
$user_site_entities=$response['site_entities'];

$_SESSION['user_site_entities']=serialize($user_site_entities);

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
				foreach ($site_list as $site_id => $site_obj) {

                                    $title=$site_obj['title'];
                                     $access=$site_obj['access'];

					if (!empty($title)) {
						if (is_null($access) || $access != 0 && $access != 22) {
							$access = 1;
						}

						$body .= '<p><b>'. $title . '</b><br />' . elgg_view('input/radio',array('internalname' => "googleapps_sites_settings[" . $site_id . "]", 'options' => $access_types, 'value' => $access)) . '</p>';
					}
				}

				$body .= '<div class="clearfloat"></div><div class="friendspicker_savebuttons">	<input type="submit" value="' . elgg_echo('save') . '" /><br /></div>	';

				echo elgg_view('input/form',array(
				'body' => $body,
				'method' => 'post',
				'action' => $vars['url'] . 'action/googleappslogin/save',
				));
			}
		}

		?>
	</div>
</div>