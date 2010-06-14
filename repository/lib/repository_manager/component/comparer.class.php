<?php
/**
 * $Id: comparer.class.php 204 2009-11-13 12:51:30Z kariboe $
 * @package repository.lib.repository_manager.component
 */
/**
 * Repository manager component which can be used to compare a learning object.
 */
class RepositoryManagerComparerComponent extends RepositoryManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $trail = BreadcrumbTrail :: get_instance();

        $object_ids = Request :: post(RepositoryVersionBrowserTable :: DEFAULT_NAME . RepositoryVersionBrowserTable :: CHECKBOX_NAME_SUFFIX);

        if ($object_ids)
        {
            $object_id = $object_ids[0];
            $version_id = $object_ids[1];
        }
        else
        {
            $object_id = Request :: get(RepositoryManager :: PARAM_COMPARE_OBJECT);
            $version_id = Request :: get(RepositoryManager :: PARAM_COMPARE_VERSION);
        }

        if ($object_id && $version_id)
        {
            $object = $this->retrieve_content_object($object_id);

            if ($object->get_state() == ContentObject :: STATE_RECYCLED)
            {
                $trail->add(new Breadcrumb($this->get_recycle_bin_url(), Translation :: get('RecycleBin')));
                $this->force_menu_url($this->get_recycle_bin_url());
            }
            $trail->add(new Breadcrumb($this->get_url(array(RepositoryManager :: PARAM_ACTION => RepositoryManager :: ACTION_VIEW_CONTENT_OBJECTS, RepositoryManager :: PARAM_CONTENT_OBJECT_ID => $object_id)), $object->get_title()));
            $trail->add(new Breadcrumb(null, Translation :: get('DifferenceBetweenTwoVersions')));
            $trail->add_help('repository comparer');
            $this->display_header($trail, false, true);

            $diff = $object->get_difference($version_id);

            $display = ContentObjectDifferenceDisplay :: factory($diff);

            echo Utilities :: add_block_hider();
            echo Utilities :: build_block_hider('compare_legend');
            echo $display->get_legend();
            echo Utilities :: build_block_hider();
            echo $display->get_diff_as_html();

            $this->display_footer();
        }
        else
        {
            $this->display_warning_page(htmlentities(Translation :: get('NoObjectSelected')));
        }
    }
}
?>