<?php
require_once dirname(__FILE__) . '/evaluation_browser/evaluation_browser_table.class.php';

class EvaluationManagerBrowserComponent extends EvaluationManagerComponent
{
    private $action_bar;

    function run()
    {   
    	$trail = $this->get_parent()->get_trail();
    	$this->display_header($trail);
        $this->action_bar = $this->get_toolbar();
        echo $this->action_bar->as_html();
        echo $this->get_table();
        //echo $this->get_export_links();
    	$this->display_footer();
    }

    function get_table()
    {
        $table = new EvaluationBrowserTable($this);
        return $table->as_html();
    }

    function get_toolbar()
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);
//      $action_bar->set_search_url($this->get_url());
        $action_bar->add_common_action(new ToolbarItem(Translation :: get('CreateEvaluation'), Theme :: get_common_image_path() . 'action_create.png', $this->get_url(array(EvaluationManager :: PARAM_EVALUATION_ACTION => EvaluationManager :: ACTION_CREATE)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
      
        
        return $action_bar;
    }
    
	/*public function get_export_links()
    {
        $list = Export :: get_supported_filetypes(array('ical'));
        $download_bar_items = array();
        $save_bar_items = array();

        foreach ($list as $export_format)
        {
            $parameters = $this->get_parent()->get_parameters();
			
            
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
        $html[] = '<div style="float:right;">' . Translation::get('Download') . ' : ';
        $html[] = $download_bar->as_html() . '</div>';
        $html[] = '<div style="float:right;">&nbsp;|&nbsp;';
        $html[] = Translation::get('Save') . ' : ';
        $html[] = $save_bar->as_html() . '</div>';
        
        return implode("\n", $html);
    }*/
}
?>