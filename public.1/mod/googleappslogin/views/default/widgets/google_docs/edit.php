<?
if (empty($vars['entity']->max_entry)) {
	$vars['entity']->max_entry = 20;
}
$vars['entity']->title = '';

//$folders = !empty($_SESSION['oauth_google_folders']) ? unserialize($_SESSION['oauth_google_folders']) : array();
//$main_folders = child_folders('', $folders);

/*
?>
<p>
	Title:<br />
	<input type="text" name="params[title]" value="<?php echo htmlentities($vars['entity']->title); ?>" />
</p>
<?
*/
?>
<input type="hidden" name="params[title]" value="" />
<p>
	How many display:
	<input type="text" name="params[max_entry]" value="<?php echo htmlentities($vars['entity']->max_entry); ?>" style="width:20px;" maxlength="2" />
</p>
<p>
	Choose folder:<br />
	<select id="google_folders" name="params[google_folder]">
		<option value="">All folders</option>
		
		
	</select>
	
</p>

