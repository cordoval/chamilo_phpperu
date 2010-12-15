<?php

namespace application\package;

use common\libraries\Database;
use common\libraries\EqualityCondition;
use common\libraries\SubselectCondition;
use common\libraries\AndCondition;
use common\libraries\InCondition;
use user\UserDataManager;
use user\User;
/**
 * @package package.datamanager
 */

/**
 * This is a data manager that uses a database for storage. It was written
 * for MySQL, but should be compatible with most SQL flavors.
 *
 * @author Sven Vanpoucke
 * @author Hans De Bisschop
 */

class DatabasePackageDataManager extends Database implements PackageDataManagerInterface
{
    /*
	 * Helper variable so we don't need to make subselects each and every row
	 */
    private $variable_ids;

    function initialize()
    {
        parent :: initialize();
        $this->set_prefix('package_');
    }

    function get_next_package_id()
    {
        return $this->get_next_id(Package :: get_table_name());
    }

    function create_package($package)
    {
        return $this->create($package);
    }

    function update_package($package)
    {
        $condition = new EqualityCondition(Package :: PROPERTY_ID, $package->get_id());
        return $this->update($package, $condition);
    }

    function delete_package($package)
    {
        $condition = new EqualityCondition(Package :: PROPERTY_ID, $package->get_id());
        return $this->delete($package->get_table_name(), $condition);
    }

    function count_packages($condition = null)
    {
        return $this->count_objects(Package :: get_table_name(), $condition);
    }

    function retrieve_package($id)
    {
        $condition = new EqualityCondition(Package :: PROPERTY_ID, $id);
        return $this->retrieve_object(Package :: get_table_name(), $condition, null, Package :: CLASS_NAME);
    }

