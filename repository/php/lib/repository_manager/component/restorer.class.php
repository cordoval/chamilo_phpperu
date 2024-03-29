<?php
namespace repository;

use common\libraries\Request;
use common\libraries\Translation;
use common\libraries\Breadcrumb;
use common\libraries\BreadcrumbTrail;
use common\libraries\EqualityCondition;
use common\libraries\Application;
use common\libraries\Utilities;
/**
 * $Id: restorer.class.php 204 2009-11-13 12:51:30Z kariboe $
 * @package repository.lib.repository_manager.component
 */
/**
 * Repository manager component to restore learning objects. This means movig
 * learning objects from the recycle bin to there original location.
 */
class RepositoryManagerRestorerComponent extends RepositoryManager
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
                    if ($object->get_state() == ContentObject :: STATE_RECYCLED)
                    {
                        $versions = $object->get_content_object_versions();
                        foreach ($versions as $version)
                        {
                            $version->set_state(ContentObject :: STATE_NORMAL);

                            if(!$this->repository_category_exists($version->get_parent_id()))
                            {
                            	$version->set_parent_id(0);
                            }

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
                    $message = Translation :: get('ObjectNotRestored', array('OBJECT' => Translation :: get('ContentObject')), Utilities :: COMMON_LIBRARIES);
                }
                else
                {
                    $message = Translation :: get('ObjectsRestored', array('OBJECTS' => Translation :: get('ContentObjects')), Utilities :: COMMON_LIBRARIES);
                }
            }
            else
            {
                if (count($ids) == 1)
                {
                    $message = Translation :: get('ObjectRestored', array('OBJECT' => Translation :: get('ContentObject')), Utilities :: COMMON_LIBRARIES);
                }
                else
                {
                    $message = Translation :: get('ObjectsRestored', array('OBJECTS' => Translation :: get('ContentObjects')), Utilities :: COMMON_LIBRARIES);
                }
            }

            $this->redirect($message, ($failures ? true : false), array(Application :: PARAM_ACTION => RepositoryManager :: ACTION_BROWSE_RECYCLED_CONTENT_OBJECTS));
        }
        else
        {
            $this->display_error_page(htmlentities(Translation :: get('NoObjectSelected', array('OBJECT' => Translation :: get('ContentObject')), Utilities :: COMMON_LIBRARIES)));
        }
    }

    function repository_category_exists($id)
    {
    	$condition = new EqualityCondition(RepositoryCategory :: PROPERTY_ID, $id);
    	return (RepositoryDataManager :: get_instance()->count_categories($condition) > 0);
    }

	function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
    	$breadcrumbtrail->add(new Breadcrumb($this->get_url(array(RepositoryManager :: PARAM_ACTION => RepositoryManager :: ACTION_BROWSE_CONTENT_OBJECTS)), Translation :: get('RepositoryManagerBrowserComponent')));
    	$breadcrumbtrail->add(new Breadcrumb($this->get_url(array(RepositoryManager :: PARAM_ACTION => RepositoryManager :: ACTION_BROWSE_RECYCLED_CONTENT_OBJECTS)), Translation :: get('RepositoryManagerRecycleBinBrowserComponent')));
    	$breadcrumbtrail->add_help('repository_restorer');
    }

    function get_additional_parameters()
    {
    	return array(RepositoryManager :: PARAM_CONTENT_OBJECT_ID);
    }
}
?>