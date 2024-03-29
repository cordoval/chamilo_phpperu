<?php

namespace application\cda;

use common\libraries\DataClass;
use common\libraries\Utilities;
use common\libraries\Translation;
use common\libraries\EqualityCondition;
use common\libraries\NotCondition;
use common\libraries\AndCondition;
use common\libraries\Theme;
/**
 * cda
 */

/**
 * This class describes a LanguagePack data object
 * @author Sven Vanpoucke
 * @author Hans De Bisschop
 */
class LanguagePack extends DataClass
{
	const CLASS_NAME = __CLASS__;

	/**
	 * LanguagePack properties
	 */
	const PROPERTY_ID = 'id';
	const PROPERTY_NAME = 'name';
	const PROPERTY_BRANCH = 'branch';
	const PROPERTY_TYPE = 'type';

	const TYPE_CORE = 1;
	const TYPE_APPLICATION = 2;

	const BRANCH_CLASSIC = 1;
	const BRANCH_LCMS = 2;

	/**
	 * Get the default properties
	 * @return array The property names.
	 */
	static function get_default_property_names()
	{
		return array (self :: PROPERTY_ID, self :: PROPERTY_NAME, self :: PROPERTY_BRANCH, self :: PROPERTY_TYPE);
	}

	function get_data_manager()
	{
		return CdaDataManager :: get_instance();
	}

	/**
	 * Returns the id of this LanguagePack.
	 * @return the id.
	 */
	function get_id()
	{
		return $this->get_default_property(self :: PROPERTY_ID);
	}

	/**
	 * Sets the id of this LanguagePack.
	 * @param id
	 */
	function set_id($id)
	{
		$this->set_default_property(self :: PROPERTY_ID, $id);
	}

	/**
	 * Returns the name of this LanguagePack.
	 * @return the name.
	 */
	function get_name()
	{
		return $this->get_default_property(self :: PROPERTY_NAME);
	}

	/**
	 * Sets the name of this LanguagePack.
	 * @param name
	 */
	function set_name($name)
	{
		$this->set_default_property(self :: PROPERTY_NAME, $name);
	}

	/**
	 * Returns the branch of this LanguagePack.
	 * @return the branch.
	 */
	function get_branch()
	{
		return $this->get_default_property(self :: PROPERTY_BRANCH);
	}

	/**
	 * Sets the branch of this LanguagePack.
	 * @param branch
	 */
	function set_branch($branch)
	{
		$this->set_default_property(self :: PROPERTY_BRANCH, $branch);
	}

	/**
	 * Returns the type of this LanguagePack.
	 * @return the type.
	 */
	function get_type()
	{
		return $this->get_default_property(self :: PROPERTY_TYPE);
	}

	/**
	 * Sets the type of this LanguagePack.
	 * @param type
	 */
	function set_type($type)
	{
		$this->set_default_property(self :: PROPERTY_TYPE, $type);
	}

	static function get_table_name()
	{
		return Utilities :: camelcase_to_underscores(Utilities :: get_classname_from_namespace(self :: CLASS_NAME));
	}

	function get_type_name()
	{
		switch($this->get_type())
		{
			case LanguagePack :: TYPE_CORE:
				return Translation :: get('Core', null, Utilities :: COMMON_LIBRARIES);
			default:
				return Translation :: get('Application', null, Utilities :: COMMON_LIBRARIES);
		}
	}

	function get_type_string()
	{
		switch($this->get_type())
		{
			case LanguagePack :: TYPE_CORE:
				return 'core';
			default:
				return 'application';
		}
	}

	function get_branch_name()
	{
		switch($this->get_branch())
		{
			case LanguagePack :: BRANCH_CLASSIC:
				return Translation :: get('ChamiloClassic');
			case LanguagePack :: BRANCH_LCMS:
				return Translation :: get('ChamiloLCMS');
			default:
				return Translation :: get('ChamiloLCMS');
		}
	}

	static function get_branch_options()
	{
		$options = array();

		$options[self :: BRANCH_CLASSIC] = Translation :: get('ChamiloClassic');
		$options[self :: BRANCH_LCMS] = Translation :: get('ChamiloLCMS');

		return $options;
	}

	function delete()
	{
		$succes = parent :: delete();
		$dm = $this->get_data_manager();

		$condition = new EqualityCondition(Variable :: PROPERTY_LANGUAGE_PACK_ID, $this->get_id());
		$variables = $dm->retrieve_variables($condition);

		while($variable = $variables->next_result())
		{
			$succes &= $variable->delete();
		}

		return $succes;

	}

	function update()
	{
		$dm = $this->get_data_manager();

		$conditions[] = new NotCondition(new EqualityCondition(LanguagePack :: PROPERTY_ID, $this->get_id()));
		$conditions[] = new EqualityCondition(LanguagePack :: PROPERTY_BRANCH, $this->get_branch());
		$condition = new AndCondition($conditions);

    	$language_packs = $dm->retrieve_language_packs($condition);
		while($lp = $language_packs->next_result())
		{
			if($lp->get_name() == $this->get_name())
				return false;
		}

		return parent :: update();
	}

	function create()
	{
		$dm = $this->get_data_manager();

		$condition = new EqualityCondition(LanguagePack :: PROPERTY_BRANCH, $this->get_branch());
    	$language_packs = $dm->retrieve_language_packs($condition);
		while($lp = $language_packs->next_result())
			if($lp->get_name() == $this->get_name())
				return false;

		return parent :: create();
	}

	function is_outdated($language_id = null)
	{
	    $count = $this->get_data_manager()->get_status_for_language_pack($this, $language_id);
	    return $count > 0;
	}

	function get_status_icon($language_id = null)
	{
		if ($this->is_outdated($language_id))
	    {
	        return '<img src="' . Theme :: get_image_path() . 'status_outdated.png" title="' . Translation :: get('OneOrMoreTranslationsOutdated') . '" alt="' . Translation :: get('OneOrMoreTranslationsOutdated') . '" />';
	    }
	    else
	    {
	        return '<img src="' . Theme :: get_image_path() . 'status_normal.png" title="' . Translation :: get('TranslationFinishedOrInProgress') . '" alt="' . Translation :: get('TranslationFinishedOrInProgress') . '" />';
	    }
	}
}

?>