<?php
/**
 * $Id: wiki_tool.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.wiki
 */

require_once dirname(__FILE__) . '/wiki_tool_component.class.php';
/**
 * This tool allows a user to publish wikis in his or her course.
 */
class WikiTool extends Tool
{
    const PARAM_WIKI_ID = 'wiki_id';
    const PARAM_WIKI_PAGE_ID = 'wiki_page_id';
    
    const ACTION_BROWSE_WIKIS = 'browse';
    const ACTION_VIEW_WIKI = 'view';
    const ACTION_VIEW_WIKI_PAGE = 'view_item';
    const ACTION_PUBLISH = 'publish';
    const ACTION_CREATE_PAGE = 'create_page';
    const ACTION_SET_AS_HOMEPAGE = 'set_as_homepage';
    const ACTION_DELETE_WIKI_CONTENTS = 'delete_wiki_contents';
    const ACTION_DISCUSS = 'discuss';
    const ACTION_HISTORY = 'history';
    const ACTION_PAGE_STATISTICS = 'page_statistics';
    const ACTION_COMPARE = 'compare';
    const ACTION_STATISTICS = 'statistics';
    const ACTION_LOCK = 'lock';
    const ACTION_ADD_LINK = 'add_wiki_link';

    /**
     * Inherited.
     */
    function run()
    {
        //wiki tool
        $action = $this->get_action();
        $component = parent :: run();
        
        if ($component)
        {
            return;
        }
        
        switch ($action)
        {
            case self :: ACTION_BROWSE_WIKIS :
                $component = WikiToolComponent :: factory('Browser', $this);
                break;
            case self :: ACTION_VIEW_WIKI :
                $component = WikiToolComponent :: factory('Viewer', $this);
                break;
            case self :: ACTION_VIEW_WIKI_PAGE :
                $component = WikiToolComponent :: factory('ItemViewer', $this);
                break;
            case self :: ACTION_PUBLISH :
                $component = WikiToolComponent :: factory('Publisher', $this);
                break;
            case self :: ACTION_CREATE_PAGE :
                $component = WikiToolComponent :: factory('PageCreator', $this);
                break;
            case self :: ACTION_SET_AS_HOMEPAGE :
                $component = WikiToolComponent :: factory('HomepageSetter', $this);
                break;
            case self :: ACTION_LOCK :
                $component = WikiToolComponent :: factory('Locker', $this);
                break;
            case self :: ACTION_DELETE_WIKI_CONTENTS :
                $component = WikiToolComponent :: factory('ContentsDeleter', $this);
                break;
            case self :: ACTION_DISCUSS :
                $component = WikiToolComponent :: factory('Discuss', $this);
                break;
            case self :: ACTION_HISTORY :
                $component = WikiToolComponent :: factory('History', $this);
                break;
            case self :: ACTION_PAGE_STATISTICS :
                $component = WikiToolComponent :: factory('PageStatisticsViewer', $this);
                break;
            case self :: ACTION_STATISTICS :
                $component = WikiToolComponent :: factory('StatisticsViewer', $this);
                break;
            case self :: ACTION_ADD_LINK :
                $component = ToolComponent :: factory('', 'WikiLinkCreator', $this);
                break;
            default :
                $component = WikiToolComponent :: factory('Browser', $this);
        }
        $component->run();
    }

    static function get_allowed_types()
    {
        return array(Wiki :: get_type_name());
    }

    static function is_wiki_locked($wiki_id)
    {
        $wiki = RepositoryDataManager :: get_instance()->retrieve_content_object($wiki_id);
        return $wiki->get_locked() == 1;
    }

    static function get_wiki_homepage($wiki_id)
    {
        require_once Path :: get_repository_path() . '/lib/content_object/wiki_page/complex_wiki_page.class.php';
        $conditions[] = new EqualityCondition(ComplexWikiPage :: PROPERTY_PARENT, $wiki_id);
        $conditions[] = new EqualityCondition(ComplexWikiPage :: PROPERTY_IS_HOMEPAGE, 1);
        $wiki_homepage = RepositoryDataManager :: get_instance()->retrieve_complex_content_object_items(new AndCondition($conditions), array(), array(), 0, - 1, 'complex_wiki_page')->next_result();
        return $wiki_homepage;
    }

}
?>