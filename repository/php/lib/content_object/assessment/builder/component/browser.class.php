<?php
/**
 * $Id: browser.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_builder.assessment.component
 */

require_once dirname(__FILE__) . '/browser/assessment_browser_table_cell_renderer.class.php';
require_once dirname(__FILE__) . '/browser/assessment_browser_table_column_model.class.php';

class AssessmentBuilderBrowserComponent extends AssessmentBuilder
{
	function run()
	{
		$browser = ComplexBuilderComponent ::factory(ComplexBuilderComponent::BROWSER_COMPONENT, $this);
		
		$browser->run();
	}

    function get_additional_links()
    {
        $link['type'] = 'Assessment';
        $link['url'] = $this->get_url(array(ComplexBuilder :: PARAM_BUILDER_ACTION => AssessmentBuilder :: ACTION_MERGE_ASSESSMENT));
        $link['title'] = Translation :: get('Merge' . ContentObject :: type_to_class('Assessment') . 'TypeName');
        
        $links[] = $link;
        
        return $links;
    }
    
    function get_complex_content_object_table_column_model()
    {
    	return new AssessmentBrowserTableColumnModel($this);
    }
    
    function get_complex_content_object_table_cell_renderer()
    {
    	return new AssessmentBrowserTableCellRenderer($this, $this->get_complex_content_object_table_condition());
    }
}

?>