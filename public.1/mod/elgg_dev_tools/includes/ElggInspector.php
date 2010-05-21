<?php

class ElggInspector {

	/**
	 * Get Elgg Event information
	 * 
	 * returns [event,type] => array(handlers)
	 */
	public function getElggEvents()
	{
		global $CONFIG;
		
		$tree = array();
		foreach ($CONFIG->events as $event => $types)
		{
			foreach ($types as $type => $handlers)
			{
				$tree[$event . ',' . $type] = array_values($handlers);
			}
		}
		
		ksort($tree);
		
		return $tree;
	}
	
	/**
	 * Get Elgg Plugin Hooks information
	 * 
	 * returns [hook,type] => array(handlers)
	 */
	public function getElggHooks()
	{
		global $CONFIG;
		
		$tree = array();
		foreach ($CONFIG->hooks as $hook => $types)
		{
			foreach ($types as $type => $handlers)
			{
				$tree[$hook . ',' . $type] = array_values($handlers);
			}
		}
		
		ksort($tree);
		
		return $tree;
	}
	
	/**
	 * Get Elgg View information
	 * 
	 * returns [view] => array(view location and extensions)
	 */
	public function getElggViews()
	{
		global $CONFIG;
		
		$coreViews = $this->recurseFileTree($CONFIG->viewpath . "default/");
		
		// remove base path and php extension 
		array_walk($coreViews, create_function('&$v,$k', 'global $CONFIG; $v = substr($v, strlen($CONFIG->viewpath . "default/"), -4);'));
		
		// setup views array before adding extensions and plugin views
		$views = array();
		foreach ($coreViews as $view)
			$views[$view] = array($CONFIG->viewpath . "default/" . $view . ".php");

		// add plugins and handle overrides
		foreach ($CONFIG->views->locations['default'] as $view => $location)
		{
			$views[$view] = array($location . $view . ".php");
		}
		
		// now extensions
		foreach ($CONFIG->views->extensions as $view => $extensions)
		{
			$view_list = array();
			foreach ($extensions as $priority => $ext_view)
			{
				if (isset($views[$ext_view]))
					$view_list[] = $views[$ext_view][0];	
			}
			if (count($view_list) > 0)
				$views[$view] = $view_list;
		}

		ksort($views);
		
		return $views;
	}
	
	/**
	 * Get Elgg Widget information
	 * 
	 * returns [widget] => array(name, contexts)
	 */
	public function getElggWidgets()
	{
		global $CONFIG;
		
		$tree = array();
		foreach ($CONFIG->widgets->handlers as $handler => $handler_obj)
		{
			$tree[$handler] = array($handler_obj->name, implode(',', array_values($handler_obj->context)));
		}
		
		ksort($tree);
		
		return $tree;
	}
	
	
	/**
	 * Get Elgg Actions information
	 * 
	 * returns [action] => array(file, public, admin)
	 */
	public function getElggActions()
	{
		global $CONFIG;
		
		$tree = array();
		foreach ($CONFIG->actions as $action => $info)
		{
			$tree[$action] = array($info['file'], ($info['public']) ? 'public' : 'logged in only', ($info['admin']) ? 'admin only' : 'non-admin');
		}
		
		ksort($tree);
		
		return $tree;
	}
	
	/**
	 * Get Simple Cache information
	 * 
	 * returns [views]
	 */
	public function getElggSimpleCache()
	{
		global $CONFIG;
		
		$tree = array();
		foreach ($CONFIG->views->simplecache as $view)
		{
			$tree[$view] = "";
		}
		
		ksort($tree);
		
		return $tree;
	}
	
	/**
	 * Get Elgg REstful API methods
	 * 
	 * returns [method] => array(function, parameters, call_method, auth_token, anonymous)
	 */
	public function getElggREST()
	{
		global $METHODS;
		
		$tree = array();
		foreach ($METHODS as $method => $info)
		{
			$tree[$method] = array(	$info['function'], 'params: ' . implode(',', array_keys($info['parameters'])), $info['call_method'], 
			 						($info['require_auth_token']) ? 'requires auth token' : 'auth token not required', 'anonymous is broken in Elgg');
		}
		
		ksort($tree);
		
		return $tree;
	}
		
	/**
	 * Create array of all php files in directory and subdirectories
	 * 
	 * @param $dir full path to directory to begin search
	 * @return array of every php file in $dir or below in file tree
	 */
	protected function recurseFileTree($dir)
	{		
		$view_list = array();
		
		$handle = opendir($dir);
		while ($file = readdir($handle))
		{
			if ($file[0] == '.')
			{
				
			}
			else if (is_dir($dir . $file))
			{
				$view_list = array_merge($view_list, $this->recurseFileTree($dir . $file. "/"));
			}
			else
			{
				$extension = strrchr(trim($file, "/"), '.');
				if ($extension === ".php")
				{	
					$view_list[] = $dir . $file;
				}
			}
		}
		closedir($handle);
				
		return $view_list;
	}		
	
}

?>