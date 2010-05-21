<?php
	/**
	 * Feedback - Set status form
	 * 
	 * @package Feedback
	 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
	 * @author Jeff Tilson
	 * @copyright THINK Global School 2010
	 * @link http://www.thinkglobalschool.com/
	 * 
	 */
	
	$guid = $vars['entity']->guid;

	$id = "feedback_status_" . $guid;
	
	$js = <<<EOT
		<script type="text/javascript">
			$(document).ready(
				function() {
					$("select#$id").change(
						function () {
							$("form#update_status_$guid").submit();
						}
					);
				}
			);
		</script>
EOT;

	$full = $vars['full'];
	
	$status_types = get_status_types();
	$flipped_status = array_flip($status_types);
	
	foreach ($status_types as $key => $type) {
		$status_types[$key] = elgg_echo('feedback:status:' . $type);
	}
	
	$form_body .= elgg_view("input/pulldown", array('internalid' => $id, 'internalname' => 's', 'options_values' => $status_types, 'value' => $flipped_status[$vars['entity']->status]));
	
	$form_body .= elgg_view("input/hidden", array('value' => $guid, 'internalname' => 'feedback_guid'));
	$form_body .= elgg_view("input/hidden", array('value' => $full, 'internalname' => 'full'));
	

	echo $js . elgg_view('input/form', array('body' => $form_body, 'action' => $vars['url'] . 'action/feedback/setstatus', 'internalname' => 'update_status_'. $guid, 'internalid' => 'update_status_'.$guid, 'js' => ' style="display: inline;" '));

?>