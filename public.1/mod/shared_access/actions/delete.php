<?php
/**
 * Elgg shared access plugin
 * 
 * @package ElggSharedAccess
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Curverider Ltd
 * @copyright Curverider Ltd 2008-2010
 * @link http://elgg.com/
 */

gatekeeper();

// yes this is duplicated from friends collections. that is being deprecated. 
// Get input data
$collection_id = (int) get_input('collection');

// Check to see that the access collection exist and grab its owner
$get_collection = get_access_collection($collection_id);

if($get_collection){
    if($get_collection->owner_guid == $_SESSION['user']->getGUID()){
		$delete_collection = delete_access_collection($collection_id);
		
		// Success message
		if ($delete_collection) 
			system_message(elgg_echo("friends:collectiondeleted"));
		else
			register_error(elgg_echo("friends:collectiondeletefailed"));
	} else {
	//Failure message
	register_error(elgg_echo("friends:collectiondeletefailed"));

	}
} else {
	// Failure message
	register_error(elgg_echo("friends:collectiondeletefailed"));
}



forward($_SERVER['HTTP_REFERER']);