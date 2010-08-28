<?php
/**
 * $Id: deleter.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_builder.component
 */
require_once dirname(__FILE__) . '/../complex_builder_component.class.php';
/**
 */
class ComplexBuilderComponentDeleterComponent extends ComplexBuilderComponent
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $ids = Request :: get(ComplexBuilder :: PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID);
        $parent_complex_content_object_item = Request :: get(ComplexBuilder :: PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID);

        $failures = 0;

        if (! empty($ids))
        {
            if (! is_array($ids))
            {
                $ids = array($ids);
            }

            $rdm = RepositoryDataManager :: get_instance();

            foreach ($ids as $complex_content_object_item_id)
            {
                $complex_content_object_item = $rdm->retrieve_complex_content_object_item($complex_content_object_item_id);

                if ($complex_content_object_item->get_user_id() == $this->get_user_id())
                {
                    // TODO: check if deletion is allowed
                    //if ($this->get_parent()->complex_content_object_item_deletion_allowed($complex_content_object_item))
                    {
                        if (! $complex_content_object_item->delete())
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

            $this->redirect(Translation :: get($message), $failures ? true : false, array(ComplexBuilder :: PARAM_BUILDER_ACTION => ComplexBuilder :: ACTION_BROWSE, ComplexBuilder :: PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID => $parent_complex_content_object_item));
        }
        else
        {
            $this->display_error_page(htmlentities(Translation :: get('NoObjectSelected')));
        }
    }
}
?>