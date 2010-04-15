<?php
/**
 * $Id: reporting_block.class.php 215 2009-11-13 14:07:59Z vanpouckesven $
 * @package reporting.lib
 * @author Michael Kyndt
 */


//class ReportingBlock extends DataClass

require_once dirname(__FILE__) . '/forms/reporting_formatter_form.class.php';
require_once PATH::get_reporting_path() . '/lib/reporting_data.class.php';

abstract class ReportingBlock
{
	const CLASS_NAME = __CLASS__;
    const PARAM_DISPLAY_MODE = "display_mode";

    private $data, $params, $parent;

	function ReportingBlock($parent)
	{
		$this->parent = $parent;
	}

	public function get_parent()
	{
		return $this->parent;
	}

	public function set_parent($parent)
	{
		$this->parent = $parent;
	}

	public abstract function count_data();

	public abstract function retrieve_data();

	public abstract function get_data_manager();

	public function display_footer()
	{
	    $html = array();
        $html[] = '<div class="reporting_footer">';
        $html[] = '<div class="reporting_footer_export">';
        $html[] = $this->get_export_links();
        $html[] = '</div>&nbsp;<div class="clear">&nbsp;</div>';
        $html[] = '</div>';
        $html[] = '</div>';

        return implode("\n", $html);
	}

	public function display_header()
	{
        $parameters = $this->parent->get_parameters();
        $bloc_parameters = array_merge($parameters, array(ReportingManager::PARAM_REPORTING_BLOCK_ID=>$this->get_id()));
		$form = new ReportingFormatterForm($this, $this->get_parent()->get_parent()->get_url($bloc_parameters));

	    $html = array();
		$html[] = '<div id="' . $this->get_id() . '" class="reporting_block">';
        $html[] = '<div class="reporting_header">';
        $html[] = '<div class="reporting_header_title">' . Translation::get(get_class($this)) . '</div>';
        $html[] = '<div class="reporting_header_displaymode">';
        if (count($this->get_available_displaymodes()) > 1)
        {
        	$html[] = $form->toHtml();
        }
        $html[] = '</div>';
        $html[] = '<div class="clear">&nbsp;</div>';
        $html[] = '</div>';

        return implode("\n", $html);
	}

	public function render_block()
	{
	    $formatter = ReportingFormatter :: factory($this);

	    $html = array();
	    $html[] = '<div class="reporting_content">';
	    $html[] = $formatter->to_html();
        $html[] = '<div class="clear">&nbsp;</div>';
        $html[] = '</div>';

		return implode("\n", $html);
	}

	public function to_html()
	{
		$html[] = $this->display_header();
		$html[] = $this->render_block();
		$html[] = $this->display_footer();
		return implode("\n", $html);
	}

	function get_id()
    {
        $conditions = array();
        $conditions[] = new EqualityCondition(ReportingBlockRegistration::PROPERTY_APPLICATION, $this->get_application());
        $conditions[] = new EqualityCondition(ReportingBlockRegistration::PROPERTY_BLOCK, $this->get_name());
        $condition = new AndCondition($conditions);
		$registrations = ReportingDataManager::get_instance()->retrieve_reporting_block_registrations($condition);
		if($registrations->size() == 1)
        {
        	return $registrations->next_result()->get_id();
        }
		else
		{
			throw new Exception(Translation :: get('RegistrationCannotBeNull'));;
		}
    }

	public function export()
	{
		$html[] = '<b>' . Utilities::underscores_to_camelcase_with_spaces($this->get_name()) . '</b><br />';
		$html[] = $this->render_block();
		$html[] = '<br />';
		return implode("\n", $html);
	}

    static function factory($registration)
    {
        $type = $registration->get_block();
        $base_path = (WebApplication :: is_application($application) ? Path :: get_application_path() . 'lib/' : Path :: get(SYS_PATH));
        $file = $base_path . $application . '/reporting/blocks/' . $type . '.class.php';
        require_once ($file);
        $class = Utilities::underscores_to_camelcase($type) . 'ReportingBlock';

        return new $class($registration);
    }

    abstract function get_application();

    function get_name()
    {
    	return Utilities::camelcase_to_underscores(get_class($this));
    }

    /**
     * Get the default properties
     * @return array The property names.
     */
    /*static function get_default_property_names()
    {
        return parent :: get_default_property_names(array(self :: PROPERTY_NAME, self :: PROPERTY_APPLICATION, self :: PROPERTY_FUNCTION, self :: PROPERTY_DISPLAYMODE, self :: PROPERTY_WIDTH, self :: PROPERTY_HEIGHT, self :: PROPERTY_EXCLUDE_DISPLAYMODES, self :: PROPERTY_SORTABLE));
    }*/

