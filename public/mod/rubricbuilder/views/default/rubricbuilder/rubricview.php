<?php

	/**
	 * Rubric full view
	 * 
	 * @package RubricBuilder
	 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
	 * @author Jeff Tilson
	 * @copyright THINK Global School 2010
	 * @link http://www.thinkglobalschool.com/
	 * 
	 * @uses $vars['entity'] Optionally, the rubric to view
	 */
	
		if (isset($vars['entity']) && $vars['entity'] instanceof Rubric) {
			
			$url 		= $vars['entity']->getURL();
			$owner 		= $vars['entity']->getOwnerEntity();
			$canedit 	= $vars['entity']->canEdit();
			$rubric 	= $vars['entity'];
			
			$revisions = get_annotations($rubric->getGUID(), "object", "rubric", 'rubric', "", 0, 9000, 0, 'asc');

			$count = count($revisions);	

			// Build an array of 'local' revisions
			// ie: map the annotation id to a local number
			// annotations 87, 88, 89, 92, 98 becomes 1, 2, 3, 4, 5
			$revisions_local = array();

			for ($i = 0; $i < $count; $i++) {
				$revisions_local[$revisions[$i]->id] = $i + 1;
			}

			
				
			//	$current_revision = $count;

			
			// If we're looking at a revision, grab the content for that
			$rev = (int)get_input('rev');
			if ($rev) {	
				$revision = get_annotation($rev);
				// Make sure we have an annotation object, and that it belongs to this rubric
				if ($revision && $revision->entity_guid == $vars['entity']->getGUID()) {
					$revision = unserialize($revision->value);

					$title 			= $revision['title'];
					$description 	= $revision['description'];
					$contents		= unserialize($revision['contents']);
					$num_rows		= $revision['rows'];
					$num_cols		= $revision['cols'];
					
					$current_revision = $revisions_local[$rev];
					
					if ($current_revision == $count)
						$current_revision = $count;
					
				} else {
					// Something funny is going on...
					forward();
				}
				
				

			} else {
				$title 			= $vars['entity']->title;
				$description 	= $vars['entity']->description;
				$contents		= $vars['entity']->getContents();
				$num_rows		= $vars['entity']->getNumRows();
				$num_cols		= $vars['entity']->getNumCols();	
				
				$current_revision = $count;			
			}
		
			// display comments link?
			if ($vars['entity']->	s_on == 'Off') {
				$comments_on = false;
			} else {
				$comments_on = true;
			}
			
			// Are we allowed to delete?
			$can_delete = false;
			if (($vars['user']->getGUID() == $rubric->owner_guid) || ($vars['user']->admin || $vars['user']->siteadmin)) {
				$can_delete = true;
			}
			
			// Build rubric table
			$rubric_table = "<table class='rubric_table'>";
			for ($i = 0; $i < $num_rows; $i++) {
				$rubric_table .= "<tr>";
				for ($j = 0; $j < $num_cols; $j++) {

					$input_class = 'rubric_td';

					// Zebra stripes
					if ($i % 2 == 0)
						$input_class .= " alt";

					if ($i == 0) {
						$rubric_table .= "<td style='height: 15px;' class='$input_class rubric_header'><p>";
						$rubric_table .= elgg_view('output/text', array('internalname' => $i . '|' . $j, 'value' => elgg_echo($contents[$i][$j])));
					} else {
						$rubric_table .= "<td class='$input_class'><p>";
				    	$rubric_table .=  elgg_view('output/text', array('internalname' => $i . '|' . $j, 'value' => elgg_echo($contents[$i][$j])));
					}

					$rubric_table .= "&nbsp;</p></td>";
				}
				$rubric_table .= "</tr>";
			} 
			$rubric_table .= "</table><br />";
			
			// Views/Content
			$user_icon = elgg_view("profile/icon",array('entity' => $owner, 'size' => 'tiny'));
			$tags_output = elgg_view('output/tags', array('tags' => $vars['entity']->tags));
			$description_output = elgg_view('output/longtext',array('value' => $description));
			$revisions_output = elgg_view('rubricbuilder/revisionmenu', array('rev_guid' => $rev, 'rubric_guid' => $rubric->getGUID(), 'local_revisions' => $revisions_local, 'current_local_revision' => $current_revision));
			
			$strapline = sprintf(elgg_echo("rubricbuilder:strapline"), date("F j, Y",$vars['entity']->time_created));
			$strapline .= " " . elgg_echo('by') . " <a href='{$vars['url']}pg/rubric/{$owner->username}'>{$owner->name}</a> ";
			
			if ($comments_on) {
	        	//get the number of comments
	    		$num_comments = elgg_count_comments($vars['entity']);
				$strapline .= "&nbsp;&nbsp;<a href='$url'>" . sprintf(elgg_echo("comments")) . " ($num_comments)</a><br />";
    		}

			// Options menu
			$options = "";
			// Only show editing options if we're not viewing a revision
			if (!$rev || $current_revision == $count) {
				if ($canedit) {
					$revision = $rev ? "?rev=$rev"  : "";
					$options .= "<span style='padding-right: 20px;'><a href={$vars['url']}pg/rubric/edit/{$vars['entity']->getGUID()}$revision>" . elgg_echo("edit") . "</a></span>";
				} 
				
				$options .= elgg_view("output/confirmlink", array(
							'href' => $vars['url'] . "action/rubric/fork?rubric_guid=" . $vars['entity']->getGUID(),
							'text' => elgg_echo('rubricbuilder:fork'),
							'confirm' => elgg_echo('rubricbuilder:forkconfirm'),
							));
					
				if ($canedit && $can_delete) {
					if ($can_delete) {
					$options .= "<span style='padding-left: 20px;'>" . 
								elgg_view("output/confirmlink", array(
									'href' => $vars['url'] . "action/rubric/delete?rubric_guid=" . $vars['entity']->getGUID(),
									'text' => elgg_echo('delete'),
									'confirm' => elgg_echo('deleteconfirm'),
								)) ."</span>";
					}
					// Allow the menu to be extended
					$options .= elgg_view("editmenu",array('entity' => $vars['entity']));
				}
			} 
			
			echo <<<EOT
			
			<div class="contentWrapper singleview">
				<div class="rubric">
					<h3><a href="$url">$title</a></h3>
					<div class="rubric_icon">
						$user_icon
					</div>
					<p class="strapline">
						$strapline
					</p>
					<p class="tags">
						$tags_output
					</p>
					<div class="rubric_description">
						$description_output
					</div>
						$revisions_output
					<div class="clearfloat"></div>
				</div>
			</div>
			<div class="contentWrapper singleview">
				<div class="rubric">	
					<div class="rubric_body">
						$rubric_table
					</div>
					<div class="clearfloat"></div>			
					<p class="options">
						$options
					</p>	
				</div>
			</div>
EOT;
		} else {
			
			$url = 'javascript:history.go(-1);';
			$owner = $vars['user'];
			$canedit = false;
			
		}
?>