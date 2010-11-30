<?php

namespace common\libraries;

/**
 * A rest message renderer to html format
 */

class HtmlRestMessageRenderer extends RestMessageRenderer
{
    function render_object(DataClass $object)
    {
        $this->render_header(array(Translation :: get('Property'), Translation :: get('Value')));

        $html = array();

        $counter = 0;
        foreach($object->get_default_properties() as $name => $value)
        {
            $class = ($counter % 2 == 0) ? 'row_odd' : 'row_even';
            $html[] = '<tr class="' . $class . '">';
            $html[] = '<td>' . Translation :: get(Utilities :: underscores_to_camelcase($name), null, Utilities :: get_namespace_from_object($object)) . '</td><td>' . $value . '</td>';
            $html[] = '</tr>';

            $counter++;
        }

        echo implode("\n", $html);

        $this->render_footer();

    }

    function render_multiple_objects(array $objects)
    {
        $column_names = array_keys($objects[0]->get_default_properties());
        $this->render_header($column_names);

        $counter = 0;
        foreach($objects as $object)
        {
            $class = ($counter % 2 == 0) ? 'row_odd' : 'row_even';
            $html[] = '<tr class="' . $class . '">';

            foreach($object->get_default_properties() as $name => $value)
            {
                $html[] = '<td>' . $value . '</td>';
            }

            $html[] = '</tr>';

            $counter++;
        }

        echo implode("\n", $html);
        $this->render_footer();
    }

    private function render_header($column_names)
    {
        Display :: header();

        $html = array();
        $html[] = '<br />';
        $html[] = '<div style="overflow: auto;">';
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
        echo implode("\n", $html);

    }

    private function render_footer()
    {
        $html = array();
        $html[] = '</tbody>';
        $html[] = '</table>';
        $html[] = '<br />';
        $html[] = '</div>';

        echo implode("\n", $html);

        Display :: footer();
    }
}

?>
