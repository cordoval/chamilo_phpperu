<?php
class StreamingMediaManager extends SubManager
{
	const PARAM_STREAMING_MEDIA_MANAGER_ACTION = 'streaming_action';
	const ACTION_VIEW_TEMPLATE = 'view';
	const ACTION_EXPORT_TEMPLATE = 'export';
	const ACTION_CREATE_TEMPLATE = 'create';
	const ACTION_IMPORT_TEMPLATE = 'import';
	const ACTION_BROWSE_TEMPLATE = 'browse';
	const ACTION_DOWNLOAD_TEMPALTE = 'download';
	
//	private $template;
//	private $trail;
//	private $export;
//	private $show_all_blocks;
	
	function StreamingMediaManager($parent)
	{
		parent :: __construct($parent);
		
		$streaming_media_manager_action = Request :: get(self :: PARAM_STREAMING_MEDIA_MANAGER_ACTION);
        if ($streaming_media_manager_action)
        {
            $this->set_parameter(self :: PARAM_STREAMING_MEDIA_MANAGER_ACTION, $streaming_media_manager_action);
        }
	}
	
	function factory($type, $parent)
	{
		$file = dirname(__FILE__) . '/type/' . $type . '/' . $type . '_streaming_media_manager.class.php';
    	if(!file_exists($file))
    	{
    		throw new Exception(Translation :: get('StreamingMediaManagerTypeDoesNotExist', array('type' => $type)));
    	}
    	
    	require_once $file;
    	
    	$class = Utilities :: underscores_to_camelcase($type) . 'StreamingMediaManager';
    	
    	return new $class($parent);
	}
	
	function is_ready_to_be_used()
    {
//        $action = $this->get_parameter(self :: PARAM_STREAMING_MEDIA_MANAGER_ACTION);
//
//        return self :: any_object_selected() && ($action == self :: ACTION_PUBLISHER);
		return false;
    }
    
	function any_object_selected()
    {
        //$object = Request :: get(self :: PARAM_ID);
        //return isset($object);
    }
	
//	function show_all_blocks()
//	{
//		$this->show_all_blocks = true;
//	}
//	
//	function hide_all_blocks()
//	{
//		$this->show_all_blocks = false;
//	}
//	
//	function are_all_blocks_visible()
//	{
//		return $this->show_all_blocks;
//	}
	
//	function add_template_by_id($template_id)
//	{
//		$registration = ReportingDataManager :: get_instance()->retrieve_reporting_template_registration($template_id);
//		$this->set_template($registration);
//	}
//	
//	function add_template_by_name($template, $application)
//	{
//		$condition [] = new EqualityCondition(ReportingTemplateRegistration::PROPERTY_APPLICATION, $application);
//		$condition [] = new EqualityCondition(ReportingTemplateRegistration::PROPERTY_TEMPLATE, $template);
//		$conditions = new AndCondition($condition);
//		$registration = ReportingDataManager :: get_instance()->retrieve_reporting_template_registration_by_condition($conditions);
//		$this->set_template($registration);
//	}
//	
//	function add_template_by_registration($registration)
//	{
//		$this->set_template($registration);
//	}
//	
//	function add_export($export)
//	{
//		$this->set_export($export);
//	}
//	
//	function set_export($export)
//	{
//		$this->export = $export;
//	}
//	
//	function get_export()
//	{
//		return $this->export;	
//	}
//	
//	function get_template()
//	{
//		return $this->template;
//	}
//	
//	function set_template($template)
//	{
//		$this->template = $template;
//		$this->set_parameter(ReportingManager::PARAM_TEMPLATE_ID, $template->get_id());
//	}
//	
//	function set_breadcrumb_trail($trail)
//	{
//		$this->trail = $trail;
//	}
//	
//	function get_breadcrumb_trail()
//	{
//		if (isset($this->trail))
//		{
//			return $this->trail;
//		}
//		else
//		{
//			return new BreadcrumbTrail();
//		}
//	}
	
	function run()
	{
		$parent = $this->get_parameter(self :: PARAM_STREAMING_MEDIA_MANAGER_ACTION);
        
        switch ($parent)
        {
            case self :: ACTION_VIEW_TEMPLATE :
                $component = $this->create_component('Viewer');
                break;
            case self :: ACTION_EXPORT_TEMPLATE :
                $component = $this->create_component('Exporter');
                break;
  			case self :: ACTION_CREATE_TEMPLATE :
  				$component = $this->create_component('Creator');
  				break;
  			case self :: ACTION_IMPORT_TEMPLATE :
  				$component = $this->create_component('Importer');
  				break;
  			case self :: ACTION_BROWSE_TEMPLATE :
  				$component = $this->create_component('Browser');
  				break;   
  			case self :: ACTION_DOWNLOAD_TEMPALTE :
  				$component = $this->create_component('Downloader');
  				break; 
            default :
                $component = $this->create_component('Viewer');
                $this->set_parameter(self :: PARAM_STREAMING_MEDIA_MANAGER_ACTION, self :: ACTION_VIEW_TEMPLATE);
                break;
        }
        
        $component->run();
	}
	
	function get_application_component_path()
	{
		return Path :: get_application_library_path() . 'streaming_media_manager/component/';
	}
}
?>