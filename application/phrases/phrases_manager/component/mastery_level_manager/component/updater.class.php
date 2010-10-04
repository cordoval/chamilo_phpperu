<?php
/**
 * $Id: updater.class.php 193 2009-11-13 11:53:37Z chellee $
 * @package application.lib.assessment.assessment_manager.component
 */
require_once dirname(__FILE__) . '/../../../../forms/phrases_mastery_level_form.class.php';

/**
 * Component to edit an existing assessment_mastery_level object
 * @author Hans De Bisschop
 * @author
 */
class PhrasesMasteryLevelManagerUpdaterComponent extends PhrasesMasteryLevelManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $trail = BreadcrumbTrail :: get_instance();
        //        $trail->add(new Breadcrumb($this->get_url(array(AssessmentManager :: PARAM_ACTION => AssessmentManager :: ACTION_BROWSE_ASSESSMENT_MASTERY_LEVELS)), Translation :: get('BrowseAssessmentMasteryLevels')));
        //        $trail->add(new Breadcrumb($this->get_url(), Translation :: get('UpdateAssessmentMasteryLevel')));

        $mastery_level_id = Request :: get(PhrasesMasteryLevelManager :: PARAM_PHRASES_MASTERY_LEVEL_ID);

        $mastery_level = $this->retrieve_phrases_mastery_level($mastery_level_id);
        $form = new PhrasesMasteryLevelForm(PhrasesMasteryLevelForm :: TYPE_EDIT, $mastery_level, $this->get_url(array(PhrasesMasteryLevelManager :: PARAM_PHRASES_MASTERY_LEVEL_ID => $mastery_level->get_id())), $this->get_user());

        if ($form->validate())
        {
            $success = $form->update_phrases_mastery_level();
            $this->redirect($success ? Translation :: get('PhrasesMasteryLevelUpdated') : Translation :: get('PhrasesMasteryLevelNotUpdated'), ! $success, array(PhrasesMasteryLevelManager :: PARAM_MASTERY_LEVEL_MANAGER_ACTION => PhrasesMasteryLevelManager :: ACTION_BROWSE));
        }
        else
        {
            $this->display_header($trail);
            $form->display();
            $this->display_footer();
        }
    }
}
?>