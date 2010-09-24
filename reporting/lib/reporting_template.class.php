<?php
/**
 * $Id: reporting_template.class.php 215 2009-11-13 14:07:59Z vanpouckesven $
 * Extendable class for the reporting templates
 * This contains the general shared template properties such as
 * Properties (name, description, etc)
 * Layout (header,menu, footer)
 * @package reporting.lib
 * @author Michael Kyndt
 */
require_once dirname(__FILE__) . '/reporting_template_menu.class.php';

abstract class ReportingTemplate
{   
    private $blocks = array();
    private $parent;

    function ReportingTemplate($parent)
    {
        $this->set_parent($parent);
    }

    public static function factory($registration, $parent)
    {
    	$application = $registration->get_application();
        $base_path = (WebApplication :: is_application($application) ? Path :: get_application_path() . 'lib/' : Path :: get(SYS_PATH));
        $file = $base_path . $application . '/reporting/templates/' . Utilities :: camelcase_to_underscores($registration->get_template()) . '.class.php';
        require_once ($file);
        $new_template = Utilities :: underscores_to_camelcase($registration->get_template());
        return new $new_template($parent);
    }

    function get_id()
    {
        $conditions = array();
        $conditions[] = new EqualityCondition(ReportingTemplateRegistration :: PROPERTY_APPLICATION, $this->get_application());
        $conditions[] = new EqualityCondition(ReportingTemplateRegistration :: PROPERTY_TEMPLATE, $this->get_name());
        $condition = new AndCondition($conditions);
        $registrations = ReportingDataManager :: get_instance()->retrieve_reporting_template_registrations($condition);
        if ($registrations->size() == 1)
        {
            return $registrations->next_result()->get_id();
        }
        else
        {
            return 0;
        }
    }

    abstract function get_application();

    function get_name()
    {
        return Utilities :: camelcase_to_underscores(get_class($this));
    }

    public function to_html()
    {
        $display_all = $this->get_parent()->are_all_blocks_visible();
        
    	$html[] = $this->display_header();
		$html[] = $this->display_filter();
		
        if ($display_all)
        {
            foreach($this->get_reporting_blocks() as $block)
            {
            	$html[] = $block->to_html();
            }
        }
        else
        {
        	if ($this->get_number_of_reporting_blocks() > 1)
            {
            	$html[] = $this->get_menu();
            	$html[] = '<div id="tool_browser_left">';
            }
            $block = Request :: get(ReportingManager :: PARAM_REPORTING_BLOCK_ID);
            if (isset($block))
            {
                $html[] = $this->get_reporting_block($block)->to_html();
            }
            else
            {
            	$keys = array_keys($this->get_reporting_blocks());
                $html[] = $this->get_reporting_block($keys[0])->to_html();
            }
            if ($this->get_number_of_reporting_blocks() > 1)
            {
                $html[] = '</div>';
            }
            $html[] = '<div class="clear"></div>';
            return implode($html, "\n");
            $html[] = $this->render_block();
        }
        $html[] = $this->display_footer();
        return implode("\n", $html);
    }

    public function get_menu()
    {
        $html = array();
        if ($this->get_number_of_reporting_blocks() > 1)
        {
            //$html[] = '<div style="float: left; width: 18%; overflow: auto; height: 500px;">'; 
            

            $menu = new ReportingTemplateMenu($this);
            $html[] = $menu->as_html();
        }
        return implode("\n", $html);
    }

    public function get_number_of_reporting_blocks()
    {
        return count($this->get_reporting_blocks());
    }

    public function render_all_blocks()
    {
        $blocks = $this->get_reporting_blocks();
        $html = array();
        foreach ($blocks as $block)
        {
            $html[] = $block->export();
        }
        return implode($html, "\n");
    }

    public function render_block($id)
    {
        $html = array();
        if ($this->get_number_of_reporting_blocks() > 1)
        {
            $html[] = '<div id="tool_browser_left">';
        }
       
        $html[] = $this->get_current_block()->to_html();
        if ($this->get_number_of_reporting_blocks() > 1)
        {
            $html[] = '</div>';
        }
        $html[] = '<div class="clear"></div>';
        return implode($html, "\n");
    }
    
    public function get_current_block()
    {
    	$block = Request :: get(ReportingManager :: PARAM_REPORTING_BLOCK_ID);
        if (isset($block))
        {
            return $this->get_reporting_block($block);
        }
        else
        {
            $keys = array_keys($this->get_reporting_blocks());
            return $this->get_reporting_block($keys[0]);
        }
    }

    public function export()
    {
        $block = Request :: get(ReportingManager :: PARAM_REPORTING_BLOCK_ID);
        if (isset($block))
        {
            $html[] = $this->get_reporting_block($block)->export();
        }
        else
        {
            $html[] = $this->display_context();
            $html[] = $this->render_all_blocks();
        }
        return implode("\n", $html);
    }

