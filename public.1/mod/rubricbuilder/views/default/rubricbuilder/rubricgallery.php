<?php
	/**
	 * Rubric gallery view
	 * 
	 * @package RubricBuilder
	 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
	 * @author Jeff Tilson
	 * @copyright THINK Global School 2010
	 * @link http://www.thinkglobalschool.com/
	 * 
	 * Code borrows from Pages plugin
	 */
	
	if (isset($vars['entity']) && $vars['entity'] instanceof Rubric) {
		
		$url = $vars['entity']->getURL();
		$owner = $vars['entity']->getOwnerEntity();
		$canedit = $vars['entity']->canEdit();
			
		$can_delete = false;
			
		if (($vars['user']->getGUID() == $vars['entity']->owner_guid) || ($vars['user']->admin || $vars['user']->siteadmin)) {
			$can_delete = true;
		}
					
		$icon = elgg_view(
				"graphics/icon", array(
				'entity' => $vars['entity'],
				'size' => 'small',
			  )
			);
		

		$comments_on = $vars['entity']->comments_on;
		
		if ($comments_on){
	        //get the number of comments
	    	$num_comments = elgg_count_comments($vars['entity']);
		} 

		$info .= "<div class='rubric'><p><b><a href=\"" . $vars['entity']->getUrl() . "\">" . $vars['entity']->title . "</a></b></p>";
	
		$info .= "<p class='listingstrapline'>" . sprintf(elgg_echo("blog:strapline"), date("F j, Y",$vars['entity']->time_created));
		$info .= "<br /><br /><a href=" . $vars['url'] . "pg/rubric/$owner->username> $owner->name</a><br /><br />";
	
		$info .= "<a href=$url>" . sprintf(elgg_echo("comments")) . " (" . $num_comments . ")</a><br /></p>";
	
		$tags = elgg_view('output/tags', array('tags' => $vars['entity']->tags));
		if (!empty($tags)) {
			$info .=  '<p class="gallerytags">' . $tags . '</p>';
		}
	
		if ($canedit) {
			$info .= "<p class='controls'><a href=" . $vars['url'] . "pg/rubric/edit/" . $vars['entity']->getGUID() . ">" . elgg_echo("edit") . "</a>  &nbsp;&nbsp;&nbsp;"; 
		}	
		
		$info .= elgg_view("output/confirmlink", array(
				'href' => $vars['url'] . "action/rubric/fork?rubric_guid=" . $vars['entity']->getGUID(),
				'text' => elgg_echo('rubricbuilder:fork'),
				'confirm' => elgg_echo('rubricbuilder:forkconfirm'),
				));
		
		$info .= "&nbsp;&nbsp;&nbsp;";
		
		if ($can_delete && $canedit) {
			$info .= elgg_view("output/confirmlink", array(
					'href' => $vars['url'] . "action/rubric/delete?rubric_guid=" . $vars['entity']->getGUID(),
					'text' => elgg_echo('delete'),
					'confirm' => elgg_echo('deleteconfirm'),
			));
		}
				// Allow the menu to be extended
		$info .= elgg_view("editmenu",array('entity' => $vars['entity']));
		$info .= "</p>";		
		
	
		$info .= "</div>";
	
		echo elgg_view_listing("", $info);
	} else {
			$url = 'javascript:history.go(-1);';
			$owner = $vars['user'];
			$canedit = false;
	}
?>