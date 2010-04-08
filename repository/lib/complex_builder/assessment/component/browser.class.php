<?php
/**
 * $Id: browser.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_builder.assessment.component
 */
require_once dirname(__FILE__) . '/../assessment_builder_component.class.php';
require_once Path :: get_repository_path() . '/lib/content_object/assessment/assessment.class.php';
require_once dirname(__FILE__) . '/browser/assessment_browser_table_cell_renderer.class.php';

class AssessmentBuilderBrowserComponent extends AssessmentBuilderComponent
{

    function run()
    {
        $trail = new BreadcrumbTrail(false);
        //$trail->add(new Breadcrumb($this->get_url(array('builder_action' => null, Application :: PARAM_ACTION => RepositoryManager :: ACTION_BROWSE_CONTENT_OBJECTS)), Translation :: get('Repository')));
        $trail->add(new Breadcrumb($this->get_url(array(ComplexBuilder :: PARAM_ROOT_LO => $this->get_root_lo()->get_id())), $this->get_root_lo()->get_title()));
        $trail->add_help('repository assessment builder');
        $this->display_header($trail);
        $assessment = $this->get_root_lo();
        $action_bar = $this->get_action_bar($assessment);
        
        echo '<br />';
        if ($action_bar)
        {
            echo $action_bar->as_html();
            echo '<br />';
        }
        
        $display = ContentObjectDisplay :: factory($this->get_root_lo());
        echo $display->get_full_html();
        
        echo '<br />';
        echo $this->get_creation_links($assessment, array(), $this->get_additional_links());
        echo '<div class="clear">&nbsp;</div><br />';
        
        echo $this->get_clo_table_html(false, new AssessmentBrowserTableColumnModel(false), new AssessmentBrowserTableCellRenderer($this->get_parent(), $this->get_clo_table_condition()));
        
        $this->display_footer();
    }

    function get_additional_links()
    {
        $link['type'] = 'Assessment';
        $link['url'] = $this->get_url(array(ComplexBuilder :: PARAM_BUILDER_ACTION => AssessmentBuilder :: ACTION_MERGE_ASSESSMENT, ComplexBuilder :: PARAM_ROOT_LO => $this->get_root_lo()->get_id(), 'publish' => Request :: get('publish')));
        $link['title'] = Translation :: get('Merge' . ContentObject :: type_to_class('Assessment') . 'TypeName');
        
        $links[] = $link;
        
        return $links;
    }
}

?>