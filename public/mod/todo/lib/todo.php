<?php
	/**
	 * Todo Helper functions
	 * 
	 * @package Todo
	 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
	 * @author Jeff Tilson
	 * @copyright THINK Global School 2010
	 * @link http://www.thinkglobalschool.com/
	 * 
	 */
	
	/**
	 * Return an array containing the todo access levels
	 * 
	 * @return array
	 */
	function get_todo_access_array() {
		$access = array(TODO_ACCESS_LEVEL_LOGGED_IN => elgg_echo('todo:label:loggedin'),
						TODO_ACCESS_LEVEL_ASSIGNEES_ONLY => elgg_echo('todo:label:assigneesonly'));
		return $access;
	}
	
	/**
	 * Return an array containing a list of all site groups for use
	 * in a pulldown/dropdown box
	 * 
	 * @return array 
	 */
	function get_todo_groups_array() {
		$groups = elgg_get_entities(array('types' => 'group'));
		$groups_array = array();
		foreach ($groups as $group) {
			$groups_array[$group->getGUID()] = $group->name;
		}
		return $groups_array;
	}
	
	/**
	 * If enabled, return an array of rubrics for use in pulldowns
	 * 
	 * @return mixed
	 */
	function get_todo_rubric_array() {
		if (TODO_RUBRIC_ENABLED) {
			$rubrics = elgg_get_entities(array('types' => 'object', 'subtypes' => 'rubric'));
			$rubrics_array = array();
			
			foreach ($rubrics as $rubric) {
				$rubrics_array[$rubric->getGUID()] = $rubric->title;
			}
			return $rubrics_array;
		}
		return false;
	}
	
	/**
	 * Return an array of users assigned to given todo
	 *
	 * @param int $guid // todo guid
	 * @return array
	 */
	function get_todo_assignees($guid) {
		

		
		$entities = elgg_get_entities_from_relationship(array(
															'relationship' => TODO_ASSIGNEE_RELATIONSHIP,
															'relationship_guid' => $guid,
															'inverse_relationship' => TRUE,
															'types' => array('user', 'group'),
															'limit' => 9999,
															'offset' => 0,
															'count' => false,
														));
				
									
		$assignees = array();
		
		// Need to be flexible, most likely will have either just users, or just 
		// groups, but will take into account both just in case
		foreach($entities as $entity) {
			if ($entity instanceof ElggUser) {
				$assignees[] = $entity;
			} else if ($entity instanceof ElggGroup) {
				foreach ($entity->getMembers() as $member) {
					$assignees[] = $member;
				}
			}
		}
		
		return $assignees;
	}
	
	/**
	 * Return an array submissions for given todo
	 *
	 * @param int $guid todo_guid
	 * @return array
	 */
	function get_todo_submissions($guid) {
		$entities = elgg_get_entities_from_relationship(array(
															'relationship' => SUBMISSION_RELATIONSHIP,
															'relationship_guid' => $guid,
															'inverse_relationship' => TRUE,
															'types' => array('object'),
															'limit' => 9999,
															'offset' => 0,
															'count' => false,
														));
		
		return $entities;
	}
	
	/**
	 * Return all todos a user has been assigned
	 *
	 * @param int 
	 * @return array 
	 */
	function get_users_todos($user_guid) {
		return elgg_get_entities_from_relationship(array('relationship' => TODO_ASSIGNEE_RELATIONSHIP, 
														 'relationship_guid' => $user_guid, 
														 'inverse_relationship' => FALSE,
														 'limit' => 9999,
														 'offset' => 0,));
	}
	
	function is_todo_assignee($todo_guid, $user_guid) {
 		$object = check_entity_relationship($user_guid, TODO_ASSIGNEE_RELATIONSHIP , $todo_guid);
		if ($object) {
 			return true;
		} else {
			return false;
		}
	}
	
	/**
	 * Determine if given user has made a submission to given todo
	 * 
	 * @param int $user_guid
	 * @param int $todo_guid
	 * @return bool
	 */
	function has_user_submitted($user_guid, $todo_guid) {
		$submissions = get_todo_submissions($todo_guid);
		foreach ($submissions as $submission) {
			if ($user_guid == $submission->owner_guid) {
				return $submission;
			}
		}
		return false;
	}
	
	/**
	 * Determine if all users for a given todo have submiited to
	 * or complete the todo
	 *
	 * @param int $todo_guid
	 * @return bool
	 */
	function have_assignees_completed_todo($todo_guid) {
		$assignees = get_todo_assignees($todo_guid);
		$complete = true;
		foreach ($assignees as $assignee) {
			$complete &= has_user_submitted($assignee->getGUID(), $todo_guid);
		}
		return $complete;
	}
	
	/**
	 * Return todos with a due date before givin date
	 *
	 * @param array $todos
	 * @param int $date (Timestamp)
	 * @return array
	 */
	function get_todos_due_before($todos, $date) {
		foreach($todos as $idx => $todo) {
			if ($todo->due_date <= $date) {
				continue;
			} else {
				unset($todos[$idx]);
			}
		}
		return $todos;
	}
	
	/**
	 * Return todos with a due date after givin dates
	 *
	 * @param array $todos
	 * @param int $date (Timestamp)
	 * @return array
	 */
	function get_todos_due_after($todos, $date) {
		foreach($todos as $idx => $todo) {
			if ($todo->due_date > $date) {
				continue;
			} else {
				unset($todos[$idx]);
			}
		}
		return $todos;
	}
	
	/**
	 * Return todos with a due date between givin dates
	 *
	 * @param array $todos
	 * @param int $start_date Timestamp
	 * @param int $end_date Timestamp, default null for no end date
	 * @return array
	 */
	function get_todos_due_between($todos, $start_date, $end_date) {
		foreach($todos as $idx => $todo) {
			if (($todo->due_date > $start_date) && ($todo->due_date <= $end_date)) {
				continue;
			} else {
				unset($todos[$idx]);
			}
		}
		return $todos;
	}
	
	/**
	 * S
	 *
	 *
	 */
	function sort_todos_by_due_date(&$todos, $descending = false) {
		if ($descending) {
			usort($todos, "compare_todo_due_dates_desc");
		} else {
			usort($todos, "compare_todo_due_dates_asc");	
		}
	}
	
	function compare_todo_due_dates_desc($a, $b) {
		if ($a->due_date == $b->due_date) {
			return 0;
		}
		return ($a->due_date > $b->due_date) ? -1 : 1;
	}
	
	function compare_todo_due_dates_asc($a, $b) {
		if ($a->due_date == $b->due_date) {
			return 0;
		}
		return ($a->due_date < $b->due_date) ? -1 : 1;
	}
	

	/**
	 * Clears any cached data
	 * @return bool 
	 */	
	function clear_todo_cached_data() {
		remove_metadata($_SESSION['user']->guid,'is_todo_cached');
		remove_metadata($_SESSION['user']->guid,'todo_title');
		remove_metadata($_SESSION['user']->guid,'todo_description');
		remove_metadata($_SESSION['user']->guid,'todo_tags');
		remove_metadata($_SESSION['user']->guid,'todo_due_date');
		remove_metadata($_SESSION['user']->guid,'todo_assignees');
		remove_metadata($_SESSION['user']->guid,'todo_return_required');
		remove_metadata($_SESSION['user']->guid,'todo_rubric_select');
		remove_metadata($_SESSION['user']->guid,'todo_rubric_guid');
		remove_metadata($_SESSION['user']->guid,'todo_access_level');
		return true;
	}
	
	/** Hacky hacks **/
	function get_viewed_entity() { 
	     if ($backtrace = debug_backtrace()) { 
	         foreach($backtrace as $step) { 
	             if ($step['function'] == 'elgg_view' 
	                 && isset($step['args'][1]['entity']) 
	                 && $step['args'][1]['entity'] instanceof ElggObject) { 
	                 return $step['args'][1]['entity']; 
	             } 
	         } 
	     } 
	     return false; 
	}
?>