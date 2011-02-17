<?php

namespace home;

use common\libraries\BasicApplication;
use common\libraries\Translation;
use common\libraries\EqualityCondition;
use common\libraries\AndCondition;
use common\libraries\Theme;
use common\libraries\Block;
use common\libraries\PlatformSetting;
use common\libraries\Path;
use user\User;
use common\libraries\Request;
use common\libraries\Display;
use common\libraries\Application;
use DOMDocument;
use common\libraries\StringUtilities;
use common\libraries\Utilities;

/**
 * Input: Chamilo's block
 * Output: Open Social module defininition.
 * 
 */
class WidgetOpenSocialModuleHomeRenderer extends HomeRenderer {

    function get_user_id() {
        return Session::get_user_id();
    }

    /**
     *
     * @return HomeBlock|null
     */
    function get_home_block($id) {
        return HomeDataManager :: get_instance()->retrieve_home_block($id);
    }

    /**
     *
     * @return Block|null
     */
    function get_block($block_info) {
        $result = $block_info ? Block :: factory($this, $block_info) : null;
        return $result;
    }

    function get_widget_view_url($id) {
        $base_url = Path::get(WEB_PATH);
        if($id){
            return $base_url . '/index.php?view_type=widget&' . HomeManager :: PARAM_WIDGET_ID . '=' . $id;
        }else{
            return $base_url . '/index.php?view_type=widget';
        }
    }

    function get_sitename() {
        return PlatformSetting :: get('site_name');
    }

    function get_administrator_email() {
        return PlatformSetting :: get('administrator_email');
    }

    function get_portal_home() {
        return PlatformSetting :: get('portal_home');
    }


    /**
     *    
     * User Preferences
     *
     * name  Required "symbolic" name of the user preference; displayed to the user during editing if no display_name is defined. Must contain only letters, number and underscores, i.e. the regular expression ^[a-zA-Z0-9_]+$. Must be unique.
     * display_name  Optional string to display alongside the user preferences in the edit window. Must be unique.
     * urlparam  Optional string to pass as the parameter name for content type="url".
     * datatype  Optional string that indicates the data type of this attribute. Can be string, bool, enum, hidden (a string that is not visible or user editable), or list (dynamic array generated from user input). The default is string.
     * required  Optional boolean argument (true or false) indicating whether this user preference is required. The default is false.
     * default_value  Optional string that indicates a user preference's default value.
     *
     * For example, this <UserPref> lets users set the level of difficulty for a game. Each value that will appear in the menu (Easy, Medium, and Hard) is defined using an <EnumValue> tag.
     *
     * <UserPref name="difficulty"
     *      display_name="Difficulty"
     *      datatype="enum"
     *      default_value="4">
     * <EnumValue value="3" display_value="Easy"/>
     * <EnumValue value="4" display_value="Medium"/>
     * <EnumValue value="5" display_value="Hard"/>
     * </UserPref>
     *
     * The following table lists the <EnumValue> attributes:
     *
     *      value           Required string that provides a unique value. This value is displayed in the menu in the user preferences edit box unless a display_value is provided.
     *      display_value   Optional string that is displayed in the menu in the user preferences edit box. If you do not specify a display_value, the value is displayed in the user interface.
     * 
     * @param string $name
     * @param string $display_name
     * @param string $urlparam
     * @param string $datatype
     * @param string $required
     * @param string $default_value
     * @param array $enums
     * @return string
     */
    function get_user_pref($name, $display_name = '', $urlparam = '', $datatype = '', $required = false, $default_value = null, $enums = array()) {
        $required = $required ? 'true' : 'false';

        $result = array();
        $result[] = '<UserPref';
        $result[] = ' name="' . $name . '" ';
        $result[] = $display_name ? ' display_name="' . $display_name . '" ' : '';
        $result[] = $urlparam ? ' urlparam="' . $urlparam . '" ' : '';
        $result[] = $datatype ? ' datatype="' . $datatype . '" ' : '';
        $result[] = $required ? ' required="' . $required . '" ' : '';
        $result[] = !is_null($default_value) ? ' default_value="' . $default_value . '" ' : '';
        $result[] = '>';
        foreach ($enums as $value => $display_name) {
            $result[] = '<EnumValue';
            $result[] = ' value="' . $value . '" ';
            $result[] = ' display_value="' . $display_name . '" ';
            $result[] = '/>';
        }
        $result[] = '</UserPref>';

        return implode('', $result);
    }

