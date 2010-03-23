<?php
/**
 * $Id: prerequisites_builder_form.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_builder.learning_path.component.prerequisites_builder
 */

class PrerequisitesBuilderForm extends FormValidator
{
    private $parent;
    private $user;
    private $clo_item;

    /**
     * Creates a new AccountForm
     */
    function PrerequisitesBuilderForm($user, $clo_item, $action)
    {
        parent :: __construct('prerequisites', 'post', $action);

        $this->user = $user;
        $this->clo_item = $clo_item;

        $this->setDefaults();

        $this->handle_session_values();
        $this->build_basic_form();
    }

//    function build_basic_form()
//    {
//        $this->build_list();
//        $buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('SavePrerequisites'), array('class' => 'positive save'));
//        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
//    }

    function handle_session_values()
    {
        if (! $this->isSubmitted())
        {
            unset($_SESSION['number_of_groups']);
            unset($_SESSION['number_of_items']);
            unset($_SESSION['skip_items']);
            unset($_SESSION['skip_groups']);
        }

        if (! isset($_SESSION['number_of_groups']))
        {
            $_SESSION['number_of_groups'] = 1;
            $_SESSION['number_of_items'][0] = 2;
        }
        if (! isset($_SESSION['skip_groups']))
        {
            $_SESSION['skip_groups'] = array();
            $_SESSION['skip_items'][0] = array();
        }

        if ($this->number_of_groups)
        {
            $_SESSION['number_of_groups'] = $this->number_of_groups;
        }

        if ($this->number_of_items)
        {
            $_SESSION['number_of_items'] = $this->number_of_items;
        }

        if (isset($_POST['add_group']))
        {
            $_SESSION['number_of_groups'] = $_SESSION['number_of_groups'] + 1;
            $group_number = $_SESSION['number_of_groups'] - 1;
            $_SESSION['number_of_items'][$group_number] = 2;
            $_SESSION['skip_items'][$group_number] = array();
        }
        if (isset($_POST['remove_group']))
        {
            $indexes = array_keys($_POST['remove_group']);
            $_SESSION['skip_groups'][] = $indexes[0];
            unset($_SESSION['number_of_items'][$indexes[0]]);
        }

        if (isset($_POST['add_item']))
        {
            $indexes = array_keys($_POST['add_item']);
            $_SESSION['number_of_items'][$indexes[0]] ++;
        }
        if (isset($_POST['remove_item']))
        {
            foreach ($_POST['remove_item'] as $group_number => $item)
            {
                $indexes = array_keys($item);
                $_SESSION['skip_items'][$group_number][] = $indexes[0];
            }
        }
    }

