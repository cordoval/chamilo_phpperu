<?php

namespace application\handbook;

use common\libraries\Translation;
use common\libraries\Theme;
use common\libraries\CoreApplication;
use home\HomeManager;
use common\libraries\Application;
use repository\content_object\handbook\Handbook;
use common\libraries\EqualityCondition;
use common\libraries\StringUtilities;
use repository\content_object\handbook_item\HandbookItem;
use repository\content_object\document\Document;
use application\handbook\HandbookManager;
use repository\content_object\glossary\Glossary;
use common\libraries\ToolbarItem;
use repository\ContentObjectBlock;
use repository\ContentObjectDisplay;
use repository\RepositoryDataManager;
use repository\ComplexContentObjectItem;
use repository\content_object\handbook_topic\HandbookTopic;
use common\libraries\Redirect;
use repository\content_object\link\Link;
use repository\RepositoryManager;

require_once CoreApplication :: get_application_class_path('repository') . 'blocks/content_object_block.class.php';
require_once CoreApplication :: get_application_class_path('handbook') . 'lib/handbook_menu.class.php';

/**
 * Block to display a handbook's content
 *
 * @copyright (c) 2011 University of Geneva
 * @license GNU General Public License - http://www.gnu.org/copyleft/gpl.html
 * @author lopprecht
 * @package handbook.block
 */
class HandbookDisplay extends ContentObjectBlock {

    /**
     * Returns the list of type names that this block can map to.
     *
     * @return array
     */
    static function get_supported_types() {
        $result = array();
        $result[] = Handbook::get_type_name();
        return $result;
    }

    function __construct($parent, $block_info) {
        parent::__construct($parent, $block_info);
        $this->default_title = Translation::get('Handbook');
    }
    
    function is_visible()
    {
        return true; //i.e.display on homepage when anonymous
    }

//    function get_title() {
//        $content_object = $this->get_object();
//        if (empty($content_object)) {
//            return $this->get_default_title();
//        }
//        $title = $content_object->get_title();
//        $href = $this->get_view_handbook_url($content_object);
//
//        $result = "<a href=\"$href\">$title</a>";
//        return $result;
//    }

    /**
     * Returns the html to display when the block is configured.
     *
     * @return string
     */
    function display_content() {
        $content_object = $this->get_object();
        $display = ContentObjectDisplay :: factory($content_object);
        $DESCRIPTION = $display->get_description();

        $html = array();
        $children = $this->get_children();
        foreach ($children as $coid => $child) {
            $html[] = $this->display_child($child, $coid);
        }
        $CHILDREN = implode(StringUtilities::NEW_LINE, $html);

        $result = $this->get_content_template();
        return $this->process_template($result, get_defined_vars());
    }

    function get_content_template() {
        $html = array();
        $html[] = '{$DESCRIPTION}';
        $html[] = '<div class="tool_menu">';
        $html[] = '<ul>';
        $html[] = '{$CHILDREN}';
        $html[] = '</ul>';
        $html[] = '</div>';
        return implode(StringUtilities::NEW_LINE, $html);
    }

