<?php
namespace repository;

use rights\RightsManager;
use user\UserManager;
use group\GroupManager;
use common\libraries\FormValidator;
use common\libraries\Translation;
use common\libraries\Path;
use common\libraries\ToolbarItem;
use common\libraries\Toolbar;
use common\libraries\Theme;
use common\libraries\Utilities;

/**
 * $Id: user_view_form.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.forms
 * @author Sven Vanpoucke
 */
class ContentObjectShareForm extends FormValidator
{
	const PARAM_RIGHT = 'right';
    const PARAM_TARGET = 'target_users_and_groups';
    const PARAM_TARGET_ELEMENTS = 'target_users_and_groups_elements';
    const PARAM_USER = 'user';
    const PARAM_GROUP = 'group';
    const PARAM_TARGET_OPTION = 'target_users_and_groups_option';

    const TYPE_CREATE = 1;
    const TYPE_EDIT = 2;

    private $content_object_ids;
    private $form_type;
    private $user;

    function __construct($form_type, $content_object_ids = array(), $user, $action)
    {
        parent :: __construct('content_object_share_form', 'post', $action);

        $this->content_object_ids = $content_object_ids;
        $this->form_type = $form_type;
        $this->user = $user;

        if ($this->form_type == self :: TYPE_EDIT)
        {
            $this->build_editing_form();
        }
        elseif ($this->form_type == self :: TYPE_CREATE)
        {
            $this->build_creation_form();
        }

        $this->setDefaults();
    }

    function get_content_object_ids()
    {
        return $this->content_object_ids;
    }
    
    static function factory($form_type, $content_object_ids = array(), $user, $action)
    {
        if(count($content_object_ids) === 1){

            $rdm = RepositoryDataManager :: get_instance();

            $content_object = $rdm->retrieve_content_object($content_object_ids[0]);

            if ($content_object)
            {
                $type = $content_object->get_type_name();
                $file = Path :: get_repository_content_object_path() . $type . '/php/' . $type . '_content_object_share_form.class.php';
                if (file_exists($file))
                {
                    require_once $file;
                    $class =  __NAMESPACE__ . '\content_object\\' . $type . '\\' . Utilities :: underscores_to_camelcase($type) . 'ContentObjectShareForm';

                    $share_form = new $class($form_type, $content_object_ids, $user, $action);

                    return $share_form;
                }
            }
        }
        
        return new ContentObjectShareForm($form_type, $content_object_ids, $user, $action);
    }

    function build_basic_form()
    {
        $this->addElement('select', self :: PARAM_RIGHT, Translation :: get('Rights', null, RightsManager :: APPLICATION_NAME), ContentObjectShare :: get_rights());
    }

