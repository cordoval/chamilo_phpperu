<?php
namespace common\libraries;

use common\extensions\repo_viewer\RepoViewerBrowserComponent;
/**
 * $Id: browser.class.php 205 2009-11-13 12:57:33Z vanpouckesven $
 * @package application.common.repo_viewer.component
 */
require_once Path :: get_common_extensions_path() . 'repo_viewer/php/component/browser.class.php';
require_once dirname(__FILE__) . '/flash_content_object_table/flash_content_object_table.class.php';
/**
 * This class represents a encyclopedia repo_viewer component which can be used
 * to browse through the possible learning objects to publish.
 */
class HtmlEditorFlashRepoViewerBrowserComponent extends RepoViewerBrowserComponent
{
    protected function get_object_table($actions)
    {
        return new FlashContentObjectTable($this, $this->get_user(), $this->get_types(), $this->get_query(), $actions);
    }
}
?>