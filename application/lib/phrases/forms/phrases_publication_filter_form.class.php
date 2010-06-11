<?php
/**
 * $Id: phrases_publication_filter_form.class.php 196 2009-11-13 12:19:18Z chellee $
 * @package application.lib.laika.forms
 */
require_once dirname(__FILE__) . '/../phrases_manager/phrases_manager.class.php';
require_once dirname(__FILE__) . '/../phrases_data_manager.class.php';

class PhrasesPublicationFilterForm extends FormValidator
{
    const BROWSER_FILTER_MASTERY_LEVEL = 'filter_level';
    const BROWSER_FILTER_LANGUAGE = 'filter_language';

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
        parent :: __construct('phrases_publication_filter_form', 'post', $url);

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

        $this->addElement('select', self :: BROWSER_FILTER_LANGUAGE, Translation :: get('Language'), $this->get_languages());
        $this->addElement('select', self :: BROWSER_FILTER_MASTERY_LEVEL, Translation :: get('MasteryLevel'), $this->get_mastery_levels());
        $this->addElement('style_submit_button', 'submit', Translation :: get('Filter'), array('class' => 'normal search'));
    }

    function get_mastery_levels()
    {
        $levels = array();

        $mastery_levels = PhrasesDataManager::get_instance()->retrieve_phrases_mastery_levels(null, new ObjectTableOrder(PhrasesMasteryLevel::PROPERTY_DISPLAY_ORDER, SORT_ASC));

        while($mastery_level = $mastery_levels->next_result())
        {
            $levels[$mastery_level->get_id()] = Translation :: get($mastery_level->get_level());
        }

        return $levels;
    }

    function get_languages()
    {
        $languages = AdminDataManager :: get_languages(false);
        asort($languages);
        return $languages;
    }

    function get_filter_conditions()
    {
        if (! $this->validate() && ! $this->get_parameters_are_set())
        {
            return null;
        }

        $filter_mastery_level = Request :: get(self :: BROWSER_FILTER_MASTERY_LEVEL);
        $filter_language = Request :: get(self :: BROWSER_FILTER_LANGUAGE);

        $form_validates = $this->validate();

        if ($form_validates)
        {
            $values = $this->exportValues();

            $filter_mastery_level = $values[self :: BROWSER_FILTER_MASTERY_LEVEL];
            $filter_language = $values[self :: BROWSER_FILTER_LANGUAGE];
        }
        else
        {
            $filter_mastery_level = Request :: get(self :: BROWSER_FILTER_MASTERY_LEVEL);
            $filter_language = Request :: get(self :: BROWSER_FILTER_LANGUAGE);
        }

        $conditions = array();
        $conditions[] = new EqualityCondition(PhrasesPublication :: PROPERTY_MASTERY_LEVEL_ID, $filter_mastery_level);
        $conditions[] = new EqualityCondition(PhrasesPublication :: PROPERTY_LANGUAGE_ID, $filter_language);

        $condition = new AndCondition($conditions);

        return $condition;
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
            $parameters[self :: BROWSER_FILTER_MASTERY_LEVEL] = $values[self :: BROWSER_FILTER_MASTERY_LEVEL];
            $parameters[self :: BROWSER_FILTER_LANGUAGE] = $values[self :: BROWSER_FILTER_LANGUAGE];

            return $parameters;
        }
        else
        {
            $parameters = array();
            $parameters[self :: BROWSER_FILTER_MASTERY_LEVEL] = Request :: get(self :: BROWSER_FILTER_MASTERY_LEVEL);
            $parameters[self :: BROWSER_FILTER_LANGUAGE] = Request :: get(self :: BROWSER_FILTER_LANGUAGE);

            return $parameters;
        }
    }

    function get_parameters_are_set()
    {
        $filter_mastery_level = Request :: get(self :: BROWSER_FILTER_MASTERY_LEVEL);
        $filter_language = Request :: get(self :: BROWSER_FILTER_LANGUAGE);

        return (isset($filter_mastery_level) && isset($filter_language));
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
        $filter_mastery_level = Request :: get(self :: BROWSER_FILTER_MASTERY_LEVEL);
        $filter_language = Request :: get(self :: BROWSER_FILTER_LANGUAGE);

        $filter_mastery_level_set = isset($filter_mastery_level);
        $filter_language_set = isset($filter_language);

        if ($filter_mastery_level_set && $filter_language_set)
        {
            $defaults[self :: BROWSER_FILTER_MASTERY_LEVEL] = Request :: get(self :: BROWSER_FILTER_MASTERY_LEVEL);
            $defaults[self :: BROWSER_FILTER_LANGUAGE] = Request :: get(self :: BROWSER_FILTER_LANGUAGE);
        }

        parent :: setDefaults($defaults);
    }
}
?>