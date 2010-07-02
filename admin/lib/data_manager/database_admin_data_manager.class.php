<?php
/**
 * $Id: database_admin_data_manager.class.php 231 2009-11-16 09:53:00Z vanpouckesven $
 * @package admin.lib.data_manager
 */
require_once 'MDB2.php';
require_once dirname(__FILE__) . '/../admin_data_manager_interface.class.php';

class DatabaseAdminDataManager extends Database implements AdminDataManagerInterface
{

    function initialize()
    {
        parent :: initialize();
        $this->set_prefix('admin_');
    }

    function retrieve_languages($condition = null, $order_by = array (), $offset = 0, $max_objects = -1)
    {
        return $this->retrieve_objects(Language :: get_table_name(), $condition, $offset, $max_objects, $order_by);
    }

    function count_settings($condition = null)
    {
        return $this->count_objects(Setting :: get_table_name(), $condition);
    }

    function retrieve_settings($condition = null, $order_by = array (), $offset = 0, $max_objects = -1)
    {
        return $this->retrieve_objects(Setting :: get_table_name(), $condition, $offset, $max_objects, $order_by);
    }

    function delete_settings($condition = null)
    {
        return $this->delete_objects(Setting :: get_table_name(), $condition);
    }

    function delete_language($language)
    {
        $condition = new EqualityCondition(Language :: PROPERTY_ID, $language->get_id());
        return $this->delete_objects(Language :: get_table_name(), $condition);
    }

    function count_registrations($condition = null)
    {
        return $this->count_objects(Registration :: get_table_name(), $condition);
    }

    function retrieve_registration($id)
    {
        $condition = new EqualityCondition(Registration :: PROPERTY_ID, $id);
        return $this->retrieve_object(Registration :: get_table_name(), $condition);
    
    }

    function retrieve_registrations($condition = null, $order_by = array (), $offset = 0, $max_objects = -1)
    {
        return $this->retrieve_objects(Registration :: get_table_name(), $condition, $offset, $max_objects, $order_by);
    }

    function retrieve_language_from_english_name($english_name)
    {
        $condition = new EqualityCondition(Language :: PROPERTY_ENGLISH_NAME, $english_name);
        return $this->retrieve_object(Language :: get_table_name(), $condition);
    }

    function retrieve_language($id)
    {
        $condition = new EqualityCondition(Language :: PROPERTY_ID, $id);
        return $this->retrieve_object(Language :: get_table_name(), $condition);
    }

    function retrieve_setting_from_variable_name($variable, $application = 'admin')
    {
        $conditions[] = new EqualityCondition(Setting :: PROPERTY_APPLICATION, $application);
        $conditions[] = new EqualityCondition(Setting :: PROPERTY_VARIABLE, $variable);
        $condition = new AndCondition($conditions);
        
        return $this->retrieve_object(Setting :: get_table_name(), $condition);
    }

    function update_setting($setting)
    {
        $condition = new EqualityCondition(Setting :: PROPERTY_ID, $setting->get_id());
        return $this->update($setting, $condition);
    }

    function update_registration($registration)
    {
        $condition = new EqualityCondition(Registration :: PROPERTY_ID, $registration->get_id());
        return $this->update($registration, $condition);
    }

    function update_system_announcement_publication($system_announcement_publication)
    {
        // Delete existing target users and groups
        $query = 'DELETE FROM ' . $this->escape_table_name('system_announcement_publication_user') . ' WHERE system_announcement_publication_id = ' . $this->quote($system_announcement_publication->get_id());
        $res = $this->query($query);
        $res->free();
        $query = 'DELETE FROM ' . $this->escape_table_name('system_announcement_publication_group') . ' WHERE system_announcement_publication_id = ' . $this->quote($system_announcement_publication->get_id());
        $res = $this->query($query);
        $res->free();
        // Add updated target users and course_groups
        $users = $system_announcement_publication->get_target_users();
        $this->get_connection()->loadModule('Extended');
        foreach ($users as $user_id)
        {
            $props = array();
            $props[$this->escape_column_name('system_announcement_publication_id')] = $system_announcement_publication->get_id();
            $props[$this->escape_column_name('user_id')] = $user_id;
            $this->get_connection()->extended->autoExecute($this->get_table_name('system_announcement_publication_user'), $props, MDB2_AUTOQUERY_INSERT);
        }
        $groups = $system_announcement_publication->get_target_groups();
        foreach ($groups as $group_id)
        {
            $props = array();
            $props[$this->escape_column_name('system_announcement_publication_id')] = $system_announcement_publication->get_id();
            $props[$this->escape_column_name('group_id')] = $group_id;
            $this->get_connection()->extended->autoExecute($this->get_table_name('system_announcement_publication_group'), $props, MDB2_AUTOQUERY_INSERT);
        }
        
        $condition = new EqualityCondition(SystemAnnouncementPublication :: PROPERTY_ID, $system_announcement_publication->get_id());
        return $this->update($system_announcement_publication, $condition);
    }

