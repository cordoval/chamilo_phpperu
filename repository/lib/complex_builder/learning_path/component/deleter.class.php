<?php
/**
 * $Id: deleter.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_builder.learning_path.component
 */
require_once dirname(__FILE__) . '/../learning_path_builder.class.php';
require_once dirname(__FILE__) . '/../learning_path_builder_component.class.php';
/**
 */
class LearningPathBuilderDeleterComponent extends LearningPathBuilderComponent
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $ids = Request :: get(ComplexBuilder :: PARAM_SELECTED_CLOI_ID);
        $root = Request :: get(ComplexBuilder :: PARAM_ROOT_LO);
        $parent_cloi = Request :: get(ComplexBuilder :: PARAM_CLOI_ID);
        
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
                        else
                        {
                            if (get_class($cloi) == 'ComplexLearningPathItem')
                            {
                                $rdm->delete_content_object_by_id($cloi->get_ref());
                            }
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
            
            $this->redirect(Translation :: get($message), $failures ? true : false, array(ComplexBuilder :: PARAM_BUILDER_ACTION => ComplexBuilder :: ACTION_BROWSE_CLO, ComplexBuilder :: PARAM_CLOI_ID => $parent_cloi, ComplexBuilder :: PARAM_ROOT_LO => $root, 'publish' => Request :: get('publish')));
        }
        else
        {
            $this->display_error_page(htmlentities(Translation :: get('NoObjectSelected')));
        }
    }
}
?>