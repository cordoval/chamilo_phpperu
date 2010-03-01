<?php
/**
 * $Id: finder.class.php 191 2009-11-13 11:50:28Z chellee $
 * @package application.common.repo_viewer.component
 */
require_once dirname(__FILE__) . '/browser.class.php';
/**
 * This class represents a encyclopedia repo_viewer component which can be used
 * to search for a certain learning object.
 */
class RepoViewerFinderComponent extends RepoViewerBrowserComponent
{
    /**
     * The search form
     */
    private $form;
    /**
     * The renderer for the search form
     */
    private $renderer;

    /**
     * Constructor.
     * @param ObjectRepoViewer $parent The creator of this object.
     */
    function RepoViewerFinderComponent($parent)
    {
        parent :: __construct($parent);
        $this->form = new FormValidator('search', 'post', $this->get_url($this->get_parameters()), '', null, false);
        $this->form->addElement('hidden', RepoViewer :: PARAM_ACTION);
        $this->form->addElement('text', RepoViewer :: PARAM_QUERY, Translation :: get('Find'), 'size="40" class="search_query"');
        $this->form->addElement('submit', 'submit', Translation :: get('Ok'));
    }

    /*
	 * Inherited
	 */
    function as_html()
    {
        $this->renderer = clone $this->form->defaultRenderer();
        $this->renderer->setElementTemplate('<span>{element}</span> ');
        $this->form->accept($this->renderer);

        $html = array();
        $html[] = '<div class="lofinder_search_form" style="margin: 0 0 1em 0;">';
        $html[] = $this->renderer->toHTML();
        $html[] = '</div>';

        if (strlen(trim($this->get_query())) > 0)
        {
            $html[] = parent :: as_html();
        }

        return implode("\n", $html);
    }

    /*
	 * Overriding
	 */
    protected function get_query()
    {
        if ($this->form->validate())
        {
            return $this->form->exportValue(RepoViewer :: PARAM_QUERY);
        }

        if (Request :: get(RepoViewer :: PARAM_QUERY))
        {
            return Request :: get(RepoViewer :: PARAM_QUERY);
        }

        return null;
    }

    function get_form()
    {
        return $this->form;
    }
}
?>