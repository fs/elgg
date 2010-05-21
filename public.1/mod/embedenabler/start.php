<?php
/*******************************************************************************
 * embedenabler
 *
 * @author Jeff Tilson
 ******************************************************************************/

	function embedenabler_init()
	{
		global $CONFIG;
		
		return true;
	}


	
	register_elgg_event_handler('init', 'system', 'embedenabler_init');
?>