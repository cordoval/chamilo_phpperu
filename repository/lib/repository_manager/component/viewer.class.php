<?php
/**
 * $Id: viewer.class.php 204 2009-11-13 12:51:30Z kariboe $
 * @package repository.lib.repository_manager.component
 */
/**
 * Repository manager component which can be used to view a learning object.
 */
class RepositoryManagerViewerComponent extends RepositoryManager
{
    private $action_bar;
    private $object;
    private $tabs;

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $id = Request :: get(RepositoryManager :: PARAM_CONTENT_OBJECT_ID);
        if ($id)
        {
            $renderer_name = Utilities :: camelcase_to_underscores(get_class($this));
            $this->tabs = new DynamicTabsRenderer($renderer_name);

            $object = $this->retrieve_content_object($id);
            $this->object = $object;
            // TODO: Use Roles & Rights here.
            if ($object->get_owner_id() != $this->get_user_id() && ! $this->has_right($object, $this->get_user_id(), RepositoryRights :: VIEW_RIGHT))
            {
                $this->not_allowed();
            }

            $display = ContentObjectDisplay :: factory($object);
            $trail = BreadcrumbTrail :: get_instance();
            $trail->add_help('repository general');

            if ($object->get_state() == ContentObject :: STATE_RECYCLED)
            {
                $trail->add(new Breadcrumb($this->get_recycle_bin_url(), Translation :: get('RecycleBin')));
                $this->force_menu_url($this->get_recycle_bin_url());
            }
            $trail->add(new Breadcrumb($this->get_url(), $object->get_title() . ($object->is_latest_version() ? '' : ' (' . Translation :: get('OldVersion') . ')')));

            $version_data = array();
            $versions = $object->get_content_object_versions();

            $publication_attr = array();

            foreach ($object->get_content_object_versions() as $version)
            {
                // If this learning object is published somewhere in an application, these locations are listed here.
                $publications = $this->get_content_object_publication_attributes($this->get_user(), $version->get_id());
                $publication_attr = array_merge($publication_attr, $publications);
            }

            $this->action_bar = $this->get_action_bar($object);

            if (count($versions) >= 2)
            {
                Utilities :: order_content_objects_by_id_desc($versions);
                foreach ($versions as $version)
                {
                    $version_entry = array();
                    $version_entry['id'] = $version->get_id();
                    if (strlen($version->get_title()) > 20)
                    {
                        $version_entry['title'] = substr($version->get_title(), 0, 20) . '...';
                    }
                    else
                    {
                        $version_entry['title'] = $version->get_title();
                    }
                    $version_entry['date'] = date('d M y, H:i', $version->get_creation_date());
                    $version_entry['comment'] = $version->get_comment();
                    $version_entry['viewing_link'] = $this->get_content_object_viewing_url($version);

                    $delete_url = $this->get_content_object_deletion_url($version, 'version');
                    if (isset($delete_url))
                    {
                        $version_entry['delete_link'] = $delete_url;
                    }

                    $revert_url = $this->get_content_object_revert_url($version, 'version');
                    if (isset($revert_url))
                    {
                        $version_entry['revert_link'] = $revert_url;
                    }

                    $version_data[] = $display->get_version_as_html($version_entry);
                }

                $version_tab_content = array();
                //                $form = ContentObjectForm :: factory(ContentObjectForm :: TYPE_COMPARE, $object, 'compare', 'post', $this->get_url(array(RepositoryManager :: PARAM_CONTENT_OBJECT_ID => $object->get_id())), array('version_data' => $version_data));
                //                if ($form->validate())
                //                {
                //                    $params = $form->compare_content_object();
                //                    $params[Application :: PARAM_ACTION] = RepositoryManager :: ACTION_COMPARE_CONTENT_OBJECTS;
                //                    $this->redirect(null, false, $params);
                //                }
                //                else
                //                {
                $this->display_header($trail, false, true);

                if ($this->action_bar)
                {
                    echo '<br />' . $this->action_bar->as_html();
                }

                echo $display->get_full_html();
                //                    $version_tab_content[] = $form->toHtml();
                //                }


                $version_parameters = array(RepositoryManager :: PARAM_APPLICATION => RepositoryManager :: APPLICATION_NAME, RepositoryManager :: PARAM_CONTENT_OBJECT_ID => $this->object->get_id(), RepositoryManager :: PARAM_ACTION => RepositoryManager :: ACTION_COMPARE_CONTENT_OBJECTS);

                $version_browser = new RepositoryVersionBrowserTable($this, $version_parameters, new EqualityCondition(ContentObject :: PROPERTY_OBJECT_NUMBER, $object->get_object_number()));
                $version_tab_content[] = $version_browser->as_html();
                $version_tab_content[] = ResourceManager :: get_instance()->get_resource_html(Path :: get(WEB_LIB_PATH) . 'javascript/repository.js');
                $version_tab_content[] = $display->get_version_quota_as_html($version_data);
                $this->tabs->add_tab(new DynamicContentTab('versions', Translation :: get('Versions'), Theme :: get_image_path() . 'place_mini_versions.png', implode("\n", $version_tab_content)));
            }
            elseif (count($publication_attr) > 0)
            {
                $this->display_header($trail, false, true);

                if ($this->action_bar)
                {
                    echo '<br />' . $this->action_bar->as_html();
                }

                echo $display->get_full_html();
            }
            else
            {
                $this->display_header($trail, false, true);

                if ($this->action_bar)
                {
                    echo '<br />' . $this->action_bar->as_html();
                }

                echo $display->get_full_html();
            }

            $this->add_links_to_content_object_tabs($object);
            echo $this->tabs->render();
            $this->display_footer();
        }
        else
        {
            $this->display_error_page(htmlentities(Translation :: get('NoObjectSelected')));
        }
    }

    private function get_action_bar($object)
    {
        if ($object->get_owner_id() == $this->get_user_id())
        {
            $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);

            $edit_url = $this->get_content_object_editing_url($object);
            if (isset($edit_url))
            {
                $recycle_url = $this->get_content_object_recycling_url($object);
                $in_recycle_bin = false;
                if (isset($recycle_url))
                {
                    $action_bar->add_common_action(new ToolbarItem(Translation :: get('Remove'), Theme :: get_common_image_path() . 'action_recycle_bin.png', $recycle_url, ToolbarItem :: DISPLAY_ICON_AND_LABEL, true));
                }
                else
                {
                    $delete_url = $this->get_content_object_deletion_url($object);
                    if (isset($delete_url))
                    {
                        $recycle_bin_button = new ToolbarItem(Translation :: get('Delete'), Theme :: get_common_image_path() . 'action_delete.png', $delete_url, ToolbarItem :: DISPLAY_ICON_AND_LABEL, true);
                        $in_recycle_bin = true;
                    }
                    else
                    {
                        $recycle_bin_button = new ToolbarItem(Translation :: get('Remove'), Theme :: get_common_image_path() . 'action_recycle_bin_na.png');
                    }
                }

                if (! $in_recycle_bin)
                {
                    $delete_link_url = $this->get_content_object_unlinker_url($object);

                    if (! isset($recycle_url))
                    {
                        $force_delete_button = new ToolbarItem(Translation :: get('Unlink'), Theme :: get_common_image_path() . 'action_unlink.png', $delete_link_url, ToolbarItem :: DISPLAY_ICON_AND_LABEL, true);
                    }

                    $edit_url = $this->get_content_object_editing_url($object);
                    if (isset($edit_url))
                    {
                        $action_bar->add_common_action(new ToolbarItem(Translation :: get('Edit'), Theme :: get_common_image_path() . 'action_edit.png', $edit_url, ToolbarItem :: DISPLAY_ICON_AND_LABEL));
                    }
                    else
                    {
                        $action_bar->add_common_action(new ToolbarItem(Translation :: get('EditNA'), Theme :: get_common_image_path() . 'action_edit_na.png'));
                    }

                    if (isset($recycle_bin_button))
                    {
                        $action_bar->add_common_action($recycle_bin_button);
                    }

                    if (isset($force_delete_button))
                    {
                        $action_bar->add_common_action($force_delete_button);
                    }

                    if (RepositoryDataManager :: get_number_of_categories($this->get_user_id()) > 1)
                    {
                        $move_url = $this->get_content_object_moving_url($object);
                        $action_bar->add_common_action(new ToolbarItem(Translation :: get('Move'), Theme :: get_common_image_path() . 'action_move.png', $move_url, ToolbarItem :: DISPLAY_ICON_AND_LABEL));
                    }

                    $metadata_url = $this->get_content_object_metadata_editing_url($object);
                    $action_bar->add_common_action(new ToolbarItem(Translation :: get('Metadata'), Theme :: get_common_image_path() . 'action_metadata.png', $metadata_url, ToolbarItem :: DISPLAY_ICON_AND_LABEL));

                    $rights_url = $this->get_content_object_rights_editing_url($object);
                    $action_bar->add_common_action(new ToolbarItem(Translation :: get('Rights'), Theme :: get_common_image_path() . 'action_rights.png', $rights_url, ToolbarItem :: DISPLAY_ICON_AND_LABEL));

                    if ($object instanceof ComplexContentObjectSupport)
                    {
                        $clo_url = $this->get_browse_complex_content_object_url($object);
                        $action_bar->add_common_action(new ToolbarItem(Translation :: get('BuildComplexContentObject'), Theme :: get_common_image_path() . 'action_bar.png', $clo_url, ToolbarItem :: DISPLAY_ICON_AND_LABEL));
                    }
                }
                else
                {
                    $restore_url = $this->get_content_object_restoring_url($object);
                    $action_bar->add_common_action(new ToolbarItem(Translation :: get('Restore'), Theme :: get_common_image_path() . 'action_restore.png', $restore_url, ToolbarItem :: DISPLAY_ICON_AND_LABEL, true));
                    if (isset($recycle_bin_button))
                    {
                        $action_bar->add_common_action($recycle_bin_button);
                    }
                }
            }

            $action_bar->add_tool_action(new ToolbarItem(Translation :: get('CopyToTemplates'), Theme :: get_common_image_path() . 'export_template.png', $this->get_copy_content_object_url($object->get_id(), 0)));

            return $action_bar;
        }

    }

    function add_links_to_content_object_tabs($content_object)
    {
        $renderer_name = Utilities :: camelcase_to_underscores(get_class($this));
        $tabs = new DynamicTabsRenderer($renderer_name);
        $parameters = array(RepositoryManager :: PARAM_APPLICATION => RepositoryManager :: APPLICATION_NAME, RepositoryManager :: PARAM_CONTENT_OBJECT_ID => $this->object->get_id(), RepositoryManager :: PARAM_ACTION => RepositoryManager :: ACTION_VIEW_CONTENT_OBJECTS);

        // LINKS | PUBLICATIONS
        $browser = new LinkBrowserTable($this, $parameters, null, LinkBrowserTable :: TYPE_PUBLICATIONS);
        $this->tabs->add_tab(new DynamicContentTab(LinkBrowserTable :: TYPE_PUBLICATIONS, Translation :: get('Publications'), Theme :: get_image_path() . 'place_mini_publications.png', $browser->as_html()));

        // LINKS | PARENTS
        $browser = new LinkBrowserTable($this, $parameters, null, LinkBrowserTable :: TYPE_PARENTS);
        $this->tabs->add_tab(new DynamicContentTab(LinkBrowserTable :: TYPE_PARENTS, Translation :: get('Parents'), Theme :: get_image_path() . 'place_mini_parents.png', $browser->as_html()));

        // LINKS | CHILDREN
        $browser = new LinkBrowserTable($this, $parameters, null, LinkBrowserTable :: TYPE_CHILDREN);
        $this->tabs->add_tab(new DynamicContentTab(LinkBrowserTable :: TYPE_CHILDREN, Translation :: get('Children'), Theme :: get_image_path() . 'place_mini_children.png', $browser->as_html()));

        // LINKS | ATTACHED TO
        $browser = new LinkBrowserTable($this, $parameters, null, LinkBrowserTable :: TYPE_ATTACHMENTS);
        $this->tabs->add_tab(new DynamicContentTab(LinkBrowserTable :: TYPE_ATTACHMENTS, Translation :: get('AttachedTo'), Theme :: get_image_path() . 'place_mini_attached.png', $browser->as_html()));

        // LINKS | INCLUDED IN
        $browser = new LinkBrowserTable($this, $parameters, null, LinkBrowserTable :: TYPE_INCLUDES);
        $this->tabs->add_tab(new DynamicContentTab(LinkBrowserTable :: TYPE_INCLUDES, Translation :: get('IncludedIn'), Theme :: get_image_path() . 'place_mini_included.png', $browser->as_html()));
    }

    function get_object()
    {
        return $this->object;
    }
}
?>