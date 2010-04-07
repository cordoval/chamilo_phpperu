<?php
/**
 * $Id: html_editor_image_repo_viewer_component.class.php 191 2009-11-13 11:50:28Z chellee $
 * @package application.common.repo_viewer
 */

require_once Path :: get_application_library_path() . 'repo_viewer/repo_viewer.class.php';

/**
==============================================================================
 *	This class represents a component of a EncyclopediaRepoViewer. Its output
 *	is included in the publisher's output.
==============================================================================
 */
abstract class CompetenceRepoViewerComponent extends RepoViewerComponent
{
    static function factory($type, $repoviewer)
    {
        $path = dirname(__FILE__) . '/component/' . Utilities :: camelcase_to_underscores($type) . '.class.php';
        
        if (! file_exists($path) || ! is_file($path))
        {
            $message = Translation :: get('ComponentFailedToLoad') . ': ' . Translation :: get($type);
            Display :: error_message($message);
        }
        
        $class = 'CompetenceRepoViewer' . $type . 'Component';
        require_once $path;
        return new $class($repoviewer);
    }
}
?>