<?php

namespace application\package;
/**
 * This is a skeleton for a data manager for the Package Application.
 * Data managers must extend this class and implement its abstract methods.
 *
 * @author Sven Vanpoucke
 * @author Hans De Bisschop
 */

interface PackageDataManagerInterface
{

    function initialize();

    function create_storage_unit($name, $properties, $indexes);

    function get_next_package_id();

    function create_package($package);

    function update_package($package);

    function delete_package($package);

    function count_packages($conditions = null);

    function retrieve_package($id);

    //function retrieve_package_language_english();

    function retrieve_packages($condition = null, $offset = null, $count = null, $order_property = null);

//    function get_next_language_pack_id();
//
//    function create_language_pack($language_pack);
//
//    function update_language_pack($language_pack);
//
//    function delete_language_pack($language_pack);
//
//    function count_language_packs($conditions = null);
//
//    function retrieve_language_pack($id);
//
//    function retrieve_language_packs($condition = null, $offset = null, $count = null, $order_property = null);
//
//    function get_next_variable_id();
//
//    function create_variable($variable);
//
//    function update_variable($variable);
//
//    function delete_variable($variable);
//
//    function count_variables($conditions = null);
//
//    function retrieve_variable($id);
//
//    function retrieve_variables($condition = null, $offset = null, $count = null, $order_property = null);
//
//    function create_variable_translation($variable_translation);
//
//    function delete_variable_translation($variable_translation);
//
//    function update_variable_translation($variable_translation);
//
//    function count_variable_translations($conditions = null);
//
//    function retrieve_variable_translation($variable_translation_id);
//
//    function retrieve_variable_translation_by_parameters($language_id, $variable_id);
//
//    function retrieve_variable_translations($condition = null, $offset = null, $count = null, $order_property = null);
//
//    function retrieve_english_translation($variable_id);
//
//    function create_historic_variable_translation($historic_variable_translation);
//
//    function delete_historic_variable_translation($historic_variable_translation);
//
//    function update_historic_variable_translation($historic_variable_translation);
//
//    function count_historic_variable_translations($conditions = null);
//
//    function retrieve_historic_variable_translation($historic_variable_translation_id);
//
//    function retrieve_historic_variable_translations($condition = null, $offset = null, $count = null, $order_property = null);
//
//    function can_language_be_locked($language);
//
//    function can_language_be_unlocked($language);
//
//    function can_language_pack_be_locked($language_pack, $language_id);
//
//    function can_language_pack_be_unlocked($language_pack, $language_id);
//
//    function get_progress_for_language($language);
//
//    function get_progress_for_language_pack($language_pack, $language_id = null);
//
//    function get_status_for_language($language);
//
//    function get_status_for_language_pack($language_pack, $language_id = null);
//
//    function get_next_translator_application_id();
//
//    function create_translator_application($translator_application);
//
//    function update_translator_application($translator_application);
//
//    function delete_translator_application($translator_application);
//
//    function count_translator_applications($conditions = null);
//
//    function retrieve_translator_application($id);
//
//    function retrieve_translator_applications($condition = null, $offset = null, $count = null, $order_property = null);
//
//    function get_number_of_translations_for_user($user_id);
//
//    function get_number_of_translations_by_user();
//
//    function retrieve_first_untranslated_variable_translation($language_id, $language_pack_id = null, $status = null);
}
?>