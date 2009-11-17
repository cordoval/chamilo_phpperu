<?php
/**
 * $Id: reporting_block.class.php 215 2009-11-13 14:07:59Z vanpouckesven $
 * @package reporting.lib
 * @author Michael Kyndt
 */


class ReportingBlock extends DataClass
{
    const CLASS_NAME = __CLASS__;
    
    const PROPERTY_NAME = 'name';
    const PROPERTY_APPLICATION = 'application';
    const PROPERTY_FUNCTION = 'function';
    const PROPERTY_DISPLAYMODE = 'displaymode';
    const PROPERTY_EXCLUDE_DISPLAYMODES = 'exclude_displaymodes';
    const PROPERTY_WIDTH = 'width';
    const PROPERTY_HEIGHT = 'height';
    const PROPERTY_SORTABLE = 'sortalbe';
    
    private $data, $params;

    /**
     * Get the default properties
     * @return array The property names.
     */
    static function get_default_property_names()
    {
        return parent :: get_default_property_names(array(self :: PROPERTY_NAME, self :: PROPERTY_APPLICATION, self :: PROPERTY_FUNCTION, self :: PROPERTY_DISPLAYMODE, self :: PROPERTY_WIDTH, self :: PROPERTY_HEIGHT, self :: PROPERTY_EXCLUDE_DISPLAYMODES, self :: PROPERTY_SORTABLE));
    }

    /**
     * inherited
     */
    function get_data_manager()
    {
        return ReportingDataManager :: get_instance();
    }

    /**
     * Retrieves the data for this block
     */
    private function retrieve_data()
    {
        //require_once($this->get_applicationUrl());
        $base_path = (WebApplication :: is_application($this->get_application()) ? Path :: get_application_path() . 'lib/' : Path :: get(SYS_PATH));
        
        $file = $base_path . $this->get_application() . '/reporting/reporting_' . $this->get_application() . '.class.php';
        require_once $file;
        $this->data = call_user_func('Reporting' . $this->get_application() . '::' . $this->get_function(), $this->get_function_parameters());
    }

    /**
     * Returns all available displaymodes
     */
    //    public function get_displaymodes()
    //    {
    //        $data = $this->get_data();
    //        $datadescription = $data[1];
    //        $chartdata = $data[0];
    //        $names = sizeof($chartdata);
    //        $series = sizeof($datadescription["Values"]);
    //
    //        $modes = array();
    //        $modes["Text"] = Translation :: get('Text');
    //        $modes["Table"] = Translation :: get('Table');
    //        if($series == 1)
    //        {
    //            $modes["Chart:Pie"] = Translation :: get('Pie');
    //            if($names > 2)
    //            {
    //                $modes["Chart:Bar"] = Translation :: get('Bar');
    //                $modes["Chart:Line"] = Translation :: get('Line');
    //                $modes["Chart:FilledCubic"] = Translation :: get('FilledCubic');
    //            }
    //        }else
    //        {
    //            $modes["Chart:Bar"] = Translation :: get('Bar');
    //            $modes["Chart:Line"] = Translation :: get('Line');
    //            $modes["Chart:FilledCubic"] = Translation :: get('FilledCubic');
    //        }
    //
    //        return $modes;
    //    }
    

    public function get_displaymodes()
    {
        $data = $this->get_data();
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
        return $diff;
    }

    /**
     *
     * @return array modes
     * @todo build modes dynamically
     */
    private function get_available_displaymodes()
    {
        $modes = array();
        $modes["Text"] = Translation :: get('Text');
        $modes["Table"] = Translation :: get('Table');
        $modes["Chart:Pie"] = Translation :: get('Chart:Pie');
        $modes["Chart:Bar"] = Translation :: get('Chart:Bar');
        $modes["Chart:Line"] = Translation :: get('Chart:Line');
        $modes["Chart:FilledCubic"] = Translation :: get('Chart:FilledCubic');
        return $modes;
    }

