<?php
namespace application\handbook;

use common\extensions\repo_viewer\ContentObjectTable;
use common\libraries\Request;


class WikiConvertorContentObjectTable extends ContentObjectTable
{
    const DEFAULT_NAME = 'content_object_table';

    static function get_selected_ids($table_name)
    {

    	$selected_ids = Request :: post($table_name . self :: CHECKBOX_NAME_SUFFIX);
        if (empty($selected_ids))
        {
            $selected_ids = array();
        }
        elseif (! is_array($selected_ids))
        {
            $selected_ids = array($selected_ids);
        }

        return $selected_ids;
    }

    static function handle_table_action()
    {
        $class = Utilities :: get_classname_from_namespace(__CLASS__, true);
        $ids = self :: get_selected_ids($class);
        $leeg = array();
        var_dump($ids);
//        Request :: set_get(RepoViewer :: PARAM_ID, $leeg);
//        Request :: set_get(RepoViewer :: PARAM_ID, $ids);
    }
}
?>