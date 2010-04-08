<?php
/**
 * $Id: reporting_exporter.class.php 215 2009-11-13 14:07:59Z vanpouckesven $
 * @package reporting.lib
 * @author Michael Kyndt
 */

class ReportingExporter
{

    private $parent;

    public function ReportingExporter($parent)
    {
        $this->parent = $parent;
    }

    /**
     * @see Application :: get_url()
     */
    function get_url($parameters = array (), $filter = array(), $encode_entities = false)
    {
        return $this->parent->get_url($parameters, $filter, $encode_entities);
    }

    /*public function export_reporting_block($rbi, $export, $params)
    {
        $rep_block = ReportingDataManager :: get_instance()->retrieve_reporting_block_registration($rbi);
        $rep_block->set_function_parameters($params);
        $displaymode = $rep_block->get_displaymode();
        if (strpos($displaymode, 'Chart:') !== false)
        {
            $displaymode = 'image';
            $test = ReportingFormatter :: factory($rep_block)->to_html('SYS');
        }
        else
        {
            $displaymode = strtolower($displaymode);
            $data = $rep_block->get_data();
            $datadescription = $data[1];
            $data = $data[0];
            $series = sizeof($datadescription["Values"]);
            $orientation = $datadescription[Reporting :: PARAM_ORIENTATION];

            $j = 0;
            foreach ($data as $key => $value)
            {
                foreach ($value as $key2 => $value2)
                {
                    $value[$j] = $value[$key2];
                    unset($value[$key2]);
                    $j ++;
                }
                $data[$key] = $value;
                $j = 0;
            }

            if ($orientation == Reporting :: ORIENTATION_HORIZONTAL)
            {
                foreach ($data as $key => $value)
                {
                    $datadescription["Description"][$j] = $value[0];
                    unset($value[0]);
                    $data[$key] = $value;
                    $j ++;
                }
                foreach ($data as $key => $value)
                {
                    foreach ($value as $key2 => $value2)
                    {
                        $data2[$key2 - 1][] = $value2;
                    }
                }
                $data = $data2;
            }
        }
        $test = $data;
        $temp = $test;
        $test = str_replace(Path :: get(WEB_PATH), Path :: get(SYS_PATH), $temp);
        $this->export_report($export, $test, $rep_block->get_name(), $rep_block);
    }*/

    public function get_template_id()
    {
    	return Request :: get(ReportingManager :: PARAM_TEMPLATE_ID);
    }

    public function get_block_id()
    {
    	return Request :: get(ReportingManager :: PARAM_REPORTING_BLOCK_ID);
    }

    public function export()
    {
        
        $export_type = Request :: get(ReportingManager :: PARAM_EXPORT_TYPE);
        
        if (Request :: get(ReportingManager :: PARAM_TEMPLATE_ID))
        {
            $ti = Request :: get(ReportingManager :: PARAM_TEMPLATE_ID);
            $template = ReportingTemplate :: factory($this->get_template_id(), $this);
            $template->add_parameters(ReportingManager :: PARAM_TEMPLATE_ID, $this->get_template_id());
            $html[] = $this->get_export_header();
            $html[] = $template->export();
            $html[] = $this->get_export_footer();
        }
        $filename = $template->get_name() . date('_Y-m-d_H-i-s');
        $export = Export :: factory($export_type, $filename);
        
        switch ($export_type)
        {
            case 'xml' :
                $export->write_to_file($data);
                
                break;
            
            case 'pdf' :
                $data = implode("\n", $html);
                $data = str_replace(Path :: get(WEB_PATH), Path :: get(SYS_PATH), $data);
                $export->write_to_file_html($data);
                break;
            
            case 'csv' :
                $export->write_to_file($data);
                break;
            
            default :
                $export->write_to_file_html($data);
                break;
        }
    }

    /*public function export_template($ti, $export, $params)
    {
        $rpdm = ReportingDataManager :: get_instance();
        if ($reporting_template_registration = $rpdm->retrieve_reporting_template_registration($ti))
        {
            $application = $reporting_template_registration->get_application();
            $base_path = (WebApplication :: is_application($application) ? Path :: get_application_path() . 'lib/' : Path :: get(SYS_PATH));
            $file = $base_path . $application . '/reporting/templates/' . Utilities :: camelcase_to_underscores($reporting_template_registration->get_classname()) . '.class.php';

            require_once ($file);
            $classname = $reporting_template_registration->get_classname();
            $template = new $classname($this->parent, $ti, $params, null);
            if (Request :: get('s'))
            {
                $template->show_reporting_block(Request :: get('s'));
            }
            $html .= $this->get_export_header();
            $temp = $template->to_html_export();
            $html .= str_replace(Path :: get(WEB_PATH), Path :: get(SYS_PATH), $temp);
            $html .= $this->get_export_footer();
            $display = $html;
            $this->export_report($export, $display, $reporting_template_registration->get_title(), null);
        }
    }*/

    function get_export_header()
    {
        $html = array();
        $html[] = '<html><head>';
//        $html[] = '<link rel="stylesheet" type="text/css" href="'. Theme :: get_common_css_path() .'" />';
        $html[] = '<link rel="stylesheet" type="text/css" href="'. Theme :: get_theme_path() .'common_general.css" />';
        $html[] = '<link rel="stylesheet" type="text/css" href="'. Theme :: get_theme_path() .'common_form.css" />';
        $html[] = '<link rel="stylesheet" type="text/css" href="'. Theme :: get_theme_path() .'common_menu.css" />';
        $html[] = '<link rel="stylesheet" type="text/css" href="'. Theme :: get_theme_path() .'common_table.css" />';
        $html[] = '<link rel="stylesheet" type="text/css" href="'. Theme :: get_theme_path() .'common_tabs.css" />';
        $html[] = '<link rel="stylesheet" type="text/css" href="'. Theme :: get_theme_path() .'common_tree.css" />';
//        $html[] = '<link rel="stylesheet" type="text/css" href="layout/aqua/css/common.css" />';
//        $html[] = '<link rel="stylesheet" type="text/css" href="layout/aqua/css/common_form.css" />';
//        $html[] = '<link rel="stylesheet" type="text/css" href="layout/aqua/css/common_menu.css" />';
//        $html[] = '<link rel="stylesheet" type="text/css" href="layout/aqua/css/common_table.css" />';
//        $html[] = '<link rel="stylesheet" type="text/css" href="layout/aqua/css/common_tree.css" />';
        $html[] = '</head><body>';
        return implode ("\n", $html);
    }

    function get_export_footer()
    {
        $html .= '</body></html>';
        return $html;
    }

    function export_report($file_type, $data, $name, $rep_block)
    {
        $filename = $name . date('_Y-m-d_H-i-s');
        $export = Export :: factory($file_type, $filename);
        if ($file_type == 'pdf')
        {
            if (isset($rep_block))
            {
            	$temp = Reporting :: generate_block_export($rep_block);
            	$data = str_replace(Path :: get(WEB_PATH), Path :: get(SYS_PATH), $temp);
                $export->write_to_file_html($data);
            }
            else
            {
            	$export->write_to_file_html($data);
            }
        }
        else
        {
            $export->write_to_file($data);
        }
        return;
    }

    function get_parameters()
    {
        return $this->parent->get_parameters();
    }
}
?>