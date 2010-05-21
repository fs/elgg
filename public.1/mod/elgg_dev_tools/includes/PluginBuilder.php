<?php

class PluginBuilder {
	protected $params;
	protected $base_dir;
	protected $template_dir;
	
	public function build($base_dir, $template_dir, $params)
	{
		$this->params = $params;
		
		$this->base_dir = $base_dir;
		$this->template_dir = $template_dir;
		
		// make the plugin directory
		$plugin_dir = $params['plugin_name'] . '/';
		$this->createDirectory($plugin_dir);
		
		// make all the primary subdirs
		$dirs = array('actions', 'graphics', 'languages', 'lib', 'pages', 'views');
		foreach ($dirs as $dir)
		{
			$this->createDirectory($plugin_dir . $dir);
		}
		
		// make html default directory
		$html_dir = $plugin_dir . 'views/default/';
		$this->createDirectory($html_dir);
				
			
		// plugin settings
		if ($params['plugin_settings'])
		{
			$this->createDirectory($html_dir . 'settings/');
			$this->createDirectory($html_dir . 'settings/' . $params['plugin_name']);
			$this->createFile($html_dir . 'settings/' . $params['plugin_name'] . '/edit.php', 'edit.tmpl', $params);
		}
		
		// user settings
		if ($params['user_settings'])
		{
			$this->createDirectory($html_dir . 'usersettings/');
			$this->createDirectory($html_dir . 'usersettings/' . $params['plugin_name']);
			$this->createFile($html_dir . "usersettings/" . $params['plugin_name'] . '/edit.php', 'edit.tmpl', $params);
		}
		
		// widget
		if ($params['widget'])
		{
			$this->createDirectory($html_dir . 'widgets/');
			$this->createDirectory($html_dir . 'widgets/' . $params['plugin_name']);
			$this->createFile($html_dir . "widgets/" . $params['plugin_name'] . '/edit.php', 'edit.tmpl', $params);
			$this->createFile($html_dir . "widgets/" . $params['plugin_name'] . '/view.php', 'view.tmpl', $params);
		}
		
		// css
		if ($params['css'])
		{
			$this->createDirectory($html_dir . $params['plugin_name']);
			$this->createFile($html_dir . $params['plugin_name'] . '/css.php', 'css.tmpl', $params);
		}
		
		// primary pages
		if ($params['pages'])
		{
			$pages = explode(',', $params['pages']);
			foreach ($pages as $page)
			{
				$page = trim($page);
				$this->createFile($plugin_dir . 'pages/' . $page . '.php', 'page.tmpl', $params);
			}
		}
		
		// actions
		if ($params['actions'])
		{
			$actions = explode(',', $params['actions']);
			foreach ($actions as $action)
			{
				$action = trim($action);
				$this->createFile($plugin_dir . 'actions/' . $action . '.php', 'action.tmpl', $params);
			}
		}
		
		// manifest.xml
		$this->createFile($plugin_dir . 'manifest.xml', 'manifest.xml', $params);
		// language file
		$this->createLanguageFile($plugin_dir, $params);
		
		// start.php
		$this->createStartFile($plugin_dir, $params);
		
	}
		
	public function createFile($filename, $template, array $vars)
	{
		//error_log('template is ' . $this->template_dir . $template);
		//error_log('new file is ' . $this->base_dir . $filename);
		
		$file = file_get_contents($this->template_dir . $template);
			
		if (!$file) return false; 
			
		foreach ($vars as $k => $v)
			$file = str_replace("%%$k%%", $v, $file);
			
		if (!file_put_contents($this->base_dir . $filename, $file))
		{
			register_error(sprintf(elgg_echo('elgg_dev_tools:error:file_error'), $filename));
			forward('pg/elgg_dev_tools/builder/');	
		}
	}
	
	public function createDirectory($new_dir)
	{
		//error_log('trying to create directory ' . $this->base_dir . $new_dir);
		
		if (file_exists($this->base_dir . $new_dir))
			return;
			
		if (!mkdir($this->base_dir . $new_dir))
		{
			register_error(sprintf(elgg_echo('elgg_dev_tools:error:dir_error'), $new_dir));
			forward('pg/elgg_dev_tools/builder/');	
		}
	}
	
