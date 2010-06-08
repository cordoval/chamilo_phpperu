<?php
/**
 * $Id: sticky.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_builder.forum.component
 */

class ForumBuilderStickyComponent extends ForumBuilder
{

    function run()
    {
        $repository_data_manager = RepositoryDataManager :: get_instance();
        
        $topic = $repository_data_manager->retrieve_complex_content_object_item(Request :: get(ComplexBuilder :: PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID));
        
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
        
        $this->redirect($message, '', array(ComplexBuilder :: PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID => Request :: get(ComplexBuilder :: PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID), ComplexBuilder :: PARAM_BUILDER_ACTION => ComplexBuilder :: ACTION_BROWSE));
    }
}

?>