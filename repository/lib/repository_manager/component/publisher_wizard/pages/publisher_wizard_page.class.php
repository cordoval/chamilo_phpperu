<?php
/**
 * $Id: publisher_wizard_page.class.php 204 2009-11-13 12:51:30Z kariboe $
 * @package repository.lib.repository_manager.component.publication_wizard.pages
 */
/**
 * This abstract class defines a page which is used in a publisher wizard.
 */


abstract class PublisherWizardPage extends FormValidatorPage
{
    /**
     * The repository tool in which the wizard runs.
     */
    private $parent;

    public function PublisherWizardPage($name, $parent)
    {
        $this->parent = $parent;
        parent :: __construct($name, 'post');
        $this->updateAttributes(array('action' => $parent->get_url(array(RepositoryManager :: PARAM_CONTENT_OBJECT_ID => Request :: get(RepositoryManager :: PARAM_CONTENT_OBJECT_ID)))));
    }

    /**
     * Returns the repository tool in which this wizard runs
     * @return Tool
     */
    function get_parent()
    {
        return $this->parent;
    }

}
?>