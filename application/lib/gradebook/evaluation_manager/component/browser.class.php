<?php
require_once dirname(__FILE__) . '/evaluation_browser/evaluation_browser_table.class.php';

class EvaluationManagerBrowserComponent extends EvaluationManagerComponent
{
    private $action_bar;

    function run()
    {   
        $trail = new BreadcrumbTrail();
        $trail->add(new Breadcrumb($this->get_url(array())));
        $trail->add(new Breadcrumb($this->get_url(array(EvaluationManager :: PARAM_ACTION => EvaluationManager :: ACTION_BROWSE, 'publication' => Request :: get('publication'))), Translation :: get('WikiEvaluation')));
    	$this->display_header($trail);
        $this->action_bar = $this->get_toolbar();
        echo $this->action_bar->as_html();
        echo $this->get_table();
    	$this->display_footer();
    }

    function get_table()
    {
        $table = new EvaluationBrowserTable($this/*, array(Application :: PARAM_APPLICATION => EvaluationManager :: APPLICATION_NAME, Application :: PARAM_ACTION => EvaluationManager :: ACTION_BROWSE), null*/);
        return $table->as_html();
    }

    function get_toolbar()
    {
    	echo 
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);
        $action_bar->set_search_url($this->get_url());
        $parameters[EvaluationManager :: PARAM_PUBLICATION_ID] = $this->get_parent()->get_publication()->get_id();
        $parameter_string = base64_encode(serialize($parameters));
        $action_bar->add_common_action(new ToolbarItem(Translation :: get('CreateEvaluation'), Theme :: get_common_image_path() . 'action_create.png', $this->get_url(array(EvaluationManager :: PARAM_ACTION => EvaluationManager :: ACTION_CREATE, EvaluationManager :: PARAM_PARAMETERS => $parameter_string)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        
        return $action_bar;
    }
    
    function display_header()
    {
    	$this->get_parent()->display_header();
    }
    
    function display_footer()
    {
    	$this->get_parent()->display_footer();
    }
}
?>