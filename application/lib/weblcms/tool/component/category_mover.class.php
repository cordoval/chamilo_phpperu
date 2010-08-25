<?php
/**
 * $Id: move_selected_to_category.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.component
 */
require_once dirname(__FILE__) . '/../tool_component.class.php';

class ToolCategoryMoverComponent extends ToolComponent
{

    function run()
    {
        if ($this->is_allowed(EDIT_RIGHT))
        {
            $form = $this->build_move_to_category_form();
            if (! $form)
            {
                $this->display_header();
                $this->display_error_message('CategoryFormCouldNotBeBuild');
                $this->display_footer();
            }

            $publication_ids = Request :: get(Tool :: PARAM_PUBLICATION_ID);
            if (! is_array($publication_ids))
            {
                $publication_ids = array($publication_ids);
            }
            $form->addElement('hidden', 'pids', implode('-', $publication_ids));
            if ($form->validate())
            {
                $values = $form->exportValues();
                $publication_ids = explode('-', $values['pids']);
                //TODO: update all publications in a single action/query
                foreach ($publication_ids as $index => $publication_id)
                {
                    $publication = WeblcmsDataManager :: get_instance()->retrieve_content_object_publication($publication_id);
                    $publication->set_category_id($form->exportValue('category'));
                    $publication->update();
                    
		        	if($publication->get_category_id())
		        	{
		        		$new_parent_id = WeblcmsRights :: get_location_id_by_identifier_from_courses_subtree(WeblcmsRights :: TYPE_COURSE_CATEGORY, $publication->get_category_id(), $publication->get_course_id());
		        	}
		        	else
		        	{
		        		$course_module_id = WeblcmsDataManager :: get_instance()->retrieve_course_module_by_name($publication->get_course_id(), $publication->get_tool())->get_id();
		        		$new_parent_id = WeblcmsRights :: get_location_id_by_identifier_from_courses_subtree(WeblcmsRights :: TYPE_COURSE_MODULE, $course_module_id, $publication->get_course_id());	
		        	}
		        	
		        	$location =  WeblcmsRights :: get_location_by_identifier_from_courses_subtree(WeblcmsRights :: TYPE_PUBLICATION, $publication->get_id(), $publication->get_course_id());
		        	if($location)
		        	{
		        		$location->move($new_parent_id);
		        	}

                }
                if (count($publication_ids) == 1)
                {
                    $message = Translation :: get('ContentObjectPublicationMoved');
                }
                else
                {
                    $message = Translation :: get('ContentObjectPublicationsMoved');
                }
                $this->redirect($message, false, array('tool_action' => null, Tool :: PARAM_PUBLICATION_ID => null));
            }
            else
            {
                //$message = $form->toHtml();
                $trail = BreadcrumbTrail :: get_instance();
                $trail->add_help('courses general');

                $this->display_header();
                $form->display();
                $this->display_footer();
            }
        }
    }

    private $tree;

    function build_move_to_category_form()
    {
        $publication_ids = Request :: get(Tool :: PARAM_PUBLICATION_ID);
        if (! is_array($publication_ids))
        {
            $publication_ids = array($publication_ids);
        }

        if (count($publication_ids) > 0)
        {
            $pub = WeblcmsDataManager :: get_instance()->retrieve_content_object_publication($publication_ids[0]);
            if ($pub)
            {
                $cat = $pub->get_category_id();
                if ($cat != 0)
                    $this->tree[0] = Translation :: get('Root');
                $this->build_category_tree(0, $cat);
                $form = new FormValidator('select_category', 'post', $this->get_url(array(Tool :: PARAM_ACTION => Tool :: ACTION_MOVE_TO_CATEGORY, Tool :: PARAM_PUBLICATION_ID => Request :: get(Tool :: PARAM_PUBLICATION_ID))));
                $form->addElement('select', 'category', Translation :: get('Category'), $this->tree);
                //$form->addElement('submit', 'submit', Translation :: get('Ok'));
                $buttons[] = $form->createElement('style_submit_button', 'submit', Translation :: get('Move'), array('class' => 'positive move'));
                $buttons[] = $form->createElement('style_reset_button', 'reset', Translation :: get('Reset'), array('class' => 'normal empty'));

                $form->addGroup($buttons, 'buttons', null, '&nbsp;', false);
                return $form;

            }
        }
    }

    private $level = 1;

    function build_category_tree($parent_id, $exclude)
    {
        $dm = WeblcmsDataManager :: get_instance();
        $conditions[] = new EqualityCondition(ContentObjectPublicationCategory :: PROPERTY_PARENT, $parent_id);
        $conditions[] = new EqualityCondition(ContentObjectPublicationCategory :: PROPERTY_COURSE, $this->get_course_id());
        $conditions[] = new EqualityCondition(ContentObjectPublicationCategory :: PROPERTY_TOOL, $this->get_tool_id());
        $condition = new AndCondition($conditions);
        $categories = WeblcmsDataManager :: get_instance()->retrieve_content_object_publication_categories($condition);

        $tree = array();
        while ($cat = $categories->next_result())
        {
            if ($cat->get_id() != $exclude)
            {
                $this->tree[$cat->get_id()] = str_repeat('--', $this->level) . ' ' . $cat->get_name();
            }
            $this->level ++;
            $this->build_category_tree($cat->get_id(), $exclude);
            $this->level --;
        }
    }
}

?>