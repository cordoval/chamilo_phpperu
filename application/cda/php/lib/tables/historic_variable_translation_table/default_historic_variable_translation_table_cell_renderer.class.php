<?php
/**
 * @package cda.tables.variable_translation_table
 */
/**
 * Default cell renderer for the historic_variable_translation table
 *
 * @author Sven Vanpoucke
 * @author Hans De Bisschop
 */
class DefaultHistoricVariableTranslationTableCellRenderer extends ObjectTableCellRenderer
{
	/**
	 * Constructor
	 */
	function DefaultHistoricVariableTranslationTableCellRenderer()
	{
	}

	/**
	 * Renders a table cell
	 * @param ContentObjectTableColumnModel $column The column which should be
	 * rendered
	 * @param VariableTranslation $variable_translation - The variable_translation
	 * @return string A HTML representation of the rendered table cell
	 */
	function render_cell($column, $historic_variable_translation)
	{
		switch ($column->get_name())
		{
			case HistoricVariableTranslation :: PROPERTY_TRANSLATION :
				return $historic_variable_translation->get_translation();
			case HistoricVariableTranslation :: PROPERTY_DATE :
				return $historic_variable_translation->get_date();
			case Translation :: get('User') :
				return $historic_variable_translation->get_user()->get_fullname();
			case HistoricVariableTranslation :: PROPERTY_RATING :
				return $historic_variable_translation->get_rating();
			case HistoricVariableTranslation :: PROPERTY_RATED :
				return $historic_variable_translation->get_rated();
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