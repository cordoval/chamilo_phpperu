<?php
/**
 * $Id: wiki_history.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_display.wiki.component
 */

require_once Path :: get_repository_path() . 'lib/content_object/wiki/display/wiki_parser.class.php';
require_once Path :: get_repository_path() . 'lib/content_object/wiki/display/wiki_display.class.php';

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
            $wiki_page = $complex_wiki_page->get_ref_object();
            $version_parameters = $this->get_parameters();
            $version_parameters[ComplexDisplay :: PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID] = $this->complex_wiki_page_id;

            $version_browser = new RepositoryVersionBrowserTable($this, $version_parameters, new EqualityCondition(ContentObject :: PROPERTY_OBJECT_NUMBER, $wiki_page->get_object_number()));
            $actions = new ObjectTableFormActions(ComplexDisplay :: PARAM_DISPLAY_ACTION);
            $actions->add_form_action(new ObjectTableFormAction(WikiDisplay :: ACTION_COMPARE, Translation :: get('CompareSelected')));
            $version_browser->set_form_actions($actions);

            $this->display_header($complex_wiki_page);

            $html = array();
            $html[] = '<div class="wiki-pane-content-title">' . Translation :: get('RevisionHistory') . ': ' . $wiki_page->get_title() . '</div>';
            $html[] = '<div class="wiki-pane-content-subtitle">' . Translation :: get('From') . ' ' . $this->get_root_content_object()->get_title() . '</div>';
            $html[] = '<div class="wiki-pane-content-history">';
            $html[] = $version_browser->as_html();
            $html[] = ResourceManager :: get_instance()->get_resource_html(Path :: get(WEB_LIB_PATH) . 'javascript/repository.js');
            $html[] = '<div class="clear"></div>';
            $html[] = '</div>';
            echo implode("\n", $html);

            $this->display_footer();
        }
        else
        {
            $this->redirect(null, false, array(ComplexDisplay :: PARAM_DISPLAY_ACTION => WikiDisplay :: ACTION_VIEW_WIKI));
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
        return $this->get_url(array(ComplexDisplay :: PARAM_DISPLAY_ACTION => WikiDisplay :: ACTION_VIEW_WIKI_PAGE, ComplexDisplay :: PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID => $this->complex_wiki_page_id, RepositoryManager :: PARAM_CONTENT_OBJECT_ID => $content_object->get_id()));
    }

    function get_content_object_deletion_url($content_object, $type = null)
    {
        $delete_allowed = RepositoryDataManager :: content_object_deletion_allowed($content_object, $type);

        if (! $delete_allowed)
        {
            return null;
        }

        return $this->get_url(array(ComplexDisplay :: PARAM_DISPLAY_ACTION => WikiDisplay :: ACTION_VERSION_DELETE, ComplexDisplay :: PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID => $this->complex_wiki_page_id, RepositoryManager :: PARAM_CONTENT_OBJECT_ID => $content_object->get_id()));
    }

    function get_content_object_revert_url($content_object)
    {
        $revert_allowed = RepositoryDataManager :: content_object_revert_allowed($content_object);

        if (! $revert_allowed)
        {
            return null;
        }

        return $this->get_url(array(ComplexDisplay :: PARAM_DISPLAY_ACTION => WikiDisplay :: ACTION_VERSION_REVERT, ComplexDisplay :: PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID => $this->complex_wiki_page_id, RepositoryManager :: PARAM_CONTENT_OBJECT_ID => $content_object->get_id()));
    }
}
?>