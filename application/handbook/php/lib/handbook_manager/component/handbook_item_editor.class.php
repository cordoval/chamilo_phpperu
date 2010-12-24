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
            $allow_new_version = false;


            $handbook_publication_id = Request :: get(HandbookManager::PARAM_HANDBOOK_PUBLICATION_ID);
            $handbook_id = Request :: get(HandbookManager::PARAM_HANDBOOK_ID);
            $selected_object_id = Request :: get(HandbookManager::PARAM_HANDBOOK_SELECTION_ID);
            $rdm = RepositoryDataManager::get_instance();
            $selected_object = $rdm->retrieve_content_object($selected_object_id);

            $params = array();
            $params['action'] = 'edit';
            
            $url = $this->get_url($params);

            $form = ContentObjectForm :: factory(ContentObjectForm :: TYPE_EDIT, $selected_object, 'content_object_form', 'post', $url, null, null, $allow_new_version);

            if ($form->validate())
            {               
                $success &= $form->update_content_object();
                
                $redirect_params = array();
                $redirect_params[HandbookManager :: PARAM_ACTION] = HandbookManager::ACTION_VIEW_HANDBOOK;
                $redirect_params[HandbookManager ::PARAM_HANDBOOK_SELECTION_ID] = $selected_object_id;
                $redirect_params[HandbookManager ::PARAM_HANDBOOK_PUBLICATION_ID] = $handbook_publication_id;
                
               $this->redirect($success ? Translation :: get('HandbookUpdated') : Translation :: get('HandbookNotUpdated'), ! $success, $redirect_params);
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