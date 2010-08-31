<?php
/**
 * $Id: repo_viewer.class.php 205 2009-11-13 12:57:33Z vanpouckesven $
 * @package repository.lib.repository_manager.component
 */

/**
 * Weblcms component allows the user to manage course categories
 */
class RepositoryManagerRepoViewerComponent extends RepositoryManager implements RepoViewerInterface
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        

        if (!RepoViewer::is_ready_to_be_published())
        {
            $repo_viewer = RepoViewer :: construct($this);
            $repo_viewer->set_maximum_select(RepoViewer :: SELECT_SINGLE);
            $repo_viewer->run();
        }
        else
        {
            $html[] = '<script type="text/javascript">';
            $html[] = 'window.parent.object_selected(' . RepoViewer::get_selected_objects() . ');';
            $html[] = '</script>';
        }
    }

    function get_allowed_content_object_types()
    {
        return array(Document :: get_type_name());
    }
}
?>