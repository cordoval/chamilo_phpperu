<?php
/**
 * @package cda.datamanager
 */

require_once 'MDB2.php';

/**
 *	This is a data manager that uses a database for storage. It was written
 *	for MySQL, but should be compatible with most SQL flavors.
 *
 *  @author Sven Vanpoucke
 *  @author 
 */

class DatabaseCdaDataManager extends CdaDataManager
{
	private $database;

	function initialize()
	{
		$aliases = array();
		$aliases[CdaLanguage :: get_table_name()] = 'cdge';
		$aliases[LanguagePack :: get_table_name()] = 'lack';
		$aliases[Variable :: get_table_name()] = 'vale';
		$aliases[VariableTranslation :: get_table_name()] = 'vaon';

		$this->database = new Database($aliases);
		$this->database->set_prefix('cda_');
	}

	function create_storage_unit($name, $properties, $indexes)
	{
		return $this->database->create_storage_unit($name, $properties, $indexes);
	}

	function get_next_cda_language_id()
	{
		return $this->database->get_next_id(CdaLanguage :: get_table_name());
	}

	function create_cda_language($cda_language)
	{
		return $this->database->create($cda_language);
	}

	function update_cda_language($cda_language)
	{
		$condition = new EqualityCondition(CdaLanguage :: PROPERTY_ID, $cda_language->get_id());
		return $this->database->update($cda_language, $condition);
	}

	function delete_cda_language($cda_language)
	{
		$condition = new EqualityCondition(CdaLanguage :: PROPERTY_ID, $cda_language->get_id());
		return $this->database->delete($cda_language->get_table_name(), $condition);
	}

	function count_cda_languages($condition = null)
	{
		return $this->database->count_objects(CdaLanguage :: get_table_name(), $condition);
	}

	function retrieve_cda_language($id)
	{
		$condition = new EqualityCondition(CdaLanguage :: PROPERTY_ID, $id);
		return $this->database->retrieve_object(CdaLanguage :: get_table_name(), $condition);
	}
	
	function retrieve_cda_language_english()
	{
		$conditions[] = new EqualityCondition(CdaLanguage :: PROPERTY_ENGLISH_NAME, 'english');
		$conditions[] = new EqualityCondition(CdaLanguage :: PROPERTY_ENGLISH_NAME, 'english_org');
		$condition = new OrCondition($conditions);
		
		return $this->database->retrieve_objects(CdaLanguage :: get_table_name(), $condition, 0, 1)->next_result();
	}

	function retrieve_cda_languages($condition = null, $offset = null, $max_objects = null, $order_by = null)
	{
		return $this->database->retrieve_objects(CdaLanguage :: get_table_name(), $condition, $offset, $max_objects, $order_by);
	}

	function get_next_language_pack_id()
	{
		return $this->database->get_next_id(LanguagePack :: get_table_name());
	}

	function create_language_pack($language_pack)
	{
		return $this->database->create($language_pack);
	}

	function update_language_pack($language_pack)
	{
		$condition = new EqualityCondition(LanguagePack :: PROPERTY_ID, $language_pack->get_id());
		return $this->database->update($language_pack, $condition);
	}

	function delete_language_pack($language_pack)
	{
		$condition = new EqualityCondition(LanguagePack :: PROPERTY_ID, $language_pack->get_id());
		return $this->database->delete($language_pack->get_table_name(), $condition);
	}

	function count_language_packs($condition = null)
	{
		return $this->database->count_objects(LanguagePack :: get_table_name(), $condition);
	}

	function retrieve_language_pack($id)
	{
		$condition = new EqualityCondition(LanguagePack :: PROPERTY_ID, $id);
		return $this->database->retrieve_object(LanguagePack :: get_table_name(), $condition);
	}

	function retrieve_language_packs($condition = null, $offset = null, $max_objects = null, $order_by = null)
	{
		return $this->database->retrieve_objects(LanguagePack :: get_table_name(), $condition, $offset, $max_objects, $order_by);
	}

	function get_next_variable_id()
	{
		return $this->database->get_next_id(Variable :: get_table_name());
	}

	function create_variable($variable)
	{
		return $this->database->create($variable);
	}

	function update_variable($variable)
	{
		$condition = new EqualityCondition(Variable :: PROPERTY_ID, $variable->get_id());
		return $this->database->update($variable, $condition);
	}

