<?php

	/**
	 * Elgg long text input with the tinymce text editor intacts
	 * Displays a long text input field
	 * 
	 * @package ElggTinyMCE
	 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
	 * @author Curverider Ltd
	 * @copyright Curverider Ltd 2008-2010
	 * @link http://elgg.org/
	 * 
	 * @uses $vars['value'] The current value, if any
	 * @uses $vars['js'] Any Javascript to enter into the input tag
	 * @uses $vars['internalname'] The name of the input field
	 * 
	 */

	global $tinymce_js_loaded;
	
	$input = rand(0,9999);
	
	if (!isset($tinymce_js_loaded)) $tinymce_js_loaded = false;

	if (!$tinymce_js_loaded) {
	
?>
<!-- include tinymce -->
<script language="javascript" type="text/javascript" src="<?php echo $vars['url']; ?>mod/tinymce/tinymce/jscripts/tiny_mce/tiny_mce.js"></script>
<!-- intialise tinymce, you can find other configurations here http://wiki.moxiecode.com/examples/tinymce/installation_example_01.php -->
<script language="javascript" type="text/javascript">
   tinyMCE.init({
	mode : "specific_textareas", 
	editor_selector : "mceEditor",
	theme : "advanced",
	plugins : "flash,safari,spellchecker,pagebreak,style,layer,table,save,advhr,advimage,advlink,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras",

	// Theme options
	theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,formatselect, cut,copy,paste,pastetext,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquot,|,undo,redo,|,link,unlink",
	theme_advanced_buttons2 : "anchor,code,|,insertdate,inserttime,|,forecolor,backcolor,removeformat,|,sub,sup,|,charmap,iespell,fullscreen",
	theme_advanced_buttons3 : "",
	theme_advanced_buttons4 : "spellchecker,|,cite,abbr,acronym,del,ins",
	theme_advanced_toolbar_location : "top",
	theme_advanced_toolbar_align : "left",
	theme_advanced_statusbar_location : "bottom",
	theme_advanced_resizing : true,
	
});
function toggleEditor(id) {
if (!tinyMCE.get(id))
	tinyMCE.execCommand('mceAddControl', false, id);
else
	tinyMCE.execCommand('mceRemoveControl', false, id);
}
</script>
<?php

		$tinymce_js_loaded = true;
	}

?>

<!-- show the textarea -->
<textarea class="input-textarea mceEditor" name="<?php echo $vars['internalname']; ?>" <?php echo $vars['js']; ?>><?php echo htmlentities($vars['value'], null, 'UTF-8'); ?></textarea> 
<div class="toggle_editor_container"><a class="toggle_editor" href="javascript:toggleEditor('<?php echo $vars['internalname']; ?>');"><?php echo elgg_echo('tinymce:remove'); ?></a></div>

<script type="text/javascript">
	$(document).ready(function() {
		$('textarea').parents('form').submit(function() {
			tinyMCE.triggerSave();
		});
	});
</script>