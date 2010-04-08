<?php

	/**
	 * Elgg profile icon hover over: actions
	 * 
	 * @package ElggProfile
	 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
	 * @author Curverider Ltd <info@elgg.com>
	 * @copyright Curverider Ltd 2008-2010
	 * @link http://elgg.com/
	 * 
	 * @uses $vars['entity'] The user entity. If none specified, the current user is assumed. 
	 */
	/*
	$user = $_SESSION['user'];
	
	if (isloggedin() && $user->getGUID() == $vars['entity']->getGUID()) {
		
		if (empty($user->home_latitude)) {
			create_metadata($user->guid, 'home_latitude', 0, 'text', $user->guid, $access_id);
		}
		
		if (empty($vars['entity']->home_latitude)) {
		//	$vars['entity']->home_latitude = '0';
		}
		if (empty($vars['entity']->home_longitude)) {
		//	$vars['entity']->home_longitude = '0';
		}
		?>
		
		<script type="text/javascript">
		
		var $form = null;
		
		$(function () {
			$form = $('input.submit_button').parents()
											.map(function () {
													if (this.tagName == 'FORM') {
														return this;
													}
											});
			$form.append(
				'<input type="text" value="<?=$vars['entity']->home_latitude?>" name="home_latitude" id="home_geolocation_latitude" />' +
				'<input type="text" value="<?=$vars['entity']->home_longitude?>" name="home_longitude" id="home_geolocation_longitude" />'+
				'<input type="text" value="<?=$vars['entity']->current_latitude?>" name="current_latitude" id="current_geolocation_latitude" />' +
				'<input type="text" value="<?=$vars['entity']->current_longitude?>" name="current_longitude" id="current_geolocation_longitude" />'
			);
		});
		
		</script>
		
		<p class="user_menu_profile">
			<a href="show_map();return_false;">Edit home location</a>
		</p>
		
		<p class="user_menu_profile">
			<a href="show_map();return_false;">Edit current location</a>
		</p>
		
		<?
	}
	*/
	
?>
