<?php

namespace common\libraries;

/**
 * A rest message renderer to html format
 */

class HtmlRestMessageRenderer extends RestMessageRenderer
{
    function render(DataClass $object)
    {
        Display :: header();

        $html = array();
        $html[] = '<br />';
        $html[] = '<table class="data_table">';
        $html[] = '<thead>';
        $html[] = '<tr>';

        foreach($column_names as $column_name)
        {
            $html[] = '<th>' . $column_name . '</th>';
        }

        $html[] = '</tr>';
        $html[] = '</thead>';
        $html[] = '<tbody>';

        $counter = 0;
        foreach($object->get_default_properties() as $name => $value)
        {
            $class = ($counter % 2 == 0) ? 'row_odd' : 'row_even';
            $html[] = '<tr class="' . $class . '">';
            $html[] = '<td>' . Translation :: get(Utilities :: underscores_to_camelcase($name), null, Utilities :: get_namespace_from_object($object)) . '</td><td>' . $value . '</td>';
            $html[] = '</tr>';

            $counter++;
        }

        $html[] = '</tbody>';
        $html[] = '</table>';

        echo implode("\n", $html);

        Display :: footer();
    }
}

?>