	public function createLanguageFile($plugin_dir, $params)
	{
		$map = "";
		if ($params['pages'])
			$map .= "	'{$params['plugin_name']}:pagetitle'	=> 'My Page Title',\n";
		if ($params['widget'] || $params['user_settings'] || $params['plugin_settings'])
			$map .= "	'{$params['plugin_name']}:param_label'	=> 'My Parameter',\n";
		if ($params['actions'])
		{
			$map .= "	'{$params['plugin_name']}:success'	=> 'Your action was successful',\n";
			$map .= "	'{$params['plugin_name']}:failure'	=> 'Your action failed',\n";
		}
			
		$params['language_map'] = $map;
		
		$this->createFile($plugin_dir . 'languages/en.php', 'en.tmpl', $params);
	}
	
	public function createStartFile($plugin_dir, $params)
	{
		$plugin_name = $params['plugin_name'];
		
		// actions
		$action_reg = "";
		if ($params['actions'])
		{
			$actions = explode(',', $params['actions']);
			foreach ($actions as $action)
			{
				$action = trim($action);
				$action_reg .= "		register_action('{$plugin_name}/{$action}', false, '{$this->base_dir}{$plugin_dir}actions/{$action}.php');\n";
			}
		}
		
		// page handler
		$ph_reg = "";
		$ph_func = "";
		$ph = $params['page_handler'];
		if ($ph)
		{
			$ph_reg = "		register_page_handler('{$ph}','{$plugin_name}_page_handler');\n";
			
			// replace this with a template in future
			$ph_func .= "	function {$plugin_name}_page_handler(\$page)\n";
			$ph_func .= "	{\n";
			$ph_func .= "		global \$CONFIG;\n\n"; 
			$ph_func .= "		\n";
			$ph_func .= "		switch (\$page[0])\n";
			$ph_func .= "		{\n";
		
			if ($params['pages'])
			{
				$pages = explode(',', $params['pages']);
				foreach ($pages as $page)
				{
					$page = trim($page);
					$ph_func .= "			case '{$page}':\n";
					$ph_func .= "				include \$CONFIG->pluginspath . '{$plugin_name}/pages/{$page}.php';\n";
					$ph_func .= "				break;\n";
				}
			}
			
			$ph_func .= "		\n";
			$ph_func .= "		}\n";
			$ph_func .= "		\n";
			$ph_func .= "		return true;\n";
			$ph_func .= "	}\n";		
		}
		
		// tool menu item and sidebar menu
		$menu_reg = "";
		$sidebar_reg = "";
		$sidebar_func = "";
		if ($params['pages'])
		{
			$pages = explode(',', $params['pages']);
			$page = trim($pages[0]);
			if ($params['page_handler'])
			{
				$menu_reg = "		add_menu({$plugin_name}, \$CONFIG->wwwroot . 'pg/{$ph}/{$page}/');\n";
			}
			else
			{
				$menu_reg = "		add_menu({$plugin_name}, \$CONFIG->wwwroot . 'mod/{$plugin_name}/pages/{$page}.php');\n";
			}
			
			if ($ph)
			{
				$sidebar_reg = "		register_elgg_event_handler('pagesetup','system','{$plugin_name}_submenus');\n";
				
				// replace this with a template in future
				$sidebar_func .= "	function {$plugin_name}_submenus()\n";
				$sidebar_func .= "	{\n";
				$sidebar_func .= "		global \$CONFIG;\n\n"; 
				$sidebar_func .= "		\n";
				$sidebar_func .= "		if (get_context() == '{$ph}')\n";
				$sidebar_func .= "		{\n";
			
				if ($params['pages'])
				{
					$pages = explode(',', $params['pages']);
					foreach ($pages as $page)
					{
						$page = trim($page);
						$sidebar_func .= "			add_submenu_item('{$page}', \$CONFIG->wwwroot . 'pg/{$ph}/{$page}/');\n";
					}
				}
	
				$sidebar_func .= "		}\n";
				$sidebar_func .= "		\n";
				$sidebar_func .= "	}\n";
			}
		}
		
		// widget
		$widget_reg = "";
		if ($params['widget'])
		{
			$widget_reg = "		add_widget_type('{$plugin_name}', 'My Widget', 'Description of my widget');\n";
		}
		
		// css
		$css_reg = "";
		if ($params['css'])
		{
			$css_reg = "		extend_view('css','{$plugin_name}/css');\n";
		}
		
		// set start.php parameters
		$params['action_registration'] = $action_reg;
		$params['page_handler_registration'] = $ph_reg;
		$params['page_handler_func'] = $ph_func;
		$params['menu_registration'] = $menu_reg;
		$params['sidebar_menu_registration'] = $sidebar_reg;
		$params['sidebar_menu_func'] = $sidebar_func;
		$params['widget_registration'] = $widget_reg;
		$params['extend_css_call'] = $css_reg;
		
		$this->createFile($plugin_dir . 'start.php', 'start.tmpl', $params);
	}
	
}


?>