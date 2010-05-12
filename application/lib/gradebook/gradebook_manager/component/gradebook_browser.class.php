<?php
require_once dirname(__FILE__).'/gradebook_internal_publication_browser/gradebook_internal_publication_browser_table.class.php';
require_once dirname(__FILE__).'/gradebook_external_publication_browser/gradebook_external_publication_browser_table.class.php';
require_once Path :: get_admin_path() . 'lib/admin_manager/admin_manager.class.php';
require_once Path :: get_library_path() . 'html/action_bar/action_bar_renderer.class.php';
require_once Path :: get_application_path() . '/lib/gradebook/gradebook_manager/gradebook_manager.class.php';
require_once Path :: get_repository_path() . 'lib/repository_manager/repository_manager.class.php';
require_once Path :: get_common_path() . '/html/menu/tree_menu/tree_menu.class.php';
require_once Path :: get_application_path() . '/lib/gradebook/data_provider/gradebook_tree_menu_data_provider.class.php';

class GradebookManagerGradebookBrowserComponent extends GradebookManager
{
	private $action_bar;
	private $content_object_ids = array();
	private $data_provider;
	private $type;
	private $applications;
	private $application;
	private $category;
	
	private $table;

	function run()
	{
		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb($this->get_url(array(GradebookManager :: PARAM_ACTION => GradebookManager :: ACTION_VIEW_HOME)), Translation :: get('Gradebook')));
		$this->applications = $this->retrieve_internal_item_applications();
		$this->application = Request :: get(GradebookManager :: PARAM_PUBLICATION_APP);
		$trail->add(new Breadcrumb($this->get_url(array(GradebookManager :: PARAM_ACTION => GradebookManager :: ACTION_VIEW_HOME, GradebookManager :: PARAM_PUBLICATION_APP => $this->application)), Translation :: get('BrowsePublicationsOf') . ' ' . $this->application));
		$this->type = Request :: get(GradebookManager :: PARAM_PUBLICATION_TYPE);
		$this->display_header($trail);
		$this->action_bar = $this->get_action_bar();
		
