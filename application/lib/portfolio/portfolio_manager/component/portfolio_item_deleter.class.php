<?php
/**
 * $Id: portfolio_item_deleter.class.php 206 2009-11-13 13:08:01Z chellee $
 * @package application.portfolio.portfolio_manager.component
 */
require_once dirname(__FILE__) . '/../portfolio_manager.class.php';
require_once dirname(__FILE__) . '/../portfolio_manager_component.class.php';
require_once dirname(__FILE__) . '/../../portfolio_rights.class.php';

/**
 * Component to delete portfolio_publications objects
 * @author Sven Vanpoucke
 */
class PortfolioManagerPortfolioItemDeleterComponent extends PortfolioManagerComponent
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $ids = $_GET[PortfolioManager :: PARAM_PORTFOLIO_ITEM];
        $failures = 0;
        
        if (! empty($ids))
        {
            if (! is_array($ids))
            {
                $ids = array($ids);
            }
            
            $rdm = RepositoryDataManager :: get_instance();
            
            foreach ($ids as $cid)
            {
                $item = $rdm->retrieve_complex_content_object_item($cid);
                $ref = $rdm->retrieve_content_object($item->get_ref());
                
                if (! $item->delete())
                {
                    $failures ++;
                }
                else if(!portfolioRights::delete_location($cid))
                {
                    $failures ++;
                }
                if ($ref->get_type() == 'portfolio_item')
                {
                    $ref->delete();
                }
            }
            
            if ($failures)
            {
                if (count($ids) == 1)
                {
                    $message = 'SelectedPortfolioItemDeleted';
                }
                else
                {
                    $message = 'SelectedPortfolioItemsDeleted';
                }
            }
            else
            {
                if (count($ids) == 1)
                {
                    $message = 'SelectedPortfolioItemNotDeleted';
                }
                else
                {
                    $message = 'SelectedPortfolioItemsNotDeleted';
                }
            }
            
            $this->redirect(Translation :: get($message), ($failures ? true : false), array(PortfolioManager :: PARAM_ACTION => PortfolioManager :: ACTION_VIEW_PORTFOLIO, PortfolioManager :: PARAM_USER_ID => $this->get_user_id()));
        }
        else
        {
            $this->display_error_page(htmlentities(Translation :: get('NoPortfolioPublicationsSelected')));
        }
    }
}
?>