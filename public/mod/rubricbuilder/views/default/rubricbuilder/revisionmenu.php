<?php
	/**
	 * Rubric revision history navigation menu
	 * 
	 * @package RubricBuilder
	 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
	 * @author Jeff Tilson
	 * @copyright THINK Global School 2010
	 * @link http://www.thinkglobalschool.com/
	 *
	 * @uses $vars['rev_guid'] - Current Revision Guide
	 * @uses $vars['rubric_guid'] - Rubric guid
	 * @uses $vars['local_revisions'] - Array of revision_guid => local_id (1, 2, 3 etc..)
	 * @uses $vars['current_local_revision'] - Current local revision from above
	 */
	
	$rubric_guid = $vars['rubric_guid'];

	// Some jQuery to set the revision dropdown to the current viewing revision
	$script = "<script type='text/javascript'>
					$(document).ready(function() {
						var current_revision = $('input#current_revision').val();
						$('#rev').val(current_revision);
						
						$('#revselect').change(function() {
						    this.submit();
						});
					});			
				</script>";
	
	$revisions_local = $vars['local_revisions'];
	$current_revision = $vars['current_local_revision'];
	
	$count = count($revisions_local);
	
	// Create the dropdown array, sorting in reverse order
	$revisions_pulldown = $revisions_local;
	arsort($revisions_pulldown);
	
	$counter = 0;
	foreach ($revisions_pulldown as $key => $value) {
		if ($counter == 1) break;
		$revisions_pulldown[$key] = $value . " (Latest)";
		$counter++;
	}
	
	// Set up previous and next buttons
	$flipped_revisions = array_flip($revisions_local);
	$previous_button = "";
	$next_button = "";
	if ($current_revision > 1) {
		$prev = $flipped_revisions[$current_revision - 1];
		$previous_button = "<form action={$CONFIG->url}pg/rubric/view/$rubric_guid/?rev=$prev'>
								<input type='submit' style='width: 82px;' value='<< Previous' />
								<input type='hidden' name='rev' value='$prev' />
							</form>";
	}  
	
	if ($current_revision < $count){
		$next = $flipped_revisions[$current_revision + 1];
		$next_button = "<form action={$CONFIG->url}pg/rubric/view/$rubric_guid/>
							<input type='submit' style='width: 82px;' value='Next >>' />
							<input type='hidden' name='rev' value='$next' />
						</form>";
	}
	
	if ($current_revision != $count) {
		$restore_link = elgg_view("output/confirmlink", array(
					'href' => $vars['url'] . "action/rubric/restore?rubric_guid=" . $rubric_guid . "&rev=" . $vars['rev_guid'],
					'text' => elgg_echo('rubricbuilder:restore'),
					'confirm' => elgg_echo('rubricbuilder:restoreconfirm'),
					));
	}
	
	$history_url = $CONFIG->url . "pg/rubric/history/" . $rubric_guid;

	// Get revision author
	$revision_author = get_entity(get_annotation($flipped_revisions[$vars['current_local_revision']])->owner_guid);
	$author_content = "<a href='{$vars['url']}pg/rubric/{$revision_author->username}'>{$revision_author->name}</a>";
	
	$show_div = "";
	if ($vars['rev_guid']) {
		$show_div = " style='display: block;' ";
	}
	
	$content = "<br /><a href='#' id='show_hide_history' onclick=\"$('#rubric_revision_menu').toggle(200); return false;\">Revision History</a><br />
				<div id='rubric_revision_menu' $show_div>
					<table id='revision_menu_table'>
						<tr>
							<td colspan=3 class='revision_desc'>
								Viewing Revision: $current_revision/<a href='$history_url'>$count</a> <br /> Revision Author: $author_content <br /> $restore_link  
							</td>
						</tr>
						<tr>
							<td class='revision_prev'>
								$previous_button
							</td>
							<td class='revision_select'>
									<form id='revselect' action='{$CONFIG->url}pg/rubric/view/$rubric_guid/'>
										Revision: " 
											. elgg_view("input/pulldown", array('options_values' => $revisions_pulldown, 'internalname' => 'rev', 'internalid' => 'rev')) . "											
											<input type='hidden' id='current_revision' value='$current_revision' />
									</form>
							</td>
							<td class='revision_next'>
								$next_button
							</td>
						</tr>
					</table>
				</div>
	";
	
	echo $script . $content;

?>