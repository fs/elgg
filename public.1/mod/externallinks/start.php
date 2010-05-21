<?php
	/**
	 * Externallinks start.php
	 * 
	 * @package ExternalLinks
	 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
	 * @author Jeff Tilson
	 * @copyright THINK Global School 2010
	 * @link http://www.thinkglobalschool.com/
	 * 
	 */
	
	function externallinks_init()
	{
		global $CONFIG;

		// Extend Metatags (for js)
		elgg_extend_view('metatags','externallinks/metatags');
		
		return true;
	}
	
	register_elgg_event_handler('init', 'system', 'externallinks_init');
?>