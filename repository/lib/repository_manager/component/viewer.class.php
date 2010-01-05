<?php
/**
 * $Id: viewer.class.php 204 2009-11-13 12:51:30Z kariboe $
 * @package repository.lib.repository_manager.component
 */
/**
 * Repository manager component which can be used to view a learning object.
 */
class RepositoryManagerViewerComponent extends RepositoryManagerComponent
{
    private $action_bar;
    private $object;

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $id = Request :: get(RepositoryManager :: PARAM_CONTENT_OBJECT_ID);
        if ($id)
        {
            $object = $this->retrieve_content_object($id);
            $this->object = $object;
            // TODO: Use Roles & Rights here.
            if ($object->get_owner_id() != $this->get_user_id() && ! $this->get_parent()->has_right($object, $this->get_user_id(), RepositoryRights :: VIEW_RIGHT))
            {
                $this->not_allowed();
            }
            
            $display = ContentObjectDisplay :: factory($object);
            $trail = new BreadcrumbTrail(false);
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
                
                $form = ContentObjectForm :: factory(ContentObjectForm :: TYPE_COMPARE, $object, 'compare', 'post', $this->get_url(array(RepositoryManager :: PARAM_CONTENT_OBJECT_ID => $object->get_id())), array('version_data' => $version_data));
                if ($form->validate())
                {
                    $params = $form->compare_content_object();
                    $params[Application :: PARAM_ACTION] = RepositoryManager :: ACTION_COMPARE_CONTENT_OBJECTS;
                    $this->redirect(null, false, $params);
                }
                else
                {
                    $this->display_header($trail, false, true);
                    
                    if ($this->action_bar)
                        echo '<br />' . $this->action_bar->as_html();
                    
                    echo $display->get_full_html();
                    echo Utilities :: add_block_hider();
                    echo Utilities :: build_block_hider('content_object_extras');
                    $form->display();
                }
                echo $display->get_version_quota_as_html($version_data);
            }
            elseif (count($publication_attr) > 0)
            {
                $this->display_header($trail, false, true);
                
                if ($this->action_bar)
                    echo '<br />' . $this->action_bar->as_html();
                
                echo $display->get_full_html();
                /*echo Utilities :: add_block_hider();
                echo Utilities :: build_block_hider('content_object_extras');*/
            }
            else
            {
                $this->display_header($trail, false, true);
                
                if ($this->action_bar)
                    echo '<br />' . $this->action_bar->as_html();
                
                echo $display->get_full_html();
            }
            
            /*if (count($publication_attr) > 0)
            {
                echo $display->get_publications_as_html($publication_attr);
                //echo Utilities :: build_uses($publication_attr);
            }
            
            if (count($versions) >= 2 || count($publication_attr) > 0)
            {
                echo Utilities :: build_block_hider();
            }*/
            
            echo $this->display_links_to_content_object($object);
            
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
                    
                    $dm = RepositoryDataManager :: get_instance();
                    if ($dm->get_number_of_categories($this->get_user_id()) > 1)
                    {
                        $move_url = $this->get_content_object_moving_url($object);
                        $action_bar->add_common_action(new ToolbarItem(Translation :: get('Move'), Theme :: get_common_image_path() . 'action_move.png', $move_url, ToolbarItem :: DISPLAY_ICON_AND_LABEL));
                    }
                    
                    $metadata_url = $this->get_content_object_metadata_editing_url($object);
                    $action_bar->add_common_action(new ToolbarItem(Translation :: get('Metadata'), Theme :: get_common_image_path() . 'action_metadata.png', $metadata_url, ToolbarItem :: DISPLAY_ICON_AND_LABEL));
                    
                    $rights_url = $this->get_content_object_rights_editing_url($object);
                    $action_bar->add_common_action(new ToolbarItem(Translation :: get('Rights'), Theme :: get_common_image_path() . 'action_rights.png', $rights_url, ToolbarItem :: DISPLAY_ICON_AND_LABEL));
                    
