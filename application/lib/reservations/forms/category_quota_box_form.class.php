<?php
/**
 * $Id: category_quota_box_form.class.php 217 2009-11-13 14:12:25Z chellee $
 * @package application.reservations.forms
 */
require_once dirname(__FILE__) . '/../quota_box_rel_category.class.php';
require_once dirname(__FILE__) . '/../reservations_data_manager.class.php';

class CategoryQuotaBoxForm extends FormValidator
{

    const TYPE_CREATE = 1;
    const TYPE_EDIT = 2;
    const RESULT_SUCCESS = 'QuotaBoxRelCategoryUpdated';
    const RESULT_ERROR = 'QuotaBoxRelCategoryUpdateFailed';

    private $quota_box_rel_category;
    private $user;
    private $form_type;

    /**
     * Creates a new LanguageForm
     */
    function CategoryQuotaBoxForm($form_type, $action, $quota_box_rel_category, $user)
    {
        parent :: __construct('quota_box_rel_category_form', 'post', $action);

        $this->quota_box_rel_category = $quota_box_rel_category;
        $this->user = $user;
        $this->form_type = $form_type;

        if ($this->form_type == self :: TYPE_EDIT)
        {
            $this->build_editing_form();
        }
        else
        {
            $this->build_creation_form();
        }

        $this->build_basic_form();

        $this->setDefaults();
    }

    function build_creation_form()
    {
        $dm = ReservationsDataManager :: get_instance();

        $conditions = array();
        $qrc = $dm->retrieve_quota_box_rel_categories(new EqualityCondition(QuotaBoxRelCategory :: PROPERTY_CATEGORY_ID, $this->quota_box_rel_category->get_category_id()));
        while ($relation = $qrc->next_result())
        {
            $conditions[] = new NotCondition(new EqualityCondition(QuotaBox :: PROPERTY_ID, $relation->get_quota_box_id()));
        }

        if (count($conditions) > 0)
            $condition = new AndCondition($conditions);

        $quota_boxes[- 1] = Translation :: get('SelectQuotaBox');

        $qbs = $dm->retrieve_quota_boxes($condition);
        while ($qb = $qbs->next_result())
            $quota_boxes[$qb->get_id()] = $qb->get_name();

        $this->addElement('html', '<div class="configuration_form">');
        $this->addElement('html', '<span class="category">' . Translation :: get('Required') . '</span>');
        $this->addElement('select', QuotaBoxRelCategory :: PROPERTY_QUOTA_BOX_ID, Translation :: get('QuotaBox'), $quota_boxes);
        $this->addElement('html', '<div style="clear: both;"></div>');
        $this->addElement('html', '</div>');
    }

