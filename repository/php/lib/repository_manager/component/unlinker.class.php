<?php
namespace repository;

use common\libraries\Request;
use common\libraries\Translation;
use common\libraries\Breadcrumb;
use common\libraries\BreadcrumbTrail;
use common\libraries\Application;
use common\libraries\Utilities;

/**
 * $Id: publication_deleter.class.php 204 2009-11-13 12:51:30Z kariboe $
 * @package repository.lib.repository_manager.component
 */
/**
 * Repository manager component which provides functionality to delete a
 * learning object publication from the publication overview.
 */
class RepositoryManagerUnlinkerComponent extends RepositoryManager
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
                $object = $this->retrieve_content_object($object_id);
                // TODO: Roles & Rights.
                if ($object->get_owner_id() == $this->get_user_id())
                {
                    $versions = $object->get_content_object_versions();

                    foreach ($versions as $version)
                    {
                        if (! $version->delete_links())
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
                    $message = 'ObjectNotUnlinked';
                    $parameter = array('OBJECT' => Translation :: get('ContentObject'));
                }
                else
                {
                    $message = 'ObjectsNotUnlinked';
                    $parameter = array('OBJECTS' => Translation :: get('ContentObjects'));
                }
            }
            else
            {
                if (count($ids) == 1)
                {
                    $message = 'ObjectUnlinked';
                    $parameter = array('OBJECT' => Translation :: get('ContentObject'));
                }
                else
                {
                    $message = 'ObjectsUnlinked';
                    $parameter = array('OBJECTS' => Translation :: get('ContentObjects'));
                }
            }

            if (count($ids) == 1)
            {
                $parameters = array(Application :: PARAM_ACTION => RepositoryManager :: ACTION_VIEW_CONTENT_OBJECTS, RepositoryManager :: PARAM_CONTENT_OBJECT_ID => $ids[0]);
            }
            else
            {
                $parameters = array(Application :: PARAM_ACTION => RepositoryManager :: ACTION_BROWSE_CONTENT_OBJECTS);
            }

            $this->redirect(Translation :: get($message, $parameter, Utilities :: COMMON_LIBRARIES), ($failures ? true : false), $parameters);
        }
        else
        {
            $this->display_error_page(htmlentities(Translation :: get('NoObjectSelected', null, Utilities :: COMMON_LIBRARIES)));
        }
    }

    function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(RepositoryManager :: PARAM_ACTION => RepositoryManager :: ACTION_BROWSE_CONTENT_OBJECTS)), Translation :: get('RepositoryManagerBrowserComponent')));
        $breadcrumbtrail->add_help('repository_unlinker');
    }

    function get_additional_parameters()
    {
        return array(RepositoryManager :: PARAM_CONTENT_OBJECT_ID);
    }
}
?>