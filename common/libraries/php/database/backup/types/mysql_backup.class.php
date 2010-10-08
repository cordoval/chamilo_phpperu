<?php
class MysqlBackup extends DatabaseBackup
{
	function MysqlBackup(array $storage_units, $data_manager)
	{
		parent :: __construct($storage_units, $data_manager);
	}
	
	function start_data()
	{
		$sql_data = "#\n";
		$sql_data .= "# Dump of tables for Chamilo\n";
		$sql_data .= "# DATE : " . gmdate("d-m-Y H:i:s", time()) . " GMT\n";
		$sql_data .= "#\n";
		//$this->flush($sql_data);
		return $sql_data;
	}	
	
	function write_data($storage_unit)
	{
		$table_name = $this->get_data_manager()->escape_table_name($storage_unit);
		$select = 'SELECT * FROM ' . $table_name;
		$record_set = $this->get_data_manager()->retrieve_record_set($select, $storage_unit);
				
		if (! MDB2 :: isError($record_set))
		{
			if ($record_set->size() > 0)
			{
				$fields_count = $record_set->get_handle()->numCols();
				$field_set = $record_set->get_handle()->getColumnNames(true);
	
				$search			= array("\\", "'", "\x00", "\x0a", "\x0d", "\x1a", '"');
				$replace		= array("\\\\", "\\'", '\0', '\n', '\r', '\Z', '\\"');
				$fields			= implode(', ', $field_set);
				$sql_data		= 'INSERT INTO ' . $table_name . ' (' . $fields . ') VALUES ';
				$first_set		= true;
				$query_len		= 0;
				$max_len		= Utilities :: get_usable_memory();
	
				while ($row = $record_set->next_result())
				{
					$values	= array();
					if ($first_set)
					{
						$query = $sql_data . '(';
					}
					else
					{
						$query  .= ',(';
						
					}
	
					for ($j = 0; $j < $fields_count; $j++)
					{
						if (!isset($row[$field_set[$j]]) || is_null($row[$field_set[$j]]))
						{
							$values[$j] = 'NULL';
						}
						else if (($field[$j]->flags & 32768) && !($field[$j]->flags & 1024))
						{
							$values[$j] = $row[$field_set[$j]];
						}
						else
						{
							$values[$j] = "'" . str_replace($search, $replace, $row[$field_set[$j]]) . "'";
						}
					}
					$query .= implode(', ', $values) . ')' . "\n";
	
					$query_len += strlen($query);
	                if ($query_len > $max_len)
	                {
	                    $sql_data .= $query . ";\n\n";
	                    $query = '';
	                    $query_len = 0;
	                    $first_set = true;
	                }
	                else
	                {
						$first_set = false;
					}
				}
			}

			// check to make sure we have nothing left to flush
			if (!$first_set && $query)
			{
				$sql_data .= $query . ";\n\n";
			}
		}
		return $sql_data;
	}
	
	function write_table($storage_unit)
	{
		$table_name = $this->get_data_manager()->escape_table_name($storage_unit);
		$query = 'SHOW CREATE TABLE ' . $table_name;
		$record_set = $this->get_data_manager()->retrieve_record_set($query, $storage_unit);
		$result = $record_set->next_result();

		$sql_data = '# Table: ' . $table_name . "\n";
		$sql_data .= "DROP TABLE IF EXISTS $table_name;\n";
		$sql_data .= $result['create table']  . ";\n\n";
		return $sql_data;
	}
}
?>