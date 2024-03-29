<?php

namespace application\cda;

use common\libraries\ObjectTableCellRenderer;
use common\libraries\LocalSetting;
use common\libraries\Translation;
/**
 * @package cda.tables.variable_translation_table
 */
/**
 * Default cell renderer for the variable_translation table
 *
 * @author Sven Vanpoucke
 * @author Hans De Bisschop
 */
class DefaultVariableTranslationTableCellRenderer extends ObjectTableCellRenderer
{
	/**
	 * Constructor
	 */
	function __construct()
	{
	}

	/**
	 * Renders a table cell
	 * @param ContentObjectTableColumnModel $column The column which should be
	 * rendered
	 * @param VariableTranslation $variable_translation - The variable_translation
	 * @return string A HTML representation of the rendered table cell
	 */
	function render_cell($column, $variable_translation)
	{
		switch ($column->get_name())
		{
			case VariableTranslation :: PROPERTY_LANGUAGE_ID :
				return $variable_translation->get_language_id();
			case VariableTranslation :: PROPERTY_VARIABLE_ID :
				return $variable_translation->get_variable_id();
			case VariableTranslation :: PROPERTY_TRANSLATION :
				return $variable_translation->get_translation();
			case VariableTranslation :: PROPERTY_DATE :
				return $variable_translation->get_date();
			case VariableTranslation :: PROPERTY_USER_ID :
				return $variable_translation->get_user_id();
			case VariableTranslation :: PROPERTY_RATING :
				return $variable_translation->get_rating();
			case VariableTranslation :: PROPERTY_RATED :
				return $variable_translation->get_rated();
			case 'Status' :
				return $variable_translation->get_status_icon();
			case 'SourceTranslation' :
				$source_id = LocalSetting :: get('source_language', CdaManager :: APPLICATION_NAME);
				$translation = CdaDataManager :: get_instance()->retrieve_variable_translation_by_parameters($source_id, $variable_translation->get_variable_id());
				if($translation)
					return $translation->get_translation();
				else
					return  '&nbsp;';
			default :
				return '&nbsp;';
		}
	}

	function render_id_cell($object)
	{
		return $object->get_id();
	}
}
?>