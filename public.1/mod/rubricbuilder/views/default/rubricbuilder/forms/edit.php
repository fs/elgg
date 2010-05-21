<?php
	/**
	 * Edit rubric form
	 * 
	 * @package RubricBuilder
	 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
	 * @author Jeff Tilson
	 * @copyright THINK Global School 2010
	 * @link http://www.thinkglobalschool.com/
	 * 
	 */
?>
<!-- Javascript -->
<script type="text/javascript">
	
	// Fire this when document is 100% loaded
	$(document).ready(function() {	
		counter = $("#num_rows").val();

		// Set up event bindings
		bindRemoveClickHandler();
	
		// Set up click event for adding rows
		$("a#add").click(function() {
			// re-bind all the remove buttons when a new row is added
			bindRemoveClickHandler();
		});
	});

</script>
<?php
		
		// If entity exists, we're editing existing
		if (isset($vars['entity'])) {
				
			// If we're editing a revision, load that one up
			$rev = (int)get_input('rev');
			if ($rev) {	
				$revision = get_annotation($rev);

				if ($revision) {
					$revision = unserialize($revision->value);
					$title 			= $revision['title'];
					$description 	= $revision['description'];
					$contents		= unserialize($revision['contents']);
					$num_rows		= $revision['rows'];
					$num_cols		= $revision['cols'];
				}

			} else {
				$title 			= $vars['entity']->title;
				$description 	= $vars['entity']->description;
				$contents		= $vars['entity']->getContents();
				$num_rows		= $vars['entity']->getNumRows();
				$num_cols		= $vars['entity']->getNumCols();				
			}
			
			$action			= "rubric/edit";
			$tags 			= $vars['entity']->tags;
			$access_id 		= $vars['entity']->access_id;
			$write_access_id= $vars['entity']->write_access_id;
			$rubric 		= $vars['entity'];
			
			if ($vars['entity']->comments_on == 'Off') {
				$comments_on = false;
			} else {
				$comments_on = true;
			}

			$num_rows_input = elgg_view('input/hidden', array('internalname' => 'num_rows', 'internalid' => 'num_rows' , 'value' => $num_rows));
			$num_cols_input = elgg_view('input/hidden', array('internalname' => 'num_cols', 'internalid' => 'num_cols', 'value' => $num_cols));
					
		} else  {
			// Creating a new rubric
			$action			= "rubric/add";
			$tags 			= "";
			$title 			= "";
			$comments_on 	= true;
			$description 	= "";
			
			if (defined('ACCESS_DEFAULT')) {
				$access_id = ACCESS_DEFAULT;
				$write_access_id = ACCESS_DEFAULT;
			} else {
				$access_id = 0;
				$write_access_id = 0;
			}
				
			$container = $vars['container_guid'] ? elgg_view('input/hidden', array('internalname' => 'container_guid', 'value' => $vars['container_guid'])) : "";
			
			$rubric = new Rubric();
			$rubric->setContents(Rubric::getDefaultHeaders());
			
			$contents = $rubric->getContents();
			
			$num_rows		= $rubric->getNumRows();
			$num_cols		= $rubric->getNumCols();
			
			$num_rows_input = elgg_view('input/hidden', array('internalname' => 'num_rows', 'internalid' => 'num_rows' , 'value' => $num_rows));
			$num_cols_input = elgg_view('input/hidden', array('internalname' => 'num_cols', 'internalid' => 'num_cols' , 'value' => $num_cols));	
		}
		
		// Get cached info if there was an oops
		if ($vars['user']->rubriccached) {
			$contents 		= unserialize($vars['user']->rubriccontents);
			$title 			= $vars['user']->rubrictitle;
			$description	= $vars['user']->rubricdescription;
			$tags 			= $vars['user']->rubrictags;
		}
		
		// Get views for inputs
		$title_label 			= elgg_echo('title');
        $title_textbox 			= elgg_view('input/text', array('internalname' => 'rubric_title', 'value' => $title));
		$description_label 		= elgg_echo('description');
        $description_textbox 	= elgg_view('input/longtext', array('internalname' => 'rubric_description', 'value' => $description));
        $rubric_label 			= elgg_echo('rubricbuilder:title');
        $tag_label 				= elgg_echo('tags');
        $tag_input 				= elgg_view('input/tags', array('internalname' => 'rubric_tags', 'value' => $tags));
        $write_access_label		= elgg_echo('Write Access');
		$access_label 			= elgg_echo('access');

		if($comments_on)
		  	$comments_on_switch = "checked=\"checked\"";
		else
			$comment_on_switch = "";
			
		$write_access_input	= elgg_view('input/access', array('internalname' => 'write_access_id', 'value' => $write_access_id));
		$access_input 		= elgg_view('input/access', array('internalname' => 'access_id', 'value' => $access_id));
        $submit_input 		= elgg_view('input/submit', array('internalname' => 'submit', 'value' => elgg_echo('publish')));
		$publish 			= elgg_echo('publish');
		$privacy 			= elgg_echo('access');;
		$allowcomments 		= elgg_echo('blog:comments:allow');
		
		$add 	= $vars['url'] . "mod/rubricbuilder/images/plus.gif";
		$remove = $vars['url'] . "mod/rubricbuilder/images/minus.gif";
		  
		// Build rubric input form
		$rubric_input = "<table class='rubric_table' id='rubric'>";
		for ($i = 0; $i < $num_rows; $i++) {
			$rubric_input .= "<tr id='row" . $i . "'>";
			for ($j = 0; $j < $num_cols; $j++) {
			
				$input_class = 'rubric_input';
					
				// Zebra stripes
				if ($i % 2 == 0)
					$input_class .= " alt";
					
				
				
				if ($i == 0) {
					$rubric_input .= "<td style='height: 17px; font-size: 120%;'>";
					$rubric_input .= elgg_view('input/text', array('internalname' => $i . '|' . $j, 'value' => elgg_echo($contents[$i][$j]), 'class' => $input_class . " rubric_header"));
				} else {
					$rubric_input .= "<td>";
			    	$rubric_input .=  elgg_view('input/plaintext', array('internalname' => $i . '|' . $j, 'value' => elgg_echo($contents[$i][$j]), 'class' => $input_class));
				}

				$rubric_input .= "</td>";
			}
			if ($i != 0) {
				$rubric_input .= "<td style='vertical-align: middle;'><div id='remove_row' class='remove_img' onmouseout='this.className=\"remove_img\"'  onmouseover='this.className=\"remove_img_over\"'></div></td>";
			}
			$rubric_input .= "</tr>";
		} 
		$rubric_input .= "</table><br />";
	
	// Check if we're editing an existing rubric, and set the guid
	if (isset($vars['entity'])) {
      $entity_hidden = elgg_view('input/hidden', array('internalname' => 'rubric_guid', 'value' => $vars['entity']->getGUID()));
    } else {
      $entity_hidden = '';
    }

	// Build form body
	$form_body = <<<EOT
	<div class="contentWrapper">
		<p>
			<label>$title_label</label><br />
            $title_textbox
		</p>
		<p>
			<label>$description_label</label><br />
            $description_textbox
		</p>
		<p>
			<label>$rubric_label</label<br />
			$rubric_input
			<label>Rows: </label> <a id='add' href="#" onclick="addRow(); return false;"><img src="$add" /></a><br /><br />
		</p>
		<p>
			<label>$tag_label</label><br />
			$tag_input
		</p>
		<p>
			<label>$access_label</label><br />
			$access_input
		</p>
		<p>
			<label>$write_access_label</label><br />
			$write_access_input
		</p>
		<p>
			<label><input type="checkbox" name="rubric_comments_select"  {$comments_on_switch} /> {$allowcomments}</label>
		</p>
		<p>
			$num_rows_input
			$num_cols_input
		</p>
		<p>
			$entity_hidden
			$container
			$submit_input
			
		</p>
	</div>
EOT;
	
	// Output
	echo elgg_view('input/form', array('action' => "{$vars['url']}action/$action", 'body' => $form_body, 'internalid' => 'rubricPostForm'));

?>