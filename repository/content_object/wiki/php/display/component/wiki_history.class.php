<?php
namespace repository\content_object\wiki;

use common\libraries\Display;
use common\libraries\Request;
use common\libraries\Translation;
use common\libraries\Utilities;
use common\libraries\Path;
use common\libraries\EqualityCondition;
use common\libraries\ResourceManager;
use common\libraries\BasicApplication;
use common\libraries\ObjectTableFormActions;
use common\libraries\ObjectTableFormAction;

use repository\ComplexDisplay;
use repository\RepositoryDataManager;
use repository\RepositoryVersionBrowserTable;
use repository\ContentObject;
use repository\RepositoryManager;
use repository\ContentObjectDifferenceDisplay;

/**
 * $Id: wiki_history.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_display.wiki.component
 */

require_once Path :: get_repository_content_object_path() . 'wiki/php/display/wiki_display.class.php';

class WikiDisplayWikiHistoryComponent extends WikiDisplay
{
    private $complex_wiki_page_id;

    function run()
    {
        if (! $this->is_allowed(VIEW_RIGHT))
        {
            Display :: not_allowed();
            return;
        }

        $this->complex_wiki_page_id = Request :: get(ComplexDisplay :: PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID);

        if ($this->complex_wiki_page_id)
        {
            $complex_wiki_page = RepositoryDataManager :: get_instance()->retrieve_complex_content_object_item($this->complex_wiki_page_id);
            $compare_object_ids = Request :: post(RepositoryVersionBrowserTable :: DEFAULT_NAME . RepositoryVersionBrowserTable :: CHECKBOX_NAME_SUFFIX);

            if ($compare_object_ids)
            {
                $compare_object_id = $compare_object_ids[0];
                $compare_version_id = $compare_object_ids[1];

                $compare_object = RepositoryDataManager :: get_instance()->retrieve_content_object($compare_object_id);

                $compare_difference = $compare_object->get_difference($compare_version_id);

                $compare_display = ContentObjectDifferenceDisplay :: factory($compare_difference);

                $html = array();
                $html[] = Utilities :: add_block_hider();
                $html[] = Utilities :: build_block_hider('compare_legend');
                $html[] = $compare_display->get_legend();
                $html[] = Utilities :: build_block_hider();
                $html[] = $compare_display->get_diff_as_html();
            }
            else
            {
                $wiki_page = $complex_wiki_page->get_ref_object();
                $version_parameters = $this->get_parameters();
                $version_parameters[ComplexDisplay :: PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID] = $this->complex_wiki_page_id;

                $version_browser = new RepositoryVersionBrowserTable($this, $version_parameters, new EqualityCondition(ContentObject :: PROPERTY_OBJECT_NUMBER, $wiki_page->get_object_number()));
                $actions = new ObjectTableFormActions(__NAMESPACE__, ComplexDisplay :: PARAM_DISPLAY_ACTION);
                $actions->add_form_action(new ObjectTableFormAction(WikiDisplay :: ACTION_COMPARE, Translation :: get('CompareSelected')));
                $version_browser->set_form_actions($actions);

                $html = array();
                $html[] = '<div class="wiki-pane-content-title">' . Translation :: get('RevisionHistory') . ': ' . $wiki_page->get_title() . '</div>';
                $html[] = '<div class="wiki-pane-content-subtitle">' . Translation :: get('From', null, Utilities :: COMMON_LIBRARIES) . ' ' . $this->get_root_content_object()->get_title() . '</div>';
                $html[] = '<div class="wiki-pane-content-history">';
                $html[] = $version_browser->as_html();
                $html[] = ResourceManager :: get_instance()->get_resource_html(BasicApplication :: get_application_web_resources_javascript_path(RepositoryManager :: APPLICATION_NAME) . 'repository.js');
                $html[] = '<div class="clear"></div>';
                $html[] = '</div>';
            }

            $this->display_header($complex_wiki_page);
            echo implode("\n", $html);
            $this->display_footer();
        }
        else
        {
            $this->redirect(null, false, array(
                    ComplexDisplay :: PARAM_DISPLAY_ACTION => WikiDisplay :: ACTION_VIEW_WIKI));
        }
    }

    function count_content_object_versions_resultset($condition = null)
    {
        return RepositoryDataManager :: get_instance()->count_content_object_versions_resultset($condition);
    }

    function retrieve_content_object_versions_resultset($condition = null, $order_by = array (), $offset = 0, $max_objects = -1)
    {
        return RepositoryDataManager :: get_instance()->retrieve_content_object_versions_resultset($condition, $order_by, $offset, $max_objects);
    }

    function get_content_object_viewing_url($content_object)
    {
        return $this->get_url(array(
                ComplexDisplay :: PARAM_DISPLAY_ACTION => WikiDisplay :: ACTION_VIEW_WIKI_PAGE,
                ComplexDisplay :: PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID => $this->complex_wiki_page_id,
                self :: PARAM_WIKI_VERSION_ID => $content_object->get_id()));
    }

    function get_content_object_deletion_url($content_object, $type = null)
    {
        $delete_allowed = RepositoryDataManager :: content_object_deletion_allowed($content_object, $type);

        if (! $delete_allowed)
        {
            return null;
        }

        return $this->get_url(array(
                ComplexDisplay :: PARAM_DISPLAY_ACTION => WikiDisplay :: ACTION_VERSION_DELETE,
                ComplexDisplay :: PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID => $this->complex_wiki_page_id,
                self :: PARAM_WIKI_VERSION_ID => $content_object->get_id()));
    }

    function get_content_object_revert_url($content_object)
    {
        $revert_allowed = RepositoryDataManager :: content_object_revert_allowed($content_object);

        if (! $revert_allowed)
        {
            return null;
        }

        return $this->get_url(array(
                ComplexDisplay :: PARAM_DISPLAY_ACTION => WikiDisplay :: ACTION_VERSION_REVERT,
                ComplexDisplay :: PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID => $this->complex_wiki_page_id,
                self :: PARAM_WIKI_VERSION_ID => $content_object->get_id()));
    }
}
?>