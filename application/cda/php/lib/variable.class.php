<?php

namespace application\cda;

use common\libraries\DataClass;
use common\libraries\Utilities;
use common\libraries\EqualityCondition;
/**
 * cda
 */

/**
 * This class describes a Variable data object
 * @author Sven Vanpoucke
 * @author Hans De Bisschop
 */
class Variable extends DataClass
{
	const CLASS_NAME = __CLASS__;

	/**
	 * Variable properties
	 */
	const PROPERTY_ID = 'id';
	const PROPERTY_VARIABLE = 'variable';
	const PROPERTY_LANGUAGE_PACK_ID = 'language_pack_id';

	/**
	 * Get the default properties
	 * @return array The property names.
	 */
	static function get_default_property_names()
	{
		return array (self :: PROPERTY_ID, self :: PROPERTY_VARIABLE, self :: PROPERTY_LANGUAGE_PACK_ID);
	}

	function get_data_manager()
	{
		return CdaDataManager :: get_instance();
	}

	/**
	 * Returns the id of this Variable.
	 * @return the id.
	 */
	function get_id()
	{
		return $this->get_default_property(self :: PROPERTY_ID);
	}

	/**
	 * Sets the id of this Variable.
	 * @param id
	 */
	function set_id($id)
	{
		$this->set_default_property(self :: PROPERTY_ID, trim($id));
	}

	/**
	 * Returns the variable of this Variable.
	 * @return the variable.
	 */
	function get_variable()
	{
		return $this->get_default_property(self :: PROPERTY_VARIABLE);
	}

	/**
	 * Sets the variable of this Variable.
	 * @param variable
	 */
	function set_variable($variable)
	{
		$this->set_default_property(self :: PROPERTY_VARIABLE, $variable);
	}

	/**
	 * Returns the language_pack_id of this Variable.
	 * @return the language_pack_id.
	 */
	function get_language_pack_id()
	{
		return $this->get_default_property(self :: PROPERTY_LANGUAGE_PACK_ID);
	}

	/**
	 * Sets the language_pack_id of this Variable.
	 * @param language_pack_id
	 */
	function set_language_pack_id($language_pack_id)
	{
		$this->set_default_property(self :: PROPERTY_LANGUAGE_PACK_ID, $language_pack_id);
	}


	static function get_table_name()
	{
		return Utilities :: underscores_to_camelcase(Utilities :: get_classname_from_namespace(self :: CLASS_NAME));
	}
	
	function create()
	{
		$dm = $this->get_data_manager();
		
    	$condition = new EqualityCondition(Variable :: PROPERTY_LANGUAGE_PACK_ID, $this->get_language_pack_id());
		$variables = $dm->retrieve_variables($condition);
		while($var = $variables->next_result())
			if($var->get_variable() == $this->get_variable())
				return false;
				
		$succes = parent :: create();
		
		$languages = $dm->retrieve_cda_languages();
		
		while($language = $languages->next_result())
		{
			$translation = new VariableTranslation();
			$translation->set_user_id(0);
			$translation->set_language_id($language->get_id());
			$translation->set_variable_id($this->get_id());
			$translation->set_date(time());
			$translation->set_rated(0);
			$translation->set_rating(0);
			$translation->set_translation(' ');
			$translation->set_status(VariableTranslation :: STATUS_NORMAL);
			$succes &= $translation->create();
		}

		return $succes;
	}
	
	function update()
	{
		$dm = $this->get_data_manager();
		
		$conditions[] = new EqualityCondition(Variable :: PROPERTY_LANGUAGE_PACK_ID, $this->get_language_pack_id());
    	$conditions[] = new NotCondition(new EqualityCondition(Variable :: PROPERTY_ID, $this->get_id()));
    	$condition = new AndCondition($conditions);
		$variables = $dm->retrieve_variables($condition);
		while($var = $variables->next_result())
			if($var->get_variable() == $this->get_variable())
				return false;
		
		return parent :: update();
	}
	
	function delete()
	{
		$succes = parent :: delete();
		$dm = $this->get_data_manager();
		
		$condition = new EqualityCondition(VariableTranslation :: PROPERTY_VARIABLE_ID, $this->get_id());
		$translations = $dm->retrieve_variable_translations($condition);
		
		while($translation = $translations->next_result())
		{
			$succes &= $translation->delete();
		}
		
		return $succes;
		
	}
}

?>