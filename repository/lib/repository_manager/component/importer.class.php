<?php
/**
 * $Id: importer.class.php 204 2009-11-13 12:51:30Z kariboe $
 * @package repository.lib.repository_manager.component
 */
/**
 * Repository manager component which provides functionality to delete a
 * learning object from the users repository.
 */
class RepositoryManagerImporterComponent extends RepositoryManagerComponent
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $trail = new BreadcrumbTrail(false);
        $trail->add(new Breadcrumb($this->get_url(), Translation :: get('ContentObjectImport')));
        $trail->add_help('repository importer');
        
        $import_form = new ContentObjectImportForm('import', 'post', $this->get_url(), $this->get_parameter(RepositoryManager :: PARAM_CATEGORY_ID), $this->get_user());
        
        if ($import_form->validate())
        {
            $succes = $import_form->import_content_object();
            
            $message = $succes ? 'ContentObjectImported' : 'ContentObjectNotImported';
            $this->redirect(Translation :: get($message), ! $succes, array(Application :: PARAM_ACTION => RepositoryManager :: ACTION_BROWSE_CONTENT_OBJECTS));
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