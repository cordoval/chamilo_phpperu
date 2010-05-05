<?php
require_once PATH :: get_application_library_path() . 'repo_viewer/repo_viewer_component.class.php';
abstract class StreamingMediaManagerRepoViewerComponent extends RepoViewerComponent
{
    static function factory($type, $repoviewer)
    {
        $path = dirname(__FILE__) . '/component/' . Utilities :: camelcase_to_underscores($type) . '.class.php';

        if (! file_exists($path) || ! is_file($path))
        {
            $message = Translation :: get('ComponentFailedToLoad') . ': ' . Translation :: get($type);
            Display :: error_message($message);
        }

        $class = 'StreamingMediaManagerRepoViewer' . $type . 'Component';
        require_once $path;
        return new $class($repoviewer);
    }
}
?>