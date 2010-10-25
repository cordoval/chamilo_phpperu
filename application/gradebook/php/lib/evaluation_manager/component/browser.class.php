<?php
require_once WebApplication :: get_application_class_lib_path('gradebook') . 'evaluation_manager/component/evaluation_browser/evaluation_browser_table.class.php';

class EvaluationManagerBrowserComponent extends EvaluationManager
{
    private $action_bar;

    function run()
    {
        $this->display_header();
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
        $action_bar->add_common_action(new ToolbarItem(Translation :: get('CreateEvaluation'), Theme :: get_common_image_path() . 'action_create.png', $this->get_url(array(EvaluationManager :: PARAM_EVALUATION_ACTION => EvaluationManager :: ACTION_CREATE)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        
        return $action_bar;
    }
}
?>