    function retrieve_packages($condition = null, $offset = null, $max_objects = null, $order_by = null)
    {
        return $this->retrieve_objects(Package :: get_table_name(), $condition, $offset, $max_objects, $order_by, Package :: CLASS_NAME);
    }

//    function get_next_language_pack_id()
//    {
//        return $this->get_next_id(LanguagePack :: get_table_name());
//    }
//
//    function create_language_pack($language_pack)
//    {
//        return $this->create($language_pack);
//    }
//
//    function update_language_pack($language_pack)
//    {
//        $condition = new EqualityCondition(LanguagePack :: PROPERTY_ID, $language_pack->get_id());
//        return $this->update($language_pack, $condition);
//    }
//
//    function delete_language_pack($language_pack)
//    {
//        $condition = new EqualityCondition(LanguagePack :: PROPERTY_ID, $language_pack->get_id());
//        return $this->delete($language_pack->get_table_name(), $condition);
//    }
//
//    function count_language_packs($condition = null)
//    {
//        return $this->count_objects(LanguagePack :: get_table_name(), $condition);
//    }
//
//    function retrieve_language_pack($id)
//    {
//        $condition = new EqualityCondition(LanguagePack :: PROPERTY_ID, $id);
//        return $this->retrieve_object(LanguagePack :: get_table_name(), $condition, null, LanguagePack :: CLASS_NAME);
//    }
//
//    function retrieve_language_packs($condition = null, $offset = null, $max_objects = null, $order_by = null)
//    {
//        return $this->retrieve_objects(LanguagePack :: get_table_name(), $condition, $offset, $max_objects, $order_by, LanguagePack :: CLASS_NAME);
//    }
//
//    function get_next_variable_id()
//    {
//        return $this->get_next_id(Variable :: get_table_name());
//    }
//
//    function create_variable($variable)
//    {
//        return $this->create($variable);
//    }
//
//    function update_variable($variable)
//    {
//        $condition = new EqualityCondition(Variable :: PROPERTY_ID, $variable->get_id());
//        return $this->update($variable, $condition);
//    }
//
//    function delete_variable($variable)
//    {
//        $condition = new EqualityCondition(Variable :: PROPERTY_ID, $variable->get_id());
//        return $this->delete($variable->get_table_name(), $condition);
//    }
//
//    function count_variables($condition = null)
//    {
//        return $this->count_objects(Variable :: get_table_name(), $condition);
//    }
//
//    function retrieve_variable($id)
//    {
//        $condition = new EqualityCondition(Variable :: PROPERTY_ID, $id);
//        return $this->retrieve_object(Variable :: get_table_name(), $condition, null, Variable :: CLASS_NAME);
//    }
//
//    function retrieve_variables($condition = null, $offset = null, $max_objects = null, $order_by = null)
//    {
//        return $this->retrieve_objects(Variable :: get_table_name(), $condition, $offset, $max_objects, $order_by, Variable :: CLASS_NAME);
//    }
//
//    function create_variable_translation($variable_translation)
//    {
//        return $this->create($variable_translation);
//    }
//
//    function delete_variable_translation($variable_translation)
//    {
//        $condition = new EqualityCondition(VariableTranslation :: PROPERTY_ID, $variable_translation->get_id());
//        return $this->delete($variable_translation->get_table_name(), $condition);
//    }
//
//    function update_variable_translation($variable_translation)
//    {
//        $condition = new EqualityCondition(VariableTranslation :: PROPERTY_ID, $variable_translation->get_id());
//        return $this->update($variable_translation, $condition);
//    }
//
//    function update_variable_translations($properties = array(), $condition, $offset = null, $max_objects = null, $order_by = array())
//    {
//        return $this->update_objects(VariableTranslation :: get_table_name(), $properties, $condition, $offset, $max_objects, $order_by);
//    }
//
//    function count_variable_translations($condition = null)
//    {
//        return $this->count_objects(VariableTranslation :: get_table_name(), $condition);
//    }
//
//    function retrieve_variable_translation($variable_translation_id)
//    {
//        $condition = new EqualityCondition(VariableTranslation :: PROPERTY_ID, $variable_translation_id);
//        return $this->retrieve_object(VariableTranslation :: get_table_name(), $condition, null, VariableTranslation :: CLASS_NAME);
//    }
//
//    function retrieve_variable_translation_by_parameters($language_id, $variable_id)
//    {
//        $conditions[] = new EqualityCondition(VariableTranslation :: PROPERTY_VARIABLE_ID, $variable_id);
//
//        if (! is_numeric($language_id))
//        {
//            $subcondition = new EqualityCondition(PackageLanguage :: PROPERTY_ENGLISH_NAME, $language_id);
//            $conditions[] = new SubselectCondition(VariableTranslation :: PROPERTY_LANGUAGE_ID, PackageLanguage :: PROPERTY_ID, PackageLanguage :: get_table_name(), $subcondition);
//        }
//        else
//        {
//            $conditions[] = new EqualityCondition(VariableTranslation :: PROPERTY_LANGUAGE_ID, $language_id);
//        }
//
//        $condition = new AndCondition($conditions);
//
//        return $this->retrieve_object(VariableTranslation :: get_table_name(), $condition, null, VariableTranslation :: CLASS_NAME);
//    }
//
//    function retrieve_variable_translations($condition = null, $offset = null, $max_objects = null, $order_by = null)
//    {
//        $variable_translation_alias = $this->get_alias(VariableTranslation :: get_table_name());
//        $variable_translation_table = $this->escape_table_name(VariableTranslation :: get_table_name());
//        $variable_alias = $this->get_alias(Variable :: get_table_name());
//        $variable_table = $this->escape_table_name(Variable :: get_table_name());
//
//        $query = 'SELECT ' . $variable_translation_alias . '.*, ' . $variable_alias . '.variable FROM ' . $variable_translation_table . ' AS ' . $variable_translation_alias;
//        //$query = 'SELECT * FROM ' . $variable_translation_table . ' AS ' . $variable_translation_alias;
//        $query .= ' JOIN ' . $variable_table . ' AS ' . $variable_alias . ' ON ' . $variable_translation_alias . '.variable_id = ' . $variable_alias . '.id';
//
//        return $this->retrieve_object_set($query, VariableTranslation :: get_table_name(), $condition, $offset, $max_objects, $order_by, VariableTranslation :: CLASS_NAME);
//    }
//
//    function retrieve_english_translation($variable_id)
//    {
//        $conditions[] = new EqualityCondition(VariableTranslation :: PROPERTY_VARIABLE_ID, $variable_id);
//        $subcondition = new EqualityCondition(PackageLanguage :: PROPERTY_ENGLISH_NAME, 'english');
//        $conditions[] = new SubSelectcondition(VariableTranslation :: PROPERTY_LANGUAGE_ID, PackageLanguage :: PROPERTY_ID, PackageLanguage :: get_table_name(), $subcondition);
//        $condition = new AndCondition($conditions);
//
//        return $this->retrieve_object(VariableTranslation :: get_table_name(), $condition, null, VariableTranslation :: CLASS_NAME);
//    }
//
//    function can_language_be_locked($language)
//    {
//        $conditions[] = new EqualityCondition(VariableTranslation :: PROPERTY_LANGUAGE_ID, $language->get_id());
//        $conditions[] = new EqualityCondition(VariableTranslation :: PROPERTY_STATUS, VariableTranslation :: STATUS_NORMAL);
//        $condition = new AndCondition($conditions);
//
//        return ($this->count_variable_translations($condition) > 0);
//    }
//
//    function can_language_be_unlocked($language)
//    {
//        $conditions[] = new EqualityCondition(VariableTranslation :: PROPERTY_LANGUAGE_ID, $language->get_id());
//        $conditions[] = new EqualityCondition(VariableTranslation :: PROPERTY_STATUS, VariableTranslation :: STATUS_BLOCKED);
//        $condition = new AndCondition($conditions);
//
//        return ($this->count_variable_translations($condition) > 0);
//    }
//
//    function can_language_pack_be_locked($language_pack, $language_id)
//    {
//        $conditions[] = new EqualityCondition(VariableTranslation :: PROPERTY_LANGUAGE_ID, $language_id);
//        $conditions[] = new EqualityCondition(VariableTranslation :: PROPERTY_STATUS, VariableTranslation :: STATUS_NORMAL);
//
//        /*$subcondition = new EqualityCondition(Variable :: PROPERTY_LANGUAGE_PACK_ID, $language_pack->get_id());
//		$conditions[] = new SubSelectCondition(VariableTranslation :: PROPERTY_VARIABLE_ID, Variable :: PROPERTY_ID, 'package_' . Variable :: get_table_name(), $subcondition);*/
//        $conditions[] = new InCondition(VariableTranslation :: PROPERTY_VARIABLE_ID, $this->retrieve_variables_from_language_pack($language_pack->get_id()));
//
//        $condition = new AndCondition($conditions);
//
//        return ($this->count_variable_translations($condition) > 0);
//    }
//
//    function can_language_pack_be_unlocked($language_pack, $language_id)
//    {
//        $conditions[] = new EqualityCondition(VariableTranslation :: PROPERTY_LANGUAGE_ID, $language_id);
//        $conditions[] = new EqualityCondition(VariableTranslation :: PROPERTY_STATUS, VariableTranslation :: STATUS_BLOCKED);
//
//        /*$subcondition = new EqualityCondition(Variable :: PROPERTY_LANGUAGE_PACK_ID, $language_pack->get_id());
//		$conditions[] = new SubSelectCondition(VariableTranslation :: PROPERTY_VARIABLE_ID, Variable :: PROPERTY_ID, 'package_' . Variable :: get_table_name(), $subcondition);*/
//        $conditions[] = new InCondition(VariableTranslation :: PROPERTY_VARIABLE_ID, $this->retrieve_variables_from_language_pack($language_pack->get_id()));
//
//        $condition = new AndCondition($conditions);
//
//        return ($this->count_variable_translations($condition) > 0);
//    }
//
//    function get_progress_for_language($language)
//    {
//        $condition = new EqualityCondition(VariableTranslation :: PROPERTY_LANGUAGE_ID, $language->get_id());
//        $total_languages = $this->count_variable_translations($condition);
//
//        $conditions[] = new EqualityCondition(VariableTranslation :: PROPERTY_LANGUAGE_ID, $language->get_id());
//        $conditions[] = new EqualityCondition(VariableTranslation :: PROPERTY_TRANSLATED, 1);
//        $condition = new AndCondition($conditions);
//
//        $translated_variables = $this->count_variable_translations($condition);
//
//        return (int) (($translated_variables / $total_languages) * 100);
//    }
//
//    function get_progress_for_language_pack($language_pack, $language_id = null)
//    {
//        $variable_translation_alias = $this->get_alias(VariableTranslation :: get_table_name());
//        $variable_translation_table = $this->escape_table_name(VariableTranslation :: get_table_name());
//        $variable_alias = $this->get_alias(Variable :: get_table_name());
//        $variable_table = $this->escape_table_name(Variable :: get_table_name());
//
//        if (! is_null($language_id))
//        {
//            $conditions[] = new EqualityCondition(VariableTranslation :: PROPERTY_LANGUAGE_ID, $language_id);
//        }
//
//        /*$subcondition = new EqualityCondition(Variable :: PROPERTY_LANGUAGE_PACK_ID, $language_pack->get_id());
//		$conditions[] = new SubselectCondition(VariableTranslation :: PROPERTY_VARIABLE_ID, Variable :: PROPERTY_ID, 'package_' . Variable :: get_table_name(), $subcondition);*/
//        $conditions[] = new InCondition(VariableTranslation :: PROPERTY_VARIABLE_ID, $this->retrieve_variables_from_language_pack($language_pack->get_id()));
//
//        $condition = new AndCondition($conditions);
//
//        /*$query = 'SELECT COUNT(*) FROM ' . $variable_translation_table . ' AS ' . $variable_translation_alias . ' ' .
//			     'JOIN ' . $variable_table . ' AS ' . $variable_alias . ' ON ' . $variable_translation_alias .
//			     '.variable_id = ' . $variable_alias . '.id';*/
//
//        $total_languages = $this->count_objects(VariableTranslation :: get_table_name(), $condition);
//
//        $conditions = array();
//
//        if (! is_null($language_id))
//        {
//            $conditions[] = new EqualityCondition(VariableTranslation :: PROPERTY_LANGUAGE_ID, $language_id);
//        }
//        $conditions[] = new EqualityCondition(VariableTranslation :: PROPERTY_TRANSLATED, 1);
//
//        /*$subcondition = new EqualityCondition(Variable :: PROPERTY_LANGUAGE_PACK_ID, $language_pack->get_id());
//		$conditions[] = new SubselectCondition(VariableTranslation :: PROPERTY_VARIABLE_ID, Variable :: PROPERTY_ID, 'package_' . Variable :: get_table_name(), $subcondition);*/
//        $conditions[] = new InCondition(VariableTranslation :: PROPERTY_VARIABLE_ID, $this->retrieve_variables_from_language_pack($language_pack->get_id()));
//
//        $condition = new AndCondition($conditions);
//
//        $translated_variables = $this->count_objects(VariableTranslation :: get_table_name(), $condition);
//
//        if ($total_languages != 0 || $translated_variables != 0)
//        {
//            return (int) (($translated_variables / $total_languages) * 100);
//        }
//        else
//        {
//            return 100;
//        }
//    }
//
//    function get_status_for_language($language)
//    {
//        $conditions[] = new EqualityCondition(VariableTranslation :: PROPERTY_LANGUAGE_ID, $language->get_id());
//        $conditions[] = new EqualityCondition(VariableTranslation :: PROPERTY_TRANSLATED, 1);
//        $conditions[] = new EqualityCondition(VariableTranslation :: PROPERTY_STATUS, VariableTranslation :: STATUS_OUTDATED);
//        $condition = new AndCondition($conditions);
//
//        return $this->count_variable_translations($condition);
//    }
//
//    function get_status_for_language_pack($language_pack, $language_id = null)
//    {
//        $variable_translation_alias = $this->get_alias(VariableTranslation :: get_table_name());
//        $variable_translation_table = $this->escape_table_name(VariableTranslation :: get_table_name());
//        $variable_alias = $this->get_alias(Variable :: get_table_name());
//        $variable_table = $this->escape_table_name(Variable :: get_table_name());
//
//        /*$query = 'SELECT COUNT(*) FROM ' . $variable_translation_table . ' AS ' . $variable_translation_alias . ' ' .
//			     'JOIN ' . $variable_table . ' AS ' . $variable_alias . ' ON ' . $this->escape_column_name(VariableTranslation :: PROPERTY_VARIABLE_ID, $variable_translation_alias) .
//			     ' = ' . $this->escape_column_name(Variable :: PROPERTY_ID, $variable_alias);*/
//
//        $conditions = array();
//
//        if (! is_null($language_id))
//        {
//            $conditions[] = new EqualityCondition(VariableTranslation :: PROPERTY_LANGUAGE_ID, $language_id);
//        }
//        $conditions[] = new EqualityCondition(VariableTranslation :: PROPERTY_TRANSLATED, 1);
//        $conditions[] = new EqualityCondition(VariableTranslation :: PROPERTY_STATUS, VariableTranslation :: STATUS_OUTDATED);
//
//        /*$subcondition = new EqualityCondition(Variable :: PROPERTY_LANGUAGE_PACK_ID, $language_pack->get_id());
//		$conditions[] = new SubselectCondition(VariableTranslation :: PROPERTY_VARIABLE_ID, Variable :: PROPERTY_ID, 'package_' . Variable :: get_table_name(), $subcondition);*/
//        $conditions[] = new InCondition(VariableTranslation :: PROPERTY_VARIABLE_ID, $this->retrieve_variables_from_language_pack($language_pack->get_id()));
//
//        $condition = new AndCondition($conditions);
//
//        return $this->count_objects(VariableTranslation :: get_table_name(), $condition);
//    }
//
//    function get_next_translator_application_id()
//    {
//        return $this->get_next_id(TranslatorApplication :: get_table_name());
//    }
//
//    function create_translator_application($translator_application)
//    {
//        return $this->create($translator_application);
//    }
//
//    function update_translator_application($translator_application)
//    {
//        $condition = new EqualityCondition(TranslatorApplication :: PROPERTY_ID, $translator_application->get_id());
//        return $this->update($translator_application, $condition);
//    }
//
//    function delete_translator_application($translator_application)
//    {
//        $condition = new EqualityCondition(TranslatorApplication :: PROPERTY_ID, $translator_application->get_id());
//        return $this->delete($translator_application->get_table_name(), $condition);
//    }
//
//    function count_translator_applications($condition = null)
//    {
//        return $this->count_objects(TranslatorApplication :: get_table_name(), $condition);
//    }
//
//    function retrieve_translator_application($id)
//    {
//        $condition = new EqualityCondition(TranslatorApplication :: PROPERTY_ID, $id);
//        return $this->retrieve_object(TranslatorApplication :: get_table_name(), $condition, null, TranslatorApplication :: CLASS_NAME);
//    }
//
//    function retrieve_translator_applications($condition = null, $offset = null, $max_objects = null, $order_by = null)
//    {
//        $translator_application_alias = $this->get_alias(TranslatorApplication :: get_table_name());
//        $translator_application_table = $this->escape_table_name(TranslatorApplication :: get_table_name());
//        $package_language_alias = $this->get_alias(PackageLanguage :: get_table_name());
//        $package_language_table = $this->escape_table_name(PackageLanguage :: get_table_name());
//
//        $udm = UserDataManager :: get_instance();
//        $user_alias = $udm->get_alias(User :: get_table_name());
//        $user_table = $udm->escape_table_name(User :: get_table_name());
//
//        $query = 'SELECT ' . $translator_application_alias . '.* FROM ' . $translator_application_table . ' AS ' . $translator_application_alias;
//        $query .= ' JOIN ' . $package_language_table . ' AS ' . $package_language_alias . ' ON ' . $translator_application_alias . '.source_language_id = ' . $package_language_alias . '.id';
//        $query .= ' JOIN ' . $package_language_table . ' AS ' . $package_language_alias . '2 ON ' . $translator_application_alias . '.destination_language_id = ' . $package_language_alias . '2.id';
//        $query .= ' JOIN ' . $user_table . ' AS ' . $user_alias . ' ON ' . $translator_application_alias . '.user_id = ' . $user_alias . '.id';
//
//        return $this->retrieve_object_set($query, TranslatorApplication :: get_table_name(), $condition, $offset, $max_objects, $order_by, TranslatorApplication :: CLASS_NAME);
//    }
//
//    function get_number_of_translations_for_user($user_id)
//    {
//        $condition = new EqualityCondition(VariableTranslation :: PROPERTY_USER_ID, $user_id);
//        return $this->count_variable_translations($condition);
//    }
//
//    function get_number_of_translations_by_user()
//    {
//        $user_id_column = $this->escape_column_name(VariableTranslation :: PROPERTY_USER_ID);
//        $variable_translation_table = $this->escape_table_name(VariableTranslation :: get_table_name());
//
//        $query = 'SELECT ' . $user_id_column . ', COUNT(*) AS count FROM ' . $variable_translation_table . ' GROUP BY ' . $user_id_column . ' ORDER BY count DESC;';
//
//        $number_of_translations = array();
//
//        $result = $this->query($query);
//        while ($record = $result->fetchRow(MDB2_FETCHMODE_ASSOC))
//        {
//            $user = $record[VariableTranslation :: PROPERTY_USER_ID];
//            $user = $user ? $user : 0;
//            $number_of_translations[$user] = $record['count'];
//        }
//
//        $result->free();
//
//        return $number_of_translations;
//    }
//
//    function create_historic_variable_translation($historic_variable_translation)
//    {
//        return $this->create($historic_variable_translation);
//    }
//
//    function delete_historic_variable_translation($historic_variable_translation)
//    {
//        $condition = new EqualityCondition(HistoricVariableTranslation :: PROPERTY_ID, $historic_variable_translation->get_id());
//        return $this->delete($historic_variable_translation->get_table_name(), $condition);
//    }
//
//    function update_historic_variable_translation($historic_variable_translation)
//    {
//        $condition = new EqualityCondition(HistoricVariableTranslation :: PROPERTY_ID, $historic_variable_translation->get_id());
//        return $this->update($historic_variable_translation, $condition);
//    }
//
//    function count_historic_variable_translations($condition = null)
//    {
//        return $this->count_objects(HistoricVariableTranslation :: get_table_name(), $condition);
//    }
//
//    function retrieve_historic_variable_translation($historic_variable_translation_id)
//    {
//        $condition = new EqualityCondition(HistoricVariableTranslation :: PROPERTY_ID, $historic_variable_translation_id);
//        return $this->retrieve_object(HistoricVariableTranslation :: get_table_name(), $condition, null, HistoricVariableTranslation :: CLASS_NAME);
//    }
//
//    function retrieve_historic_variable_translations($condition = null, $offset = null, $max_objects = null, $order_by = null)
//    {
//        return $this->retrieve_objects(HistoricVariableTranslation :: get_table_name(), $condition, $offset, $max_objects, $order_by, HistoricVariableTranslation :: CLASS_NAME);
//    }
//
//    function retrieve_first_untranslated_variable_translation($language_id, $language_pack_id = null, $status = null)
//    {
//        $conditions = array();
//
//        if ($status)
//        {
//            $conditions[] = new EqualityCondition(VariableTranslation :: PROPERTY_STATUS, $status);
//        }
//
//        $conditions[] = new EqualityCondition(VariableTranslation :: PROPERTY_LANGUAGE_ID, $language_id);
//        $conditions[] = new EqualityCondition(VariableTranslation :: PROPERTY_TRANSLATED, 0);
//
//        if ($language_pack_id)
//        {
//            /*$subcondition = new EqualityCondition(Variable :: PROPERTY_LANGUAGE_PACK_ID, $language_pack_id);
//			$conditions[] = new SubselectCondition(VariableTranslation :: PROPERTY_VARIABLE_ID, Variable :: PROPERTY_ID, 'package_' . Variable :: get_table_name(), $subcondition);*/
//            $conditions[] = new InCondition(VariableTranslation :: PROPERTY_VARIABLE_ID, $this->retrieve_variables_from_language_pack($language_pack_id));
//        }
//
//        $condition = new AndCondition($conditions);
//        return $this->retrieve_objects(VariableTranslation :: get_table_name(), $condition, 0, 1, null, VariableTranslation :: CLASS_NAME)->next_result();
//    }
//
//    private function retrieve_variables_from_language_pack($language_pack_id)
//    {
//        if (! array_key_exists($language_pack_id, $this->variable_ids))
//        {
//            $condition = new EqualityCondition(Variable :: PROPERTY_LANGUAGE_PACK_ID, $language_pack_id);
//            $variables = $this->retrieve_variables($condition);
//
//            $ids = array();
//            while ($variable = $variables->next_result())
//            {
//                $ids[] = $variable->get_id();
//            }
//
//            $this->variable_ids[$language_pack_id] = $ids;
//        }
//
//        return $this->variable_ids[$language_pack_id];
//    }
}
?>