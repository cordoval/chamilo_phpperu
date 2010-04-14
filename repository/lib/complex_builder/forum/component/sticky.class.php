<?php
/**
 * $Id: sticky.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_builder.forum.component
 */
require_once dirname(__FILE__) . '/../forum_builder_component.class.php';
require_once dirname(__FILE__) . '/../../complex_repo_viewer.class.php';

class ForumBuilderStickyComponent extends ForumBuilderComponent
{

    function run()
    {
        $rdm = RepositoryDataManager :: get_instance();
        
        $topic = $rdm->retrieve_complex_content_object_item(Request :: get(ComplexBuilder :: PARAM_SELECTED_CLOI_ID));
        
        if ($topic->get_type() == 1)
        {
            $topic->set_type(null);
            $message = 'TopicUnStickied';
        }
        else
        {
            $topic->set_type(1);
            $message = 'TopicStickied';
        }
        $topic->update();
        
        $this->redirect($message, '', array(ComplexBuilder :: PARAM_ROOT_LO => Request :: get(ComplexBuilder :: PARAM_ROOT_LO),
        								    ComplexBuilder :: PARAM_CLOI_ID => Request :: get(ComplexBuilder :: PARAM_CLOI_ID), ComplexBuilder :: PARAM_BUILDER_ACTION => ComplexBuilder :: ACTION_BROWSE_CLO));
    }
}

?>