    function delete_registration($registration)
    {
        $condition = new EqualityCondition(Registration :: PROPERTY_ID, $registration->get_id());
        return $this->delete($registration->get_table_name(), $condition);
    }

    function delete_setting($setting)
    {
        $condition = new EqualityCondition(Setting :: PROPERTY_ID, $setting->get_id());
        return $this->delete($setting->get_table_name(), $condition);
    }

    function delete_system_announcement_publication($system_announcement_publication)
    {
        $condition = new EqualityCondition(SystemAnnouncementPublication :: PROPERTY_ID, $system_announcement_publication->get_id());
        return $this->delete($system_announcement_publication->get_table_name(), $condition);
    }

    function create_language($language)
    {
        return $this->create($language);
    }

    function create_registration($registration)
    {
        return $this->create($registration);
    }

    function create_setting($setting)
    {
        return $this->create($setting);
    }

    function create_system_announcement_publication($system_announcement_publication)
    {
        if ($this->create($system_announcement_publication))
        {
            $users = $system_announcement_publication->get_target_users();
            foreach ($users as $user_id)
            {
                $props = array();
                $props[$this->escape_column_name('system_announcement_publication_id')] = $system_announcement_publication->get_id();
                $props[$this->escape_column_name('user_id')] = $user_id;
                $this->get_connection()->extended->autoExecute($this->get_table_name('system_announcement_publication_user'), $props, MDB2_AUTOQUERY_INSERT);
            }
            $groups = $system_announcement_publication->get_target_groups();
            foreach ($groups as $group_id)
            {
                $props = array();
                $props[$this->escape_column_name('system_announcement_publication_id')] = $system_announcement_publication->get_id();
                $props[$this->escape_column_name('group_id')] = $group_id;
                $this->get_connection()->extended->autoExecute($this->get_table_name('system_announcement_publication_group'), $props, MDB2_AUTOQUERY_INSERT);
            }
            
            return true;
        }
        else
        {
            return false;
        }
    }

    function count_system_announcement_publications($condition = null)
    {
        return $this->count_objects(SystemAnnouncementPublication :: get_table_name(), $condition);
    }

    function retrieve_system_announcement_publication($id)
    {
        $condition = new EqualityCondition(SystemAnnouncementPublication :: PROPERTY_ID, $id);
        return $this->retrieve_object(SystemAnnouncementPublication :: get_table_name(), $condition);
    }

    function retrieve_system_announcement_publications($condition = null, $order_by = array (), $offset = 0, $max_objects = -1)
    {
        return $this->retrieve_objects(SystemAnnouncementPublication :: get_table_name(), $condition, $offset, $max_objects, $order_by);
    }

    function retrieve_system_announcement_publication_target_groups($system_announcement_publication)
    {
        $query = 'SELECT * FROM ' . $this->escape_table_name('system_announcement_publication_group') . ' WHERE system_announcement_publication_id = ' . $this->quote($system_announcement_publication->get_id());
        $res = $this->query($query);
        $groups = array();
        while ($target_group = $res->fetchRow(MDB2_FETCHMODE_ASSOC))
        {
            $groups[] = $target_group['group_id'];
        }
        
        $res->free();
        
        return $groups;
    }

    function retrieve_system_announcement_publication_target_users($system_announcement_publication)
    {
        $query = 'SELECT * FROM ' . $this->escape_table_name('system_announcement_publication_user') . ' WHERE system_announcement_publication_id = ' . $this->quote($system_announcement_publication->get_id());
        $res = $this->query($query);
        $users = array();
        while ($target_user = $res->fetchRow(MDB2_FETCHMODE_ASSOC))
        {
            $users[] = $target_user['user_id'];
        }
        
        $res->free();
        
        return $users;
    }

