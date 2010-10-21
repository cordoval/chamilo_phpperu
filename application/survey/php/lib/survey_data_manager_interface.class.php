<?php 
namespace survey;

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

    function create_survey_publication_rel_reporting_template_registration($survey_publication_rel_reporting_template_registration);

    function delete_survey_publication_rel_reporting_template_registration($survey_publication_rel_reporting_template_registration);

    function count_survey_publication_rel_reporting_template_registrations($conditions = null);

    function update_survey_publication_rel_reporting_template_registration($survey_publication_rel_reporting_template_registration);

    function retrieve_survey_publication_rel_reporting_template_registration_by_id($survey_publication_rel_reporting_template_registration_id);

    function retrieve_survey_publication_rel_reporting_template_registrations($condition = null, $offset = null, $count = null, $order_property = null);

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