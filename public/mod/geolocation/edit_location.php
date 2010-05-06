<?php	

	// Get the Elgg engine
	require_once(dirname(dirname(dirname(__FILE__))) . "/engine/start.php");
	
	// If we're not logged on, forward the user elsewhere
	if (!isloggedin()) {
		forward();
	}

	// Get owner of profile - set in page handler
	$user = $_SESSION['user'];
	if (!$user) {		
		register_error(elgg_echo("profile:notfound"));
		forward();
	}

	// check if logged in user can edit this profile
	if (!$user->canEdit()) {
		register_error(elgg_echo("profile:noaccess"));
		forward();
	}

	// Get edit form

	
	if($page[0] == 'home') {
		$title = elgg_echo('geolocation:home_location');
	} else {
		$title = elgg_echo('geolocation:current_location');
	}
	$area2 = elgg_view_title($title);
	
	$area2 .= elgg_view(
			"geolocation/edit_location",
			array(
				'entity' => $user,
				'page' => $page[0]
				)
			);

	$area1 = '';
	
	$body = elgg_view_layout("two_column_left_sidebar", $area1, $area2);

	page_draw($title,$body);