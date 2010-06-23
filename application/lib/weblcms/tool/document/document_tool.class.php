<?php
/**
 * $Id: document_tool.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.document
 */

/**
 * This tool allows a user to publish announcements in his or her course.
 */
class DocumentTool extends Tool implements Categorizable
{
    const ACTION_VIEW_DOCUMENTS = 'view';
    const ACTION_DOWNLOAD = 'download';
    const ACTION_ZIP_AND_DOWNLOAD = 'zipanddownload';
    const ACTION_SLIDESHOW = 'slideshow';
    const ACTION_SLIDESHOW_SETTINGS = 'slideshow_settings';

    /**
     * Inherited.
     */
    function run()
    {
        $action = $this->get_action();

        switch ($action)
        {
            case self :: ACTION_VIEW_DOCUMENTS :
                $component = $this->create_component('Viewer');
                break;
            case self :: ACTION_MANAGE_CATEGORIES :
                $component = $this->create_component('CategoryManager');
                break;
            case self :: ACTION_MOVE_TO_CATEGORY :
                $component = $this->create_component('CategoryMover');
                break;
            case self :: ACTION_PUBLISH_INTRODUCTION :
                $component = $this->create_component('IntroductionPublisher');
                break;
            case self :: ACTION_PUBLISH :
                $component = $this->create_component('Publisher');
                break;
            case self :: ACTION_DOWNLOAD :
                $component = $this->create_component('Downloader');
                break;
            case self :: ACTION_ZIP_AND_DOWNLOAD :
                $component = $this->create_component('ZipAndDownload');
                break;
            case self :: ACTION_SLIDESHOW :
                $component = $this->create_component('Slideshow');
                break;
            case self :: ACTION_SLIDESHOW_SETTINGS :
                $component = $this->create_component('SlideshowSettings');
                break;
            case self :: ACTION_UPDATE :
                $component = $this->create_component('Updater');
                break;
            case self :: ACTION_TOGGLE_VISIBILITY :
                $component = $this->create_component('ToggleVisibility');
                break;
            case self :: ACTION_MOVE_DOWN :
                $component = $this->create_component('MoveDown');
                break;
            case self :: ACTION_MOVE_UP :
                $component = $this->create_component('MoveUp');
                break;
            case self :: ACTION_VIEW_REPORTING_TEMPLATE :
                $component = $this->create_component('ReportingViewer');
                break;
            case self :: ACTION_DELETE :
                $component = $this->create_component('Deleter');
                break;
            case self :: ACTION_SHOW_PUBLICATION:
            	$component = $this->create_component('ShowPublication');
                break;
            case self :: ACTION_HIDE_PUBLICATION:
            	$component = $this->create_component('HidePublication');
                break;
            default :
                $component = $this->create_component('Browser');
        }
        $component->run();
    }

    static function get_allowed_types()
    {
        return array(Document :: get_type_name());
    }

    function get_application_component_path()
    {
        return dirname(__FILE__) . '/component/';
    }

    function get_available_browser_types()
    {
        $browser_types = array();
        $browser_types[] = ContentObjectPublicationListRenderer :: TYPE_TABLE;
        $browser_types[] = ContentObjectPublicationListRenderer :: TYPE_GALLERY;
        $browser_types[] = ContentObjectPublicationListRenderer :: TYPE_SLIDESHOW;
        $browser_types[] = ContentObjectPublicationListRenderer :: TYPE_LIST;
        $browser_types[] = ContentObjectPublicationListRenderer :: TYPE_CALENDAR;
        return $browser_types;
    }

    function get_content_object_publication_actions($publication)
    {
        $extra_toolbar_items = array();
        $extra_toolbar_items[] = new ToolbarItem(Translation :: get('Download'), Theme :: get_common_image_path() . 'action_download.png', $this->get_url(array(Tool :: PARAM_ACTION => DocumentTool :: ACTION_DOWNLOAD, Tool :: PARAM_PUBLICATION_ID => $publication->get_id())), ToolbarItem :: DISPLAY_ICON);
        return $extra_toolbar_items;
    }
}
?>