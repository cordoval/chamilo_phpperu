<?php

namespace application\package;

use common\libraries\FormValidator;
use common\libraries\Translation;
use common\libraries\Session;
use common\libraries\EqualityCondition;
use common\libraries\Utilities;
/**
 * $Id: variable_translation_browser_filter_form.class.php 196 2009-11-13 12:19:18Z chellee $
 *
 * @author Sven Vanpoucke
 * @author Hans De Bisschop
 */

class VariableTranslationBrowserFilterForm extends FormValidator
{
    const BROWSER_FILTER_TRANSLATION = 'filter_translation';

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
        parent :: __construct('variable_translation_filter_form', 'post', $url);

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

        $options[0] = '-- ' . Translation :: get('AllTranslations') . ' --';
        $options[1] = Translation :: get('TranslatedVariables');
        $options[2] = Translation :: get('UntranslatedVariables');
        $options[3] = Translation :: get('OutdatedVariableTranslations');

        $this->addElement('select', self :: BROWSER_FILTER_TRANSLATION, Translation :: get('TranslatingStatus'), $options);
        $this->addElement('style_submit_button', 'submit', Translation :: get('Filter', null, Utilities :: COMMON_LIBRARIES), array('class' => 'normal search'));
    }

    function get_filter_conditions()
    {
        if (! $this->validate() && ! $this->get_parameters_are_set())
        {
            return null;
        }

        $filter_translation = Session :: retrieve(self :: BROWSER_FILTER_TRANSLATION);

        $form_validates = $this->validate();

        if ($form_validates)
        {
            $values = $this->exportValues();
            $filter_translation = $values[self :: BROWSER_FILTER_TRANSLATION];
            Session :: register(self :: BROWSER_FILTER_TRANSLATION, $filter_translation);
        }

        if ($filter_translation == 0)
        {
        	return null;
        	Session :: unregister(self :: BROWSER_FILTER_TRANSLATION);
        }

     	switch($filter_translation)
     	{
     		case 1:
     			return new EqualityCondition(VariableTranslation :: PROPERTY_TRANSLATED, 1);
     		case 2:
     			return new EqualityCondition(VariableTranslation :: PROPERTY_TRANSLATED, 0);
     		case 3:
     		    return new EqualityCondition(VariableTranslation :: PROPERTY_STATUS, VariableTranslation :: STATUS_OUTDATED);
     	}
    }

    function get_parameters_are_set()
    {
        $filter_branch = Session :: retrieve(self :: BROWSER_FILTER_TRANSLATION);

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
        $filter_branch = Session :: retrieve(self :: BROWSER_FILTER_TRANSLATION);
        $filter_branch_set = isset($filter_branch);

        if ($filter_branch_set)
        {
            $defaults[self :: BROWSER_FILTER_TRANSLATION] = Session :: retrieve(self :: BROWSER_FILTER_TRANSLATION);
        }

        parent :: setDefaults($defaults);
    }
}
?>