    /**
     * Render an error module. That is one that does nothing but to display a message.
     */
    function render_error($message = '') {
        $title = Translation::get('Error');
        $message = $message ? $message : Translation::get('Unavailable');
        $title_url = Path::get(WEB_PATH);
        $icon = Theme::get_common_image_path() . 'logo_header.png';
        $email = $this->get_administrator_email();

        $result = <<<EOT
<?xml version="1.0" encoding="UTF-8" ?>
<Module>
    <ModulePrefs
        title="$title"
        title_url="$title_url"
        height="200"
        author="Chamilo"
        author_email="$email"
        description=""
        screenshot="$icon"
        thumbnail="$icon"
        />
    <Content type="html">
        $message
</Content>
</Module>
EOT;
        header("content-type:text/xml;charset=utf-8");
        echo $result;
        die;
    }

    /**
     * Render a meta widget. That is one that can display any block based on it's id.
     */
    function render_meta_widget() {
        $title = $this->get_sitename();
        $email = $this->get_administrator_email();
        $title_url = Path::get(WEB_PATH);
        $icon = Theme::get_common_image_path() .'logo_header.png';
        $widget_view_url = $this->get_widget_view_url();


        $result = <<<EOT
<?xml version="1.0" encoding="UTF-8" ?>
<Module>
    <ModulePrefs
        title="$title"
        title_url="$title_url"
        height="200"
        author="Chamilo"
        author_email="$email"
        description="Chamilo meta widget. Used to display Chamilo's blocks."
        screenshot="$icon"
        thumbnail="$icon"
        />
    <UserPref name="blockid" display_name="Block Id" required="true" datatype="string" urlparam="widget_id" default_value="0" />
    <Content view="home,canvas,profile" type="url" href="{$widget_view_url}&amp;widget_id=__UP_blockid__" />
</Module>
EOT;
        header("content-type:text/xml;charset=utf-8");
        echo $result;
        die;
    }

    /**
     * Render a module for a specific block.
     */
    function render_block_module(){
        $id = Request :: get(HomeRenderer :: PARAM_WIDGET_ID);
        $block_info = $this->get_home_block($id);
        if (empty($block_info)) {
            $this->render_error();
            return;
        }
        $block = $this->get_block($block_info);
        if (empty($block)) {
            $this->render_error();
        }
        $config = $block_info->get_configuration();

        $title = $block_info->get_title();
        $title_url = Path::get(WEB_PATH);
        $email = $this->get_administrator_email();
        $icon = $block->get_icon();
        $widget_view_url = htmlspecialchars($this->get_widget_view_url($id));

        $config = $block_info->get_configuration();
        $settings = $this->parse_block_settings($block_info);
        //$user_prefs = $this->build_user_pref($block_info, $settings, $config);
        $user_prefs = '';

        $result = <<<EOT
<?xml version="1.0" encoding="UTF-8" ?>
<Module>
    <ModulePrefs
        title="$title"
        title_url="$title_url"
        height="200"
        author="Chamilo"
        author_email="$email"
        description="Meta widget to display Chamilo's blocks inside iGoogle or other compliant containers"
        screenshot="$icon"
        thumbnail="$icon"
        />
        $user_prefs
    <Content view="home,canvas,profile"  type="url" href="$widget_view_url" preferred_height="200"/>
</Module>
EOT;
        header("content-type:text/xml;charset=utf-8");
        echo $result;
        die;
    }

    function render() {
        $id = Request :: get(HomeRenderer :: PARAM_WIDGET_ID);
        if($id){
            $this->render_block_module();
        }else{
            $this->render_meta_widget();
        }

    }

    /**
     * Displays an error page.
     * @param string $message The message.
     */
    function display_error_page($message) {
        Display::small_header();
        Display::error_message($message);
        Display::small_footer();
    }

