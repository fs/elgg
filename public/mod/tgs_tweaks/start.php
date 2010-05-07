<?php
/*******************************************************************************
 * google_links
 *
 * @author Mike Hourahine
 ******************************************************************************/

	function tgs_tweaks_init()
	{
		global $CONFIG;

		$domain = get_plugin_setting('domain','tgs_tweaks');
		add_menu(elgg_echo('Collaborative Docs'),"http://docs.google.com/a/".$domain);
		add_menu(elgg_echo('Email'),"http://mail.google.com/a/".$domain);
		add_menu(elgg_echo('Mind Maps'),"https://www.mindmeister.com/sso/start?from=google&domain=".$domain);
		add_menu(elgg_echo('Channels'),$CONFIG->site->url . 'pg/shared_access/home');
		
		elgg_extend_view('css','tgs_tweaks/css');
		elgg_extend_view('input/tags','tgs_tweaks/location');
		
		return true;
	}

	register_elgg_event_handler('init', 'system', 'tgs_tweaks_init');
?>