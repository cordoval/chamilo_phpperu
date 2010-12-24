<?php
namespace application\handbook;
use common\libraries\BreadcrumbTrail;
use common\libraries\Breadcrumb;
use common\libraries\Request;
use common\libraries\Translation;
use common\libraries\Utilities;


require_once dirname(__FILE__).'/../handbook_manager.class.php';
require_once dirname(__FILE__).'/../../forms/handbook_publication_form.class.php';

/**
 * Component to edit an existing handbook item
 * @author Nathalie Blocry
 */
class HandbookManagerHandbookItemEditorComponent extends HandbookManager
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
            $html = array();
            $success = true;
            $allow_new_version = ($this->selected_object->get_type() != Portfolio :: get_type_name());

            $form = ContentObjectForm :: factory(ContentObjectForm :: TYPE_EDIT, $this->selected_object, 'content_object_form', 'post', $this->get_url(array(PortfolioManager::PARAM_PORTFOLIO_OWNER_ID => $this->owner_user_id, 'pid' => $this->pid, 'cid' => $this->cid, 'action' => 'edit')), null, null, $allow_new_version);

            if ($form->validate())
            {
                if ($this->cid)
                {
                     if($this->selected_object->get_type() != Portfolio :: get_type_name())
                     {
                         $type = PortfolioRights::TYPE_PORTFOLIO_ITEM;
                     }
                     else
                     {
                         $type = PortfolioRights::TYPE_PORTFOLIO_SUB_FOLDER;
                     }

                }
                else
                {
                    $type = PortfolioRights::TYPE_PORTFOLIO_FOLDER;
                }
                $success &= $form->update_content_object();
                $success &=  PortfolioManager::update_portfolio_info($this->selected_object->get_id(), $type, PortfolioInformation::ACTION_EDITED, $this->owner_user_id);


                if ($form->is_version())
                {
                    $object = $form->get_content_object();
                    if ($this->publication)
                    {
                        $this->publication->set_content_object($object->get_latest_version()->get_id());
                        $success &= $this->publication->update(false);
                    }
                    else
                    {
                        $this->portfolio_item->set_reference($object->get_latest_version()->get_id());
                        $success &= $this->portfolio_item->update();
                    }
                }

                $this->redirect($success ? Translation :: get('PortfolioUpdated') : Translation :: get('PortfolioNotUpdated'), ! $success, array(PortfolioManager :: PARAM_ACTION => PortfolioManager :: ACTION_VIEW_PORTFOLIO, PortfolioManager :: PARAM_PORTFOLIO_OWNER_ID => $this->owner_user_id, 'pid' => $this->pid, 'cid' => $this->cid));
            }
            else
            {
                $html[] = $form->toHtml();
            }

            $this->display_header();
            echo implode("\n", $html);
            $this->display_footer();
	}
}
?>