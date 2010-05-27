<?php
/**
 * $Id: complex_builder.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.component
 */
class ToolComplexBuilderComponent extends ToolComponent
{
	private $content_object;
	
    function run()
    {
        if ($this->is_allowed(EDIT_RIGHT))
        {
            $pid = Request :: get(Tool :: PARAM_PUBLICATION_ID);
            $publication = WeblcmsDataManager :: get_instance()->retrieve_content_object_publication($pid);
            $this->content_object = RepositoryDataManager::get_instance()->retrieve_content_object($publication->get_content_object());
            $this->set_parameter(Tool :: PARAM_PUBLICATION_ID, $pid);
            
            //Request :: set_get(ComplexBuilder :: PARAM_ROOT_LO, $pub->get_content_object()->get_id());
            
            $complex_builder = ComplexBuilder :: factory($this, $this->content_object->get_type());
            $complex_builder->run();
        }
    }

    function display_header($trail)
    {
        $my_trail = new BreadcrumbTrail();
        //$my_trail->add(new Breadcrumb($this->get_url(), Translation :: get('BuildComplexContentObject')));
        $my_trail->merge($trail);
        
        parent :: display_header($my_trail, false, true, false);
        
        echo '<a href="' . $this->get_url(array('tool_action' => null, 'builder_action' => null)) . '">' . Translation :: get('Back') . '</a><br />';
    }

}
?>