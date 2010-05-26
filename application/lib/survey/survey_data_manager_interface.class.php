<?php
interface SurveyDataManagerInterface
{

    function initialize();

    function create_storage_unit($name, $properties, $indexes);

    function create_survey_publication($survey_publication);

    function update_survey_publication($survey_publication);

    function delete_survey_publication($survey_publication);

    function count_survey_publications($conditions = null);

    function retrieve_survey_publication($id);

    function retrieve_survey_publications($condition = null, $offset = null, $count = null, $order_property = null);

    function create_survey_publication_category($survey_category);

    function update_survey_publication_category($survey_category);

    function delete_survey_publication_category($survey_category);

    function count_survey_publication_categories($conditions = null);

    function retrieve_survey_publication_category($id);

    function retrieve_survey_publication_categories($condition = null, $offset = null, $count = null, $order_property = null);

    function select_next_survey_publication_category_display_order($parent);

    function create_survey_publication_mail($survey_publication_mail);

    function update_survey_publication_mail($survey_publication_mail);

    function delete_survey_publication_mail($survey_publication_mail);

    function count_survey_publication_mails($conditions = null);

    function retrieve_survey_publication_mail($id);

    function retrieve_survey_publication_mails($condition = null, $offset = null, $count = null, $order_property = null);

    function retrieve_survey_page($page_id);

    function count_survey_pages($survey_ids, $conditions = null);

    function retrieve_survey_pages($survey_ids, $condition = null, $offset = null, $count = null, $order_property = null);

}
?>