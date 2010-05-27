<?php
/**
 * $Id: builder.class.php 193 2009-11-13 11:53:37Z chellee $
 * @package application.lib.assessment.assessment_manager.component
 */

require_once dirname(__FILE__) . '/../assessment_manager.class.php';
require_once dirname(__FILE__) . '/../../assessment_publication_category_menu.class.php';
require_once dirname(__FILE__) . '/assessment_publication_browser/assessment_publication_browser_table.class.php';

class AssessmentManagerBuilderComponent extends AssessmentManager
{
	private $content_object;
	
    function run()
    {
    	$publication_id = Request :: get(AssessmentManager :: PARAM_ASSESSMENT_PUBLICATION);
    	$publication = AssessmentDataManager :: get_instance()->retrieve_assessment_publication($publication_id);
    	$this->content_object = RepositoryDataManager::get_instance()->retrieve_content_object($publication->get_content_object());
    	$this->set_parameter(AssessmentManager :: PARAM_ASSESSMENT_PUBLICATION, $publication_id);
    	
    	$complex_builder = ComplexBuilder :: factory($this, $this->content_object->get_type());
    	$complex_builder->run();
    }
    
    function display_header($trail)
    {
    	$new_trail = new BreadcrumbTrail();
    	$new_trail->add(new Breadcrumb($this->get_url(array(AssessmentManager :: PARAM_ACTION => AssessmentManager :: ACTION_BROWSE_ASSESSMENT_PUBLICATIONS)), Translation :: get('BrowseAssessmentPublications')));
    	
    	$new_trail->merge($trail);
    	parent :: display_header($new_trail);
    }
    
	function get_root_content_object()
    {
    	return $this->content_object;
    }
}
?>