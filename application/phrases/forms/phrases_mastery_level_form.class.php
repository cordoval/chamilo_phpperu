<?php
require_once dirname(__FILE__) . '/../phrases_mastery_level.class.php';

/**
 * This class describes the form for a PhrasesMasteryLevel object.
 * @author Hans De Bisschop
 * @author Hans De Bisschop
 **/
class PhrasesMasteryLevelForm extends FormValidator
{
    const TYPE_CREATE = 1;
    const TYPE_EDIT = 2;

    private $phrases_mastery_level;
    private $user;

    function PhrasesMasteryLevelForm($form_type, $phrases_mastery_level, $action, $user)
    {
        parent :: __construct('phrases_mastery_level_form', 'post', $action);

        $this->phrases_mastery_level = $phrases_mastery_level;
        $this->user = $user;
        $this->form_type = $form_type;

        if ($this->form_type == self :: TYPE_EDIT)
        {
            $this->build_editing_form();
        }
        elseif ($this->form_type == self :: TYPE_CREATE)
        {
            $this->build_creation_form();
        }

        $this->setDefaults();
    }

    function build_basic_form()
    {
        $this->addElement('text', PhrasesMasteryLevel :: PROPERTY_LEVEL, Translation :: get('Level'));
        $this->addElement('text', PhrasesMasteryLevel :: PROPERTY_UPGRADE_AMOUNT, Translation :: get('UpgradeAmount'));
        $this->addElement('text', PhrasesMasteryLevel :: PROPERTY_UPGRADE_SCORE, Translation :: get('UpgradeScore'));

        $this->addRule(PhrasesMasteryLevel :: PROPERTY_LEVEL, Translation :: get('ThisFieldIsRequired'), 'required');
        $this->addRule(PhrasesMasteryLevel :: PROPERTY_UPGRADE_AMOUNT, Translation :: get('ThisFieldIsRequired'), 'required');
        $this->addRule(PhrasesMasteryLevel :: PROPERTY_UPGRADE_SCORE, Translation :: get('ThisFieldIsRequired'), 'required');
    }

    function build_editing_form()
    {
        $this->build_basic_form();

        $buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Update'), array('class' => 'positive update'));
        $buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset'), array('class' => 'normal empty'));

        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    function build_creation_form()
    {
        $this->build_basic_form();

        $buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Create'), array('class' => 'positive'));
        $buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset'), array('class' => 'normal empty'));

        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    function update_phrases_mastery_level()
    {
        $phrases_mastery_level = $this->phrases_mastery_level;
        $values = $this->exportValues();

        $phrases_mastery_level->set_level($values[PhrasesMasteryLevel :: PROPERTY_LEVEL]);
        $phrases_mastery_level->set_upgrade_amount($values[PhrasesMasteryLevel :: PROPERTY_UPGRADE_AMOUNT]);
        $phrases_mastery_level->set_upgrade_score($values[PhrasesMasteryLevel :: PROPERTY_UPGRADE_SCORE]);

        if (! $phrases_mastery_level->update())
        {
            return false;
        }

        return true;
    }

    function create_phrases_mastery_level()
    {
        $phrases_mastery_level = $this->phrases_mastery_level;
        $values = $this->exportValues();

        $display_order = PhrasesDataManager :: get_instance()->get_next_mastery_level_display_order_index();

        $phrases_mastery_level->set_level($values[PhrasesMasteryLevel :: PROPERTY_LEVEL]);
        $phrases_mastery_level->set_upgrade_amount($values[PhrasesMasteryLevel :: PROPERTY_UPGRADE_AMOUNT]);
        $phrases_mastery_level->set_upgrade_score($values[PhrasesMasteryLevel :: PROPERTY_UPGRADE_SCORE]);
        $phrases_mastery_level->set_display_order($display_order);

        if (! $phrases_mastery_level->create())
        {
            return false;
        }

        return true;
    }

    /**
     * Sets default values.
     * @param array $defaults Default values for this form's parameters.
     */
    function setDefaults($defaults = array ())
    {
        $phrases_mastery_level = $this->phrases_mastery_level;

        $defaults[PhrasesMasteryLevel :: PROPERTY_LEVEL] = $phrases_mastery_level->get_level();
        $defaults[PhrasesMasteryLevel :: PROPERTY_UPGRADE_AMOUNT] = $phrases_mastery_level->get_upgrade_amount();
        $defaults[PhrasesMasteryLevel :: PROPERTY_UPGRADE_SCORE] = $phrases_mastery_level->get_upgrade_score();

        parent :: setDefaults($defaults);
    }
}
?>