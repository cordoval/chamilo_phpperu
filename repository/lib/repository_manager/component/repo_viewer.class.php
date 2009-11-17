<?php
/**
 * $Id: repo_viewer.class.php 205 2009-11-13 12:57:33Z vanpouckesven $
 * @package repository.lib.repository_manager.component
 */

/**
 * Weblcms component allows the user to manage course categories
 */
class RepositoryManagerRepoViewerComponent extends RepositoryManagerComponent
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        Display :: small_header();
        $object = Request :: get('object');
        $pub = new RepoViewer($this, 'document', false, RepoViewer :: SELECT_SINGLE, array(), true, false);
        if (! isset($object))
        {
            echo $pub->as_html();
        }
        else
        {
            $html[] = '<script language="javascript">';
            $html[] = 'window.parent.object_selected(' . $object . ');';
            $html[] = '</script>';
            echo implode("\n", $html);
        }
    }
}
?>