		if(count($this->applications) == 0)
			echo '<h2>' . Translation :: get('NoEvaluations') . '</h2>';
		else
			echo $this->get_gradebook_tabs();
		$this->display_footer();
	}

	function get_action_bar()
	{
		$action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);
		$action_bar->add_common_action(new ToolbarItem(Translation :: get('AddExternalEvaluation'), Theme :: get_common_image_path().'action_add.png', $this->get_create_external_url(), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
		
		return $action_bar;
	}
	

	
	function get_internal_condition()
	{
		if($this->data_provider)
			$category_id = Request :: get($this->data_provider->get_id_param());
		if(!$category_id)
			$category_id = 'C0';
		$conditions = array();
		$conditions[] = new EqualityCondition(InternalItem :: PROPERTY_APPLICATION, $this->application);
		$conditions[] = new EqualityCondition(InternalItem :: PROPERTY_CATEGORY, $category_id);
		$condition = new AndCondition($conditions);
		return $condition;
	}
	
	function get_external_condition()
	{
		if($this->application == 'weblcms')
		{
			$category_id = Request :: get($this->data_provider->get_id_param());
			if (!$category_id)
			{
				$category_id = 'C0';
			}
			else
			{
				
			}
			$condition = new EqualityCondition(ExternalItem :: PROPERTY_CATEGORY, $category_id);
			return $condition;
		}
		else
		{
			return new EqualityCondition(ExternalItem :: PROPERTY_CATEGORY, null);
		}	
	}
	
	function get_applications()
	{
		return $this->applications;
	}
	
	function get_internal_application_tabs($applications, $current_application = null)
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
            
            $html[] = '<a href="' . $this->get_publications_by_type_viewer_url('internal',$the_application) . '">';
            $html[] = '<div class="' . $type . '" style="background-image: url(' . Theme :: get_image_path('admin') . 'place_' . $the_application . '.png);">' . $application_name . '</div>';
            $html[] = '</a>';
        }
        
        $html[] = '</div>';
        $html[] = '<div style="clear: both;"></div>';
        return implode("\n", $html);
	}
	
	function get_external_application_tabs()
	{
		if ($this->application == 'weblcms')
        	$weblcms_type = 'application current';
        else
        	$weblcms_type = 'application';
		if ($this->application == 'general')
        	$general_type = 'application current';
        else
        	$general_type = 'application';
        $html[] = '<div class="application_selecter">';
        $html[] = '<a href="' . $this->get_publications_by_type_viewer_url('external','weblcms') .'">';
        $html[] = '<div class="' . $weblcms_type . '" style="background-image: url(' . Theme :: get_image_path('admin') . 'place_weblcms.png);">' . Translation :: get('Courses') . '</div>';
        $html[] = '</a>';
        $html[] = '<a href="' . $this->get_publications_by_type_viewer_url('external','general') . '">';
        $html[] = '<div class="' . $general_type . '" style="background-image: url(' . Theme :: get_image_path('admin') . 'place_general.png);">' . Translation :: get('General') . '</div>';
        $html[] = '</a>';
        $html[] = '</div>';
        $html[] = '<div style="clear: both;"></div>';
        
        return implode("\n", $html);
	}
	
	function get_gradebook_tabs()
	{
		$selected_tab = 0;
		if (Request :: get(GradebookManager :: PARAM_PUBLICATION_TYPE) == 'internal')
			$selected_tab = 0;
		if (Request :: get(GradebookManager :: PARAM_PUBLICATION_TYPE) == 'external')
			$selected_tab = 1;
        $html = array();
        $html[] = '<div id="gradebook_tabs">';
        $html[] = '<ul>';// Render the tabs
     
        $html[] = '<li><a href="#internal">';
        $html[] = '<span class="category">';
        $html[] = '<span class="title">' . Translation :: get('InternalPublications') . '</span>';
		$html[] = '</span>';
        $html[] = '</a></li>';
        $html[] = '<li><a href="#external">';
        $html[] = '<span class="category">';
        $html[] = '<span class="title">' . Translation :: get('ExternalPublications') . '</span>';
		$html[] = '</span>';
        $html[] = '</a></li>';
        $html[] = '</ul>';
        $html[] = ResourceManager :: get_instance()->get_resource_html(Path :: get(WEB_LIB_PATH) . 'javascript/tree_menu.js');
        $html[] = '<div id="internal">';
        $html[] = $this->get_internal_application_tabs($this->applications, $this->application);
		$this->table = new GradebookInternalPublicationBrowserTable($this, $this->get_parameters());
		if($this->application)
		{	
			$html[] = $this->show_filtered_publications('internal');
		}
        $html[] = '<div style="clear: both;"></div>';
        $html[] = '</div>';
        $html[] = '<div id="external"/>';
        $html[] = $this->get_external_application_tabs();
		$this->table = new GradebookExternalPublicationBrowserTable($this, $this->get_parameters());
		if($this->application && ($this->application == 'weblcms' || $this->application == 'general'))
		{	
			$html[] = $this->show_filtered_publications('external');
		}
        $html[] = '<div style="clear: both;"></div>';
        $html[] = '</div>';
        $html[] = '</div>';
        $html[] = '<script type="text/javascript">';
        $html[] = '  var tabnumber = ' . $selected_tab . ';';
        $html[] = '</script>';
        $html[] = ResourceManager :: get_instance()->get_resource_html(Path :: get(WEB_LIB_PATH) . 'javascript/gradebook_tabs.js');

        return implode("\n", $html);
	}
	
	function show_filtered_publications($type)
	{
		$this->set_parameter(GradebookManager :: PARAM_PUBLICATION_APP, $this->application);
		if ($this->data_provider = GradebookTreeMenuDataProvider :: factory($this->application, $this->get_url()))
		{
			$this->data_provider->set_type($type);
			$menu = new TreeMenu(ucfirst($this->application) . 'GradebookTreeMenu', $this->data_provider);
		}
        
        $width = 100;
        if ($menu)
        {
			$html[] = '<div style="float: left; width: 18%; overflow:auto;">';
			$html[] = $menu->render_as_tree();
			$html[] = '</div>';
			$width = 79;
        }
		$html[] = '<div style="float: right; width: ' . $width . '%;">';
		$html[] = $this->action_bar->as_html();
		$html[] = $this->table->as_html($this);
		
		$html[] = '</div>';
        return implode("\n", $html);
	}
}
?>