    public function get_export_links()
    {
        $list = Export :: get_supported_filetypes(array('ical'));
        
        $array = array();
        
        foreach ($list as $export_format)
        {
            $arr = array();
            $file = Theme :: get_common_image_path() . 'export_' . $export_format . '.png';
            $sys_file = Theme :: get_instance()->get_path(SYS_IMG_PATH) . 'common/export_' . $export_format . '.png';
            $parameters = array();
            $parameters[ReportingManager :: PARAM_TEMPLATE_FUNCTION_PARAMETERS] = Request :: get(ReportingManager :: PARAM_TEMPLATE_FUNCTION_PARAMETERS) ? Request :: get(ReportingManager :: PARAM_TEMPLATE_FUNCTION_PARAMETERS) : $_SESSION[ReportingManager :: PARAM_TEMPLATE_FUNCTION_PARAMETERS];
            if (! file_exists($sys_file))
                $file = Theme :: get_common_image_path() . 'export_unknown.png';
            $arr[] = '<a href="index_reporting.php?' . Application :: PARAM_ACTION . '=' . ReportingManager :: ACTION_EXPORT . '&' . ReportingManager :: PARAM_REPORTING_BLOCK_ID . '=' . $this->get_id() . '&' . ReportingManager :: PARAM_EXPORT_TYPE . '=' . $export_format . '&' . http_build_query($parameters) . '" />';
            //$arr[] = '<img src="'.$file.'" border="0" title="'.$export_format.'" alt="'.$export_format.'" width="12" height="12" />';
            $arr[] = $export_format;
            $arr[] = '</a>';
            
            $array[] = implode("\n", $arr);
        }
        
        $return = Translation :: get('Export') . ': ';
        $return .= implode('|', $array);
        
        return $return;
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

    public function get_name()
    {
        return $this->get_default_property(self :: PROPERTY_NAME);
    }

    public function set_name($value)
    {
        $this->set_default_property(self :: PROPERTY_NAME, $value);
    }

    public function get_application()
    {
        return $this->get_default_property(self :: PROPERTY_APPLICATION);
    }

    public function set_application($value)
    {
        $this->set_default_property(self :: PROPERTY_APPLICATION, $value);
    }

    public function get_function()
    {
        return $this->get_default_property(self :: PROPERTY_FUNCTION);
    }

    public function set_function($value)
    {
        $this->set_default_property(self :: PROPERTY_FUNCTION, $value);
    }

    public function get_displaymode()
    {
        return $this->get_default_property(self :: PROPERTY_DISPLAYMODE);
    }

    public function set_displaymode($value)
    {
        $this->set_default_property(self :: PROPERTY_DISPLAYMODE, $value);
    }

    public function get_excluded_displaymodes()
    {
        return $this->get_default_property(self :: PROPERTY_EXCLUDE_DISPLAYMODES);
    }

    public function set_excluded_displaymodes($value)
    {
        $this->set_default_property(self :: PROPERTY_EXCLUDE_DISPLAYMODES, $value);
    }

    public function get_width()
    {
        return $this->get_default_property(self :: PROPERTY_WIDTH);
    }

    public function set_width($value)
    {
        $this->set_default_property(self :: PROPERTY_WIDTH, $value);
    }

    public function get_height()
    {
        return $this->get_default_property(self :: PROPERTY_HEIGHT);
    }

    public function set_height($value)
    {
        $this->set_default_property(self :: PROPERTY_HEIGHT, $value);
    }

    public function get_sortable()
    {
        return $this->get_default_property(self :: PROPERTY_SORTABLE);
    }

    public function set_sortable($value)
    {
        $this->set_default_property(self :: PROPERTY_SORTABLE, $value);
    }

    public function is_sortable()
    {
        return $this->get_default_property(self :: PROPERTY_SORTABLE) == 1;
    }

    static function get_table_name()
    {
        return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
    }
}
?>