    function build_editing_form()
    {
        $this->build_basic_form();

        $buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Update', null, Utilities :: COMMON_LIBRARIES), array('class' => 'positive update'));
        $buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset', null, Utilities :: COMMON_LIBRARIES), array('class' => 'normal empty'));

        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    function build_creation_form()
    {
        $this->build_basic_form();

        $attributes = array();
        $attributes['search_url'] = Path :: get(WEB_PATH) . 'common/libraries/php/xml_feeds/xml_user_group_feed.php';
        $locale = array();
        $locale['Display'] = Translation :: get('SelectUsersGroups');
        $locale['Searching'] = Translation :: get('Searching', null, Utilities :: COMMON_LIBRARIES);
        $locale['NoResults'] = Translation :: get('NoResults', null, Utilities :: COMMON_LIBRARIES);
        $locale['Error'] = Translation :: get('Error', null, Utilities :: COMMON_LIBRARIES);
        $attributes['locale'] = $locale;
        $attributes['exclude'] = array('user_' . $this->user->get_id());
        $attributes['defaults'] = array();
        $attributes['nodesSelectable'] = true;

        $legend_items = array();
        $legend_items[] = new ToolbarItem(Translation :: get('User', null, UserManager :: APPLICATION_NAME), Theme :: get_common_image_path() . 'treemenu/user.png', null, ToolbarItem :: DISPLAY_ICON_AND_LABEL, false, 'legend');
        $legend_items[] = new ToolbarItem(Translation :: get('Group', null, GroupManager :: APPLICATION_NAME), Theme :: get_common_image_path() . 'treemenu/group.png', null, ToolbarItem :: DISPLAY_ICON_AND_LABEL, false, 'legend');

        $legend = new Toolbar();
        $legend->set_items($legend_items);
        $legend->set_type(Toolbar :: TYPE_HORIZONTAL);

        $this->add_element_finder_with_legend(self :: PARAM_TARGET, Translation :: get('SelectUsers'), $attributes, $legend);

        $buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Create', null, Utilities :: COMMON_LIBRARIES), array('class' => 'positive'));
        $buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset', null, Utilities :: COMMON_LIBRARIES), array('class' => 'normal empty'));

        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    function create_content_object_share()
    {
        $values = $this->exportValues();
        $user_ids = $values[self :: PARAM_TARGET_ELEMENTS][self :: PARAM_USER];
        $group_ids = $values[self :: PARAM_TARGET_ELEMENTS][self :: PARAM_GROUP];
        $right_id = $values[self :: PARAM_RIGHT];

        $succes = true;

        foreach($this->content_object_ids as $content_object_id)
        {
	        foreach($user_ids as $user_id)
	        {
	        	$content_object_user_share = new ContentObjectUserShare();
	        	$content_object_user_share->set_user_id($user_id);
	        	$content_object_user_share->set_content_object_id($content_object_id);
	        	$content_object_user_share->set_right_id($right_id);
	        	$succes &= $content_object_user_share->create();
	        }

        	foreach($group_ids as $group_id)
	        {
	        	$content_object_group_share = new ContentObjectGroupShare();
	        	$content_object_group_share->set_group_id($group_id);
	        	$content_object_group_share->set_content_object_id($content_object_id);
	        	$content_object_group_share->set_right_id($right_id);
	        	$succes &= $content_object_group_share->create();
	        }
        }

    	return $succes;
    }

    function update_content_object_share($target_user_ids = array(), $target_group_ids = array())
    {
        $rdm = RepositoryDataManager :: get_instance();
        $succes = true;

        $values = $this->exportValues();
        $right_id = $values[self :: PARAM_RIGHT];

    	foreach($this->content_object_ids as $content_object_id)
        {
            foreach($target_user_ids as $target_user_id)
            {
                    $content_object_user_share = $rdm->retrieve_content_object_user_share($content_object_id, $target_user_id);
            $content_object_user_share->set_right_id($right_id);
            $succes &= $content_object_user_share->update();
            }

            foreach($target_group_ids as $target_group_id)
            {
                    $content_object_group_share = $rdm->retrieve_content_object_group_share($content_object_id, $target_group_id);
            $content_object_group_share->set_right_id($right_id);
            $succes &= $content_object_group_share->update();
            }
        }

        return $succes;
    }

    /**
     * Sets default values.
     * @param array $defaults Default values for this form's parameters.
     */
function set_default_rights($target_user_ids = array(), $target_group_ids = array())
    {
    	$rdm = RepositoryDataManager :: get_instance();

    	if(count($target_user_ids) > 0)
    	{
    		$content_object_user_share = $rdm->retrieve_content_object_user_share($this->content_object_ids[0], $target_user_ids[0]);
    		$right = $content_object_user_share->get_right_id();
    	}
    	elseif(count($target_group_ids) > 0)
    	{
    		$content_object_group_share = $rdm->retrieve_content_object_group_share($this->content_object_ids[0], $target_group_ids[0]);
    		$right = $content_object_group_share->get_right_id();
    	}

    	$this->setDefaults(array(self :: PARAM_RIGHT => $right));
    }
}
?>