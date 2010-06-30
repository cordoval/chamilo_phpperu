<?php
/**
 * $Id: survey.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.content_object.survey
 */
/**
 * This class represents an survey
 */

require_once (dirname(__FILE__) . '/survey_context.class.php');

class Survey extends ContentObject implements ComplexContentObjectSupport
{
    const PROPERTY_HEADER = 'header';
    const PROPERTY_FOOTER = 'footer';
    const PROPERTY_FINISH_TEXT = 'finish_text';
    const PROPERTY_ANONYMOUS = 'anonymous';
    //    const PROPERTY_CONTEXT = 'context';
    const PROPERTY_CONTEXT_TEMPLATE_ID = 'context_template_id';

    const CLASS_NAME = __CLASS__;

    const MANAGER_CONTEXT = 'context';

    private $context;

    static function get_type_name()
    {
        return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
    }

    static function get_additional_property_names()
    {
        return array(self :: PROPERTY_HEADER, self :: PROPERTY_FOOTER, self :: PROPERTY_FINISH_TEXT, self :: PROPERTY_ANONYMOUS, self :: PROPERTY_CONTEXT_TEMPLATE_ID);
    }

    function get_header()
    {
        return $this->get_additional_property(self :: PROPERTY_HEADER);
    }

    function set_header($text)
    {
        $this->set_additional_property(self :: PROPERTY_HEADER, $text);
    }

    function get_footer()
    {
        return $this->get_additional_property(self :: PROPERTY_FOOTER);
    }

    function set_footer($text)
    {
        $this->set_additional_property(self :: PROPERTY_FOOTER, $text);
    }

    function get_finish_text()
    {
        return $this->get_additional_property(self :: PROPERTY_FINISH_TEXT);
    }

    function set_finish_text($value)
    {
        $this->set_additional_property(self :: PROPERTY_FINISH_TEXT, $value);
    }

    function get_anonymous()
    {
        return $this->get_additional_property(self :: PROPERTY_ANONYMOUS);
    }

    function set_anonymous($value)
    {
        return $this->set_additional_property(self :: PROPERTY_ANONYMOUS, $value);
    }

    //    function get_context()
    //    {
    //        $type = $this->get_additional_property(self :: PROPERTY_CONTEXT);
    //        return SurveyContext :: factory($type);
    //    }
    //
    //    function get_context_type()
    //    {
    //        return $this->get_additional_property(self :: PROPERTY_CONTEXT);
    //    }
    //
    //    function set_context($value)
    //    {
    //        $this->set_additional_property(self :: PROPERTY_CONTEXT, $value);
    //    }


    function set_context_instance($context)
    {
        $this->context = $context;
    }

    function get_context_instance()
    {
        return $this->context;
    }

    function get_context_template_id()
    {
        return $this->get_additional_property(self :: PROPERTY_CONTEXT_TEMPLATE_ID);

    }

    function get_context_template_name()
    {
        $template = SurveyContextDataManager :: get_instance()->retrieve_survey_context_template($this->get_additional_property(self :: PROPERTY_CONTEXT_TEMPLATE_ID));
        return empty($template) ? '' : $template->get_name();

    }

    function get_context_template()
    {
        return SurveyContextDataManager :: get_instance()->retrieve_survey_context_template($this->get_additional_property(self :: PROPERTY_CONTEXT_TEMPLATE_ID));

    }

    function set_context_template_id($value)
    {
        $this->set_additional_property(self :: PROPERTY_CONTEXT_TEMPLATE_ID, $value);
    }

    function get_allowed_types()
    {
        return array(SurveyPage :: get_type_name());
    }

    function get_table()
    {
        return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
    }

    function get_pages($complex_items = false)
    {

        $complex_content_objects = RepositoryDataManager :: get_instance()->retrieve_complex_content_object_items(new EqualityCondition(ComplexContentObjectItem :: PROPERTY_PARENT, $this->get_id(), ComplexContentObjectItem :: get_table_name()));

        if ($complex_items)
        {
            return $complex_content_objects;
        }

        //        $survey_page_ids = array();
        $survey_pages = array();

        while ($complex_content_object = $complex_content_objects->next_result())
        {
            //            $survey_page_ids[] = $complex_content_object->get_ref();
            $survey_pages[] = RepositoryDataManager :: get_instance()->retrieve_content_object($complex_content_object->get_ref());

        }

        return $survey_pages;

    //        if (count($survey_page_ids) == 0)
    //        {
    //            $survey_page_ids[] = 0;
    //        }
    //
    //        $condition = new InCondition(ContentObject :: PROPERTY_ID, $survey_page_ids, ContentObject :: get_table_name());
    //        return RepositoryDataManager :: get_instance()->retrieve_content_objects($condition);
    }

    function get_page_by_index($index)
    {
        $complex_content_objects = RepositoryDataManager :: get_instance()->retrieve_complex_content_object_items(new EqualityCondition(ComplexContentObjectItem :: PROPERTY_PARENT, $this->get_id(), ComplexContentObjectItem :: get_table_name()))->as_array();

        if (isset($complex_content_objects[$index - 1]))
        {
            return RepositoryDataManager :: get_instance()->retrieve_content_object($complex_content_objects[$index - 1]->get_ref());
        }
        else
        {
            return null;
        }
    }

    function count_pages()
    {
        return RepositoryDataManager :: get_instance()->count_complex_content_object_items(new EqualityCondition(ComplexContentObjectItem :: PROPERTY_PARENT, $this->get_id(), ComplexContentObjectItem :: get_table_name()));
    }

    static function get_managers()
    {
        $managers = array();
        $managers[] = self :: MANAGER_CONTEXT;

        return $managers;
    }
}
?>