    function display_child($child, $coid) {
        $handbook = $this->get_object();
        $display = ContentObjectDisplay :: factory($child);
        if ($child instanceof Glossary || $child instanceof HandbookTopic) {
            $preview_url = $this->get_simple_view_url($child);
            $onclick = '" onclick="javascript:openPopup(\'' . $preview_url . '\'); return false;';
            $toolbar_item = new ToolbarItem(' ' . $child->get_title(),
                            Theme :: get_content_object_image_path($child->get_type_name()),
                            $preview_url, ToolbarItem::DISPLAY_ICON_AND_LABEL, false, $onclick, '_blank');
            $DESCRIPTION = $toolbar_item->as_html();
        } else if ($child instanceof Link) {
            $preview_url = $child->get_url();
            $toolbar_item = new ToolbarItem(' ' . $child->get_title(),
                            Theme :: get_content_object_image_path($child->get_type_name()),
                            $preview_url, ToolbarItem::DISPLAY_ICON_AND_LABEL, false, '', '_blank');
            $DESCRIPTION = $toolbar_item->as_html();
        } else if ($child instanceof Document) {
            $preview_url = RepositoryManager :: get_document_downloader_url($child->get_id());
            $toolbar_item = new ToolbarItem(' ' . $child->get_title(),
                            Theme :: get_content_object_image_path($child->get_type_name()),
                            $preview_url, ToolbarItem::DISPLAY_ICON_AND_LABEL, false, '', '');
            $DESCRIPTION = $toolbar_item->as_html();
        } else if ($child instanceof Handbook) {
            $preview_url = $this->get_view_handbook_url($handbook);
            $toolbar_item = new ToolbarItem(' ' . $child->get_title(),
                            Theme :: get_content_object_image_path($child->get_type_name()),
                            $preview_url, ToolbarItem::DISPLAY_ICON_AND_LABEL, false, '', '');
            $DESCRIPTION = $toolbar_item->as_html();
        } else {
            $DESCRIPTION = $display->get_short_html();
        }

        $result = $this->get_display_child_template();
        return $this->process_template($result, get_defined_vars());
    }

    function get_display_child_template() {
        $html = array();
        $html[] = '<li class="tool_list_menu">{$DESCRIPTION}</li>';
        return implode(StringUtilities::NEW_LINE, $html);
    }

    function get_children() {
        $result = array();

        $content_object = $this->get_object();
        $store = RepositoryDataManager::get_instance();
        $condition = new EqualityCondition(ComplexContentObjectItem :: PROPERTY_PARENT, $content_object->get_id(), ComplexContentObjectItem :: get_table_name());
        $children = $store->retrieve_complex_content_object_items($condition);

        while ($complex_item = $children->next_result()) {
            $item = $store->retrieve_content_object($complex_item->get_ref());
            if ($item instanceof HandbookItem) {
                $child = $store->retrieve_content_object($item->get_reference());
                $result[$complex_item->get_id()] = $child;
            } else if ($item instanceof Handbook) {
                $result[$complex_item->get_id()] = $item;
            }
        }
        return $result;
    }

    function get_simple_view_url($child) {
        $params = array();
        $params[Application::PARAM_APPLICATION] = HandbookManager::APPLICATION_NAME;
        $params[HandbookManager :: PARAM_ACTION] = HandbookManager :: ACTION_ITEM_SIMPLE_VIEWER;
        $params[HandbookManager :: PARAM_HANDBOOK_SELECTION_ID] = $child->get_id();

        $url = Redirect::get_link('Handbook', $params);
        return $url;
    }

    function get_view_handbook_url($handbook) {
        $params = array();
        $params[Application::PARAM_APPLICATION] = HandbookManager::APPLICATION_NAME;
        $params[HandbookManager :: PARAM_ACTION] = HandbookManager :: ACTION_VIEW_HANDBOOK;
        $params[HandbookManager :: PARAM_HANDBOOK_ID] = $handbook->get_id();
        $params[HandbookManager :: PARAM_HANDBOOK_PUBLICATION_ID] = $this->retrieve_handbook_publication($handbook);
        $params[HandbookManager :: PARAM_TOP_HANDBOOK_ID] = $params[HandbookManager :: PARAM_HANDBOOK_PUBLICATION_ID];
        return Redirect::get_link(HandbookManager::APPLICATION_NAME, $params);
    }

    function retrieve_handbook_publication($handbook = null) {
        $handbook = empty($handbook) ? $this->get_object() : $handbook;
        $handbook_id = $handbook->get_id();
        $store = HandbookDataManager :: get_instance();
        $condition = new EqualityCondition(HandbookPublication :: PROPERTY_CONTENT_OBJECT_ID, $handbook_id);
        $publications = $store->retrieve_handbook_publications($condition);
        if (count($publications == 1)) {
            return $publications->next_result()->get_id();
        } else {
            return 0;
        }
    }

}

?>