<?php
class DatabaseAliasGenerator
{
    /**
     * Instance of this class for the singleton pattern.
     */
    private static $instance;
    
    private $aliases = array();
    
    const TYPE_TABLE = 1;
    const TYPE_CONSTRAINT = 2;

    private function DatabaseAliasGenerator()
    {
        foreach ($this->get_types() as $type)
        {
            $this->aliases[$type] = array();
        }
    }

    /**
     * Returns the instance of this class.
     * @return TableAliasGenerator The instance.
     */
    static function get_instance()
    {
        if (! isset(self :: $instance))
        {
            self :: $instance = new self();
        }
        return self :: $instance;
    }

    function get_types()
    {
        return array(self :: TYPE_TABLE, self :: TYPE_CONSTRAINT);
    }

    function get_aliases()
    {
        return $this->aliases;
    }

    function get_table_alias($table_name, $application)
    {
    	$unique_name = $application . $table_name;
        
        if (array_key_exists($unique_name, $this->aliases[self :: TYPE_TABLE]))
        {
            return $this->aliases[self :: TYPE_TABLE][$unique_name];
        }
        else
        {
	        $possible_name = 'alias_';
	        $parts = explode('_', $unique_name);
	        
	        foreach ($parts as $part)
	        {
	            $possible_name .= $part{0};
	        }
	        
	        if (in_array($possible_name, $this->aliases[self :: TYPE_TABLE]))
	        {
	        	$original_name = $possible_name;
	            $index = 'a';
	            
	            while (in_array($possible_name, $this->aliases[self :: TYPE_TABLE]))
	            {
	                $possible_name = $original_name . '_' . $index;
	                $index ++;
	            }
	        }
            
            $this->aliases[self :: TYPE_TABLE][$unique_name] = $possible_name;
            return $possible_name;
        }
    }

    function get_constraint_name($table_name, $column)
    {
        $possible_name = '';
        $parts = explode('_', $table_name);
        
        foreach ($parts as $part)
        {
            $possible_name .= $part{0};
        }
        
        $possible_name = $possible_name . '_' . $column;
        
        if (! array_key_exists($possible_name, $this->aliases[self :: TYPE_CONSTRAINT]))
        {
            $this->aliases[self :: TYPE_CONSTRAINT][$possible_name] = serialize(array($table_name, $column));
            return $possible_name;
        }
        else
        {
            $original_name = $possible_name;
            $index = 'a';
            
            while (array_key_exists($possible_name, $this->aliases[self :: TYPE_CONSTRAINT]))
            {
                $possible_name = $original_name . '_' . $index;
                $index ++;
            }
            
            $this->aliases[self :: TYPE_CONSTRAINT][$possible_name] = serialize(array($table_name, $column));
            return $possible_name;
        }
    }
}
?>