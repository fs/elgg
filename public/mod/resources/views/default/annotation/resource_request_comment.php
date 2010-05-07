<?
	/**
	 * Resources - Custom annotation view 
	 * 
	 * @package Resources
	 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
	 * @author Jeff Tilson
	 * @copyright THINK Global School 2010
	 * @link http://www.thinkglobalschool.com/
	 * 
	 */
	
	// Get the annotation value its an array we're dealing with 
	// a custom annotation
	$value = unserialize($vars['annotation']['value']);
		
	// Compatible with regular comments, just in case check if its an array
	if (is_array($value)) {
		$vars['annotation']['value'] = $value['comment_text'];
		
		switch ((int)$value['comment_view_level']) {
			case RESOURCE_REQUEST_COMMENT_PUBLIC: 
				echo elgg_view('annotation/generic_comment', $vars);
				break;
			case RESOURCE_REQUEST_COMMENT_ADMIN:
				if (isresourceadminloggedin())
					echo '<div class="resources_admin">' . elgg_view('annotation/generic_comment', $vars) . '</div>';
				break;
			default: 
				break;
		} 
	}

?>