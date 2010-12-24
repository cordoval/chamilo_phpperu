<?php
namespace repository\content_object\survey;

//require_once Path :: get_repository_path() . 'lib/data_manager/database_repository_data_manager.class.php';


interface SurveyContextDataManagerInterface
{

    function retrieve_survey_contexts($type, $condition = null, $offset = null, $count = null, $order_property = null);

    function count_survey_contexts($type, $condition = null);

    function retrieve_survey_context_by_id($context_id, $type);

    function delete_survey_context($survey_context);

    function update_survey_context($survey_context);

    function create_survey_context($survey_context);

    function retrieve_additional_survey_context_properties($survey_context);

    function create_survey_context_template($survey_context_template);

    function retrieve_survey_context_templates($condition = null, $offset = null, $count = null, $order_property = null);

    function retrieve_survey_context_template($context_template_id);

    function update_survey_context_template($context_template);

    function delete_survey_context_template($context_template);

    function count_survey_context_templates($condition = null);

    function truncate_survey_context_template($survey_id, $template_id);

    function create_survey_template($survey_template);

    function retrieve_survey_templates($condition = null, $offset = null, $count = null, $order_property = null);

    function retrieve_survey_template($template_id);

    function update_survey_template($template);

    function delete_survey_template($template);

    function count_survey_templates($condition = null);

    function truncate_survey_template($template_id);

    function retrieve_template_rel_pages($condition = null, $offset = null, $count = null, $order_property = null);

    function count_template_rel_pages($condition = null);

    function delete_survey_context_template_rel_page($survey_context_template_rel_page);

    function create_survey_context_template_rel_page($survey_context_template_rel_page);

    function retrieve_survey_context_registrations($condition = null, $offset = null, $count = null, $order_property = null);

    function retrieve_survey_context_registration($context_registration_id);

    function count_survey_context_registrations($condition = null);

    function delete_survey_context_registration($context_registration);

    function create_survey_context_registration($context_registration);

    function retrieve_survey_template_users($type, $condition = null, $offset = null, $count = null, $order_property = null);

    function count_survey_template_users($type, $condition = null);

    function retrieve_survey_template_user_by_id($template_id, $type);

    function delete_survey_template_user($survey_template_user);

    function update_survey_template_user($survey_template_user);

    function create_survey_template_user($survey_template_user);

    function retrieve_additional_survey_template_user_properties($survey_template_user);

    function delete_survey_context_rel_user($context_rel_user);

    function create_survey_context_rel_user($context_rel_user);

    function count_survey_context_rel_users($conditions = null);

    function retrieve_survey_context_rel_users($condition = null, $offset = null, $count = null, $order_property = null);

    function retrieve_survey_context_rel_user($context_id, $user_id);

    function delete_survey_context_rel_group($context_rel_group);

    function create_survey_context_rel_group($context_rel_group);

    function count_survey_context_rel_groups($conditions = null);

    function retrieve_survey_context_rel_groups($condition = null, $offset = null, $count = null, $order_property = null);

    function retrieve_survey_context_rel_group($context_id, $group_id);

}
?>