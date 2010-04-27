<?php
require_once dirname(__FILE__).'/gradebook_publication_browser/gradebook_publication_browser_table.class.php';
require_once Path :: get_admin_path() . 'lib/admin_manager/admin_manager.class.php';
require_once Path :: get_library_path() . 'html/action_bar/action_bar_renderer.class.php';
require_once Path :: get_application_path() . '/lib/gradebook/gradebook_manager/gradebook_manager.class.php';
require_once Path :: get_repository_path() . 'lib/repository_manager/repository_manager.class.php';

class GradebookManagerGradebookBrowserComponent extends GradebookManager
{
	private $ab;
	private $content_object_ids = array();
	private $application;
	private $table;

	function run()
	{
		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb($this->get_url(array(GradebookManager :: PARAM_ACTION => GradebookManager :: ACTION_VIEW_HOME)), Translation :: get('GradeBook')));

//		echo $this->ab->as_html();
		$applications = array_merge($this->retrieve_applications_with_evaluations(), $this->retrieve_calculated_applications_with_evaluation());
		if(Request :: get(GradebookManager :: PARAM_PUBLICATION_TYPE))
		{
			$this->application = Request :: get(GradebookManager :: PARAM_PUBLICATION_TYPE);
			$trail->add(new Breadcrumb($this->get_url(array(GradebookManager :: PARAM_ACTION => GradebookManager :: ACTION_VIEW_HOME, GradebookManager :: PARAM_PUBLICATION_TYPE => $this->application)), Translation :: get('BrowsePublicationsOf') . ' ' . $this->application));
			$this->table = new GradebookPublicationBrowserTable($this);
		}
		$this->display_header($trail);
		$this->ab = $this->get_action_bar();
		echo $this->get_application_tabs($applications);
		if ($this->table)
			echo $this->table->as_html($this);
		$this->display_footer();
	}

	function get_action_bar()
	{
		$action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);
		$action_bar->add_common_action(new ToolbarItem(Translation :: get('AddExternalEvaluation'), Theme :: get_common_image_path().'action_add.png', $this->get_create_gradebook_url(), ToolbarItem :: DISPLAY_ICON_AND_LABEL));

		return $action_bar;
	}
	
	function get_condition()
	{
		return new EqualityCondition(InternalItem :: PROPERTY_APPLICATION, $this->application, InternalItem :: CLASS_NAME);
	}
	
	function get_application_tabs($applications)
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
}
?>