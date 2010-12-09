<?php
namespace application\phrases;

use common\libraries\Request;
use common\libraries\Translation;
use common\libraries\BreadcrumbTrail;
use common\libraries\Breadcrumb;
use common\libraries\FormValidator;
use common\libraries\NotCondition;
use common\libraries\EqualityCondition;
use common\libraries\AndCondition;
use common\libraries\InCondition;
use common\libraries\Utilities;

/**
 * @author Hans De Bisschop
 * @package application.phrases
 */

class PhrasesManagerMoverComponent extends PhrasesManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $pid = Request :: get(self :: PARAM_PHRASES_PUBLICATION);
        if (! $pid || (is_array($pid) && count($pid) == 0))
        {
            $this->not_allowed();
            exit();
        }
        $pids = $pid;

        if (is_array($pids))
        {
            $pid = $pids[0];
        }

        $publication = $this->retrieve_phrases_publication($pid);

        if (! $publication->is_visible_for_target_user($this->get_user()))
        {
            $this->not_allowed(null, false);
        }

        $parent = $publication->get_category();

        $form = $this->build_move_form($parent, $pids);
        if ($form->validate())
        {
            $new_category_id = $this->move_publications_to_category($form, $pids);
            $this->redirect(Translation :: get('ObjectsMoved', array(
                    'OBJECTS' => Translation :: get('Categories', null, Utilities :: COMMON_LIBRARIES)), Utilities :: COMMON_LIBRARIES), false, array(
                    PhrasesManager :: PARAM_ACTION => PhrasesManager :: ACTION_BROWSE_PHRASES_PUBLICATIONS,
                    'category' => $new_category_id));
        }
        else
        {
            $this->display_header(null, true);
            echo $form->toHtml();
            $this->display_footer();
        }
    }

    function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add_help('phrases_mover');
        $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(
                PhrasesManager :: PARAM_ACTION => PhrasesManager :: ACTION_BROWSE_PHRASES_PUBLICATIONS)), Translation :: get('PhrasesManagerBrowserComponent')));
    }

    function get_additional_parameters()
    {
        return array(self :: PARAM_PHRASES_PUBLICATION);
    }

    function build_move_form($exclude_category, $pids)
    {
        $url = $this->get_url(array(PhrasesManager :: PARAM_PHRASES_PUBLICATION => $pids));
        $form = new FormValidator('phrases_publication_mover', 'post', $url);

        $this->categories = array();
        $this->categories[0] = Translation :: get('Root', null, Utilities :: COMMON_LIBRARIES);

        $this->retrieve_categories_recursive(0, $exclude_category);

        $form->addElement('select', PhrasesPublication :: PROPERTY_CATEGORY, Translation :: get('SelectCategory'), $this->categories);

        $buttons[] = $form->createElement('style_submit_button', 'submit', Translation :: get('Move', null, Utilities :: COMMON_LIBRARIES), array(
                'class' => 'positive finish'));

        $form->addGroup($buttons, 'buttons', null, '&nbsp;', false);
        return $form;
    }

    function retrieve_categories_recursive($parent, $exclude_category, $level = 1)
    {
        $conditions[] = new NotCondition(new EqualityCondition(PhrasesPublicationCategory :: PROPERTY_ID, $exclude_category));
        $conditions[] = new EqualityCondition(PhrasesPublicationCategory :: PROPERTY_PARENT, $parent);
        $condition = new AndCondition($conditions);

        $cats = PhrasesDataManager :: get_instance()->retrieve_phrases_publication_categories($condition);
        while ($cat = $cats->next_result())
        {
            $this->categories[$cat->get_id()] = str_repeat('--', $level) . ' ' . $cat->get_name();
            $this->retrieve_categories_recursive($cat->get_id(), $exclude_category, ($level + 1));
        }
    }

    function move_publications_to_category($form, $pids)
    {
        $category = $form->exportValue(PhrasesPublication :: PROPERTY_CATEGORY);

        if (! is_array($pids))
            $pids = array($pids);

        $condition = new InCondition(PhrasesPublication :: PROPERTY_ID, $pids);
        $publications = PhrasesDataManager :: get_instance()->retrieve_phrases_publications($condition);
        while ($publication = $publications->next_result())
        {
            if ($publication->get_category() == $category)
            {
                continue;
            }

            $publication->set_category($category);
            $publication->update();

            if ($category)
            {
                $new_parent_id = PhrasesRights :: get_location_id_by_identifier_from_phrasess_subtree($category, PhrasesRights :: TYPE_CATEGORY);
            }
            else
            {
                $new_parent_id = PhrasesRights :: get_phrasess_subtree_root_id();
            }

            $location = PhrasesRights :: get_location_by_identifier_from_phrasess_subtree($publication->get_id(), PhrasesRights :: TYPE_PUBLICATION);
            $location->move($new_parent_id);

        }

        return $category;
    }
}
?>