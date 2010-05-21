
<?php
	/**
	 * Resourcerequest object view
	 * 
	 * @package Resources
	 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
	 * @author Jeff Tilson
	 * @copyright THINK Global School 2010
	 * @link http://www.thinkglobalschool.com/
	 * 
	 */

	
	// Check for valid entity
	if (isset($vars['entity']) && $vars['entity'] instanceof ElggObject) {
		
		global $CONFIG;
		
		$confirm = elgg_echo('resources:question:approve');
		$set_status_url = elgg_add_action_tokens_to_url($CONFIG->wwwroot . "action/resources/setstatus");
		$request_comment_url = elgg_add_action_tokens_to_url($CONFIG->wwwroot . "action/resources/ajaxcomment");
		$approved = RESOURCE_REQUEST_STATUS_APPROVED;
		$rejected = RESOURCE_REQUEST_STATUS_NOTAPPROVED;

		$script = <<<EOT
			<script type="text/javascript">
			
				function setRequestStatus(status) {
					$.ajax({
						url: "$set_status_url",
						type: "POST",
						data: "request_guid={$vars['entity']->guid}&s=" + status,
						cache: false,
						dataType: "html",
						error: function() {
							alert('There was an error');
						},
						success: function(data){
							if (data) {
								$("span#request_status").html(data);
								$("span#request_status").effect("highlight", {}, 3000);
								$("span#request_status").attr('class','status_' + data);
							}
						}
					});
				}
				
				function submitRequestAjaxComment(entity_guid, user_guid, comment_text) {
					$.ajax({
						url: "$request_comment_url",
						type: "POST",
						data: "entity_guid=" + entity_guid + "&user_guid=" + user_guid + "&comment_text=" + comment_text,
						cache: false, 
						dataType: "html", 
						error: function() {
							alert('There was an error');	
						},
						success: function(data) {
							
						}
						
					});
				}
			
				$(function() {
					
					/** SET UP DIALOG POPUP **/
					$('#resource_dialog').dialog({
										autoOpen: false,
										width: 725,
										modal: true,
										open: function(event, ui) { 
											$(".ui-dialog-titlebar-close").hide(); 
											if (tinyMCE) {
												tinyMCE.execCommand('mceAddControl', false, 'reject_textarea');
											}
										},
										beforeclose: function(event, ui) {
											if (tinyMCE) {
									    		tinyMCE.execCommand('mceRemoveControl', false, 'reject_textarea');
											}
									    },
										buttons: {
											"X": function() { 
												$(this).dialog("close"); 
											} 
										}
									});
					
					/** REJECT CLICK HANDLER **/
					$("a#reject").click(
						function() {
							$("#resource_dialog").dialog("open");
							return false;
						}
					);
					
					/** APPROVE CLICK HANDLER **/
					$("a#approve").click(
						function() {
							if(confirm("Approve this request?")) {
								setRequestStatus($approved);
							}
						}
					);
					
					$("form#reject_request").click(
						function() {
							
						}
					);
					
					
					$("form#reject_request").submit(
						function() {
							/** May not be tinyMCE **/
							if (tinyMCE) 
								var comment_text = tinyMCE.get('reject_textarea').getContent();
							else 
								var comment_text = $("textarea#reject_textarea").val();
							
							if (comment_text) {
								setRequestStatus($rejected);
								submitRequestAjaxComment({$vars['entity']->guid}, {$vars['user']->guid}, comment_text);
								$("#resource_dialog").dialog("close");
							} else {
								$("#reject_form_title").attr('style', "color: red;");
							}
							return false;
							
						}
					);
					
							
				});
				
				
			</script>
EOT;
				
		$url = $vars['entity']->getURL();
		$owner = $vars['entity']->getOwnerEntity();
		$canedit = isresourceadminloggedin();
		$title = $vars['entity']->title;
		$description = $vars['entity']->description;
		$types = get_resource_types();
		$request_type = $types[$vars['entity']->request_type];
		
		$status_array = get_resource_status_array();
		$status = $status_array[$vars['entity']->request_status];
		
		// Content
		$icon = elgg_view("graphics/icon", array('entity' => $vars['entity'],'size' => 'small'));
		$user_icon = elgg_view("profile/icon",array('entity' => $owner, 'size' => 'tiny'));		
		$strapline = sprintf(elgg_echo("resources:strapline"), date("F j, Y",$vars['entity']->time_created));
		$strapline .= " " . elgg_echo('by') . " <a href='{$vars['url']}pg/rubric/{$owner->username}'>{$owner->name}</a> ";
		$strapline .= sprintf(elgg_echo("comments")) . " (" . count_annotations($vars['entity']->getGUID(), "", "", "resource_request_comment") . ")";
		$strapline .= " Status: <span id='request_status' class='status_" . elgg_echo($status) . "'>" . elgg_echo($status) . "</span>";
	
		$reject_form = "<form action='' method='POST' id='reject_request'>";
		$reject_form .= "<div id='reject_comment' class=\"contentWrapper\" >";
		$reject_form .= "<label id='reject_form_title'>" . elgg_echo("resources:label:commentrequired") . "</label>";
		$reject_form .= "<p class='longtext_editarea'><br /><div id='request_form_message'></div>" . elgg_view('input/plaintext',array('internalname' => 'reject_textarea', 'internalid' => 'reject_textarea')) . "</p>";
		$reject_form .= elgg_view('input/submit', array('value' => elgg_echo("save"))) . "</p>";
		$reject_form .= "</div></form>";
			
		
		if ($canedit && !get_input('view_only')) {
						
				$controls = "<a id='approve' href='#'>" . elgg_echo('resources:label:approve'). "</a>&nbsp;&nbsp;&nbsp;";
		
				$controls .= "<a id='reject' href='#'>" . elgg_echo('resources:label:reject') . "</a>&nbsp;&nbsp;&nbsp;";
		
				$controls .= elgg_view("output/confirmlink", 
										array(
											'href' => $vars['url'] . "action/resources/delete?request_guid=" . $vars['entity']->getGUID(),
											'text' => elgg_echo('delete'),
											'confirm' => elgg_echo('deleteconfirm'),
										)) . "&nbsp;&nbsp;&nbsp;";
				
				// NO EDITING! 						
				//$controls .= "<a href={$vars['url']}pg/resources/edit/{$vars['entity']->getGUID()}>" . elgg_echo("edit") . "</a>";
		}		

		// Figure out which viewing mode we're in
		if ($vars['full']) {
			$mode = 'full';
		} else {
			if (get_input('search_viewtype') == "gallery") {
				$mode = 'gallery';				
			} else {
				$mode = 'listing';
			}
		}
		
		$tags = elgg_view('output/tags', array('tags' => $vars['entity']->tags));		

		if (!empty($tags)) {
			$tags = "<p class='{$mode}tags'>" . $tags . "</p>";
		}
		
		// Default info for gallery/listing mode
		$info = <<<EOT
			<div class='resources'>
				<p>
					<b><a href='$url'>$request_type - $title</a></b>
				</p>
				<p class='listingstrapline'>
					$strapline
				</p>
					$tags
				<p class='controls'>
					$controls
				</p>
			</div>
EOT;

		
		switch ($mode) {
			case 'full':
			$comments = elgg_view_comments($vars['entity']);
			$info = <<<EOT
					$script
					<div id="resource_dialog" style="display: none;" >$reject_form</div>
					<div class='contentWrapper singleview'>
						<div class='resources'>
							<h3><a href='$url'>$request_type - $title</a></h3>
							<div class="resources_icon">
								$user_icon
							</div>
							<p class='strapline'>
								$strapline
							</p>
							$tags						
							<div class='clearfloat'></div>
							<div class='description'>
								$description
							</div>
							<br />
							$controls
						</div>
					</div>
					$comments
EOT;
				echo $info;
				break;
			case 'listing':
				echo elgg_view_listing($icon, $info);
				break;
			case 'gallery':
				echo elgg_view_listing("", $info);
				break;
		}
		
	} else {
		// If were here something went wrong..
		$url = 'javascript:history.go(-1);';
		$owner = $vars['user'];
		$canedit = false;
	}
?>