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
//        $action_bar->set_search_url($this->get_url());
        $action_bar->add_common_action(new ToolbarItem(Translation :: get('CreateEvaluation'), Theme :: get_common_image_path() . 'action_create.png', $this->get_url(array(EvaluationManager :: PARAM_EVALUATION_ACTION => EvaluationManager :: ACTION_CREATE)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        
        return $action_bar;
    }
}
?>