    function delete_category($category)
    {
        $condition = new EqualityCondition(AdminCategory :: PROPERTY_ID, $category->get_id());
        $succes = $this->delete('admin_category', $condition);
        
        $query = 'UPDATE ' . $this->escape_table_name('admin_category') . ' SET ' . $this->escape_column_name(AdminCategory :: PROPERTY_DISPLAY_ORDER) . '=' . $this->escape_column_name(AdminCategory :: PROPERTY_DISPLAY_ORDER) . '-1 WHERE ' . $this->escape_column_name(AdminCategory :: PROPERTY_DISPLAY_ORDER) . '>' . $this->quote($category->get_display_order()) . ' AND ' . $this->escape_column_name(AdminCategory :: PROPERTY_PARENT) . '=' . $this->quote($category->get_parent());
        $res = $this->query($query);
        $res->free();
        return $succes;
    }

    function update_category($category)
    {
        $condition = new EqualityCondition(AdminCategory :: PROPERTY_ID, $category->get_id());
        return $this->update($category, $condition);
    }

    function create_category($category)
    {
        return $this->create($category);
    }

    function count_categories($conditions = null)
    {
        return $this->count_objects('admin_category', $conditions);
    }

    function count_feedback_publications($pid, $cid, $application)
    {
        $conditions[] = new EqualityCondition(FeedbackPublication :: PROPERTY_PID, $pid);
        $conditions[] = new EqualityCondition(FeedbackPublication :: PROPERTY_CID, $cid);
        $conditions[] = new EqualityCondition(FeedbackPublication :: PROPERTY_APPLICATION, $application);
        $condition = new AndCondition($conditions);
        
        return $this->count_objects('feedback_publication', $condition);
    }

    function retrieve_categories($condition = null, $offset = null, $count = null, $order_property = null)
    {
        return $this->retrieve_objects('admin_category', $condition, $offset, $count, $order_property);
    }

    function select_next_display_order($parent_category_id)
    {
        $query = 'SELECT MAX(' . AdminCategory :: PROPERTY_DISPLAY_ORDER . ') AS do FROM ' . $this->escape_table_name('admin_category');
        
        $condition = new EqualityCondition(AdminCategory :: PROPERTY_PARENT, $parent_category_id);
        
        if (isset($condition))
        {
            $translator = new ConditionTranslator($this);
            $query .= $translator->render_query($condition);
        }
        
        $res = $this->query($query);
        $record = $res->fetchRow(MDB2_FETCHMODE_ORDERED);
        
        $res->free();
        
        return $record[0] + 1;
    }

    public function get_content_object_publication_attributes($object_id, $type = null, $offset = null, $count = null, $order_property = null)
    {
        if (isset($type))
        {
            if ($type == 'user')
            {
                $query = 'SELECT * FROM ' . $this->get_table_name('system_announcement_publication') . ' WHERE ' . $this->escape_column_name('publisher_id') . '=' . $this->quote(Session :: get_user_id());
                
                $order = array();
                for($i = 0; $i < count($order_property); $i ++)
                {
                    if ($order_property[$i] == 'application')
                    {
                    }
                    elseif ($order_property[$i] == 'location')
                    {
                    }
                    elseif ($order_property[$i] == 'title')
                    {
                    }
                    else
                    {
                    }
                }
                if (count($order))
                {
                    $query .= ' ORDER BY ' . implode(', ', $order);
                }
                
                $res = $this->query($query);
            }
        }
        else
        {
            $query = 'SELECT * FROM ' . $this->get_table_name('system_announcement_publication') . ' WHERE ' . $this->escape_column_name('content_object_id') . '=' . $this->quote($object_id);
            $res = $this->query($query);
        }
        $publication_attr = array();
        while ($record = $res->fetchRow(MDB2_FETCHMODE_ASSOC))
        {
            $info = new ContentObjectPublicationAttributes();
            $info->set_id($record['id']);
            $info->set_publisher_user_id($record['publisher_id']);
            $info->set_publication_date($record['published']);
            $info->set_application('admin');
            //TODO: i8n location string
            $info->set_location('');
            //TODO: set correct URL
            $info->set_url('core.php?application=admin&go=sysviewer&announcement=' . $record['id']);
            $info->set_publication_object_id($record['content_object_id']);
            $publication_attr[] = $info;
        }
        
        $res->free();
        
        return $publication_attr;
    }

    public function get_content_object_publication_attribute($publication_id)
    {
        $condition = new EqualityCondition('id', $publication_id);
        $record = $this->next_result();
        
        $info = new ContentObjectPublicationAttributes();
        $info->set_id($record->get_id());
        $info->set_publisher_user_id($record->get_publisher());
        $info->set_publication_date($record->get_publication_date());
        $info->set_application('admin');
        //TODO: i8n location string
        $info->set_location('');
        //TODO: set correct URL
        $info->set_url('index_admin.php?go=sysviewer&announcement=' . $record->get_id());
        $info->set_publication_object_id($record->get_content_object());
        return $info;
    }

