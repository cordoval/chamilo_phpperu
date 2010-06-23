<?php
/**
 * $Id: complex_browser.class.php 204 2009-11-13 12:51:30Z kariboe $
 * @package repository.lib.repository_manager.component
 */
/**
 * Default repository manager component which allows the user to browse through
 * the different categories and learning objects in the repository.
 */
class RepositoryManagerComplexBrowserComponent extends RepositoryManager
{
    private $cloi_id;
    private $root_id;

    private $action;
    private $in_creation = false;
    private $action_bar;

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $cloi_id = Request :: get(RepositoryManager :: PARAM_CLOI_ID);
        $root_id = Request :: get(RepositoryManager :: PARAM_CLOI_ROOT_ID);
        $publish = Request :: get('publish');

        $action = Request :: get('clo_action');
        if (! isset($action))
            $action = 'build';
        $this->action = $action;

        $trail = BreadcrumbTrail :: get_instance();
        $trail->add_help('repository general');
        if (! isset($publish))
            $trail->add(new Breadcrumb($this->get_link(array(Application :: PARAM_ACTION => RepositoryManager :: ACTION_BROWSE_CONTENT_OBJECTS)), Translation :: get('Repository')));

        if (isset($cloi_id) && isset($root_id))
        {
            $this->cloi_id = $cloi_id;
            $this->root_id = $root_id;
        }
        else
        {
            $this->display_header($trail, false, false);
            $this->display_error_message(Translation :: get('NoCLOISelected'));
            $this->display_footer();
            exit();
        }
        $root = $this->retrieve_content_object($root_id);
        $object = $this->retrieve_content_object($cloi_id);

        if (! isset($publish))
        {
            $trail->add(new Breadcrumb($this->get_link(array(Application :: PARAM_ACTION => RepositoryManager :: ACTION_VIEW_CONTENT_OBJECTS, RepositoryManager :: PARAM_CONTENT_OBJECT_ID => $root_id)), $root->get_title()));
            $trail->add(new Breadcrumb($this->get_url(array(RepositoryManager :: PARAM_CLOI_ID => $cloi_id, RepositoryManager :: PARAM_CLOI_ROOT_ID => $root_id)), Translation :: get('ViewComplexContentObject')));
        }

        $output = $this->get_content_html($object);
        $menu = $this->get_menu();

        $this->display_header($trail, false, false);

        if ($this->action_bar)
            echo '<br />' . $this->action_bar->as_html();

        echo '<br /><div class="tabbed-pane"><ul class="tabbed-pane-tabs">';
        echo '<li><a ' . ($action == 'build' ? 'class=current' : '') . ' href="' . $this->get_url(array(RepositoryManager :: PARAM_CLOI_ID => $cloi_id, RepositoryManager :: PARAM_CLOI_ROOT_ID => $root_id, 'clo_action' => 'build', 'publish' => Request :: get('publish'))) . '">' . Translation :: get('Build') . '</a></li>';
        echo '<li><a ' . ($action == 'organise' ? 'class=current' : '') . ' href="' . $this->get_url(array(RepositoryManager :: PARAM_CLOI_ID => $cloi_id, RepositoryManager :: PARAM_CLOI_ROOT_ID => $root_id, 'clo_action' => 'organise', 'publish' => Request :: get('publish'))) . '">' . Translation :: get('Organise') . '</a></li>';
        echo '</ul><div class="tabbed-pane-content">';
        echo '<br /><div style="width: 17%; float: left; overflow:auto;">' . $menu->render_as_tree() . '</div>';
        echo '<div style="width: 80%; float: right; border-left: 1px solid #4271B5; padding: 10px; padding-left: 20px;">' . $output . '</div>';
        echo '<div class="clear">&nbsp;</div></div></div>';

