<?php
require_once dirname(__FILE__).'/cba_manager_component.class.php';
require_once dirname(__FILE__).'/../cba_data_manager.class.php';
require_once dirname(__FILE__).'/component/competency_browser/competency_browser_table.class.php';
require_once dirname(__FILE__).'/component/indicator_browser/indicator_browser_table.class.php';
require_once dirname(__FILE__).'/component/criteria_browser/criteria_browser_table.class.php';

require_once 'HTML/Menu.php';
require_once 'HTML/Menu/ArrayRenderer.php';
/**
 * A Cba manager
 *
 * @author Nick Van Loocke
 */
 class CbaManager extends WebApplication
 {
 	const APPLICATION_NAME = 'cba';
 	
	const PARAM_COMPETENCY = 'competency';
	const PARAM_INDICATOR = 'indicator';
	const PARAM_CRITERIA = 'criteria';
	
	const PARAM_CONTENT_OBJECT_TYPE = 'type';
	const PARAM_DELETE_SELECTED_COMPETENCYS = 'delete_selected_competencys';
	const PARAM_DELETE_SELECTED_INDICATORS = 'delete_selected_indicators';
	const PARAM_DELETE_SELECTED_CRITERIAS = 'delete_selected_criterias';
	
	const ACTION_DELETE_COMPETENCYS = 'delete_competencys';
	const ACTION_DELETE_INDICATORS = 'delete_indicators';
	const ACTION_DELETE_CRITERIAS = 'delete_criterias';

	const ACTION_DELETE_COMPETENCY = 'delete_competency';
	const ACTION_DELETE_INDICATOR = 'delete_indicator';
	const ACTION_DELETE_CRITERIA = 'delete_criteria';
	
	const ACTION_EDITOR_COMPETENCY = 'editor_competency';
	const ACTION_EDITOR_INDICATOR = 'editor_indicator';
	const ACTION_EDITOR_CRITERIA = 'editor_criteria';
		
	const ACTION_BROWSE_COMPETENCY = 'competency';
	const ACTION_BROWSE_INDICATOR = 'indicator';
	const ACTION_BROWSE_CRITERIA = 'criteria';
	
	const ACTION_CREATOR_COMPETENCY = 'creator_competency';
	const ACTION_CREATOR_INDICATOR = 'creator_indicator';
	const ACTION_CREATOR_CRITERIA = 'creator_criteria';
	
	const ACTION_MANAGE_CATEGORIES_COMPETENCY = 'manage_categories_competency';
	const ACTION_MANAGE_CATEGORIES_INDICATOR = 'manage_categories_indicator';
	const ACTION_MANAGE_CATEGORIES_CRITERIA = 'manage_categories_criteria';
	
	const ACTION_CREATE = 'create';
	const ACTION_VIEW_SEARCH_COMPETENCY = 'search';
	
	const ACTION_MOVE_COMPETENCY = 'move_competency';
	const ACTION_MOVE_INDICATOR = 'move_indicator';
	const ACTION_MOVE_CRITERIA = 'move_criteria';
	

	private $category_menu;
	
	/**
	 * Constructor
	 * @param User $user The current user
	 */
    function CbaManager($user = null)
    {
    	parent :: __construct($user);
    	$this->parse_input_from_table();
    }

    /**
	 * Run this cba manager
	 */
	function run()
	{
		$action = $this->get_action();
		$component = null;
		switch ($action)
		{	
			case self :: ACTION_CREATE:
				$component = CbaManagerComponent :: factory('Create', $this);
				break;
		    case self :: ACTION_MANAGE_CATEGORIES_COMPETENCY:
				$component = CbaManagerComponent :: factory('CompetencyCategoryManager', $this);
				break;
			case self :: ACTION_MANAGE_CATEGORIES_INDICATOR:
				$component = CbaManagerComponent :: factory('IndicatorCategoryManager', $this);
				break;
			case self :: ACTION_MANAGE_CATEGORIES_CRITERIA:
				$component = CbaManagerComponent :: factory('CriteriaCategoryManager', $this);
				break;
			case self :: ACTION_BROWSE_COMPETENCY:
				$component = CbaManagerComponent :: factory('Browser', $this);
				break;
			case self :: ACTION_BROWSE_INDICATOR:
				$component = CbaManagerComponent :: factory('IndicatorBrowser', $this);
				break;		
			case self :: ACTION_BROWSE_CRITERIA:
				$component = CbaManagerComponent :: factory('CriteriaBrowser', $this);
				break;
		    case self :: ACTION_CREATOR_COMPETENCY:
		    	$component = CbaManagerComponent :: factory('CompetencyCreator', $this);
		    	break;
		    case self :: ACTION_CREATOR_INDICATOR:
		    	$component = CbaManagerComponent :: factory('IndicatorCreator', $this);
		    	break;
		    case self :: ACTION_CREATOR_CRITERIA:
		    	$component = CbaManagerComponent :: factory('CriteriaCreator', $this);
		    	break;
		    case self :: ACTION_EDITOR_COMPETENCY :
				$component = CbaManagerComponent :: factory('CompetencyEditor', $this);
				break;
			case self :: ACTION_EDITOR_INDICATOR :
				$component = CbaManagerComponent :: factory('IndicatorEditor', $this);
				break;
			case self :: ACTION_EDITOR_CRITERIA :
				$component = CbaManagerComponent :: factory('CriteriaEditor', $this);
				break;
		    case self :: ACTION_DELETE_COMPETENCY :
				$component = CbaManagerComponent :: factory('CompetencyDeleter', $this);
				break;
			case self :: ACTION_DELETE_INDICATOR :
				$component = CbaManagerComponent :: factory('IndicatorDeleter', $this);
				break;
			case self :: ACTION_DELETE_CRITERIA :
				$component = CbaManagerComponent :: factory('CriteriaDeleter', $this);
				break;
			case self :: ACTION_DELETE_COMPETENCYS :
				$component = CbaManagerComponent :: factory('CompetencyDeleter', $this);
				break;
			case self :: ACTION_DELETE_INDICATORS :
				$component = CbaManagerComponent :: factory('IndicatorDeleter', $this);
				break;
			case self :: ACTION_DELETE_CRITERIAS :
				$component = CbaManagerComponent :: factory('CriteriaDeleter', $this);
				break;
			case self :: ACTION_VIEW_SEARCH_COMPETENCY :
				$component = CbaManagerComponent :: factory('CompetencySearch', $this);
				break;
			case self :: ACTION_MOVE_COMPETENCY :
                $component = CBaManagerComponent :: factory('CompetencyMover', $this);
                break;
           	case self :: ACTION_MOVE_INDICATOR :
                $component = CbaManagerComponent :: factory('IndicatorMover', $this);
                break;
        	case self :: ACTION_MOVE_CRITERIA :
                $component = CbaManagerComponent :: factory('CriteriaMover', $this);
                break;
			default :
				$this->set_action(self :: ACTION_BROWSE_COMPETENCY);
				$component = CbaManagerComponent :: factory('Browser', $this);

		}
		$component->run();
	}

	private function parse_input_from_table()
	{
		if (isset ($_POST['action']))
		{
			switch ($_POST['action'])
			{
				case self :: PARAM_DELETE_SELECTED_COMPETENCYS:
					$selected_ids = $_POST[CompetencyBrowserTable :: DEFAULT_NAME.ObjectTable :: CHECKBOX_NAME_SUFFIX];

					if (empty ($selected_ids))
					{
						$selected_ids = array ();
					}
					elseif (!is_array($selected_ids))
					{
						$selected_ids = array ($selected_ids);
					}

					$this->set_action(self :: ACTION_DELETE_COMPETENCYS);
					$_GET[self :: PARAM_COMPETENCY] = $selected_ids;
					break;
					
				case self :: PARAM_DELETE_SELECTED_INDICATORS:
					$selected_ids = $_POST[IndicatorBrowserTable :: DEFAULT_NAME.ObjectTable :: CHECKBOX_NAME_SUFFIX];

					if (empty ($selected_ids))
					{
						$selected_ids = array ();
					}
					elseif (!is_array($selected_ids))
					{
						$selected_ids = array ($selected_ids);
					}

					$this->set_action(self :: ACTION_DELETE_INDICATORS);
					$_GET[self :: PARAM_INDICATOR] = $selected_ids;
					break;
					
				case self :: PARAM_DELETE_SELECTED_CRITERIAS:
					$selected_ids = $_POST[CriteriaBrowserTable :: DEFAULT_NAME.ObjectTable :: CHECKBOX_NAME_SUFFIX];

					if (empty ($selected_ids))
					{
						$selected_ids = array ();
					}
					elseif (!is_array($selected_ids))
					{
						$selected_ids = array ($selected_ids);
					}

					$this->set_action(self :: ACTION_DELETE_CRITERIAS);
					$_GET[self :: PARAM_CRITERIA] = $selected_ids;
					break;
			}

		}
	}
	

	function get_application_name()
	{
		return self :: APPLICATION_NAME;
	}

	// Data Retrieving
	
	// Competency
 	function count_competencys($condition)
	{
		return CbaDataManager :: get_instance()->count_competencys($condition);
	}
	
 	function retrieve_competencys($condition = null, $offset = null, $count = null, $order_property = null)
	{
		return CbaDataManager :: get_instance()->retrieve_competencys($condition, $offset, $count, $order_property);
	}
	
 	function retrieve_competency($id)
	{
		return CbaDataManager :: get_instance()->retrieve_competency($id);
	}
	
	// Indicator
 	function count_indicators($condition)
	{
		return CbaDataManager :: get_instance()->count_indicators($condition);
	}
	
 	function retrieve_indicators($condition = null, $offset = null, $count = null, $order_property = null)
	{
		return CbaDataManager :: get_instance()->retrieve_indicators($condition, $offset, $count, $order_property);
	}
	
 	function retrieve_indicator($id)
	{
		return CbaDataManager :: get_instance()->retrieve_indicator($id);
	}
	
 	// Criteria
 	function count_criterias($condition)
	{
		return CbaDataManager :: get_instance()->count_criterias($condition);
	}
	
 	function retrieve_criterias($condition = null, $offset = null, $count = null, $order_property = null)
	{
		return CbaDataManager :: get_instance()->retrieve_criterias($condition, $offset, $count, $order_property);
	}
	
 	function retrieve_criteria($id)
	{
		return CbaDataManager :: get_instance()->retrieve_criteria($id);
	}

	
	// Url Creation
	
 	function get_update_competency_url($competency)
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_EDITOR_COMPETENCY,
								    self :: PARAM_COMPETENCY => $competency->get_id()));
	}
	
 	function get_update_indicator_url($indicator)
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_EDITOR_INDICATOR,
								    self :: PARAM_INDICATOR => $indicator->get_id()));
	}
	
 	function get_update_criteria_url($criteria)
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_EDITOR_CRITERIA,
								    self :: PARAM_CRITERIA => $criteria->get_id()));
	}
	
 	function get_delete_competency_url($competency)
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_DELETE_COMPETENCY,
								    self :: PARAM_COMPETENCY => $competency->get_id()));
	}
	
 	function get_delete_indicator_url($indicator)
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_DELETE_INDICATOR,
								    self :: PARAM_INDICATOR => $indicator->get_id()));
	}
	
	function get_delete_criteria_url($criteria)
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_DELETE_CRITERIA,
								    self :: PARAM_CRITERIA => $criteria->get_id()));
	}	
	
	function get_browse_competency_url()
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE_COMPETENCY));
	}
	
 	function get_browse_indicator_url()
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE_INDICATOR));
	}
	
 	function get_browse_criteria_url()
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE_CRITERIA));
	}
	
	
	function get_create_url()
	{
		return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_CREATE));
	}
	
	
	//Url creation for the different categories
	
 	function get_competency_category_manager_url()
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_MANAGE_CATEGORIES_COMPETENCY));
    }
    
 	function get_indicator_category_manager_url()
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_MANAGE_CATEGORIES_INDICATOR));
    }
    
 	function get_criteria_category_manager_url()
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_MANAGE_CATEGORIES_CRITERIA));
    }
    
    
    // Move function
    
 	function get_competency_moving_url($competency)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_MOVE_COMPETENCY, self :: PARAM_COMPETENCY => $competency->get_id()));
    }
    
 	function get_indicator_moving_url($indicator)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_MOVE_INDICATOR, self :: PARAM_INDICATOR => $indicator->get_id()));
    }
    
 	function get_criteria_moving_url($criteria)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_MOVE_CRITERIA, self :: PARAM_CRITERIA => $criteria->get_id()));
    }
	
	// Dummy Methods which are needed because we don't work with learning objects
	function content_object_is_published($object_id)
	{
	}

	function any_content_object_is_published($object_ids)
	{
	}

	function get_content_object_publication_attributes($object_id, $type = null, $offset = null, $count = null, $order_property = null)
	{
	}

	function get_content_object_publication_attribute($object_id)
	{

	}

	function count_publication_attributes($type = null, $condition = null)
	{

	}

	function delete_content_object_publications($object_id)
	{

	}

	function update_content_object_publication_id($publication_attr)
	{

	}

	function get_content_object_publication_locations($content_object)
	{

	}

	function publish_content_object($content_object, $location)
	{

	}
	
	
 	function display_header($breadcrumbtrail, $display_search = false, $display_menu = true)
    {
        if (is_null($breadcrumbtrail))
        {
            $breadcrumbtrail = new BreadcrumbTrail();
        }
        $trail = $breadcrumbtrail;        
        $categories = $this->breadcrumbs;
        $breadcrumbtrail = $trail;

        $title = $breadcrumbtrail->get_last()->get_name();
        $title_short = $title;
        if (strlen($title_short) > 53)
        {
            $title_short = substr($title_short, 0, 50) . '&hellip;';
        }
        Display :: header($breadcrumbtrail);

        
        if ($display_menu)
        {
        	/*echo '<div id="repository_tree_container" style="float: left; width: 12%;">';
            $this->display_content_object_categories();
            echo '</div>';*/

            
            
            echo '<div id="repository_tree_container" style="float: left; width: 12%;">';
            echo '<br /><a href="' . $this->get_browse_competency_url() . '">' . Translation :: get('Competency') . '</a><hr size="1" width="90%" align="left"/>';
			echo '<br /><a href="' . $this->get_browse_indicator_url() . '">' . Translation :: get('Indicator') . '</a><hr size="1" width="90%" align="left"/>';
			echo '<br /><a href="' . $this->get_browse_criteria_url() . '">' . Translation :: get('Criteria') . '</a><hr size="1" width="90%" align="left"/>';
            echo '<hr size="1" width="90%" align="left"/><br /><a href="' . $this->get_create_url() . '">' . Translation :: get('Create') . '</a>';
			echo '</div>';
            echo '<div style="float: right; width: 85%;">';
        }
        else
        {
            echo '<div>';
        }

        echo '<div>';
        echo '<h3 style="float: left;" title="' . $title . '">' . $title_short . '</h3>';
        
        echo '</div>';
        echo '<div class="clear">&nbsp;</div>';
        if ($msg = Request :: get(Application :: PARAM_MESSAGE))
        {
            $this->display_message($msg);
        }
        if ($msg = Request :: get(Application :: PARAM_ERROR_MESSAGE))
        {
            $this->display_error_message($msg);
        }
    }
    
 	private function display_content_object_categories()
    {
        echo $this->get_category_menu()->render_as_tree();
    }
    
    private function get_category_menu($force_search = false)
    {
        if (! isset($this->category_menu))
        {
            $temp_replacement = '__CATEGORY_ID__';
            $url_format = $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE_COMPETENCY, self :: PARAM_COMPETENCY => $temp_replacement));
            $url_format = str_replace($temp_replacement, '%s', $url_format);
            $category = $this->get_parameter(self :: PARAM_COMPETENCY);
            if (! isset($category))
            {
                $category = $this->get_root_category_id();
                $this->set_parameter(self :: PARAM_COMPETENCY, $category);
            }
            $extra_items = array();
            
            $create = array();
            $create['title'] = Translation :: get('Competency');
            $create['url'] = $this->get_browse_competency_url();
            $create['class'] = 'create';
            
            $line = array();
            $line['title'] = '';
            $line['class'] = 'divider';
            
            $create = array();
            $create['title'] = Translation :: get('Indicator');
            $create['url'] = $this->get_browse_indicator_url();
            $create['class'] = 'create';
            
            $line = array();
            $line['title'] = '';
            $line['class'] = 'divider';
            
            $create = array();
            $create['title'] = Translation :: get('Criteria');
            $create['url'] = $this->get_browse_criteria_url();
            $create['class'] = 'create';
            
            $line = array();
            $line['title'] = '';
            $line['class'] = 'divider';
            $line = array();
            $line['title'] = '';
            $line['class'] = 'divider';
            
            $create = array();
            $create['title'] = Translation :: get('Create');
            $create['url'] = $this->get_create_url();
            $create['class'] = 'create';



 

            $extra_items[] = $line;

            $extra_items[] = $create;

            $extra_items[] = $line;


        }
        return $this->category_menu;
    }
    
 	function get_root_category_id()
    {
		return 0;
    }
    
 	function render_as_tree()
    {
        $renderer = new TreeMenuRenderer();
        $this->render($renderer, 'sitemap');
        return $renderer->toHTML();
    }
    
}
?>