    /**
     * Returns all available displaymodes
     */
    public function get_displaymodes()
    {
		return $this->get_available_displaymodes();
    	/*$data = $this->get_data();
        $datadescription = $data[1];
        $chartdata = $data[0];
        $names = sizeof($chartdata);
        $series = sizeof($datadescription["Values"]);

        $modes = $this->get_available_displaymodes();
        $excluded = $this->get_excluded_displaymodes();
        $excluded = explode(',', $excluded);

        if ($series == 1 && $names <= 1)
        {
            $excluded[] = 'Chart:Bar';
            $excluded[] = 'Chart:Line';
            $excluded[] = 'Chart:FilledCubic';
        }
        if ($series > 1 || $names > 5)
        {
            $excluded[] = 'Chart:Pie';
        }

        if (in_array('Charts', $excluded))
        {
            unset($excluded[array_search('Charts', $excluded)]);
            $excluded[] = 'Chart:Pie';
            $excluded[] = 'Chart:Bar';
            $excluded[] = 'Chart:Line';
            $excluded[] = 'Chart:FilledCubic';
        }
        foreach ($excluded as $key => $value)
        {
            $excluded[$value] = Translation :: get($value);
            unset($excluded[$key]);
        }
        $diff = array_diff($modes, $excluded);
        return $diff;*/
    }

    /**
     *
     * @return array modes
     * @todo build modes dynamically
     */
    abstract function get_available_displaymodes();/*{
    	$modes = array();
        $modes["Text"] = Translation :: get('Text');
        $modes["Table"] = Translation :: get('Table');
        $modes["Chart:Pie"] = Translation :: get('Chart:Pie');
        $modes["Chart:Bar"] = Translation :: get('Chart:Bar');
        $modes["Chart:Line"] = Translation :: get('Chart:Line');
        $modes["Chart:FilledCubic"] = Translation :: get('Chart:FilledCubic');
        return $modes;
    }*/

    public function get_export_links()
    {
        $list = Export :: get_supported_filetypes(array('ical'));
        $download_bar_items = array();
        $save_bar_items = array();

        foreach ($list as $export_format)
        {
            $parameters = $this->get_parent()->get_parameters();
            $parameters [ReportingManager :: PARAM_REPORTING_BLOCK_ID] = $this->get_id();
            $parameters [ReportingManager:: PARAM_TEMPLATE_ID] = $this->get_parent()->get_id();
            $parameters [ReportingManager :: PARAM_EXPORT_TYPE] = $export_format;
            $parameters [ReportingFormatterForm::FORMATTER_TYPE] = $this->get_displaymode();
            $parameters [ReportingViewer::PARAM_REPORTING_VIEWER_ACTION] = ReportingViewer::ACTION_SAVE_TEMPLATE;
            
            $link = Redirect::get_url($parameters, array(), false);
            $export_format_name = Translation :: get(Utilities :: underscores_to_camelcase($export_format));
            $save_bar_items[] = new ToolbarItem($export_format_name, Theme :: get_common_image_path() . 'export_' . $export_format . '.png', $link, ToolbarItem :: DISPLAY_ICON);

            $parameters [ReportingViewer::PARAM_REPORTING_VIEWER_ACTION] = ReportingViewer::ACTION_EXPORT_TEMPLATE;
            
            $link = Redirect::get_url($parameters, array(), false);
            $export_format_name = Translation :: get(Utilities :: underscores_to_camelcase($export_format));
            $download_bar_items[] = new ToolbarItem($export_format_name, Theme :: get_common_image_path() . 'export_' . $export_format . '.png', $link, ToolbarItem :: DISPLAY_ICON);
            
            
        }

        $download_bar = new Toolbar();
        $download_bar->set_items($download_bar_items);
        $download_bar->set_type(Toolbar :: TYPE_HORIZONTAL);
        $save_bar = new Toolbar();
        $save_bar->set_items($save_bar_items);
        $save_bar->set_type(Toolbar :: TYPE_HORIZONTAL);
        
        $html = array();
        $html[] = '<div style="float:left;">' . Translation::get('Download') . ' : ';
        $html[] = $download_bar->as_html() . '</div>';
        $html[] = '<div style="float:left;">&nbsp;|&nbsp;';
        $html[] = Translation::get('Save') . ' : ';
        $html[] = $save_bar->as_html() . '</div>';
        
        return implode("\n", $html);
    }

    /**
     * Getters and setters
     */

    public function get_data()
    {
        if (! $this->data)
        {
            $this->retrieve_data();
        }
        return $this->data;
    }

    public function add_function_parameter($key, $value)
    {
        $this->params[$key] = $value;
    }

    public function remove_function_parameter($key)
    {
        unset($this->params[$key]);
    }

    public function set_function_parameters($params)
    {
        $this->params = $params;
    }

    public function get_function_parameters()
    {
        return $this->params;
    }

    function get_name_translation()
    {
    	return Translation :: get(Utilities::underscores_to_camelcase($this->get_name()));
    }
    
    public function get_displaymode()
    {
    	$display = Request::post(ReportingFormatterForm::FORMATTER_TYPE);
    	$display_get = Request::get(ReportingFormatterForm::FORMATTER_TYPE);
    	$display_mode = $this->get_displaymodes();
        if (isset($display))
        {
        	if (array_key_exists($display, $display_mode))
        	{
        		return $display;
        	}
        	else {
        		$array_keys = array_keys($display_mode);
        		return $array_keys[0];
        	}
        }
        elseif (isset($display_get))
        {
        	if (array_key_exists($display_get, $display_mode))
        	{
        		return $display_get;
        	}
        	else {
        		$array_keys = array_keys($display_mode);
        		return $array_keys[0];
        	}
        }
        else
        {
        	$array_keys = array_keys($display_mode);
        	return $array_keys[0];
        }
    }

    public function is_sortable()
    {
        return false;
    }

    static function get_table_name()
    {
        return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
    }
}
?>