    public function display_footer()
    {
        $parameters = array();
        $parameters[ReportingManager :: PARAM_TEMPLATE_FUNCTION_PARAMETERS] = Request :: get(ReportingManager :: PARAM_TEMPLATE_FUNCTION_PARAMETERS);
        
        $html[] = '<script type="text/javascript" src="' . Path :: get(WEB_LIB_PATH) . 'javascript/reporting_charttype.js' . '"></script>';
        $html[] = '<script type="text/javascript" src="' . Path :: get(WEB_LIB_PATH) . 'javascript/reporting_template_ajax.js' . '"></script>';
        return implode("\n", $html);
    }

    public function display_header()
    {
        $html[] = '<br />' . $this->get_action_bar()->as_html() . '<br />';
        return implode("\n", $html);
    }
    
    public function display_filter()
    {
    	
    }

    public function get_parent()
    {
        return $this->parent;
    }

	function get_url($parameters = array (), $filter = array(), $encode_entities = false)
    {
        return $this->get_parent()->get_url($parameters, $filter, $encode_entities);
    }
    
	public function set_parent($parent)
    {
        $this->parent = $parent;
    }

    public function get_reporting_blocks()
    {
        return $this->blocks;
    }

    public function get_reporting_block($reporting_block_id)
    {
        return $this->blocks[$reporting_block_id];
    }

    public function add_reporting_block($block)
    {
        $this->blocks[$block->get_id()] = $block;
    }

    public function get_parameters()
    {
        return $this->get_parent()->get_parameters();
    }
    
    public function get_parameter($key)
    {
    	return $this->get_parent()->get_parameter($key);
    }

    public function set_parameters($parameters)
    {
        $this->get_parent()->set_parameters($parameters);
    }

    public function set_parameter($key, $value)
    {
        $this->get_parent()->set_parameter($key, $value);
    }

//    public function add_parameters($key, $value)
//    {
//        $this->parameters[$key] = $value;
//    }

    public abstract function display_context();

function get_action_bar()
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);
        $parameters = $this->get_parameters();
        $parameters[ReportingViewer::PARAM_REPORTING_VIEWER_ACTION] = ReportingViewer::ACTION_EXPORT_TEMPLATE;
        $parameters[ReportingManager :: PARAM_TEMPLATE_ID] = $this->get_id();
        $parameters[ReportingManager :: PARAM_EXPORT_TYPE] = 'pdf';
      	
        $display_mode = $this->get_displaymode();
        if (isset($display_mode))
        {
            $parameters[ReportingFormatterForm :: FORMATTER_TYPE] = $this->get_displaymode();
        }
        $url = Redirect :: get_url($parameters, array(), false);
        
        $action_bar->add_common_action(new ToolbarItem(Translation :: get('ExportToPdf'), Theme :: get_common_image_path() . 'export_pdf.png', $url));
        
        $parameters[ReportingManager :: PARAM_EXPORT_TYPE] = 'excel';
      	
        $display_mode = $this->get_displaymode();
        if (isset($display_mode))
        {
            $parameters[ReportingFormatterForm :: FORMATTER_TYPE] = $this->get_displaymode();
        }
        $url = Redirect :: get_url($parameters, array(), false);
        $action_bar->add_common_action(new ToolbarItem(Translation :: get('ExportToExcel'), Theme :: get_common_image_path() . 'export_excel.png', $url));
        //$action_bar->add_common_action(new ToolbarItem(Translation :: get('ExportToXml'), null, $url));
        //$action_bar->add_common_action(new ToolbarItem(Translation :: get('ExportToCsv'), null, $url));
        

        return $action_bar;
    }

    public function get_displaymode()
    {
        $display = Request :: post(ReportingFormatterForm :: FORMATTER_TYPE);
        $display_get = Request :: get(ReportingFormatterForm :: FORMATTER_TYPE);
        if (isset($display))
        {
            return $display;
        }
        elseif (isset($display_get))
        {
            return $display_get;
        }
    }

    function to_html_export()
    {
        $html[] = '<div class="template-data">';
        $html[] = '<br /><br /><br />';
        $html[] = '<b><u>Template data</u></b><br />';
        $html[] = '<b>Template title: </b><i>' . Translation :: get($properties[ReportingTemplateRegistration :: PROPERTY_TITLE]) . '</i><br />';
        $html[] = '<b>Template description: </b><i>' . Translation :: get($properties[ReportingTemplateRegistration :: PROPERTY_DESCRIPTION]) . '</i><br />';
        if (isset($this->params['course_id']))
            $html[] = '<b>Course: </b><i>' . $this->params['course_id'] . '</i>';
        $html[] = '</div><br /><br />';
        
        $html[] = $this->get_visible_reporting_blocks(true);
        return implode("\n", $html);
    }

    function is_platform()
    {
        return false;
    }
}
?>