	function delete_variable($variable)
	{
		$condition = new EqualityCondition(Variable :: PROPERTY_ID, $variable->get_id());
		return $this->database->delete($variable->get_table_name(), $condition);
	}

	function count_variables($condition = null)
	{
		return $this->database->count_objects(Variable :: get_table_name(), $condition);
	}

	function retrieve_variable($id)
	{
		$condition = new EqualityCondition(Variable :: PROPERTY_ID, $id);
		return $this->database->retrieve_object(Variable :: get_table_name(), $condition);
	}

	function retrieve_variables($condition = null, $offset = null, $max_objects = null, $order_by = null)
	{
		return $this->database->retrieve_objects(Variable :: get_table_name(), $condition, $offset, $max_objects, $order_by);
	}

	function create_variable_translation($variable_translation)
	{
		return $this->database->create($variable_translation);
	}
	
	function delete_variable_translation($variable_translation)
	{
		$conditions[] = new EqualityCondition(VariableTranslation :: PROPERTY_LANGUAGE_ID, $variable_translation->get_language_id());
		$conditions[] = new EqualityCondition(VariableTranslation :: PROPERTY_VARIABLE_ID, $variable_translation->get_variable_id());
		$condition = new AndCondition($conditions);
		return $this->database->delete($variable_translation->get_table_name(), $condition);
	}
	
	function update_variable_translation($variable_translation)
	{
		$conditions[] = new EqualityCondition(VariableTranslation :: PROPERTY_LANGUAGE_ID, $variable_translation->get_language_id());
		$conditions[] = new EqualityCondition(VariableTranslation :: PROPERTY_VARIABLE_ID, $variable_translation->get_variable_id());
		$condition = new AndCondition($conditions);
		return $this->database->update($variable_translation, $condition);
	}

	function count_variable_translations($condition = null)
	{
		return $this->database->count_objects(VariableTranslation :: get_table_name(), $condition);
	}

	function retrieve_variable_translation($language_id, $variable_id)
	{
		$conditions[] = new EqualityCondition(VariableTranslation :: PROPERTY_LANGUAGE_ID, $language_id);
		$conditions[] = new EqualityCondition(VariableTranslation :: PROPERTY_VARIABLE_ID, $variable_id);
		$condition = new AndCondition($conditions);
		
		return $this->database->retrieve_object(VariableTranslation :: get_table_name(), $condition);
	}

	function retrieve_variable_translations($condition = null, $offset = null, $max_objects = null, $order_by = null)
	{
		$variable_translation_alias = $this->database->get_alias(VariableTranslation :: get_table_name());
		$variable_translation_table = $this->database->escape_table_name(VariableTranslation :: get_table_name());
		$variable_alias = $this->database->get_alias(Variable :: get_table_name());
		$variable_table = $this->database->escape_table_name(Variable :: get_table_name());

        $query = 'SELECT ' . $variable_translation_alias . '.* FROM ' . $variable_translation_table . ' AS ' . $variable_translation_alias;
        $query .= ' JOIN ' . $variable_table . ' AS ' . $variable_alias . ' ON ' . $variable_translation_alias . '.variable_id = ' . $variable_alias . '.id';

        return $this->database->retrieve_object_set($query, VariableTranslation :: get_table_name(), $condition, $offset, $max_objects, $order_by);
	}

	function retrieve_english_translation($variable_id)
	{
		$subconditions[] = new EqualityCondition(CdaLanguage :: PROPERTY_ENGLISH_NAME, 'english');
		$subconditions[] = new EqualityCondition(CdaLanguage :: PROPERTY_ENGLISH_NAME, 'english_org');
		$subcondition = new OrCondition($subconditions);
		$conditions[] = new SubSelectcondition(VariableTranslation :: PROPERTY_LANGUAGE_ID, CdaLanguage :: PROPERTY_ID, 'cda_' . CdaLanguage :: get_table_name(), $subcondition);
		$conditions[] = new EqualityCondition(VariableTranslation :: PROPERTY_VARIABLE_ID, $variable_id);
		$condition = new AndCondition($conditions);
		
		return $this->database->retrieve_object(VariableTranslation :: get_table_name(), $condition);
	}
	
	function can_language_be_locked($language)
	{
		$conditions[] = new EqualityCondition(VariableTranslation :: PROPERTY_LANGUAGE_ID, $language->get_id());
		$conditions[] = new EqualityCondition(VariableTranslation :: PROPERTY_STATUS, VariableTranslation :: STATUS_NORMAL);
		$condition = new AndCondition($conditions);
	
		return ($this->count_variable_translations($condition) > 0);
	}
	
