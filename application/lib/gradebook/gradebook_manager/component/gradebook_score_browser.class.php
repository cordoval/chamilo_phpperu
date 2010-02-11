<?php

require_once dirname(__FILE__).'/gradebook_rel_user_browser/gradebook_rel_user_browser_table.class.php';
require_once Path :: get_library_path() . 'html/action_bar/action_bar_renderer.class.php';

class GradebookManagerGradebookScoreBrowserComponent extends GradebookManagerComponent
{

	private $gradebook;
	private $ab;

	/**
	 * Runs this component and displays its output.
	 */
	
	//niet afgewerkt !!!!
	
	function run()
	{

		$trail = new BreadcrumbTrail();

		$id = $_GET[GradebookManager :: PARAM_USER_ID];
		if ($id)
		{
			$this->gradebook = $this->retrieve_gradebook($id);
			$gradebook = $this->gradebook;
						
			if (!GradebookRights :: is_allowed(GradebookRights :: VIEW_RIGHT, 'browser', 'gradebook_component'))
			{
				$this->display_header($trail);
				$this->display_error_message(Translation :: get('NotAllowed'));
				$this->display_footer();
				exit;
			}
				
			$trail->add(new Breadcrumb($this->get_url(array(GradebookManager :: PARAM_ACTION => GradebookManager :: ACTION_VIEW_HOME)), Translation :: get('GradeBook')));
			$trail->add(new Breadcrumb($this->get_url(array(GradebookManager :: PARAM_ACTION => GradebookManager :: ACTION_BROWSE_GRADEBOOK)), Translation :: get('BrowseGradeBook')));
			$trail->add(new Breadcrumb($this->get_url(array(GradebookManager :: PARAM_ACTION => GradebookManager :: ACTION_VIEW_GRADEBOOK, GradebookManager :: PARAM_GRADEBOOK_ID => $id)), $gradebook->get_name()));

			$this->display_header($trail, false);
			$this->ab = $this->get_action_bar();
				
			echo $this->get_browser_html($gradebook);
				
			$this->display_footer();
		}
		else
		{
			$this->display_error_page(htmlentities(Translation :: get('NoGradeBookSelected')));
		}

	}

	function get_browser_html($gradebook){
		$html = array();
		$html[] = GradebookUtilities :: get_gradebook_admin_menu($this);
		$html[] = '<div id="tool_browser_right">';
		$html[] = '<div>';
		$html[] = $this->ab->as_html() . '<br />';
		$html[] = '<div class="clear"></div><div class="content_object" style="background-image: url('. Theme :: get_common_image_path() .'place_group.png);">';
		$html[] =  '<div class="title">'. Translation :: get('Description') .'</div>';
		$html[] =  $gradebook->get_description();
		$html[] =  '</div>';
		$html[] = '<div class="content_object" style="background-image: url('. Theme :: get_common_image_path() .'place_users.png);">';
		$html[] =  '<div class="title">'. Translation :: get('Users') .'</div>';
		$html[] = $this->get_table_html();
		$html[] = '</div>';
		$html[] = '<div style="clear: both;"></div>';
		$html[] = '</div>';
		$html[] = '</div>';
		return implode("\n", $html);
	}

	function get_table_html(){
		$parameters = $this->get_parameters();
		$parameters[GradebookManager :: PARAM_ACTION]=  GradebookManager :: ACTION_VIEW_GRADEBOOK;
		$table = new GradebookRelUserBrowserTable($this, $parameters, $this->get_condition());
		$html = array();
		$html[] = $table->as_html();
		$html[] = '<script type="text/javascript" src="'. Path :: get(WEB_LIB_PATH) . 'javascript/gradebook_ajax.js' .'"></script>';
		
		return implode("\n", $html);
	}

	function get_condition()
	{
		$conditions = array();
		$conditions[] = new EqualityCondition(GradebookRelUser :: PROPERTY_GRADEBOOK_ID, $this->gradebook->get_id());

		$query = $this->ab->get_query();

		if(isset($query) && $query != '')
		{
			$or_conditions[] = new LikeCondition(User :: PROPERTY_FIRSTNAME, $query);
			$or_conditions[] = new LikeCondition(User :: PROPERTY_LASTNAME, $query);
			$or_conditions[] = new LikeCondition(User :: PROPERTY_USERNAME, $query);
			$condition = new OrCondition($or_conditions);

			$users = UserDataManager :: get_instance()->retrieve_users($condition);
			while($user = $users->next_result())
			{
				$userconditions[] = new EqualityCondition(GradebookRelUser :: PROPERTY_USER_ID, $user->get_id());
			}

			if(count($userconditions))
			$conditions[] = new OrCondition($userconditions);
			else
			$conditions[] = new EqualityCondition(GradebookRelUser :: PROPERTY_USER_ID, 0);

		}

		$condition = new AndCondition($conditions);

		return $condition;
	}

	function get_action_bar()
	{
		$gradebook = $this->gradebook;

		$action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);

		$action_bar->set_search_url($this->get_url(array(GradebookManager :: PARAM_GRADEBOOK_ID => $gradebook->get_id())));
		$action_bar->add_common_action(new ToolbarItem(Translation :: get('Edit'), Theme :: get_common_image_path().'action_edit.png', $this->get_gradebook_editing_url($gradebook), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
		$action_bar->add_common_action(new ToolbarItem(Translation :: get('Delete'), Theme :: get_common_image_path().'action_delete.png', $this->get_gradebook_delete_url($gradebook), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
		$action_bar->add_tool_action(new ToolbarItem(Translation :: get('AddUsers'), Theme :: get_common_image_path().'action_subscribe.png', $this->get_gradebook_subscribe_user_browser_url($gradebook), ToolbarItem :: DISPLAY_ICON_AND_LABEL));

		$condition = new EqualityCondition(GradebookRelUser :: PROPERTY_GRADEBOOK_ID, $gradebook->get_id());
		$users = $this->retrieve_gradebook_rel_users($condition);
		$visible = ($users->size() > 0);

		if($visible)
		{
			$toolbar_data[] = array(
				'href' => $this->get_gradebook_emptying_url($gradebook),
				'label' => Translation :: get('Truncate'),
				'img' => Theme :: get_common_image_path().'action_recycle_bin.png',
				'display' => Utilities :: TOOLBAR_DISPLAY_ICON_AND_LABEL
			);
			$action_bar->add_tool_action(new ToolbarItem(Translation :: get('Truncate'), Theme :: get_common_image_path().'action_recycle_bin.png', $this->get_gradebook_emptying_url($gradebook), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
		}
		else
		{
			$toolbar_data[] = array(
				'label' => Translation :: get('TruncateNA'),
				'img' => Theme :: get_common_image_path().'action_recycle_bin_na.png',
				'display' => Utilities :: TOOLBAR_DISPLAY_ICON_AND_LABEL
			);
			$action_bar->add_tool_action(new ToolbarItem(Translation :: get('TruncateNA'), Theme :: get_common_image_path().'action_recycle_bin_na.png', null, ToolbarItem :: DISPLAY_ICON_AND_LABEL));
		}



		return $action_bar;
	}

}
?>