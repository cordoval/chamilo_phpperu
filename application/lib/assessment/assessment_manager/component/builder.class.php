<?php
/**
 * $Id: builder.class.php 193 2009-11-13 11:53:37Z chellee $
 * @package application.lib.assessment.assessment_manager.component
 */

require_once dirname(__FILE__) . '/../assessment_manager.class.php';
require_once dirname(__FILE__) . '/../assessment_manager_component.class.php';
require_once dirname(__FILE__) . '/../../assessment_publication_category_menu.class.php';
require_once dirname(__FILE__) . '/assessment_publication_browser/assessment_publication_browser_table.class.php';

class AssessmentManagerBuilderComponent extends AssessmentManagerComponent
{
    function run()
    {
    	$publication_id = Request :: get(AssessmentManager :: PARAM_ASSESSMENT_PUBLICATION);
    	$publication = AssessmentDataManager :: get_instance()->retrieve_assessment_publication($publication_id);
    	$this->set_parameter(AssessmentManager :: PARAM_ASSESSMENT_PUBLICATION, $publication_id);
    	Request :: set_get(ComplexBuilder :: PARAM_ROOT_LO, $publication->get_content_object());
    	
    	$complex_builder = ComplexBuilder :: factory($this);
    	$complex_builder->run();
    }
}
?>