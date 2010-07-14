<?php
require_once 'HTML/Table.php';

class PropertiesTable extends HTML_Table
{
    private $properties;

    /**
     * Constructor creates the table
     * @param array $properties
     */
    function PropertiesTable(array $properties)
    {
        parent :: HTML_Table(array('class' => 'data_table data_table_no_header'));
        $this->properties = $properties;

        $this->build_table();
//        $this->altRowAttributes(0, array('class' => 'row_odd'), array('class' => 'row_even'), true);
    }

    /**
     * Builds the table with given properties
     */
    function build_table()
    {
        if (count($this->properties) > 0)
        {
            foreach ($this->properties as $property => $value)
            {
                $contents = array();
                $contents[] = $property;
                $contents[] = $value;

                $this->addRow($contents);
            }

            $this->setColAttributes(0, array('class' => 'header'));
        }
        else
        {
            $contents = array();
            $contents[] = Translation :: get('NoResults');
            $row = $this->addRow($contents);
            $this->setCellAttributes($row, 0, 'style="font-style: italic;text-align:center;" colspan=2');
        }
    }
}
?>