    public function any_content_object_is_published($object_ids)
    {
        $condition = new InCondition(SystemAnnouncementPublication :: PROPERTY_CONTENT_OBJECT_ID, $object_ids);
        return $this->count_objects('system_announcement_publication', $condition) >= 1;
    }

    function count_publication_attributes($user = null, $object_id = null, $condition = null)
    {
        if (! $object_id)
        {
            $condition = new EqualityCondition(SystemAnnouncementPublication :: PROPERTY_PUBLISHER, $user->get_id());
        }
        else
        {
            $condition = new EqualityCondition(SystemAnnouncementPublication :: PROPERTY_CONTENT_OBJECT_ID, $object_id);
        }
        return $this->count_objects(SystemAnnouncementPublication :: get_table_name(), $condition);
    }

    public function delete_content_object_publications($object_id)
    {
        $condition = new EqualityCondition(SystemAnnouncementPublication :: PROPERTY_CONTENT_OBJECT_ID, $object_id);
        $this->delete('system_announcement_publication', $condition);
    }

    public function delete_content_object_publication($publication_id)
    {
        $condition = new EqualityCondition(SystemAnnouncementPublication :: PROPERTY_ID, $publication_id);
        return $this->delete('system_announcement_publication', $condition);
    }

    function create_remote_package($remote_package)
    {
        return $this->create($remote_package);
    }

    function update_remote_package($remote_package)
    {
        $condition = new EqualityCondition(RemotePackage :: PROPERTY_ID, $remote_package->get_id());
        return $this->update($remote_package, $condition);
    }

    function delete_remote_package($remote_package)
    {
        $condition = new EqualityCondition(RemotePackage :: PROPERTY_ID, $remote_package->get_id());
        return $this->delete($remote_package->get_table_name(), $condition);
    }

    function delete_remote_packages($condition)
    {
        return $this->delete_objects(RemotePackage :: get_table_name(), $condition);
    }

    function count_remote_packages($condition = null)
    {
        return $this->count_objects(RemotePackage :: get_table_name(), $condition);
    }

    function retrieve_remote_package($id)
    {
        $condition = new EqualityCondition(RemotePackage :: PROPERTY_ID, $id);
        return $this->retrieve_object(RemotePackage :: get_table_name(), $condition);
    }

    function retrieve_remote_packages($condition = null, $order_by = array(), $offset = null, $max_objects = null)
    {
        return $this->retrieve_objects(RemotePackage :: get_table_name(), $condition, $offset, $max_objects, $order_by);
    }

    function retrieve_feedback_publications($pid, $cid, $application)
    {
        $conditions[] = new EqualityCondition(FeedbackPublication :: PROPERTY_PID, $pid);
        $conditions[] = new EqualityCondition(FeedbackPublication :: PROPERTY_CID, $cid);
        $conditions[] = new EqualityCondition(FeedbackPublication :: PROPERTY_APPLICATION, $application);
        $condition = new AndCondition($conditions);
        $order_by[] = new ObjectTableOrder(FeedbackPublication :: PROPERTY_ID, SORT_DESC);
        
        return $this->retrieve_objects(FeedbackPublication :: get_table_name(), $condition, null, null, $order_by);
    }

    /* function retrieve_validations($pid,$cid,$application)
    {

        $conditions[] = new EqualityCondition(Validation :: PROPERTY_PID, $pid);
        $conditions[] = new EqualityCondition(Validation :: PROPERTY_CID, $cid);
        $conditions[] = new EqualityCondition(Validation :: PROPERTY_APPLICATION, $application);
        $condition = new AndCondition($conditions);
        //$order_by[] = new ObjectTableOrder(FeedbackPublication::PROPERTY_ID,SORT_DESC);

        return $this->retrieve_objects(Validation :: get_table_name(),$condition);
    }*/
    
    function retrieve_feedback_publication($id)
    {
        $condition = new EqualityCondition(FeedbackPublication :: PROPERTY_ID, $id);
        return $this->retrieve_object(FeedbackPublication :: get_table_name(), $condition);
    }

    function retrieve_validation($id)
    {
        $condition = new EqualityCondition(Validation :: PROPERTY_ID, $id);
        return $this->retrieve_object(Validation :: get_table_name(), $condition);
    }

