<?php
/**
 *	This is a skeleton for a data manager for the Cda Application.
 *	Data managers must extend this class and implement its abstract methods.
 *
 *  @author Sven Vanpoucke
 *	@author 
 */
abstract class CdaDataManager
{
	/**
	 * Instance of this class for the singleton pattern.
	 */
	private static $instance;

	/**
	 * Constructor.
	 */
	protected function CdaDataManager()
	{
		$this->initialize();
	}

	/**
	 * Uses a singleton pattern and a factory pattern to return the data
	 * manager. The configuration determines which data manager class is to
	 * be instantiated.
	 * @return CdaDataManager The data manager.
	 */
	static function get_instance()
	{
		if (!isset (self :: $instance))
		{
			$type = Configuration :: get_instance()->get_parameter('general', 'data_manager');
			require_once dirname(__FILE__).'/data_manager/'.Utilities :: camelcase_to_underscores($type).'.class.php';
			$class = $type.'CdaDataManager';
			self :: $instance = new $class ();
		}
		return self :: $instance;
	}

	abstract function initialize();
	abstract function create_storage_unit($name,$properties,$indexes);

	abstract function get_next_cda_language_id();
	abstract function create_cda_language($cda_language);
	abstract function update_cda_language($cda_language);
	abstract function delete_cda_language($cda_language);
	abstract function count_cda_languages($conditions = null);
	abstract function retrieve_cda_language($id);
	abstract function retrieve_cda_languages($condition = null, $offset = null, $count = null, $order_property = null);

	abstract function get_next_language_pack_id();
	abstract function create_language_pack($language_pack);
	abstract function update_language_pack($language_pack);
	abstract function delete_language_pack($language_pack);
	abstract function count_language_packs($conditions = null);
	abstract function retrieve_language_pack($id);
	abstract function retrieve_language_packs($condition = null, $offset = null, $count = null, $order_property = null);

	abstract function get_next_variable_id();
	abstract function create_variable($variable);
	abstract function update_variable($variable);
	abstract function delete_variable($variable);
	abstract function count_variables($conditions = null);
	abstract function retrieve_variable($id);
	abstract function retrieve_variables($condition = null, $offset = null, $count = null, $order_property = null);

	abstract function update_variable_translation($variable_translation);
	abstract function count_variable_translations($conditions = null);
	abstract function retrieve_variable_translation($language_id, $variable_id);
	abstract function retrieve_variable_translations($condition = null, $offset = null, $count = null, $order_property = null);

	abstract function retrieve_english_translation($variable_id);
	
}
?>