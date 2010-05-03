<?php
require_once dirname(__FILE__).'/gradebook_publication_browser/gradebook_publication_browser_table.class.php';
require_once Path :: get_admin_path() . 'lib/admin_manager/admin_manager.class.php';
require_once Path :: get_library_path() . 'html/action_bar/action_bar_renderer.class.php';
require_once Path :: get_application_path() . '/lib/gradebook/gradebook_manager/gradebook_manager.class.php';
require_once Path :: get_application_path() . '/lib/gradebook/weblcms_publications_category_menu.class.php';
require_once Path :: get_repository_path() . 'lib/repository_manager/repository_manager.class.php';

class GradebookManagerGradebookBrowserComponent extends GradebookManager
{
	private $ab;
	private $content_object_ids = array();
	private $application;
	
	private $applications;
	private $table;
	private $menu;
	private $types = array('MyEvaluatedPublications', 'MyEvaluations');
	private $origins = array('Internal', 'External');

	function run()
	{
		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb($this->get_url(array(GradebookManager :: PARAM_ACTION => GradebookManager :: ACTION_VIEW_HOME)), Translation :: get('GradeBook')));
		$this->applications = $this->retrieve_internal_item_applications();
		
//		echo $this->ab->as_html();
		//$applications = array_merge($this->retrieve_applications_with_evaluations(), $this->retrieve_calculated_applications_with_evaluation());
		//$this->applications = $this->retrieve_filtered_array_internal_evaluated_publication($this->get_user_id());
		//$this->applications = $this->retrieve_filtered_array_internal_my_evaluations($this->get_user_id());
		$this->application = Request :: get(GradebookManager :: PARAM_PUBLICATION_TYPE);
		if($this->application)
		{	
			$trail->add(new Breadcrumb($this->get_url(array(GradebookManager :: PARAM_ACTION => GradebookManager :: ACTION_VIEW_HOME, GradebookManager :: PARAM_PUBLICATION_TYPE => $this->application)), Translation :: get('BrowsePublicationsOf') . ' ' . $this->application));
			$parameters = $this->get_parameters();
			$parameters[GradebookManager :: PARAM_ACTION]=  GradebookManager :: ACTION_VIEW_HOME;
			$parameters[GradebookManager :: PARAM_PUBLICATION_TYPE]=  $this->application;
			if($this->application == 'weblcms')
			{
				$this->menu = $this->get_menu();
				if (Request :: get('tool'))
					$parameters['tool'] = Request :: get('tool');
			}
			$this->table = new GradebookPublicationBrowserTable($this, $parameters);
		}
		$this->display_header($trail);
		$this->ab = $this->get_action_bar();
		
		if(count($this->applications) == 0)
			echo '<h2>' . Translation :: get('NoEvaluations') . '</h2>';
		else
		{
				echo $this->get_gradebook_tabs();
		}
		$this->display_footer();
	}

	function get_action_bar()
	{
		$action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);
		$action_bar->add_common_action(new ToolbarItem(Translation :: get('AddExternalEvaluation'), Theme :: get_common_image_path().'action_add.png', $this->get_create_gradebook_url(), ToolbarItem :: DISPLAY_ICON_AND_LABEL));

		return $action_bar;
	}
	
	function get_menu()
	{
		$tool = Request :: get('tool');
        $tool = $tool ? $tool : 0;
		$menu = new WeblcmsPublicationsCategoryMenu($tool, null, $this->get_user());
		return $menu;
	}
	
	function get_condition($applications_array)
	{
		$ids = array_keys($applications_array);
		$conditions = array();
		$conditions[] = new EqualityCondition(InternalItem :: PROPERTY_APPLICATION, $this->application);
		$conditions[] = new InCondition(InternalItem :: PROPERTY_ID, $ids);
		$condition = new AndCondition($conditions);
		return $condition;
	}
	
	function get_applications()
	{
		return $this->applications;
	}
	
	function get_internal_application_tabs($applications)
	{
        $html = array();
        
        $html[] = ResourceManager :: get_instance()->get_resource_html(Path :: get(WEB_LIB_PATH) . 'javascript/application.js');
        $html[] = '<div class="application_selecter">';
        
        foreach ($applications as $the_application)
        {
            if (isset($current_application) && $current_application == $the_application)
            {
                $type = 'application current';
            }
            else
            {
                $type = 'application';
            }
            
            $application_name = Translation :: get(Utilities :: underscores_to_camelcase($the_application));
            
            $html[] = '<a href="' . $this->get_publications_by_type_viewer_url($the_application) . '">';
            $html[] = '<div class="' . $type . '" style="background-image: url(' . Theme :: get_image_path('admin') . 'place_' . $the_application . '.png);">' . $application_name . '</div>';
            $html[] = '</a>';
        }
        
        $html[] = '</div>';
        $html[] = '<div style="clear: both;"></div>';
        
        return implode("\n", $html);
	}
	
	function get_external_application_tabs()
	{
        $html[] = '<div class="application_selecter">';
        $html[] = '<a href="">';
        $html[] = '<div class="application" style="background-image: url(' . Theme :: get_image_path('admin') . 'place_weblcms.png);">' . Translation :: get('Courses') . '</div>';
        $html[] = '</a>';
        $html[] = '<a href="">';
        $html[] = '<div class="application" style="background-image: url(' . Theme :: get_image_path('admin') . 'place_general.png);">' . Translation :: get('General') . '</div>';
        $html[] = '</a>';
        $html[] = '</div>';
        $html[] = '<div style="clear: both;"></div>';
        
        return implode("\n", $html);
	}
	
	function get_gradebook_tabs()
	{
        $html = array();
        $html[] = '<div id="gradebook_tabs">';
        $html[] = '<ul>';// Render the tabs
        $html[] = '<li><a href="#internal">';
        $html[] = '<span class="category">';
        $html[] = '<img src="' . Theme :: get_image_path() . 'place_mini_help.png" border="0" style="vertical-align: middle;" alt="internal_publications" title="internal_publications"/>';
        $html[] = '<span class="title">' . Translation :: get('InternalPublications') . '</span>';
		$html[] = '</span>';
        $html[] = '</a></li>';
        $html[] = '<li><a href="#external">';
        $html[] = '<span class="category">';
        $html[] = '<img src="' . Theme :: get_image_path() . 'place_mini_home.png" border="0" style="vertical-align: middle;" alt="evaluated_publications" title="external_publications"/>';
        $html[] = '<span class="title">' . Translation :: get('ExternalPublications') . '</span>';
		$html[] = '</span>';
        $html[] = '</a></li>';
        $html[] = '</ul>';
        $html[] = '<div id="internal"/>';
        $html[] = $this->get_internal_application_tabs($this->applications);
        $html[] = '<h2>' . ucfirst($this->application) . '</h2>';
		if ($this->application == 'weblcms')
		{
			$html[] = '<div style="float: left; width: 12%; overflow:auto;">';
			$html[] = $this->menu->render_as_tree();
			$html[] = '</div>';
			$html[] = '<div style="float: right; width: 85%;">';
			$html[] = $this->table->as_html($this);
			$html[] = '</div>';
		}
		else
		{
			$html[] = $this->table->as_html($this);
		}
        $html[] = '</div>';
        $html[] = '<div id="external"/>';
        $html[] = $this->get_external_application_tabs();
        $html[] = '</div>';
        $html[] = '</div>';
        $html[] = ResourceManager :: get_instance()->get_resource_html(Path :: get(WEB_LIB_PATH) . 'javascript/gradebook_tabs.js');


        return implode("\n", $html);
	}
}
?>
