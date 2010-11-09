<?php
namespace reporting;

use common\libraries\Utilities;
use common\libraries\Path;
use common\libraries\Application;
use common\libraries\CoreApplication;
use common\libraries\WebApplication;
use common\libraries\BasicApplication;
use common\libraries\Redirect;
use common\libraries\Translation;
use common\libraries\Request;
use common\libraries\Theme;
use common\libraries\ToolbarItem;
use common\libraries\Toolbar;
use common\libraries\EqualityCondition;
use common\libraries\AndCondition;
use common\libraries\ActionBarRenderer;
use common\extensions\reporting_viewer\ReportingViewer;

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
        $base_path = (WebApplication :: is_application($application) ? WebApplication :: get_application_class_path($application) : CoreApplication :: get_application_class_path($application));
        $file = $base_path . 'reporting/templates/' . Utilities :: camelcase_to_underscores($registration->get_template()) . '.class.php';
        require_once ($file);
        $new_template = Application :: determine_namespace($registration->get_application()) . '\\' . Utilities :: underscores_to_camelcase($registration->get_template());
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
        return Utilities :: get_classname_from_object($this, true);
    }

    public function to_html()
    {
        $display_all = $this->get_parent()->are_all_blocks_visible();

        if ($display_all)
        {
            $html[] = $this->display_header();
            $html[] = $this->display_filter();
            foreach ($this->get_reporting_blocks() as $block)
            {
                $html[] = $block->to_html();
            }
            $html[] = $this->display_footer();
        }
        else
        {
            //            $html[] = $this->display_header();
            //            $html[] = $this->display_filter();
            $html[] = $this->render_block();
            //            $html[] = $this->display_footer();
        }
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
            $html[] = $this->get_menu();
            $html[] = '<div id="tool_browser_left" style="position: relative; float: right; width: 80%; margin-left: 0px;">';
        }

        $html[] = $this->display_header();
        $html[] = $this->display_filter();

        if ($this->get_current_block())
        {
            $html[] = $this->get_current_block()->to_html();
        }

        $html[] = $this->display_footer();

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
            if (count($keys))
            {
                return $this->get_reporting_block($keys[0]);
            }
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

        $html[] = '<script type="text/javascript" src="' . BasicApplication :: get_application_resources_javascript_path(ReportingManager :: APPLICATION_NAME) . 'reporting_charttype.js' . '"></script>';
        $html[] = '<script type="text/javascript" src="' . BasicApplication :: get_application_resources_javascript_path(ReportingManager :: APPLICATION_NAME) . 'reporting_template_ajax.js' . '"></script>';
        return implode("\n", $html);
    }

    public function display_header()
    {
        $html[] = '<br />' . $this->get_action_bar()->as_html() . '<br />';
        return implode("\n", $html);
    }

    public function display_filter()
    {
        $body = $this->display_filter_body();
        if ($body)
        {
            $html[] = $this->reporting_filter_header();
            $html[] = $body;
            $html[] = $this->reporting_filter_footer();
            return implode("\n", $html);
        }
    }

    public function display_filter_body()
    {

    }

    function reporting_filter_header()
    {
        $html = array();

        $html[] = '<div style="clear: both; height: 0px; line-height: 0px;">&nbsp;</div>';
        $html[] = '<div id="reporting_filter" class="reporting_filter">';
        $html[] = '<div class="bevel">';

        $html[] = '<div class="clear"></div>';
        return implode("\n", $html);
    }

    function reporting_filter_footer()
    {
        $html = array();

        $html[] = '<div class="clear"></div>';
        $html[] = '<div id="reporting_filter_hide_container" class="reporting_filter_hide_container">';
        $html[] = '<a id="reporting_filter_hide_link" class="reporting_filter_hide" href="#"><img src="' . Theme :: get_common_image_path() . 'action_ajax_hide.png" /></a>';
        $html[] = '</div>';
        $html[] = '</div>';
        $html[] = '</div>';

        $html[] = ResourceManager :: get_instance()->get_resource_html(BasicApplication :: get_application_resources_javascript_path(ReportingManager :: APPLICATION_NAME) . 'reporting_filter_horizontal.js');

        $html[] = '<div class="clear"></div>';

        return implode("\n", $html);
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
        $block->set_id(count($this->blocks));
        $this->blocks[] = $block;
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
        $parameters[ReportingViewer :: PARAM_REPORTING_VIEWER_ACTION] = ReportingViewer :: ACTION_EXPORT_TEMPLATE;
        $parameters[ReportingManager :: PARAM_TEMPLATE_ID] = $this->get_id();
        /*$parameters[ReportingManager :: PARAM_EXPORT_TYPE] = 'pdf';

        $display_mode = $this->get_displaymode();
        if (isset($display_mode))
        {
            $parameters[ReportingFormatterForm :: FORMATTER_TYPE] = $this->get_displaymode();
        }
        $url = Redirect :: get_url($parameters, array(), false);

        $action_bar->add_common_action(new ToolbarItem(Translation :: get('ExportToPdf'), Theme :: get_common_image_path() . 'export_pdf.png', $url));
*/
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