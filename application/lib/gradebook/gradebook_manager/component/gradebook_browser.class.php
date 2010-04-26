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

	function run()
	{
		if (!GradebookRights :: is_allowed(GradebookRights :: VIEW_RIGHT, GradebookRights :: LOCATION_BROWSER, 'gradebook_component'))
		{
			$this->display_header($trail);
			$this->display_error_message(Translation :: get('NotAllowed'));
			$this->display_footer();
			exit;
		}
		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb($this->get_url(array(GradebookManager :: PARAM_ACTION => GradebookManager :: ACTION_VIEW_HOME)), Translation :: get('GradeBook')));

		$this->display_header($trail);
		$this->ab = $this->get_action_bar();
//		echo $this->ab->as_html();
		$applications = $this->retrieve_applications_with_evaluations();
		echo $this->get_application_tabs($applications);
		if(Request :: get(GradebookManager :: PARAM_PUBLICATION_TYPE))
		{
			$application = Request :: get(GradebookManager :: PARAM_PUBLICATION_TYPE);
			$internal_items = $this->retrieve_internal_items_by_application($application);
			while($internal_item = $internal_items->next_result())
			{
				$application_manager = WebApplication :: factory($internal_item->get_application());
				$attributes = $application_manager->get_content_object_publication_attribute($internal_item->get_publication_id());
				$this->content_object_ids[] = $attributes->get_publication_object_id();;
			}
			$this->get_condition();
			$table = new GradebookPublicationBrowserTable($this);
			echo $table->as_html($this);
		}
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
		return new InCondition(ContentObject :: PROPERTY_ID, $this->content_object_ids, ContentObject :: CLASS_NAME);
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