	function can_language_be_unlocked($language)
	{
		$conditions[] = new EqualityCondition(VariableTranslation :: PROPERTY_LANGUAGE_ID, $language->get_id());
		$conditions[] = new EqualityCondition(VariableTranslation :: PROPERTY_STATUS, VariableTranslation :: STATUS_BLOCKED);
		$condition = new AndCondition($conditions);
		
		return ($this->count_variable_translations($condition) > 0);
	}
	
	function can_language_pack_be_locked($language_pack, $language_id)
	{
		$subcondition = new EqualityCondition(Variable :: PROPERTY_LANGUAGE_PACK_ID, $language_pack->get_id());
		$conditions[] = new SubSelectCondition(VariableTranslation :: PROPERTY_VARIABLE_ID, Variable :: PROPERTY_ID, 'cda_' . Variable :: get_table_name(), $subcondition);
		$conditions[] = new EqualityCondition(VariableTranslation :: PROPERTY_LANGUAGE_ID, $language_id);
		$conditions[] = new EqualityCondition(VariableTranslation :: PROPERTY_STATUS, VariableTranslation :: STATUS_NORMAL);
		$condition = new AndCondition($conditions);
		
		return ($this->count_variable_translations($condition) > 0);
	}
	
	function can_language_pack_be_unlocked($language_pack, $language_id)
	{
		$subcondition = new EqualityCondition(Variable :: PROPERTY_LANGUAGE_PACK_ID, $language_pack->get_id());
		$conditions[] = new SubSelectCondition(VariableTranslation :: PROPERTY_VARIABLE_ID, Variable :: PROPERTY_ID, 'cda_' . Variable :: get_table_name(), $subcondition);
		$conditions[] = new EqualityCondition(VariableTranslation :: PROPERTY_LANGUAGE_ID, $language_id);
		$conditions[] = new EqualityCondition(VariableTranslation :: PROPERTY_STATUS, VariableTranslation :: STATUS_BLOCKED);
		$condition = new AndCondition($conditions);
		
		return ($this->count_variable_translations($condition) > 0);
	}
	
	function get_progress_for_language($language)
	{
		$condition = new EqualityCondition(VariableTranslation :: PROPERTY_LANGUAGE_ID, $language->get_id());
		$total_languages = $this->count_variable_translations($condition);
		
		$conditions[] = new EqualityCondition(VariableTranslation :: PROPERTY_LANGUAGE_ID, $language->get_id());
		$conditions[] = new NotCondition(new EqualityCondition(VariableTranslation :: PROPERTY_TRANSLATION, ' '));
		$condition = new AndCondition($conditions);
		
		$translated_variables = $this->count_variable_translations($condition);
		
		return (int)(($translated_variables / $total_languages) * 100);
	}
	
	function get_progress_for_language_pack($language_pack, $language_id = null)
	{
		$variable_translation_alias = $this->database->get_alias(VariableTranslation :: get_table_name());
		$variable_translation_table = $this->database->escape_table_name(VariableTranslation :: get_table_name());
		$variable_alias = $this->database->get_alias(Variable :: get_table_name());
		$variable_table = $this->database->escape_table_name(Variable :: get_table_name());
		
		$conditions[] = new EqualityCondition(Variable :: PROPERTY_LANGUAGE_PACK_ID, $language_pack->get_id(), Variable :: get_table_name());
		if (!is_null($language_id))
		{
			$conditions[] = new EqualityCondition(VariableTranslation :: PROPERTY_LANGUAGE_ID, $language_id);
		}
		$condition = new AndCondition($conditions);
		
		$query = 'SELECT COUNT(*) FROM ' . $variable_translation_table . ' AS ' . $variable_translation_alias . ' ' .
			     'JOIN ' . $variable_table . ' AS ' . $variable_alias . ' ON ' . $variable_translation_alias . 
			     '.variable_id = ' . $variable_alias . '.id';
		
		$total_languages = $this->database->count_result_set($query, VariableTranslation :: get_table_name(), $condition);
		
		$conditions = array();
		$conditions[] = new EqualityCondition(Variable :: PROPERTY_LANGUAGE_PACK_ID, $language_pack->get_id(), Variable :: get_table_name());
		if (!is_null($language_id))
		{
			$conditions[] = new EqualityCondition(VariableTranslation :: PROPERTY_LANGUAGE_ID, $language_id);
		}
		$conditions[] = new NotCondition(new EqualityCondition(VariableTranslation :: PROPERTY_TRANSLATION, ' '));
		$condition = new AndCondition($conditions);
		
		$translated_variables = $this->database->count_result_set($query, VariableTranslation :: get_table_name(), $condition);
		
		if($total_languages != 0 || $translated_variables != 0)
		{
			return (int)(($translated_variables / $total_languages) * 100);
		}
		else
		{
			return 100;
		}
	}
				
