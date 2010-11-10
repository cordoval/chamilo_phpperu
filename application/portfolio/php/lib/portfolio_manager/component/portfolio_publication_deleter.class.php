<?php
namespace application\portfolio;

use common\libraries\Translation;
use common\libraries\Utilities;

/**
 * $Id: portfolio_publication_deleter.class.php 206 2009-11-13 13:08:01Z chellee $
 * @package application.portfolio.portfolio_manager.component
 */
require_once dirname(__FILE__) . '/../portfolio_manager.class.php';

/**
 * Component to delete portfolio_publications objects
 * @author Sven Vanpoucke
 */
class PortfolioManagerPortfolioPublicationDeleterComponent extends PortfolioManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $ids = $_GET[PortfolioManager :: PARAM_PORTFOLIO_PUBLICATION];
        $success = true;
        
        if (! empty($ids))
        {
            if (! is_array($ids))
            {
                $ids = array($ids);
            }
            
            foreach ($ids as $id)
            {
                $portfolio_publication = $this->retrieve_portfolio_publication($id);
                $owner_id = $portfolio_publication->get_owner();
                $object_id = $portfolio_publication->get_content_object();
                //DELETE PORTFOLIO PUBLICATION
                $success &= $portfolio_publication->delete();
                //DELETE LOCATION
                $success &=  PortfolioRights::delete_location($id, $owner_id, PortfolioRights::TYPE_PORTFOLIO_FOLDER);
                
            }
            
            if ($success)
            {
                if (count($ids) == 1)
                {
                    $message = 'ObjectsNotDeleted';
                }
                else
                {
                    $message = 'ObjectsDeleted';
                }
                
                //UPDATE PORTFOLIO INFORMATION
                $success = PortfolioManager::update_portfolio_info($object_id, PortfolioRights::TYPE_PORTFOLIO_FOLDER, PortfolioInformation::ACTION_DELETED, $owner_id);

            }
            else
            {
                if (count($ids) == 1)
                {
                    $message = 'ObjectNotDeleted';
                }
                else
                {
                    $message = 'ObjectsNotDeleted';
                }
            }
            
            $this->redirect(Translation :: get($message, array('OBJECT' => Translation::get('PortfolioPublication')), Utilities::COMMON_LIBRARIES), ($failures ? true : false), array(PortfolioManager :: PARAM_ACTION => PortfolioManager :: ACTION_VIEW_PORTFOLIO, PortfolioManager :: PARAM_PORTFOLIO_OWNER_ID => $this->get_user_id()));
        }
        else
        {
            $this->display_error_page(htmlentities(Translation :: get('NoObjectsSelected', array('OBJECT' => Translation::get('PortfolioPublications')), Utilities::COMMON_LIBRARIES)));
        }
    }
}
?>