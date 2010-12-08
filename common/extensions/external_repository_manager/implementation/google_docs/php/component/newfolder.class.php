<?php
namespace common\extensions\external_repository_manager\implementation\google_docs;

use common\libraries\Path;
use common\libraries\Redirect;
use common\libraries\Translation;
use common\libraries\Request;
use common\libraries\Application;

use common\extensions\external_repository_manager\ExternalRepositoryManager;

require_once dirname(__FILE__) . '/../forms/google_docs_external_repository_manager_form.class.php';

class GoogleDocsExternalRepositoryManagerNewFolderComponent extends GoogleDocsExternalRepositoryManager
{
    function run()
    {
        $form = new GoogleDocsExternalRepositoryManagerForm(GoogleDocsExternalRepositoryManagerForm :: TYPE_NEWFOLDER, $this->get_url(), $this);
		if ($form->validate())
        {      
        	$id = $form->create_folder();
        	if (!is_null($id))
            {
                $$parameters = $this->get_parameters();
            	$parameters[ExternalRepositoryManager :: PARAM_EXTERNAL_REPOSITORY_MANAGER_ACTION] = ExternalRepositoryManager :: ACTION_BROWSE_EXTERNAL_REPOSITORY;
            	$this->redirect('Folder is created', false, $parameters);
            }
            else
            {
                $parameters = $this->get_parameters();
            	$parameters[ExternalRepositoryManager :: PARAM_EXTERNAL_REPOSITORY_MANAGER_ACTION] = ExternalRepositoryManager :: ACTION_NEWFOLDER_EXTERNAL_REPOSITORY;
            	$this->redirect('Folder is not created', true, $parameters);
            }
        }
        else
        {
            $this->display_header();
            $form->display();
            $this->display_footer();
        }
    }
}
?>