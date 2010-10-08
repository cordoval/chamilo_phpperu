<?php
abstract class DatabaseBackup
{
	private $storage_units;
	private $data_manager;
	
	function DatabaseBackup(array $storage_units, $data_manager)
	{
		$this->storage_units = $storage_units;
		$this->data_manager = $data_manager;
	}
	
	static function factory($type, array $storage_units, $data_manager)
	{
		$class = Utilities :: underscores_to_camelcase($type) . 'Backup';
        require_once dirname(__FILE__) . '/types/' . $type . '_backup.class.php';
        return new $class($storage_units, $data_manager);
	}
	
	function backup()
	{
		$output = array();
		$output[] = $this->start_data();
		foreach($this->storage_units as $storage_unit)
		{
			$output[] = $this->write_table($storage_unit);
			$output[] = $this->write_data($storage_unit);
		}
		return implode($output, "\n");
	}
	
	function get_data_manager()
	{
		return $this->data_manager;
	}
	
	abstract function start_data();
	abstract function write_table($storage_unit);
	abstract function write_data($storage_unit);
}
?>