    function update_feedback_publication($feedback_publication)
    {
        $condition = new EqualityCondition(Feedback :: PROPERTY_ID, $feedback_publication->get_id());
        return $this->update($feedback_publication, $condition);
    }

    function update_validation($validation)
    {
        $condition = new EqualityCondition(Validation :: PROPERTY_ID, $validation->get_id());
        return $this->update($validation, $condition);
    }

    function delete_feedback_publication($feedback_publication)
    {
        $condition = new EqualityCondition(FeedbackPublication :: PROPERTY_ID, $feedback_publication->get_id());
        return $this->delete($feedback_publication->get_table_name(), $condition);
    }

    function delete_validation($validation)
    {
        $condition = new EqualityCondition(FeedbackPublication :: PROPERTY_ID, $validation->get_id());
        return $this->delete($validation->get_table_name(), $condition);
    }

    function create_feedback_publication($feedback_publication)
    {
        return $this->create($feedback_publication);
    }

    function create_validation($validation)
    {
        return $this->create($validation);
    }

    function retrieve_validations($condition = null, $order_by = array (), $offset = 0, $max_objects = -1)
    {
        $val_table = $this->escape_table_name(Validation :: get_table_name());
        $val_table_alias = $this->get_alias(Validation :: get_table_name());
        $user_table = UserDataManager :: get_instance()->escape_table_name(User :: get_table_name());
        $user_table_alias = UserDataManager :: get_instance()->get_alias(User :: get_table_name());
        
        $query = 'SELECT * FROM ' . $val_table . ' AS ' . $val_table_alias . ' JOIN ' . $user_table . ' AS ' . $user_table_alias;
        
        return $this->retrieve_object_set($query, Validation :: get_table_name(), $condition = null, $offset, $max_objects, $order_by);
    }

    function count_validations($condition = null)
    {
        return $this->count_objects(Validation :: get_table_name(), $condition);
    }

    // Dynamic Forms
    

    function delete_dynamic_form($dynamic_form)
    {
        $condition = new EqualityCondition(DynamicForm :: PROPERTY_ID, $dynamic_form->get_id());
        return $this->delete($dynamic_form->get_table_name(), $condition);
    }

    function update_dynamic_form($dynamic_form)
    {
        $condition = new EqualityCondition(DynamicForm :: PROPERTY_ID, $dynamic_form->get_id());
        return $this->update($dynamic_form, $condition);
    }

    function create_dynamic_form($dynamic_form)
    {
        return $this->create($dynamic_form);
    }

    function count_dynamic_forms($conditions = null)
    {
        return $this->count_objects(DynamicForm :: get_table_name(), $conditions);
    }

    function retrieve_dynamic_forms($condition = null, $offset = null, $count = null, $order_property = null)
    {
        return $this->retrieve_objects(DynamicForm :: get_table_name(), $condition, $offset, $count, $order_property);
    }

    function delete_dynamic_form_element($dynamic_form_element)
    {
        $condition = new EqualityCondition(DynamicFormElement :: PROPERTY_ID, $dynamic_form_element->get_id());
        return $this->delete($dynamic_form_element->get_table_name(), $condition);
    }

    function update_dynamic_form_element($dynamic_form_element)
    {
        $condition = new EqualityCondition(DynamicFormElement :: PROPERTY_ID, $dynamic_form_element->get_id());
        return $this->update($dynamic_form_element, $condition);
    }

    function create_dynamic_form_element($dynamic_form_element)
    {
        return $this->create($dynamic_form_element);
    }

    function count_dynamic_form_elements($conditions = null)
    {
        return $this->count_objects(DynamicFormElement :: get_table_name(), $conditions);
    }

    function retrieve_dynamic_form_elements($condition = null, $offset = null, $count = null, $order_property = null)
    {
        return $this->retrieve_objects(DynamicFormElement :: get_table_name(), $condition, $offset, $count, $order_property);
    }

    function select_next_dynamic_form_element_order($dynamic_form_id)
    {
        $condition = new EqualityCondition(DynamicFormElement :: PROPERTY_DYNAMIC_FORM_ID, $dynamic_form_id);
        return $this->retrieve_next_sort_value(DynamicFormElement :: get_table_name(), DynamicFormElement :: PROPERTY_DISPLAY_ORDER, $condition);
    }

    function delete_dynamic_form_element_option($dynamic_form_element_option)
    {
        $condition = new EqualityCondition(DynamicFormElementOption :: PROPERTY_ID, $dynamic_form_element_option->get_id());
        return $this->delete($dynamic_form_element_option->get_table_name(), $condition);
    }

