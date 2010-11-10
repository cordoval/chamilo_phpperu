<?php
namespace application\portfolio;
use common\libraries\Translation;
use common\libraries\Utilities;
use repository\RepositoryDataManager;
use repository\content_object\portfolio_item\PortfolioItem;



/**
 * Component to delete portfolio_publications objects
 * @author Sven Vanpoucke
 */
class PortfolioManagerPortfolioItemDeleterComponent extends PortfolioManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $ids = $_GET[PortfolioManager :: PARAM_PORTFOLIO_ITEM];
        $success = true;
        
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
                //DELETE COMPLEX CONTENT OBJECT WRAPPER
                $success &= $item->delete();
                $types = array();
                $types[] = PortfolioRights::TYPE_PORTFOLIO_ITEM;
                $types[] = PortfolioRights::TYPE_PORTFOLIO_SUB_FOLDER;
                //DELETE LOCATION
                $success &=  PortfolioRights::delete_location($cid, $this->get_user_id(), $types );
               
                if ($ref->get_type() == PortfolioItem :: get_type_name())
                {
                    $object_id = $ref->get_reference();
                    //DELETE PORTFOLIO ITEM WRAPPER
                    $success &= $ref->delete();
                }
                else
                {
                    $object_id = $ref->get_id();
                }
            }
            
            if ($success)
            {
                if (count($ids) == 1)
                {
                    $message = 'ObjectDeleted';
                }
                else
                {
                    $message = 'ObjectsDeleted';
                }
                //UPDATE INFORMATION
                $success = PortfolioManager::update_portfolio_info($object_id, PortfolioRights::TYPE_PORTFOLIO_ITEM, PortfolioInformation::ACTION_DELETED, $item->get_user_id());
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
            
            $this->redirect(Translation :: get($message, array('OBJECT' => Translation::get('PortfolioItem')), Utilities::COMMON_LIBRARIES), ($success ? false : true), array(PortfolioManager :: PARAM_ACTION => PortfolioManager :: ACTION_VIEW_PORTFOLIO, PortfolioManager :: PARAM_PORTFOLIO_OWNER_ID => $this->get_user_id()));
        }
        else
        {
            $this->display_error_page(htmlentities(Translation :: get('NoObjectsSelected', array('OBJECT' => Translation::get('PortfolioPublications')), Utilities::COMMON_LIBRARIES)));
        }
    }
}
?>