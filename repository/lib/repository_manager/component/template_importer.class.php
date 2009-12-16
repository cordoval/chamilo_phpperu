<?php
/**
 * $Id: template_importer.class.php 204 2009-11-13 12:51:30Z kariboe $
 * @package repository.lib.repository_manager.component
 */
/**
 * Repository manager component which provides functionality to delete a
 * learning object from the users repository.
 */
class RepositoryManagerTemplateImporterComponent extends RepositoryManagerComponent
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $trail = new BreadcrumbTrail(false);
        $trail->add(new Breadcrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER), array(), false, Redirect :: TYPE_CORE), Translation :: get('Administration')));
        $trail->add(new Breadcrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_IMPORTER), array(), false, Redirect :: TYPE_CORE), Translation :: get('Importer')));
        $trail->add(new Breadcrumb($this->get_url(), Translation :: get('ContentObjectTemplateImport')));
        $trail->add_help('repository importer');
        
        $extra_params = array();
        
        $user = new User();
        $user->set_id(0);
        
        $import_form = new ContentObjectImportForm('import', 'post', $this->get_url($extra_params), 0, $user);
        
        if ($import_form->validate())
        {
            $content_object = $import_form->import_content_object();
            
            if ($content_object === false)
            {
                $message = Translation :: get('ContentObjectNotImported');
            }
            else
            {
                $message = Translation :: get('ContentObjectImported');
            }
            
            $this->redirect($message, ! isset($content_object), array(Application :: PARAM_ACTION => RepositoryManager :: ACTION_BROWSE_TEMPLATES));
        }
        else
        {
            $this->display_header($trail, false, true);
            $import_form->display();
            $this->display_footer();
        }
    }
}
?>