<?php
/**
 * @package cda.datamanager
 */
require_once dirname(__FILE__).'/../cda_language.class.php';
require_once dirname(__FILE__).'/../language_pack.class.php';
require_once dirname(__FILE__).'/../variable.class.php';
require_once dirname(__FILE__).'/../variable_translation.class.php';
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

	function get_next_variable_translation_id()
	{
		return $this->database->get_next_id(VariableTranslation :: get_table_name());
	}

	function create_variable_translation($variable_translation)
	{
		return $this->database->create($variable_translation);
	}

	function update_variable_translation($variable_translation)
	{
		$condition = new EqualityCondition(VariableTranslation :: PROPERTY_ID, $variable_translation->get_id());
		return $this->database->update($variable_translation, $condition);
	}

	function delete_variable_translation($variable_translation)
	{
		$condition = new EqualityCondition(VariableTranslation :: PROPERTY_ID, $variable_translation->get_id());
		return $this->database->delete($variable_translation->get_table_name(), $condition);
	}

	function count_variable_translations($condition = null)
	{
		return $this->database->count_objects(VariableTranslation :: get_table_name(), $condition);
	}

	function retrieve_variable_translation($id)
	{
		$condition = new EqualityCondition(VariableTranslation :: PROPERTY_ID, $id);
		return $this->database->retrieve_object(VariableTranslation :: get_table_name(), $condition);
	}

	function retrieve_variable_translations($condition = null, $offset = null, $max_objects = null, $order_by = null)
	{
		return $this->database->retrieve_objects(VariableTranslation :: get_table_name(), $condition, $offset, $max_objects, $order_by);
	}

}
?>