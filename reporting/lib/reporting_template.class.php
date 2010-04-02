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
    protected $action_bar;

    private $blocks = array();
    private $parent;
    private $parameters = array();

    function ReportingTemplate($parent)
    {
        $this->set_parent($parent);
        $this->parameters = array();
        $this->action_bar = $this->get_action_bar();
    }

    public static function factory($reporting_template_id, $parent)
    {
        $registration = ReportingDataManager :: get_instance()->retrieve_reporting_template_registration($reporting_template_id);
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
        $html[] = $this->display_header();
        $html[] = $this->get_menu();
        $html[] = $this->display_context();
        $html[] = $this->render_blocks();
        $html[] = $this->display_footer();
        return implode("\n", $html);
    }

    public function get_menu()
    {
        $html = array();
        $html[] = '<div style="float: left; width: 18%; overflow: auto; height: 500px;">';
        $menu = new ReportingTemplateMenu($this);
        $html[] = $menu->render_as_tree();
        $html[] = '</div>';
        return implode("\n", $html);
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

    public function render_blocks()
    {
        $html = array();
        $html[] = '<div style="float: right; width: 80%;">';

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
        $html[] = '</div>';
        $html[] = '<div class="clear"></div>';
        return implode($html, "\n");
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
        $html[] = '<br />' . $this->action_bar->as_html() . '<br />';
        return implode("\n", $html);
    }

    public function get_parent()
    {
        return $this->parent;
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
        return $this->parameters;
    }

    public function set_parameters($parameters)
    {
    	$this->parameters = $parameters;
    }

    public function set_parameter($key, $value)
    {
        $this->parameters[$key] = $value;
    }

    public function add_parameters($key, $value)
    {
    	$this->parameters[$key] = $value;
    }

    public abstract function display_context();

    function get_action_bar()
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);
        $parameters = array();

        $parameters[Application :: PARAM_ACTION] = ReportingManager :: ACTION_EXPORT;
        $parameters[ReportingManager :: PARAM_TEMPLATE_ID] = $this->get_id();
        $parameters[ReportingManager :: PARAM_EXPORT_TYPE] = 'pdf';
        $display_mode = $this->get_displaymode();
        if (isset($display_mode))
        {
            $parameters[ReportingFormatterForm :: FORMATTER_TYPE] = $this->get_displaymode();
        }
        $url = Redirect :: get_link(ReportingManager :: APPLICATION_NAME, $parameters, array(), false, Redirect :: TYPE_CORE);

        $action_bar->add_common_action(new ToolbarItem(Translation :: get('ExportToPdf'), Theme :: get_common_image_path() . 'export_pdf.png', $url));
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

    /*
     * Reporting blocks
     */

    /**
     * Sets the visible value to 1 for this reporting block & 0 for the rest
     * @param String $name
     */
    /*function show_reporting_block($name)
    {
        foreach ($this->blocks as $key => $value)
        {
            if ($value[0]->get_name() == $name)
            {
                $value[1][self :: PARAM_VISIBLE] = self :: REPORTING_BLOCK_VISIBLE;
            }
            else
            {
                $value[1][self :: PARAM_VISIBLE] = self :: REPORTING_BLOCK_INVISIBLE;
            }
            $this->blocks[$key] = $value;
        }
    }*/

    //abstract function to_html();


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

/**
 * Generates all the visible reporting blocks
 * @return html
 */
/*function get_visible_reporting_blocks($export = false)
    {
        foreach ($this->get_reporting_blocks() as $key => $value)
        {
            // check if reporting block is visible
            if ($value[1][self :: PARAM_VISIBLE] == self :: REPORTING_BLOCK_VISIBLE)
            {
                if ($export)
                    $html[] = Reporting :: generate_block_export($value[0], $this->get_reporting_block_template_properties($value[0]->get_name()));
                else
                    $html[] = Reporting :: generate_block($value[0], $this->get_reporting_block_template_properties($value[0]->get_name()));
                $html[] = '<div class="clear">&nbsp;</div>';
            }
        }
        return implode("\n", $html);
    }*/

/*function get_reporting_block_html($name)
    {
        $array = $this->get_reporting_blocks();
        foreach ($array as $key => $value)
        {
            if ($value[0]->get_name() == $name)
            {
                return Reporting :: generate_block($value[0], $this->get_reporting_block_template_properties($name));
            }
        }
    }*/

/*function set_reporting_block_template_properties($name, $params)
    {
        $array = $this->get_reporting_blocks();
        foreach ($array as $key => $value)
        {
            if ($value[0]->get_name() == $name)
            {
                $value[1] = $params;
            }
        }
    }*/

/*function get_reporting_block_template_properties($name)
    {
        $array = $this->get_reporting_blocks();
        foreach ($array as $key => $value)
        {
            if ($value[0]->get_name() == $name)
            {
                return $value[1];
            }
        }
    }*/

/**
 * Returns all reporting blocks for this reporting template
 * @return an array of reporting blocks
 */
/*function retrieve_reporting_blocks()
    {
        return $this->blocks;
    }*/

/*function set_reporting_blocks_function_parameters($params)
    {
        $this->params = $params;
        foreach ($this->get_reporting_blocks() as $key => $value)
        {
            foreach ($params as $key2 => $value2)
            {
                $value[0]->add_function_parameter($key2, $value2);
            }
        }
    }


    function set_reporting_block_function_parameters($blockname, $params)
    {
        foreach ($this->get_reporting_blocks() as $key => $value)
        {
            if ($value[0]->get_name() == $blockname)
            {
                $value[0]->set_function_parameters($params);
            }
        }
    }*/
}
?>