    /**
     * Creates a new basic form
     */
    function build_basic_form()
    {
        $dm = ReservationsDataManager :: get_instance();
        $udm = UserDataManager :: get_instance();
        $gdm = GroupDataManager :: get_instance();

        $this->addElement('html', '<div class="configuration_form">');
        $this->addElement('html', '<span class="category">' . Translation :: get('UsersGroups') . '</span>');

        $defaults = array();

        $condition = new EqualityCondition(QuotaBoxRelCategoryRelUser :: PROPERTY_QUOTA_BOX_REL_CATEGORY_ID, $this->quota_box_rel_category->get_id());
        $users = $dm->retrieve_quota_box_rel_category_rel_users($condition);
        while ($rel_user = $users->next_result())
        {
            $user = $udm->retrieve_user($rel_user->get_user_id());
            $id = 'user_' . $user->get_id();
            $defaults[$id] = array('id' => $id, 'title' => htmlentities($user->get_fullname(), ENT_COMPAT, 'UTF-8'), 'description' => htmlentities($user->get_fullname(), ENT_COMPAT, 'UTF-8'), 'class' => 'type type_group');
        }

        $condition = new EqualityCondition(QuotaBoxRelCategoryRelGroup :: PROPERTY_QUOTA_BOX_REL_CATEGORY_ID, $this->quota_box_rel_category->get_id());
        $groups = $dm->retrieve_quota_box_rel_category_rel_groups($condition);
        while ($rel_group = $groups->next_result())
        {
            $group = $gdm->retrieve_group($rel_group->get_group_id());
            $id = 'group_' . $group->get_id();
            $defaults[$id] = array('id' => $id, 'title' => htmlentities($group->get_name(), ENT_COMPAT, 'UTF-8'), 'description' => htmlentities($group->get_name(), ENT_COMPAT, 'UTF-8'), 'class' => 'type type_group');
        }

        //$url = Path :: get(WEB_PATH).'application/lib/reservations/xml_feeds/users_groups_xml_feed.php';
        $url = Path :: get(WEB_PATH) . 'common/xml_feeds/xml_user_group_feed.php';
        $locale = array();
        $locale['Display'] = Translation :: get('SelectRecipients');
        $locale['Searching'] = Translation :: get('Searching');
        $locale['NoResults'] = Translation :: get('NoResults');
        $locale['Error'] = Translation :: get('Error');

        $this->addElement('element_finder', 'users', Translation :: get('SelectUsersOrGroups'), $url, $locale, $defaults, array('load_elements' => false));

        $this->addElement('html', '<div style="clear: both;"></div>');
        $this->addElement('html', '</div>');

        // Submit button
        $buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Save'), array('class' => 'positive'));
        $buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset'), array('class' => 'normal empty'));

        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    /**
     * Builds an editing form
     */
    function build_editing_form()
    {
        $this->addElement('hidden', QuotaBoxRelCategory :: PROPERTY_ID);
    }

    function create_quota_box_rel_category()
    {
        $rdm = ReservationsDataManager :: get_instance();

        $quota_box_rel_category = $this->quota_box_rel_category;
        $category_id = $quota_box_rel_category->get_category_id();
        $values = $this->exportValues();
        $quota_box_id = $values[QuotaBoxRelCategory :: PROPERTY_QUOTA_BOX_ID];

        if ($quota_box_id == - 1)
            return false;

        $categories[] = new Category(array('id' => $category_id));
        $categories += Category :: retrieve_sub_categories($category_id, true);

        $succes = true;

        foreach ($categories as $category)
        {
            $category_id = $category->get_id();
            $conditions = array();
            $conditions[] = new EqualityCondition(QuotaBoxRelCategory :: PROPERTY_CATEGORY_ID, $category_id);
            $conditions[] = new EqualityCondition(QuotaBoxRelCategory :: PROPERTY_QUOTA_BOX_ID, $quota_box_id);
            $condition = new AndCondition($conditions);
            $quota_box_rel_category = $rdm->retrieve_quota_box_rel_categories($condition)->next_result();

            if ($quota_box_rel_category == null)
            {
                $quota_box_rel_category = new QuotaBoxRelCategory();
                $quota_box_rel_category->set_category_id($category_id);
                $quota_box_rel_category->set_quota_box_id($quota_box_id);

                $suc = $quota_box_rel_category->create();

                if ($succes)
                {
                    Event :: trigger('create_quota_box_category', ReservationsManager :: APPLICATION_NAME, array(ChangesTracker :: PROPERTY_REFERENCE_ID => $quota_box_rel_category->get_id(), ChangesTracker :: PROPERTY_USER_ID => $this->user->get_id()));
                }

                $succes &= $suc;
            }

            $succes &= $this->update_selection_for_category($values, $quota_box_rel_category);
        }

        return $succes;

    }

    function update_quota_box_rel_category()
    {
        $rdm = ReservationsDataManager :: get_instance();

        $quota_box_rel_category = $this->quota_box_rel_category;
        $category_id = $quota_box_rel_category->get_category_id();
        $quota_box_id = $quota_box_rel_category->get_quota_box_id();
        $values = $this->exportValues();

        $categories[] = new Category(array('id' => $category_id));
        $categories += Category :: retrieve_sub_categories($category_id, true);

        $succes = true;

        foreach ($categories as $category)
        {
            $category_id = $category->get_id();
            $conditions = array();
            $conditions[] = new EqualityCondition(QuotaBoxRelCategory :: PROPERTY_CATEGORY_ID, $category_id);
            $conditions[] = new EqualityCondition(QuotaBoxRelCategory :: PROPERTY_QUOTA_BOX_ID, $quota_box_id);
            $condition = new AndCondition($conditions);
            $quota_box_rel_category = $rdm->retrieve_quota_box_rel_categories($condition)->next_result();

            if (! $quota_box_rel_category)
                break;

            $succes &= $this->update_selection_for_category($values, $quota_box_rel_category);
        }

        return $succes;
    }

    function update_selection_for_category($values, $quota_box_rel_category)
    {
        $rdm = ReservationsDataManager :: get_instance();
        $succes = true;
        $selected_users = $values['users'];

        $rdm->empty_quota_box_rel_category($quota_box_rel_category->get_id());

        foreach ($selected_users as $user)
        {
            $split = explode('_', $user);
            $type = $split[0];
            $ref = $split[1];

            if ($type == 'user')
            {
                $qbrcru = new QuotaBoxRelCategoryRelUser();
                $qbrcru->set_quota_box_rel_category_id($quota_box_rel_category->get_id());
                $qbrcru->set_user_id($ref);
                $qbrcru->create();
            }
            else
            {
                $conditions = array();
                $conditions[] = new EqualityCondition(QuotaBoxRelCategoryRelGroup :: PROPERTY_GROUP_ID, $ref);
                $conditions[] = new EqualityCondition(QuotaBoxRelCategoryRelGroup :: PROPERTY_QUOTA_BOX_REL_CATEGORY_ID, $quota_box_rel_category->get_id());
                $condition = new AndCondition($conditions);

                $count = $rdm->count_quota_box_rel_category_rel_groups($condition);
                if ($count > 0)
                    continue;

                $qbrcrg = new QuotaBoxRelCategoryRelGroup();
                $qbrcrg->set_quota_box_rel_category_id($quota_box_rel_category->get_id());
                $qbrcrg->set_group_id($ref);
                $qbrcrg->create();

                $group = GroupDataManager :: get_instance()->retrieve_group($ref);

                //$subgroups = Group :: get_subgroups_from_group($ref, true);
                $subgroups = $group->get_subgroups();
                foreach ($subgroups as $subgroup)
                {
                    $conditions = array();
                    $conditions[] = new EqualityCondition(QuotaBoxRelCategoryRelGroup :: PROPERTY_GROUP_ID, $subgroup->get_id());
                    $conditions[] = new EqualityCondition(QuotaBoxRelCategoryRelGroup :: PROPERTY_QUOTA_BOX_REL_CATEGORY_ID, $quota_box_rel_category->get_id());
                    $condition = new AndCondition($conditions);

                    $count = $rdm->count_quota_box_rel_category_rel_groups($condition);
                    if ($count > 0)
                        continue;

                    $qbrcrg = new QuotaBoxRelCategoryRelGroup();
                    $qbrcrg->set_quota_box_rel_category_id($quota_box_rel_category->get_id());
                    $qbrcrg->set_group_id($subgroup->get_id());
                    $qbrcrg->create();
                }
            }
        }

        return $succes;
    }

    /**
     * Sets default values.
     * @param array $defaults Default values for this form's parameters.
     */
    function setDefaults($defaults = array ())
    {
        $quota_box_rel_category = $this->quota_box_rel_category;
        $defaults[QuotaBoxRelCategory :: PROPERTY_ID] = $quota_box_rel_category->get_id();
        parent :: setDefaults($defaults);
    }
}
?>