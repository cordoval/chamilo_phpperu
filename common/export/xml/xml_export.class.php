<?php
/**
 * $Id: xml_export.class.php 128 2009-11-09 13:13:20Z vanpouckesven $
 * @package common.export.xml
 */
require_once dirname(__FILE__) . '/../export.class.php';

/**
 * Exports data to XML-format
 */
class XmlExport extends Export
{
    private $level = 0;
    const EXPORT_TYPE = 'xml';
    
    public function render_data()
    {
    	$all = '<?xml version="1.0" encoding="ISO-8859-1"?>' . "\n";
        $all .= str_repeat("\t", $this->level) . '<rootItem>' . "\n";
        $this->level ++;
        $all .= $this->write_array($this->get_data());
        $this->level --;
        $all .= str_repeat("\t", $this->level) . '</rootItem>' . "\n";
        return $all;
    }

    public function write_array($row)
    {
        foreach ($row as $key => $value)
        {
            if (is_numeric($key))
                $key = 'item';
            
            if (is_array($value))
            {
                $all .= str_repeat("\t", $this->level) . '<' . $key . '>' . "\n";
                $this->level ++;
                $all .= $this->write_array($value);
                $this->level --;
                $all .= str_repeat("\t", $this->level) . '</' . $key . '>' . "\n";
            }
            else
            {
                $all .=  str_repeat("\t", $this->level) . '<' . $key . '>' . $value . '</' . $key . '>' . "\n";
            }
        }
        return $all;
    }
    
	 function get_type()
	 {
	 	return self :: EXPORT_TYPE;
	 }
}
?>