<?php
/**
 * @package cda.tables.historic_variable_translation_table
 */
/**
 * Data provider for a historic_variable_translation table
 *
 * @author Sven Vanpoucke
 * @author Hans De Bisschop
 */
class HistoricVariableTranslationBrowserTableDataProvider extends ObjectTableDataProvider
{
  /**
   * Constructor
   * @param ApplicationComponent $browser
   * @param Condition $condition
   */
  function HistoricVariableTranslationBrowserTableDataProvider($browser, $condition)
  {
		parent :: __construct($browser, $condition);
  }
  /**
   * Retrieves the objects
   * @param int $offset
   * @param int $count
   * @param string $order_property
   * @return ResultSet A set of objects
   */
    function get_objects($offset, $count, $order_property = null)
    {
		$order_property = $this->get_order_property($order_property);

     	return $this->get_browser()->retrieve_historic_variable_translations($this->get_condition(), $offset, $count, $order_property);
    }
  /**
   * Gets the number of objects in the table
   * @return int
   */
    function get_object_count()
    {
      return $this->get_browser()->count_historic_variable_translations($this->get_condition());
    }
}
?>