                    if ($object->is_complex_content_object())
                    {
                        $clo_url = $this->get_browse_complex_content_object_url($object);
                        $action_bar->add_common_action(new ToolbarItem(Translation :: get('BrowseComplex'), Theme :: get_common_image_path() . 'action_browser.png', $clo_url, ToolbarItem :: DISPLAY_ICON_AND_LABEL));
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
            
            $action_bar->add_tool_action(new ToolbarItem(Translation :: get('CopyToTemplates'), Theme :: get_common_image_path() . 'export_unknown.png', $this->get_copy_content_object_url($object->get_id(), 0)));
            
            return $action_bar;
        }
    
    }
    
	function display_links_to_content_object($content_object)
	{
		$html = array();
		
		$html[] = Utilities :: add_block_hider();
		$html[] = Utilities :: build_block_hider('links', 'Links');
		
		$html[] = '<br />';
		$html[] = '<h3>' . Translation :: get('Links') . '</h3>';
		$html[] = '<h4>' . Translation :: get('Publications') . '</h4>';
		
		$browser = new LinkBrowserTable($this, array(RepositoryManager :: PARAM_APPLICATION => RepositoryManager :: APPLICATION_NAME, 
													 RepositoryManager :: PARAM_CONTENT_OBJECT_ID => $this->object->get_id(),
													 RepositoryManager :: PARAM_ACTION => RepositoryManager :: ACTION_VIEW_CONTENT_OBJECTS),
										null, LinkBrowserTable :: TYPE_PUBLICATIONS);
		$html[] = $browser->as_html();
		
		$html[] = '<h4>' . Translation :: get('Parents') . '</h4>';
		
		$browser = new LinkBrowserTable($this, array(RepositoryManager :: PARAM_APPLICATION => RepositoryManager :: APPLICATION_NAME, 
													 RepositoryManager :: PARAM_CONTENT_OBJECT_ID => $this->object->get_id(),
													 RepositoryManager :: PARAM_ACTION => RepositoryManager :: ACTION_VIEW_CONTENT_OBJECTS),
										null, LinkBrowserTable :: TYPE_PARENTS);
		$html[] = $browser->as_html();
		
		$html[] = '<h4>' . Translation :: get('Children') . '</h4>';
		
		$browser = new LinkBrowserTable($this, array(RepositoryManager :: PARAM_APPLICATION => RepositoryManager :: APPLICATION_NAME, 
													 RepositoryManager :: PARAM_CONTENT_OBJECT_ID => $this->object->get_id(),
													 RepositoryManager :: PARAM_ACTION => RepositoryManager :: ACTION_VIEW_CONTENT_OBJECTS),
										null, LinkBrowserTable :: TYPE_CHILDREN);
		$html[] = $browser->as_html();
		
		$html[] = '<h4>' . Translation :: get('AttachedTo') . '</h4>';
		
		$browser = new LinkBrowserTable($this, array(RepositoryManager :: PARAM_APPLICATION => RepositoryManager :: APPLICATION_NAME, 
													 RepositoryManager :: PARAM_CONTENT_OBJECT_ID => $this->object->get_id(),
													 RepositoryManager :: PARAM_ACTION => RepositoryManager :: ACTION_VIEW_CONTENT_OBJECTS),
										null, LinkBrowserTable :: TYPE_ATTACHMENTS);
		$html[] = $browser->as_html();
		
		$html[] = '<h4>' . Translation :: get('IncludedIn') . '</h4>';
		
		$browser = new LinkBrowserTable($this, array(RepositoryManager :: PARAM_APPLICATION => RepositoryManager :: APPLICATION_NAME, 
													 RepositoryManager :: PARAM_CONTENT_OBJECT_ID => $this->object->get_id(),
													 RepositoryManager :: PARAM_ACTION => RepositoryManager :: ACTION_VIEW_CONTENT_OBJECTS),
										null, LinkBrowserTable :: TYPE_INCLUDES);
		$html[] = $browser->as_html();
		
		$html[] = '</div></div><div class="clear"></div><br />';
		
		return implode("\n", $html);
	}
	
	function get_object()
	{
		return $this->object;
	}
}
?>