<?php
	/**
	 * User settings for googleappslogin.
	 * 
	 * @package googleappslogin
	 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
	 * @author Kevin Jardine <kevin@radagast.biz>
	 * @copyright Curverider 2009
	 * @link http://elgg.org/
	 */

	$options = array(elgg_echo('googleappslogin:settings:yes')=>'yes',
		elgg_echo('googleappslogin:settings:no')=>'no',
	);
	
	$user = page_owner_entity();
    if (!$user) {    	
    	$user = $_SESSION['user'];
    }
    
    $subtype = $user->getSubtype();

	if( $subtype == 'googleapps') {
		$googleapps_controlled_profile = $user->googleapps_controlled_profile;
	
		if (!$googleapps_controlled_profile) {
			$googleapps_controlled_profile = 'yes';
		}
?>
	<h3><?php echo elgg_echo('googleappslogin:googleapps_user_settings_title'); ?></h3>
	
	<p><?php echo elgg_echo('googleappslogin:googleapps_user_settings_description'); ?></p>
	
<?php
	echo elgg_view('input/radio',array('internalname' => "googleapps_controlled_profile", 'options' => $options, 'value' => $googleapps_controlled_profile));
	 } else if (!$subtype) {
	 	$googleapps_screen_name = $user->googleapps_screen_name;
		?>
	<h3><?php echo elgg_echo('googleappslogin:googleapps_login_title'); ?></h3>
	
	<p><?php echo elgg_echo('googleappslogin:googleapps_login_description'); ?></p>
	
<?php
		echo elgg_view('input/text',array('internalname' => "googleapps_screen_name", 'options' => $options, 'value' => $googleapps_screen_name));
	 }
?>