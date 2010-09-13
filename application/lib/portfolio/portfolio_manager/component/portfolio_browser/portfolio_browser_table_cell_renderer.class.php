<?php
//require_once   dirname(__FILE__) .  '/../../../../../../repository/lib/content_object/portfolio/portfolio.class.php';
require_once   dirname(__FILE__) .  '/portfolio_browser_table_column_model.class.php';
require_once   dirname(__FILE__) . '/../../../tables/portfolio_table/default_portfolio_table_cell_renderer.class.php';

/**
 * Cell renderer for the portfolio object browser table
 */
class PortfolioBrowserTableCellRenderer extends DefaultPortfolioTableCellRenderer
{
    /**
     * The portfolio browser component
     */
    public $browser;

    /**
     * Constructor
     * @param RepositoryManagerBrowserComponent $browser
     */
    function PortfolioBrowserTableCellRenderer($browser)
    {
        parent :: __construct();
        $this->browser = $browser;
    }

    // Inherited
    function render_cell($column, $user)
    {
        if ($column === PortfolioBrowserTableColumnModel :: get_modification_column())
        {
            return $this->get_modification_links($user);
        }
        
        // Add special features here
        switch ($column->get_name())
        {
            case User :: PROPERTY_OFFICIAL_CODE :
                return $user->get_official_code();
        }
        return parent :: render_cell($column, $user);
    }

    /**
     * Gets the action links to display
     * @param $user The user for which the
     * action links should be returned
     * @return string A HTML representation of the action links
     */
    private function get_modification_links($user)
    {
        $toolbar = new Toolbar(Toolbar :: TYPE_HORIZONTAL);
        
        $toolbar->add_item(new ToolbarItem(
        		Translation :: get('ViewPortfolio'),
        		Theme :: get_common_image_path() . 'action_browser.png',
        		$this->browser->get_view_portfolio_url($user->get_id()),
        		ToolbarItem :: DISPLAY_ICON
        ));

        return $toolbar->as_html();
    }
}
?>