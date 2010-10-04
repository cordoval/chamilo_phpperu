<?php
/**
 * $Id: language_pack_browser_filter_form.class.php 196 2009-11-13 12:19:18Z chellee $
 * @package application.lib.laika.forms
 * 
 * @author Sven Vanpoucke
 * @author Hans De Bisschop
 */
class LanguagePackBrowserFilterForm extends FormValidator
{
    const BROWSER_FILTER_BRANCH = 'filter_branch';

    private $manager;
    private $renderer;

    /**
     * Creates a new search form
     * @param RepositoryManager $manager The repository manager in which this
     * search form will be displayed
     * @param string $url The location to which the search request should be
     * posted.
     */
    function __construct($manager, $url)
    {
        parent :: __construct('language_pack_filter_form', 'post', $url);

        $this->renderer = clone $this->defaultRenderer();
        $this->manager = $manager;

        $this->build_form();

        $this->setDefaults();

        $this->accept($this->renderer);
    }

    /**
     * Build the simple search form.
     */
    private function build_form()
    {
        $this->renderer->setFormTemplate('<form {attributes}><div class="filter_form">{content}</div><div class="clear">&nbsp;</div></form>');
        $this->renderer->setElementTemplate('<div class="row"><div class="formw">{label}&nbsp;{element}</div></div>');
    	
        $options = LanguagePack :: get_branch_options();
        $options[0] = '-- ' . Translation :: get('AllBranches') . ' --';
        ksort($options);
        
        $this->addElement('select', self :: BROWSER_FILTER_BRANCH, Translation :: get('Branch'), $options);
        $this->addElement('style_submit_button', 'submit', Translation :: get('Filter'), array('class' => 'normal search'));
        //$this->addElement('submit', 'search', Translation :: get('Filter'));
    }

    function get_filter_conditions()
    {
        if (! $this->validate() && ! $this->get_parameters_are_set())
        {
            return null;
        }

        $filter_branch = Request :: get(self :: BROWSER_FILTER_BRANCH);

        $form_validates = $this->validate();

        if ($form_validates)
        {
            $values = $this->exportValues();
            $filter_branch = $values[self :: BROWSER_FILTER_BRANCH];
        }
        else
        {
            $filter_branch = $values[self :: BROWSER_FILTER_BRANCH];
        }
        
        if ($filter_branch == 0)
        {
        	return null;
        }

        return new EqualityCondition(LanguagePack :: PROPERTY_BRANCH, $filter_branch);
    }

    function get_filter_parameters()
    {
        if (! $this->validate() && ! $this->get_parameters_are_set())
        {
            return array();
        }

        if ($this->validate())
        {
            $values = $this->exportValues();

            $parameters = array();
            $parameters[self :: BROWSER_FILTER_BRANCH] = $values[self :: BROWSER_FILTER_BRANCH];
            return $parameters;
        }
        else
        {
            $parameters = array();
            $parameters[self :: BROWSER_FILTER_BRANCH] = Request :: get(self :: BROWSER_FILTER_BRANCH);
            return $parameters;
        }
    }

    function get_parameters_are_set()
    {
        $filter_branch = Request :: get(self :: BROWSER_FILTER_BRANCH);

        return isset($filter_branch);
    }

    /**
     * Display the form
     */
    function display()
    {
        $html = array();
        $html[] = '<div style="text-align: right; clear: both;">';
        $html[] = $this->renderer->toHTML();
        $html[] = '</div>';
        return implode('', $html);
    }

    function setDefaults($defaults = array ())
    {
        $filter_branch = Request :: get(self :: BROWSER_FILTER_BRANCH);
        $filter_branch_set = isset($filter_branch);

        if ($filter_branch_set)
        {
            $defaults[self :: BROWSER_FILTER_BRANCH] = Request :: get(self :: BROWSER_FILTER_BRANCH);
        }

        parent :: setDefaults($defaults);
    }
}
?>