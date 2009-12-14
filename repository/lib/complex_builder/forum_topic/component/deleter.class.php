<?php
/**
 * $Id: deleter.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_builder.component
 */
require_once dirname(__FILE__) . '/../forum_topic_builder_component.class.php';
/**
 */
class ForumTopicBuilderDeleterComponent extends ForumTopicBuilderComponent
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $ids = Request :: get(ComplexBuilder :: PARAM_SELECTED_CLOI_ID);
        $root = Request :: get(ComplexBuilder :: PARAM_ROOT_LO);
        
        $failures = 0;
        
        if (! empty($ids))
        {
            if (! is_array($ids))
            {
                $ids = array($ids);
            }
            
            $rdm = RepositoryDataManager :: get_instance();
            
            foreach ($ids as $cloi_id)
            {
                $cloi = $rdm->retrieve_complex_content_object_item($cloi_id);

                if ($cloi->get_user_id() == $this->get_user_id())
                {
                    // TODO: check if deletion is allowed
                    //if ($this->get_parent()->complex_content_object_item_deletion_allowed($cloi))
                    {
                        if (! $cloi->delete())
                        {
                            $failures ++;
                        }
                    }
                }
                else
                { 
                    $failures ++;
                }
            }
  
            if ($parent == $root)
                $parent = null;
         
            if ($failures)
            {
                if (count($ids) == 1)
                {
                    $message = 'SelectedObjectNotDeleted';
                }
                else
                {
                    $message = 'NotAllSelectedObjectsDeleted';
                }
            }
            else
            {
                if (count($ids) == 1)
                {
                    $message = 'SelectedObjectDeleted';
                }
                else
                {
                    $message = 'AllSelectedObjectsDeleted';
                }
            }
   
            $count = $rdm->count_content_objects(new EqualityCondition(ContentObject :: PROPERTY_ID, $root));
            
            if($count == 1)
            	$this->redirect(Translation :: get($message), $failures ? true : false, array(ComplexBuilder :: PARAM_BUILDER_ACTION => ComplexBuilder :: ACTION_BROWSE_CLO, ComplexBuilder :: PARAM_ROOT_LO => $root, 'publish' => Request :: get('publish')));
            else
            	$this->redirect(Translation :: get($message), $failures ? true : false, array(Application :: PARAM_ACTION => RepositoryManager :: ACTION_BROWSE_CONTENT_OBJECTS, ComplexBuilder :: PARAM_BUILDER_ACTION => null));
        }
        else
        {
            $this->display_error_page(htmlentities(Translation :: get('NoObjectSelected')));
        }
    }
}
?>