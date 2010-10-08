<?php
class HtmlEditorDailymotionRepoViewer extends HtmlEditorRepoViewer
{

    function HtmlEditorDailymotionRepoViewer($parent, $types, $mail_option = false, $maximum_select = RepoViewer :: SELECT_MULTIPLE, $excluded_objects = array(), $parse_input = true, $redirect = true)
    {
        parent :: __construct($parent, $types, $mail_option, $maximum_select, $excluded_objects, $parse_input, $redirect);
    }

    static function get_allowed_content_object_types()
    {
        return array(Dailymotion :: get_type_name());
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