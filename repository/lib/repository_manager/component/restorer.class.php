<?php
/**
 * $Id: restorer.class.php 204 2009-11-13 12:51:30Z kariboe $
 * @package repository.lib.repository_manager.component
 */
/**
 * Repository manager component to restore learning objects. This means movig
 * learning objects from the recycle bin to there original location.
 */
class RepositoryManagerRestorerComponent extends RepositoryManagerComponent
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $ids = Request :: get(RepositoryManager :: PARAM_CONTENT_OBJECT_ID);
        if (! empty($ids))
        {
            if (! is_array($ids))
            {
                $ids = array($ids);
            }
            $failures = 0;
            foreach ($ids as $object_id)
            {
                $object = $this->get_parent()->retrieve_content_object($object_id);
                // TODO: Roles & Rights.
                if ($object->get_owner_id() == $this->get_user_id())
                {
                    if ($object->get_state() == ContentObject :: STATE_RECYCLED)
                    {
                        $versions = $object->get_content_object_versions();
                        foreach ($versions as $version)
                        {
                            $version->set_state(ContentObject :: STATE_NORMAL);
                            $version->update();
                        }
                    }
                    else
                    {
                        $failures ++;
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
                    $message = 'SelectedObjectNotRestored';
                }
                else
                {
                    $message = 'NotAllSelectedObjectsRestored';
                }
            }
            else
            {
                if (count($ids) == 1)
                {
                    $message = 'SelectedObjectRestored';
                }
                else
                {
                    $message = 'AllSelectedObjectsRestored';
                }
            }
            
            $this->redirect(Translation :: get($message), ($failures ? true : false), array(Application :: PARAM_ACTION => RepositoryManager :: ACTION_BROWSE_RECYCLED_CONTENT_OBJECTS));
        }
        else
        {
            $this->display_error_page(htmlentities(Translation :: get('NoObjectSelected')));
        }
    }
}
?>