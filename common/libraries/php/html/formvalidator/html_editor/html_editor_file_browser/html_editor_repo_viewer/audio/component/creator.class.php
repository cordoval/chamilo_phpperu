<?php
/**
 * $Id: creator.class.php 205 2009-11-13 12:57:33Z vanpouckesven $
 * @package application.common.repo_viewer.component
 */
require_once Path :: get_repository_path() . 'lib/content_object/document/document.class.php';
require_once Path :: get_common_extensions_path() . 'repo_viewer/component/creator.class.php';
/**
 * This class represents a encyclopedia repo_viewer component which can be used
 * to create a new learning object before publishing it.
 */
class HtmlEditorAudioRepoViewerCreatorComponent extends RepoViewerCreatorComponent
{
    function get_object_form_variant()
    {
        return Document :: TYPE_AUDIO;
    }
}
?>