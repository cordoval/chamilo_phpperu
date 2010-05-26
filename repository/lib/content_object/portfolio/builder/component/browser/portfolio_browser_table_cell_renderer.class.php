<?php
/**
 * $Id: portfolio_browser_table_cell_renderer.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_builder.portfolio.component.browser
 */
require_once Path :: get_repository_path() . 'lib/repository_manager/component/complex_browser/complex_browser_table_cell_renderer.class.php';
/**
 * Cell rendere for the learning object browser table
 */
class PortfolioBrowserTableCellRenderer extends ComplexBrowserTableCellRenderer
{

    /**
     * Constructor
     * @param RepositoryManagerBrowserComponent $browser
     */
    function PortfolioBrowserTableCellRenderer($browser, $condition)
    {
        parent :: __construct($browser, $condition);
    }
    
    private $lpi_ref_object;

    // Inherited
    function render_cell($column, $complex_content_object_item)
    {
        $content_object = $this->retrieve_content_object($complex_content_object_item->get_ref());
        
        if ($content_object->get_type() == PortfolioItem :: get_type_name())
        {
            if (! $this->lpi_ref_object || $this->lpi_ref_object->get_id() != $content_object->get_reference())
            {
                $ref_content_object = RepositoryDataManager :: get_instance()->retrieve_content_object($content_object->get_reference());
                $this->lpi_ref_object = $ref_content_object;
            }
            else
            {
                $ref_content_object = $this->lpi_ref_object;
            }
        }
        else
        {
            $ref_content_object = $content_object;
        }
        
        switch ($column->get_name())
        {
            case Translation :: get(Utilities :: underscores_to_camelcase(ContentObject :: PROPERTY_TITLE)) :
                $title = htmlspecialchars($ref_content_object->get_title());
                $title_short = $title;
                
                $title_short = Utilities :: truncate_string($title_short, 53, false);
                
                if ($ref_content_object->get_type() == Portfolio :: get_type_name())
                {
                    $title_short = '<a href="' . $this->browser->get_url(array(ComplexBuilder :: PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID => $complex_content_object_item->get_id())) . '">' . $title_short . '</a>';
                }
                
                return $title_short;
        }
        
        return parent :: render_cell($column, $complex_content_object_item, $ref_content_object);
    }

}
?>