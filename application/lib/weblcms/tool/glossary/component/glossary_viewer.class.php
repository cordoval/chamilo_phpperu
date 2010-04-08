<?php
/**
 * $Id: glossary_viewer.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.glossary.component
 */


/**
 * Represents the view component for the assessment tool.
 *
 */
class GlossaryToolViewerComponent extends GlossaryToolComponent
{

    function run()
    {
        if (! $this->is_allowed(VIEW_RIGHT))
        {
            Display :: not_allowed();
            return;
        }
        
        /*$publication_id = Request :: get(Tool :: PARAM_PUBLICATION_ID);
		$publication = WeblcmsDataManager :: get_instance()->retrieve_content_object_publication($publication_id);
		$object = $publication->get_content_object();
	
		Request :: set_get(Tool :: PARAM_PUBLICATION_ID,$object->get_id())*/
        
        $object = RepositoryDataManager :: get_instance()->retrieve_content_object(Request :: get(Tool :: PARAM_PUBLICATION_ID));
        
        $this->set_parameter(Tool :: PARAM_ACTION, GlossaryTool :: ACTION_VIEW_GLOSSARY);
        
        $trail = new BreadcrumbTrail();
        $trail->add(new Breadcrumb($this->get_url(), Translation :: get('ViewGlossary')));
        
        //$this->display_header($trail);
        
        $display = ComplexDisplay :: factory($this, $object->get_type());
        $display->run();
        //$this->display_footer();
    }
}

?>