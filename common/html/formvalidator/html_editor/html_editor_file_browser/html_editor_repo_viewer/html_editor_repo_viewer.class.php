<?php
/**
 * $Id: content_object_repo_viewer.class.php 218 2009-11-13 14:21:26Z kariboe $
 * @package application.lib.weblcms
 */

/**
 * Abstract implementation of the platform-wide RepoViewer for
 * the Html Editor Repo Viewer
 *
 * @package common.html.formvalidator.html_editor.html_editor_file_browser.html_editor_repo_viewer
 * @author Hans De Bisschop
 */

class HtmlEditorRepoViewer extends RepoViewer
{
//    RepoViewer($parent, $types, $mail_option = false, $maximum_select = RepoViewer :: SELECT_MULTIPLE, $excluded_objects = array(), $parse_input = true, $redirect = true)

    public static function factory($type, $parent, $types, $mail_option = false, $maximum_select = RepoViewer :: SELECT_MULTIPLE, $excluded_objects = array(), $parse_input = true, $redirect = true)
    {
        $file = dirname(__FILE__) . '/' . $type . '/html_editor_' . $type . '_repo_viewer.class.php';
        $class = 'HtmlEditor' . Utilities :: underscores_to_camelcase($type) . 'RepoViewer';

        if (file_exists($file))
        {
            require_once ($file);
            return new $class($parent, $types, $mail_option, $maximum_select, $excluded_objects, $parse_input, $redirect);
        }
    }
}
?>
