<?php
namespace application\weblcms\tool\document;

use repository\content_object\document\Document;
use common\libraries\Categorizable;
use application\weblcms\ContentObjectPublicationListRenderer;
use application\weblcms\Tool;
use common\libraries\ToolbarItem;
use common\libraries\Theme;
use common\libraries\Translation;

/**
 * $Id: document_tool.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.document
 */

/**
 * This tool allows a user to publish announcements in his or her course.
 */
class DocumentTool extends Tool implements Categorizable
{
    const ACTION_VIEW_DOCUMENTS = 'viewer';
    const ACTION_DOWNLOAD = 'downloader';
    const ACTION_ZIP_AND_DOWNLOAD = 'zip_and_download';
    const ACTION_SLIDESHOW = 'slideshow';
    const ACTION_SLIDESHOW_SETTINGS = 'slideshow_settings';

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

    function add_content_object_publication_actions($toolbar, $publication)
    {
         $toolbar->add_item(new ToolbarItem(Translation :: get('Download'), Theme :: get_common_image_path() . 'action_download.png', $this->get_url(array(Tool :: PARAM_ACTION => DocumentTool :: ACTION_DOWNLOAD, Tool :: PARAM_PUBLICATION_ID => $publication->get_id())), ToolbarItem :: DISPLAY_ICON));
    }

    /**
     * Helper function for the SubManager class,
     * pending access to class constants via variables in PHP 5.3
     * e.g. $name = $class :: DEFAULT_ACTION
     *
     * DO NOT USE IN THIS SUBMANAGER'S CONTEXT
     * Instead use:
     * - self :: DEFAULT_ACTION in the context of this class
     * - YourSubManager :: DEFAULT_ACTION in all other application classes
     */
    static function get_default_action()
    {
        return self :: DEFAULT_ACTION;
    }

    /**
     * Helper function for the SubManager class,
     * pending access to class constants via variables in PHP 5.3
     * e.g. $name = $class :: PARAM_ACTION
     *
     * DO NOT USE IN THIS SUBMANAGER'S CONTEXT
     * Instead use:
     * - self :: PARAM_ACTION in the context of this class
     * - YourSubManager :: PARAM_ACTION in all other application classes
     */
    static function get_action_parameter()
    {
        return self :: PARAM_ACTION;
    }
}
?>