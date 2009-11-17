<?php
/**
 * $Id: learning_path_clo_viewer.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.learning_path.component
 */

class LearningPathToolCloViewerComponent extends LearningPathToolComponent
{

    function run()
    {
        $object_id = Request :: get('pid');
        $object = RepositoryDataManager :: get_instance()->retrieve_content_object($object_id);
        $this->set_parameter(LearningPathTool :: PARAM_ACTION, LearningPathTool :: ACTION_VIEW_CLO);
        $this->set_parameter('pid', $object_id);
        $display = ComplexDisplay :: factory($this, $object->get_type());
        $display->set_root_lo($object);
        Display :: small_header();
        $display->run();
    
    }

}
?>