        $this->display_footer();
    }

    /**
     * Gets the  table which shows the learning objects in the currently active
     * category
     */
    private function get_content_html($object)
    {
        $html[] = '<h3>' . Translation :: get('SelectedContentObject') . '</h3><br />';
        $html[] = ContentObjectDisplay :: factory($object)->get_full_html();

        if (! $object instanceof ComplexContentObjectSupport)
        {
            $this->action_bar = $this->get_action_bar();
            return implode("\n", $html);
        }

        //$html[] = '<br /><div style="border-bottom: 1px solid #4271B5; width:100%;"></div><br />';


        if ($this->action == 'organise')
        {
            $html[] = '<br /><h3>' . Translation :: get('OrganiseChildren') . '</h3>';

            $parameters = $this->get_parameters();
            $parameters[ActionBarSearchForm :: PARAM_SIMPLE_SEARCH_QUERY] = $this->action_bar->get_query();

            $table = new ComplexBrowserTable($this, $parameters, $this->get_condition());
            $this->action_bar = $this->get_action_bar();
            $html[] = $table->as_html();
            return implode("\n", $html);
        }
        else
        {
            $html[] = $this->get_create_html();
            $this->action_bar = $this->get_action_bar();
            $html[] = $this->get_select_existing_html();
            return implode("\n", $html);
        }
    }

    private function get_create_html()
    {
        $html[] = '<h3>' . Translation :: get('AddToSelectedContentObject') . '</h3><br />';
        $html[] = '<h4>' . Translation :: get('CreateNew') . '</h4>';

        $clo = $this->retrieve_content_object($this->cloi_id);
        $types = $clo->get_allowed_types();
        foreach ($types as $type)
        {
            $type_options[$type] = Translation :: get(ContentObject :: type_to_class($type) . 'TypeName');
        }

        $type_form = new FormValidator('create_type', 'post', $this->get_parameters());

        asort($type_options);
        $type_form->addElement('select', RepositoryManager :: PARAM_CONTENT_OBJECT_TYPE, Translation :: get('CreateANew'), $type_options, array('class' => 'learning-object-creation-type', 'style' => 'width: 300px;'));
        //$type_form->addElement('submit', 'submit', Translation :: get('Ok'));
        $buttons[] = $type_form->createElement('style_submit_button', 'submit', Translation :: get('Select'), array('class' => 'normal select'));
        //$buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset'), array('class' => 'normal empty'));
        $type_form->addGroup($buttons, 'buttons', null, '&nbsp;', false);

        $type = ($type_form->validate() ? $type_form->exportValue(RepositoryManager :: PARAM_CONTENT_OBJECT_TYPE) : Request :: get(RepositoryManager :: PARAM_CONTENT_OBJECT_TYPE));

        if ($type || Request :: get('type'))
        {
            $this->in_creation = true;
            $object = ContentObject :: factory($type);
            $object->set_owner_id($this->get_user_id());

            $cloi = ComplexContentObjectItem :: factory($type);

            $cloi->set_user_id($this->get_user_id());
            $cloi->set_parent($this->cloi_id);
            $cloi->set_display_order(RepositoryDataManager :: get_instance()->select_next_display_order($this->cloi_id));
            $cloi_form = ComplexContentObjectItemForm :: factory_with_type(ComplexContentObjectItemForm :: TYPE_CREATE, $type, $cloi, 'create_complex', 'post', $this->get_url(array_merge($this->get_parameters(), array('type' => $type, 'object' => $objectid))));

            if ($cloi_form)
            {
                $elements = $cloi_form->get_elements();
                //$defaults = $cloi_form->get_default_values();
            }

            $lo_form = ContentObjectForm :: factory(ContentObjectForm :: TYPE_CREATE, $object, 'create', 'post', $this->get_url(array_merge($this->get_parameters(), array('type' => $type))), null, $elements);
            $lo_form->setDefaults($defaults);

            if ($lo_form->validate() || Request :: get('object'))
            {
                /*if(Request :: get('object'))
				{
					$objectid = Request :: get('object');
				}
				else
				{*/
                $object = $lo_form->create_content_object();
                $objectid = $object->get_id();
                //}


                if ($cloi_form)
                {
                    $cloi_form->get_complex_content_object_item()->set_ref($objectid);
                    $cloi_form->create_cloi_from_values($lo_form->exportValues());
                }
                else
                {
                    $cloi->set_ref($objectid);
                    $cloi->create();
                }

                $this->in_creation = false;
                $this->redirect(Translation :: get('ContentObjectAdded'), false, array(Application :: PARAM_ACTION => RepositoryManager :: ACTION_BROWSE_COMPLEX_CONTENT_OBJECTS, RepositoryManager :: PARAM_CLOI_ID => $this->get_cloi_id(), RepositoryManager :: PARAM_CLOI_ROOT_ID => $this->get_root_id(), 'publish' => Request :: get('publish'), 'clo_action' => 'build'));

            /*$cloi = ComplexContentObjectItem :: factory($type);

				$cloi->set_ref($objectid);
				$cloi->set_user_id($this->get_user_id());
				$cloi->set_parent($this->cloi_id);
				$cloi->set_display_order(RepositoryDataManager :: get_instance()->select_next_display_order($this->cloi_id));

				$cloi_form = ComplexContentObjectItemForm :: factory(ComplexContentObjectItemForm :: TYPE_CREATE, $cloi, 'create_complex', 'post', $this->get_url(array_merge($this->get_parameters(), array('type' => $type, 'object' => $objectid))));

				if($cloi_form)
				{
					if ($cloi_form->validate() || !$cloi->is_extended())
					{
						$cloi_form->create_complex_content_object_item();
						/*$cloi = $cloi_form->get_complex_content_object_item();
						$root_id = $root_id?$root_id:$cloi->get_id();
						if($cloi->is_complex()) $id = $cloi->get_ref(); else $id = $cloi->get_parent();
						$this->redirect(Translation :: get('ObjectCreated'), false, array(Application :: PARAM_ACTION => RepositoryManager :: RepositoryManager :: ACTION_BROWSE_COMPLEX_CONTENT_OBJECTS, RepositoryManager :: PARAM_CLOI_ID => $id,  RepositoryManager :: PARAM_CLOI_ROOT_ID => $root_id, 'publish' => Request :: get('publish')));*/
            /*$renderer = clone $type_form->defaultRenderer();
						$renderer->setElementTemplate('{label} {element} ');
						$type_form->accept($renderer);
						$html[] = $renderer->toHTML();
						$this->in_creation = false;
						$this->redirect(Translation :: get('ContentObjectAdded'), false, array(Application :: PARAM_ACTION => RepositoryManager :: RepositoryManager :: ACTION_BROWSE_COMPLEX_CONTENT_OBJECTS, RepositoryManager :: PARAM_CLOI_ID => $this->get_cloi_id(),  RepositoryManager :: PARAM_CLOI_ROOT_ID => $this->get_root_id(), 'publish' => Request :: get('publish'), 'clo_action' => 'build'));
					}
					else
					{
						//$html[] = '<p>' . Translation :: get('FillIn') . '</p>';
						$html[] = $cloi_form->toHTML();
					}
				}
				else
				{
					$cloi->create();
					$this->in_creation = false;
					/*$renderer = clone $type_form->defaultRenderer();
					$renderer->setElementTemplate('{label} {element} ');
					$type_form->accept($renderer);
					$html[] = $renderer->toHTML();
					$this->redirect(Translation :: get('ContentObjectAdded'), false, array(Application :: PARAM_ACTION => RepositoryManager :: RepositoryManager :: ACTION_BROWSE_COMPLEX_CONTENT_OBJECTS, RepositoryManager :: PARAM_CLOI_ID => $this->get_cloi_id(),  RepositoryManager :: PARAM_CLOI_ROOT_ID => $this->get_root_id(), 'publish' => Request :: get('publish'), 'clo_action' => 'build'));
				}*/

            }
            else
            {
                $html[] = $lo_form->toHTML();
            }
        }
        else
        {
            $quotamanager = new QuotaManager($this->get_user());
            if ($quotamanager->get_available_database_space() <= 0)
            {
                Display :: warning_message(htmlentities(Translation :: get('MaxNumberOfContentObjectsReached')));
            }
            else
            {
                $renderer = clone $type_form->defaultRenderer();
                $renderer->setElementTemplate('{label} {element} ');
                $type_form->accept($renderer);
                $html[] = $renderer->toHTML();
            }
        }

        return implode("\n", $html);
    }

    private function get_select_existing_html()
    {
        if (! $this->in_creation)
        {
            $html[] = '<br /><h4>' . Translation :: get('SelectExisting') . '</h4>';

            $clo = $this->retrieve_content_object($this->cloi_id);
            $types = $clo->get_allowed_types();

            $parameters = array_merge(array('types' => $types), $this->get_parameters());
            $parameters[ActionBarSearchForm :: PARAM_SIMPLE_SEARCH_QUERY] = $this->action_bar->get_query();

            $table = new RepositoryBrowserTable($this, $parameters, $this->get_selector_condition($types));
            $html[] = $table->as_html();

            return implode("\n", $html);
        }
    }

    public function get_parameters()
    {
        $param = array(RepositoryManager :: PARAM_CLOI_ROOT_ID => $this->root_id, RepositoryManager :: PARAM_CLOI_ID => $this->cloi_id, 'publish' => Request :: get('publish'), 'action' => $this->action);
        return array_merge($param, parent :: get_parameters());
    }

    function get_condition()
    {
        if (isset($this->cloi_id))
        {
            return new EqualityCondition(ComplexContentObjectItem :: PROPERTY_PARENT, $this->cloi_id, ComplexContentObjectItem :: get_table_name());
        }
        return null;
    }

    private function get_selector_condition($types)
    {
        $conditions = array();
        $conditions1 = array();
        $conditions2 = array();

        if ($this->action_bar)
        {
            $query = $this->action_bar->get_query();
            if ($query)
            {
                $conditions2[] = new PatternMatchCondition(ContentObject :: PROPERTY_TITLE, '*' . $query . '*');
                $conditions2[] = new PatternMatchCondition(ContentObject :: PROPERTY_DESCRIPTION, '*' . $query . '*');
                $conditions[] = new OrCondition($conditions2);
            }
        }

        foreach ($types as $type)
        {
            $conditions1[] = new EqualityCondition(ContentObject :: PROPERTY_TYPE, $type);
        }
        if ($conditions1)
            $conditions[] = new OrCondition($conditions1);
        else
            $conditions[] = new EqualityCondition(ContentObject :: PROPERTY_TYPE, 'none');

        $conditions = array_merge($conditions, $this->retrieve_used_items($this->root_id));
        $conditions[] = new NotCondition(new EqualityCondition(ContentObject :: PROPERTY_ID, $this->root_id, ContentObject :: get_table_name()));
        return new AndCondition($conditions);
    }

    private function retrieve_used_items($cloi_id)
    {
        $conditions = array();

        $clois = $this->retrieve_complex_content_object_items(new EqualityCondition(ComplexContentObjectItem :: PROPERTY_PARENT, $cloi_id, ComplexContentObjectItem :: get_table_name()));
        while ($cloi = $clois->next_result())
        {
            if ($cloi->is_complex())
            {
                $conditions[] = new NotCondition(new EqualityCondition(ContentObject :: PROPERTY_ID, $cloi->get_ref(), ContentObject :: get_table_name()));
                $conditions = array_merge($conditions, $this->retrieve_used_items($cloi->get_ref()));
            }
        }

        return $conditions;
    }

    private function get_menu()
    {
        if (isset($this->cloi_id) && isset($this->root_id))
        {
            return new ComplexContentObjectMenu($this->root_id, $this->cloi_id, '?go=browsecomplex&cloi_id=%s&cloi_root_id=%s', true);
        }
        return null;
    }

    function get_action_bar()
    {
        $pub = Request :: get('publish');
        if (($pub != 1 && $this->action == 'organise') || $this->in_creation)
            return null;

        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);

        if (! $this->in_creation)
        {
            $action_bar->set_search_url($this->get_url($this->get_parameters()));
            $action_bar->add_common_action(new ToolbarItem(Translation :: get('ShowAll'), Theme :: get_common_image_path() . 'action_browser.png', $this->get_url($this->get_parameters())));
        }

        if ($pub && $pub != '')
        {
            $action_bar->add_common_action(new ToolbarItem(Translation :: get('Publish'), Theme :: get_common_image_path() . 'action_publish.png', $_SESSION['redirect_url']));
        }

        return $action_bar;
    }

    function get_root()
    {
        return $this->root_id;
    }

    function get_root_id()
    {
        return $this->root_id;
    }

    function get_cloi_id()
    {
        return $this->cloi_id;
    }
}
?>