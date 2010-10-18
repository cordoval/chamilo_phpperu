<?php

class InternshipOrganizerPeriodSubscribeCategoryForm extends FormValidator
{
    
    const APPLICATION_NAME = 'internship_organizer';
    const PARAM_TARGET = 'categories';
    
    private $parent;
    private $period;
    private $user;

    function InternshipOrganizerPeriodSubscribeCategoryForm($period, $action, $user)
    {
        parent :: __construct('period_subscribe_categories', 'post', $action);
        
        $this->period = $period;
        $this->user = $user;
        $this->build_form();
        $this->setDefaults();
    }

    function build_form()
    {
        $period = $this->perod;
        $parent = $this->parent;
        
        $url = Path :: get(WEB_PATH) . 'application/internship_organizer/php/xml_feeds/xml_category_feed.php';
        $locale = array();
        $locale['Display'] = Translation :: get('ChooseCategories');
        $locale['Searching'] = Translation :: get('Searching');
        $locale['NoResults'] = Translation :: get('NoResults');
        $locale['Error'] = Translation :: get('Error');
        
        $elem = $this->addElement('element_finder', self :: PARAM_TARGET, Translation :: get('Categories'), $url, $locale, $this->get_categories());
        
        $defaults = array();
        $elem->setDefaults($this->get_categories());
        $elem->setDefaultCollapsed(false);
        
        $buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Create'), array('class' => 'positive update'));
        $buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset'), array('class' => 'normal empty'));
        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    
    }

    function create_categroy_rel_period()
    {
        $period_id = $this->period->get_id();
        
        $values = $this->exportValues();
        $category_ids = $values[self :: PARAM_TARGET]['category'];
        
        $succes = false;
        
        foreach ($category_ids as $category_id)
        {
            $conditions = array();
            $conditions[] = new EqualityCondition(InternshipOrganizerCategoryRelPeriod :: PROPERTY_PERIOD_ID, $period_id);
            $conditions[] = new EqualityCondition(InternshipOrganizerCategoryRelPeriod :: PROPERTY_CATEGORY_ID, $category_id);
            $condition = new AndCondition($conditions);
            $category_rel_periods = InternshipOrganizerDataManager :: get_instance()->retrieve_category_rel_periods($condition);
            if ($category_rel_periods->next_result())
            {
                continue;
            }
            else
            {
                $category_rel_period = new InternshipOrganizerCategoryRelPeriod();
                $category_rel_period->set_period_id($period_id);
                $category_rel_period->set_category_id($category_id);
                $succes = $category_rel_period->create();
                if ($succes)
                {
                    //                        Event :: trigger('create', 'agreement_rel_user', array('target_agreement_id' => $agreement->get_id(), 'action_user_id' => $this->user->get_id()));
                }
            }
        
        }
        
        return $succes;
    }

    function get_categories()
    {
        $condition = new EqualityCondition(InternshipOrganizerCategoryRelPeriod :: PROPERTY_PERIOD_ID, $this->period->get_id());
        $categorie_rel_periods = InternshipOrganizerDataManager :: get_instance()->retrieve_category_rel_periods($condition);
        $categorie_elements = array();
        while ($category_rel_period = $categorie_rel_periods->next_result())
        {
            $element = array();
            $element['id'] = $category_rel_period->get_category_id();
            $element['classes'] = 'category unlinked';
            $name = $category_rel_period->get_optional_property(InternshipOrganizerCategory :: PROPERTY_NAME);
            $element['title'] = $name;
            $description = $category_rel_period->get_optional_property(InternshipOrganizerCategory :: PROPERTY_DESCRIPTION);
            if (! isset($description) && empty($description))
            {
                $description = $name;
            }
            $element['description'] = $description;
            $categorie_elements[] = $element;
        }
        return $categorie_elements;
    }
}

?>