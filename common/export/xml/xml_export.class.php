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
    private $handle;
    private $level = 0;

    public function write_to_file($data)
    {
        $file = $this->get_path(SYS_TEMP_PATH) . Filesystem :: create_unique_name($this->get_path(SYS_TEMP_PATH), $this->get_filename());
        $this->handle = fopen($file, 'a+');
        fwrite($this->handle, '<?xml version="1.0" encoding="ISO-8859-1"?>' . "\n");
        $this->write_array($data);
        fclose($this->handle);
        Filesystem :: file_send_for_download($file, true, $file);
        exit();
    }

    public function write_array($row)
    {
        foreach ($row as $key => $value)
        {
            if (is_numeric($key))
                $key = 'item';
            
            if (is_array($value))
            {
                fwrite($this->handle, str_repeat("\t", $this->level) . '<' . $key . '>' . "\n");
                $this->level ++;
                $this->write_array($value);
                $this->level --;
                fwrite($this->handle, str_repeat("\t", $this->level) . '</' . $key . '>' . "\n");
            }
            else
            {
                fwrite($this->handle, str_repeat("\t", $this->level) . '<' . $key . '>' . $value . '</' . $key . '>' . "\n");
            }
        
        }
    }
}
?>