<?php
/**
 * $Id: important.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_builder.forum.component
 */
require_once dirname(__FILE__) . '/../../complex_repo_viewer.class.php';

class ForumBuilderImportantComponent extends ForumBuilder
{

    function run()
    {
        $repository_data_manager = RepositoryDataManager :: get_instance();
        
        $topic = $repository_data_manager->retrieve_complex_content_object_item(Request :: get(ComplexBuilder :: PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID));
        
        if ($topic->get_type() == 2)
        {
            $topic->set_type(null);
            $message = 'TopicUnImortant';
        }
        else
        {
            $topic->set_type(2);
            $message = 'TopicImportant';
        }
        $topic->update();
        
        $this->redirect($message, '', array(ComplexBuilder :: PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID => Request :: get(ComplexBuilder :: PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID), ComplexBuilder :: PARAM_BUILDER_ACTION => ComplexBuilder :: ACTION_BROWSE_COMPLEX_CONTENT_OBJECT));
    }
}

?>