    function update_dynamic_form_element_option($dynamic_form_element_option)
    {
        $condition = new EqualityCondition(DynamicFormElementOption :: PROPERTY_ID, $dynamic_form_element_option->get_id());
        return $this->update($dynamic_form_element_option, $condition);
    }

    function create_dynamic_form_element_option($dynamic_form_element_option)
    {
        return $this->create($dynamic_form_element_option);
    }

    function count_dynamic_form_element_options($conditions = null)
    {
        return $this->count_objects(DynamicFormElementOption :: get_table_name(), $conditions);
    }

    function retrieve_dynamic_form_element_options($condition = null, $offset = null, $count = null, $order_property = null)
    {
        return $this->retrieve_objects(DynamicFormElementOption :: get_table_name(), $condition, $offset, $count, $order_property);
    }

    function select_next_dynamic_form_element_option_order($dynamic_form_element_id)
    {
        $condition = new EqualityCondition(DynamicFormElementOption :: PROPERTY_DYNAMIC_FORM_ELEMENT_ID, $dynamic_form_element_id);
        return $this->retrieve_next_sort_value(DynamicFormElementOption :: get_table_name(), DynamicFormElementOption :: PROPERTY_DISPLAY_ORDER, $condition);
    }

    function delete_all_options_from_form_element($dynamic_form_element_id)
    {
        $condition = new EqualityCondition(DynamicFormElementOption :: PROPERTY_DYNAMIC_FORM_ELEMENT_ID, $dynamic_form_element_id);
        return $this->delete(DynamicFormElementOption :: get_table_name(), $condition);
    }

    function delete_dynamic_form_element_value($dynamic_form_element_value)
    {
        $condition = new EqualityCondition(DynamicFormElementValue :: PROPERTY_ID, $dynamic_form_element_value->get_id());
        return $this->delete($dynamic_form_element_value->get_table_name(), $condition);
    }

    function update_dynamic_form_element_value($dynamic_form_element_value)
    {
        $condition = new EqualityCondition(DynamicFormElementValue :: PROPERTY_ID, $dynamic_form_element_value->get_id());
        return $this->update($dynamic_form_element_value, $condition);
    }

    function create_dynamic_form_element_value($dynamic_form_element_value)
    {
        return $this->create($dynamic_form_element_value);
    }

    function count_dynamic_form_element_values($conditions = null)
    {
        return $this->count_objects(DynamicFormElementValue :: get_table_name(), $conditions);
    }

    function retrieve_dynamic_form_element_values($condition = null, $offset = null, $count = null, $order_property = null)
    {
        return $this->retrieve_objects(DynamicFormElementValue :: get_table_name(), $condition, $offset, $count, $order_property);
    }

    function delete_dynamic_form_element_values_from_form($dynamic_form_id)
    {
        $subcondition = new EqualityCondition(DynamicFormElement :: PROPERTY_DYNAMIC_FORM_ID, $dynamic_form_id);
        $subselect = new SubselectCondition(DynamicFormElementValue :: PROPERTY_DYNAMIC_FORM_ELEMENT_ID, DynamicFormElement :: PROPERTY_ID, DynamicFormElement :: get_table_name(), $subcondition);
        
        return $this->delete(DynamicFormElementValue :: get_table_name(), $subselect);
    }

    function retrieve_invitation($id)
    {
        $condition = new EqualityCondition(Invitation :: PROPERTY_ID, $id);
        return $this->retrieve_object(Invitation :: get_table_name(), $condition);
    }
    
    function retrieve_invitation_by_code($code)
    {
        $condition = new EqualityCondition(Invitation :: PROPERTY_CODE, $code);
        return $this->retrieve_object(Invitation :: get_table_name(), $condition);
    }

    function retrieve_invitations($condition = null, $order_by = array (), $offset = 0, $max_objects = -1)
    {
        return $this->retrieve_objects(Invitation :: get_table_name(), $condition, $offset, $max_objects, $order_by);
    }

    function create_invitation($invitation)
    {
        return $this->create($invitation);
    }

    function delete_invitation($invitation)
    {
        $condition = new EqualityCondition(Invitation :: PROPERTY_ID, $invitation->get_id());
        return $this->delete_objects(Invitation :: get_table_name(), $invitation);
    }
    
    function update_invitation($invitation)
    {
        $condition = new EqualityCondition(Invitation :: PROPERTY_ID, $invitation->get_id());
        return $this->update($invitation, $condition);
    }
}
?>