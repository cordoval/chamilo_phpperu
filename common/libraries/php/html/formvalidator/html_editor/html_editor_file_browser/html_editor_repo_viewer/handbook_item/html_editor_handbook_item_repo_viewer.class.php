<?php
namespace common\libraries;

use common\extensions\repo_viewer\RepoViewer;
use repository\content_object\handbook_item\HandbookItem;

class HtmlEditorHandbookItemRepoViewer extends HtmlEditorRepoViewer
{

    function __construct($parent, $types, $maximum_select = RepoViewer :: SELECT_MULTIPLE, $excluded_objects = array(), $parse_input = true)
    {
        var_dump('HtmlEditorHandbookItemRepoViewer');
        var_dump($types);
        var_dump($parent);
        parent :: __construct($parent, $types, $maximum_select, $excluded_objects, $parse_input);
    }

    function get_application_component_path()
    {
        return dirname(__FILE__) . '/component/';
    }

    static function get_allowed_content_object_types()
    {
        return array(HandbookItem :: get_type_name());
    }

    /**
     * @param Application $application
     * @return RepoViewer
     */
    static function construct($application)
    {
        return parent :: construct(__CLASS__, $application);
    }

    /**
     * @param Application $application
     */
    static function launch($application)
    {
        self :: construct(__CLASS__, $application)->run();
    }
}
?>