    /**
     * Parse the xml settings file - if one exists - and returns an array populated with the file's values.
     *
     * @param HomeBlock $homeblock
     * @return array
     */
    function parse_block_settings($homeblock) {
        $application = $homeblock->get_application();
        $component = $homeblock->get_component();

        $file = BasicApplication :: get_application_class_path($application) . 'blocks/type/' . $component . '.xml';
        $result = array();

        if (file_exists($file)) {
            $doc = new DOMDocument();
            $doc->load($file);
            $object = $doc->getElementsByTagname('block')->item(0);
            $name = $object->getAttribute('name');

            // Get categories
            $categories = $doc->getElementsByTagname('category');
            $settings = array();

            foreach ($categories as $index => $category) {
                $category_name = $category->getAttribute('name');
                $category_properties = array();

                // Get settings in category
                $properties = $category->getElementsByTagname('setting');
                $attributes = array('field', 'default', 'locked');

                foreach ($properties as $index => $property) {
                    $property_info = array();

                    foreach ($attributes as $index => $attribute) {
                        if ($property->hasAttribute($attribute)) {
                            $property_info[$attribute] = $property->getAttribute($attribute);
                        }
                    }

                    if ($property->hasChildNodes()) {
                        $property_options = $property->getElementsByTagname('options')->item(0);
                        $property_options_attributes = array('type', 'source');
                        foreach ($property_options_attributes as $index => $options_attribute) {
                            if ($property_options->hasAttribute($options_attribute)) {
                                $property_info['options'][$options_attribute] = $property_options->getAttribute($options_attribute);
                            }
                        }

                        if ($property_options->getAttribute('type') == 'static' && $property_options->hasChildNodes()) {
                            $options = $property_options->getElementsByTagname('option');
                            $options_info = array();
                            foreach ($options as $option) {
                                $options_info[$option->getAttribute('value')] = $option->getAttribute('name');
                            }
                            $property_info['options']['values'] = $options_info;
                        }
                    }
                    $category_properties[$property->getAttribute('name')] = $property_info;
                }

                $settings[$category_name] = $category_properties;
            }

            $result['name'] = $name;
            $result['settings'] = $settings;
        }

        return $result;
    }

    /**
     * Generate the User Pref section of Open Socials gadgets for a block based on the xml settings files and the current config.
     * Note that dynamic settings are not handled as Open Socials do not support them.
     * If a default is provided for a block defaults to it. Otherwise defaults to the settting's default.
     *
     * @param object $homeblock
     * @param object $homeblock_config
     * @param array $current_config
     * @return string
     */
    function build_user_pref($homeblock, $homeblock_config, $current_config = array()) {

        $result = array();

        $application = $homeblock->get_application();
        $component = $homeblock->get_component();

        foreach ($homeblock_config['settings'] as $category_name => $settings) {
            foreach ($settings as $name => $setting) {
                if ($setting['field'] == 'text') {
                    $default = isset($current_config[$name]) ? $current_config[$name] : $setting['default'];
                    $result[] = $this->get_user_pref($name, Translation :: get(Utilities :: underscores_to_camelcase($name)), '', 'string', true, $default);
                } elseif ($setting['field'] == 'checkbox') {
                    $default = isset($current_config[$name]) ? $current_config[$name] : $setting['default'];
                    $result[] = $this->get_user_pref($name, Translation :: get(Utilities :: underscores_to_camelcase($name)), '', 'bool', true, $default, $options);
                } elseif ($setting['field'] == 'radio' || $setting['field'] == 'select') {
                    $options_type = $setting['options']['type'];
                    $default = isset($current_config[$name]) ? $current_config[$name] : $setting['default'];
                    if ($options_type == 'dynamic') {
                        //do nothing, not supported by open social widgets
                    } else {
                        $options = $setting['options']['values'];
                        $result[] = $this->get_user_pref($name, Translation :: get(Utilities :: underscores_to_camelcase($name)), '', 'enum', true, $default, $options);
                    }
                }
            }
        }
        return implode(StringUtilities::NEW_LINE, $result);
    }

}

?>