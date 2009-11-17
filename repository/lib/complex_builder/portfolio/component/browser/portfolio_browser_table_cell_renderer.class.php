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
    function render_cell($column, $cloi)
    {
        $lo = $this->retrieve_content_object($cloi->get_ref());
        
        if ($lo->get_type() == 'portfolio_item')
        {
            if (! $this->lpi_ref_object || $this->lpi_ref_object->get_id() != $lo->get_reference())
            {
                $ref_lo = RepositoryDataManager :: get_instance()->retrieve_content_object($lo->get_reference());
                $this->lpi_ref_object = $ref_lo;
            }
            else
            {
                $ref_lo = $this->lpi_ref_object;
            }
        }
        else
        {
            $ref_lo = $lo;
        }
        
        switch ($column->get_name())
        {
            case Translation :: get(Utilities :: underscores_to_camelcase(ContentObject :: PROPERTY_TITLE)) :
                $title = htmlspecialchars($ref_lo->get_title());
                $title_short = $title;
                
                $title_short = Utilities :: truncate_string($title_short, 53, false);
                
                if ($ref_lo->get_type() == 'portfolio')
                {
                    $title_short = '<a href="' . $this->browser->get_url(array(ComplexBuilder :: PARAM_ROOT_LO => $this->browser->get_root(), ComplexBuilder :: PARAM_CLOI_ID => $cloi->get_id(), 'publish' => Request :: get('publish'))) . '">' . $title_short . '</a>';
                }
                
                return $title_short;
        }
        
        return parent :: render_cell($column, $cloi, $ref_lo);
    }

}
?>