	function get_alias($table_name)
	{
		return $this->database->get_alias($table_name);
	}
	
	function get_next_translator_application_id()
	{
		return $this->database->get_next_id(TranslatorApplication :: get_table_name());
	}

	function create_translator_application($translator_application)
	{
		return $this->database->create($translator_application);
	}

	function update_translator_application($translator_application)
	{
		$condition = new EqualityCondition(TranslatorApplication :: PROPERTY_ID, $translator_application->get_id());
		return $this->database->update($translator_application, $condition);
	}

	function delete_translator_application($translator_application)
	{
		$condition = new EqualityCondition(TranslatorApplication :: PROPERTY_ID, $translator_application->get_id());
		return $this->database->delete($translator_application->get_table_name(), $condition);
	}

	function count_translator_applications($condition = null)
	{
		return $this->database->count_objects(TranslatorApplication :: get_table_name(), $condition);
	}

	function retrieve_translator_application($id)
	{
		$condition = new EqualityCondition(TranslatorApplication :: PROPERTY_ID, $id);
		return $this->database->retrieve_object(TranslatorApplication :: get_table_name(), $condition);
	}

	function retrieve_translator_applications($condition = null, $offset = null, $max_objects = null, $order_by = null)
	{
		$translator_application_alias = $this->database->get_alias(TranslatorApplication :: get_table_name());
		$translator_application_table = $this->database->escape_table_name(TranslatorApplication :: get_table_name());
		$cda_language_alias = $this->database->get_alias(CdaLanguage :: get_table_name());
		$cda_language_table = $this->database->escape_table_name(CdaLanguage :: get_table_name());
		
		$udm = UserDataMAnager :: get_instance();
		$user_alias = $udm->get_database()->get_alias(User :: get_table_name());
		$user_table = $udm->get_database()->escape_table_name(User :: get_table_name());
		
        $query = 'SELECT ' . $translator_application_alias . '.* FROM ' . $translator_application_table . ' AS ' . $translator_application_alias;
        $query .= ' JOIN ' . $cda_language_table . ' AS ' . $cda_language_alias . ' ON ' . $translator_application_alias . '.source_language_id = ' . $cda_language_alias . '.id';
        $query .= ' JOIN ' . $cda_language_table . ' AS ' . $cda_language_alias . '2 ON ' . $translator_application_alias . '.destination_language_id = ' . $cda_language_alias . '2.id';
        $query .= ' JOIN ' . $user_table . ' AS ' . $user_alias . ' ON ' . $translator_application_alias . '.user_id = ' . $user_alias . '.id';

        return $this->database->retrieve_object_set($query, TranslatorApplication :: get_table_name(), $condition, $offset, $max_objects, $order_by);
	}
	
	function get_number_of_translations_for_user($user_id)
	{
		$condition = new EqualityCondition(VariableTranslation :: PROPERTY_USER_ID, $user_id);
		return $this->count_variable_translations($condition);
	}
	
	function get_number_of_translations_by_user()
	{
		$user_id_column = $this->database->escape_column_name(VariableTranslation :: PROPERTY_USER_ID);
		$variable_translation_table = $this->database->escape_table_name(VariableTranslation :: get_table_name());
		
		$query = 'SELECT ' . $user_id_column . ', COUNT(*) AS count FROM ' . $variable_translation_table . ' GROUP BY ' . $user_id_column . ' ORDER BY count DESC;';
		
		$number_of_translations = array();
		
		$result = $this->database->query($query);
		while($record = $result->fetchRow(MDB2_FETCHMODE_ASSOC))
        {
        	$user = $record[VariableTranslation :: PROPERTY_USER_ID];
        	$user = $user ? $user : 0;
        	$number_of_translations[$user] = $record['count'];
        }
        
        return $number_of_translations;
	}
}
?>