    function build_basic_form()
    {
        $renderer = &$this->defaultRenderer();
//        $renderer->setElementTemplate('<div style="margin: 10px 0px;">{element}<div class="clear"></div></div>', 'option_buttons');
//        $renderer->setGroupElementTemplate('<div style="text-align: center; margin-right: 10px;">{element}</div>', 'option_buttons');

//        $gbuttons = array();
//        $gbuttons[] = $this->createElement('style_button', 'add_group[]', Translation :: get('AddPrerequisiteGroup'), array('class' => 'normal add', 'id' => 'add_group'));
//        $this->addGroup($gbuttons, 'option_buttons', null, '', false);

        $operator = array('' => Translation :: get('Operator'), '&' => Translation :: get('And'), '|' => Translation :: get('Or'));
        $goperator = array('&' => Translation :: get('And'), '|' => Translation :: get('Or'));
        //$goperator = array('' => Translation :: get('GroupOperator'), '&' => Translation :: get('And'), '|' => Translation :: get('Or'));

        $not = array('' => '', '~' => Translation :: get('Not'));
        $siblings = $this->retrieve_siblings();

        $number_of_groups = intval($_SESSION['number_of_groups']);
        $gcounter = 0;
        for($group_number = 0; $group_number < $number_of_groups; $group_number ++)
        {
            if (! in_array($group_number, $_SESSION['skip_groups']))
            {
                $category_html = array();
                $category_html[] = '<div class="prerequisite_group">';
                $category_html[] = '<div class="header">';
                $category_html[] = '<div class="operator">';
                $this->addElement('html', implode("\n", $category_html));

                if ($gcounter > 0 && $group_number != 0)
                {
                    $renderer->setElementTemplate('{element}', 'group_operator[' . $group_number . ']');
                    $this->addElement('select', 'group_operator[' . $group_number . ']', '', $goperator);
                }

                $category_html = array();
                $category_html[] = '</div>';
                $category_html[] = '<div class="title">' . Translation :: get('PrerequisiteGroup') . ' ' . ($gcounter + 1) . '</div>';
                $category_html[] = '<div class="actions">';
                $this->addElement('html', implode("\n", $category_html));

                if ($_SESSION['number_of_groups'] - count($_SESSION['skip_groups']) > 1)
                {
                    $renderer->setElementTemplate('{element}', 'remove_group[' . $group_number . ']');
                    $group[] = $this->addElement('image', 'remove_group[' . $group_number . ']', Theme :: get_common_image_path() . 'action_delete.png', array('title' => Translation :: get('RemoveGroup'), 'class' => 'remove_group', 'id' => $group_number));
                }

                $category_html = array();
                $category_html[] = '</div>';
                $category_html[] = '</div>';
                $category_html[] = '<div class="body">';
                $this->addElement('html', implode("\n", $category_html));
                //$this->addElement('category', Translation :: get('PrerequisiteGroup') . ' ' . ($gcounter + 1));

                $number_of_items = intval($_SESSION['number_of_items'][$group_number]);

                $counter = 0;
                for($item_number = 0; $item_number < $number_of_items; $item_number ++)
                {
                    if (! in_array($item_number, $_SESSION['skip_items'][$group_number]))
                    {
                        $identifier = '[' . $group_number . '][' . $item_number . ']';
                        $group = array();

                        if ($counter > 0)
                        {
                            $group[] = $this->createElement('select', 'operator' . $identifier, '', $operator);
                        }
                        else
                        {
                            $element = $this->createElement('select', 'operator' . $identifier, '', $operator, array('disabled'));
                            $group[] = $element;
                        }

                        $group[] = $this->createElement('select', 'not' . $identifier, '', $not);
                        $group[] = $this->createElement('select', 'prerequisite' . $identifier, '', $siblings);

                        if ($_SESSION['number_of_items'][$group_number] - count($_SESSION['skip_items'][$group_number]) > 1)
                        {
                            $group[] = & $this->createElement('image', 'remove_item[' . $group_number . '][' . $item_number . ']', Theme :: get_common_image_path() . 'action_delete.png', array('title' => Translation :: get('RemoveItem'), 'class' => 'remove_item', 'id' => $group_number . '_' . $item_number));
                        }

                        $this->addGroup($group, 'item_' . $group_number . '_' . $item_number, null, '', false);
                        $renderer->setGroupElementTemplate('{element} &nbsp; ', 'item_' . $group_number . '_' . $item_number);

                        $counter ++;
                    }
                }

                $gcounter ++;

//                $group = array();
                //$group[] = &$this->createElement('image', 'create_item[' . $group_number . ']', Theme :: get_common_image_path() . 'action_add.png', array('title' => Translation :: get('AddItem'), 'class' => 'create_item', 'id' => $group_number));

                $renderer->setElementTemplate('{element}', 'add_item[' . $group_number . ']');
                $this->addElement('html', '<div style="border-top: 1px dotted #cecece; padding: 10px;">');
                $this->addElement('image', 'add_item[' . $group_number . ']', Theme :: get_common_image_path() . 'action_add.png', array('title' => Translation :: get('AddItem'), 'class' => 'add_item', 'id' => $group_number));
                $this->addElement('html', '</div>');

//                if ($_SESSION['number_of_groups'] - count($_SESSION['skip_groups']) > 1)
//                {
//                    $group[] = &$this->createElement('image', 'remove_group[' . $group_number . ']', Theme :: get_common_image_path() . 'action_delete.png', array('title' => Translation :: get('RemoveGroup'), 'class' => 'remove_group', 'id' => $group_number));
//                }

//                $this->addGroup($group, 'group_' . $group_number, null, '', false);
//                $renderer->setElementTemplate('<div style="float: right; margin: 10px 0px 10px 0px;">{element}<div class="clear"></div></div>', 'group_' . $group_number);

                $category_html = array();
                $category_html[] = '<div style="clear: both;"></div>';
                $category_html[] = '</div>';
                $category_html[] = '<div style="clear: both;"></div>';
                $category_html[] = '</div>';
                $this->addElement('html', implode("\n", $category_html));
            }
        }

        $form_buttons = array();
        $form_buttons[] = $this->createElement('style_button', 'add_group[]', Translation :: get('AddPrerequisiteGroup'), array('class' => 'normal add', 'id' => 'add_group'));
        $form_buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('SavePrerequisites'), array('class' => 'positive save'));
        $this->addGroup($form_buttons, 'option_buttons', null, '&nbsp;', false);
    }

    private $siblings;

    function retrieve_siblings()
    {
        $conditions = array();
        $conditions[] = new EqualityCondition(ComplexContentObjectItem :: PROPERTY_PARENT, $this->clo_item->get_parent(), ComplexContentObjectItem :: get_table_name());
        $conditions[] = new NotCondition(new EqualityCondition(ComplexContentObjectItem :: PROPERTY_ID, $this->clo_item->get_id(), ComplexContentObjectItem :: get_table_name()));
        $condition = new AndCondition($conditions);

        $rdm = RepositoryDataManager :: get_instance();

        $siblings_list = $rdm->retrieve_complex_content_object_items($condition);
        while ($sibbling = $siblings_list->next_result())
        {
            $lo = $rdm->retrieve_content_object($sibbling->get_ref());
            
            if($lo->get_type() == 'learning_path')
            {
            	continue;
            }
            
            if ($lo->get_type() == 'learning_path_item')
            {
                $lo = $rdm->retrieve_content_object($lo->get_reference());
            }

            $siblings[$sibbling->get_id()] = $lo->get_title();
        }

        return $siblings;
    }

    function validate()
    {
        if (isset($_POST['submit']))
        {
            return parent :: validate();
        }

        return false;
    }

    private $number_of_groups;
    private $number_of_items;

    function setDefaults($defaults = array())
    {
        $prerequisites = $this->clo_item->get_prerequisites();
        if ($prerequisites && ! $this->isSubmitted())
        {
            $pattern = '/\([^\)]*\)/';
            $matches = array();
            preg_match_all($pattern, $prerequisites, $matches);
            $groups = $matches[0];

            foreach ($groups as $i => $group)
            {
                $prerequisites = str_replace($group, '_', $prerequisites);
                $group = str_replace('(', '', $group);
                $group = str_replace(')', '', $group);

                $or_values = explode('|', $group);

                $item_counter = 0;
                foreach ($or_values as $or_value)
                {
                    if (strpos($or_value, '&') === false)
                    {
                        if (Text :: char_at($or_value, 0) == '~')
                        {
                            $or_value = substr($or_value, 1);
                            $defaults['not'][$i][$item_counter] = '~';
                        }

                        $defaults['prerequisite'][$i][$item_counter] = $or_value;
                        if ($item_counter > 0)
                            $defaults['operator'][$i][$item_counter] = '|';

                        $item_counter ++;
                        continue;
                    }

                    $and_values = explode('&', $or_value);
                    foreach ($and_values as $and_value)
                    {
                        if (Text :: char_at($and_value, 0) == '~')
                        {
                            $and_value = substr($and_value, 1);
                            $defaults['not'][$i][$item_counter] = '~';
                        }

                        $defaults['prerequisite'][$i][$item_counter] = $and_value;

                        if ($item_counter > 0)
                            $defaults['operator'][$i][$item_counter] = '&';
                        $item_counter ++;
                    }
                }

                $this->number_of_items[$i] = $item_counter;

            }

            $this->number_of_groups = count($groups);

            $operators = explode('_', $prerequisites);

            $defaults['group_operator'] = $operators;

            if($this->number_of_groups == 0 && is_numeric($prerequisites))
            {
            	$this->number_of_groups = 1;
            	$this->number_of_items[0] = 1;
            	$defaults['prerequisite'][0][0] = $prerequisites;
            }
        }

        parent :: setDefaults($defaults);
    }

    function build_prerequisites()
    {
        $values = $this->exportValues();
        $prereq_formula = '';

        foreach ($values['prerequisite'] as $group_number => $items)
        {
            $prereq_formula .= $values['group_operator'][$group_number] . '(';

            foreach ($items as $item_number => $item)
            {
                $prereq_formula .= $values['operator'][$group_number][$item_number] . $values['not'][$group_number][$item_number] . $item;
            }

            $prereq_formula .= ')';
        }

        $this->clo_item->set_prerequisites($prereq_formula);
        return $this->clo_item->update();
    }

}
?>