<?php
/**
 * Elgg shared access js
 * 
 * @package ElggSharedAccess
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Curverider Ltd
 * @copyright Curverider Ltd 2008-2010
 * @link http://elgg.com/
 */
?>
<script type="text/javascript">
// bind to dropdown
$(document).ready(function() {
	$('a.ajax_content').click(function() {
		var target = $(this).parents('div.shared_access_collection').find('.ajax_content_target');
		var url = $(this).attr('href') + '?ajax=1';

		shared_access_load_ajax(url, target);
		return false;
	});
});

function shared_access_load_ajax(url, target) {
	// close if clicked on again
	if ($(target).children().length && $(target).is(':visible')) {
		$(target).slideUp();
		return false;
	}

	target.load(url, '', function(text, status, XHR) {
		if (status == 'success') {
			$(target).slideDown();
		} else {
			// do nothing
		};
	});
}
</script>