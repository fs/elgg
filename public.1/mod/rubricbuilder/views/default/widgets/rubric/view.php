<style type="text/css">
#rubric_widget .pagination {
    display:none;
}
</style>
<?php
	/**
	 * Rubric widget view
	 * 
	 * @package RubricBuilder
	 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
	 * @author Jeff Tilson
	 * @copyright THINK Global School 2010
	 * @link http://www.thinkglobalschool.com/
	 * 
	 */
     //set_input('search_viewtype','widget');
     $num_display = (int) $vars['entity']->rubric_num;
	 if (!$num_display)
		$num_display = 5;
     
     $rubrics = elgg_list_entities(array('types' => 'object', 'subtypes' => 'rubric', 'container_guid' => page_owner(), 'limit' => $num_display, 'view_type_toggle' => false, 'full_view' => FALSE));
	 $rubricsurl = $vars['url'] . "pg/rubric/index/";
     $rubrics .= "<div class=\"pages_widget_singleitem_more\"><a href=\"{$rubricsurl}\">" . elgg_echo('rubricbuilder:more') . "</a></div>";
     
     echo "<div id=\"rubric_widget\">" . $rubrics . "</div>";
     
?>