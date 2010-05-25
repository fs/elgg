<?php
	/**
	 * User settings for googleappslogin.
	 * 
	 * @package googleappslogin
	 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
	 * @author Alexander Ulitin
	 * @copyright Flatsourcing 2010
	 * @link http://elgg.org/
	 */
	
	$options = array(elgg_echo('googleappslogin:settings:yes')=>'yes',
		elgg_echo('googleappslogin:settings:no')=>'no'
	);
	
	$access_types = array(
		'private' => '0',
		'logged-in' => '1',
		'public' => '22'
	);
	$user = page_owner_entity();
	//echo '<pre>';print_r($user->googleapps_controlled_profile);exit;
	$logged_user = $_SESSION['user'];
    //echo '<pre>';print_r($user->googleapps_controlled_profile);exit;
    $subtype = $user->getSubtype();
	if ($user->connect == 1) {
		$subtype = 'googleapps';
		$user->google = 1;
	}
	
	googleapps_sync_sites();
?>
<div class="user_settings googleappslogin">
<?
	
	if ($user->google == 1 || $subtype == 'googleapps') {
		
		//echo elgg_echo('googleappslogin:googleapps_controlled_profile') . "<br />";
		//echo elgg_view('input/radio', array('internalname' => 'googleapps_controlled_profile', 'options' => $options, 'value' => $googleapps_controlled_profile));
		
		$site_list = unserialize($user->site_list);
		if (!empty($site_list)) {
			?>
			<h3><?php echo elgg_echo('googleappslogin:google_sites_settings'); ?></h3>
			
			<p><?php echo elgg_echo('googleappslogin:google_sites_settings_description'); ?></p>
			<?php
			foreach ($site_list as $title => $access) {
				if (!empty($title)){
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
?>
</div>
