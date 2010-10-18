<?php

class DefaultMetadataNamespaceTableCellRenderer extends ObjectTableCellRenderer
{
    function DefaultMetadataNamespaceTableCellRenderer($browser)
    {
    
    }

    /**
     * Renders a table cell
     * @param ContentObjectTableColumnModel $column The column which should be
     * rendered
     * @param Learning Object $content_object The learning object to render
     * @return string A HTML representation of the rendered table cell
     */
    function render_cell($column, $namespace)
    {
        switch ($column->get_name())
        {
            case MetadataNamespace :: PROPERTY_URL :
                return $namespace->get_url();
            case MetadataNamespace :: PROPERTY_NS_PREFIX:
                return $namespace->get_ns_prefix();
            case MetadataNamespace :: PROPERTY_NAME:
                return $namespace->get_name();
        }
        
        $title = $column->get_title();
        if ($title == '')
        {
            $img = Theme :: get_common_image_path() . ('treemenu_types/profile.png');
            return '<img src="' . $img . '"alt="namespace" />';
        }
        
        return '&nbsp;';
    }

    function render_id_cell($namespace)
    {
        return $namespace->get_id();
    }
}
?>