<?php
/**
 * $Id: user.class.php 227 2009-11-13 14:45:05Z kariboe $
 * @package home.lib.home_manager.component
 */

class HomeManagerUserComponent extends HomeManager
{

    /**
     * Runs this component and displays its output.
     * This component is only meant for use within the home-component and not as a standalone item.
     */
    function run()
    {
    }

    function render_as_html()
    {
        $html = array();
        
        $html[] = '<div class="block" id="block_user" style="background-image: url(' . Theme :: get_common_image_path() . 'block_user.png);">';
        $html[] = '<div class="title">' . Translation :: get('User') . '<a href="#" class="closeEl">[-]</a></div>';
        $html[] = '<div class="description">';
        $html[] = $this->display_anonymous_right_menu();
        $html[] = '<div style="clear: both;"></div>';
        $html[] = '</div>';
        $html[] = '</div>';
        
        return implode("\n", $html);
    }

    function display_anonymous_right_menu()
    {
        global $loginFailed, $plugins;
        $html = array();
        
        if (! Authentication :: is_valid())
        {
            // TODO: New languageform
            //api_display_language_form();
            $html[] = $this->display_login_form();
            
            if ($loginFailed)
            {
                $html[] = $this->handle_login_failed();
            }
            //			if ($this->get_platform_setting('allow_lostpassword') == 'true' OR $this->get_platform_setting('allow_registration') == 'true')
        //			{
        //				$html[] = '<div class="menusection"><span class="menusectioncaption">'.Translation :: get('MenuUser').'</span><ul class="menulist">';
        //				if (get_setting('allow_registration') == 'true')
        //				{
        //					$html[] = '<li><a href="index_user.php?go=register">'.Translation :: get('Reg').'</a></li>';
        //				}
        //				if (get_setting('allow_lostpassword') == 'true')
        //				{
        //					//display_lost_password_info();
        //				}
        //				$html[] = '</ul></div>';
        //			}
        }
        else
        {
            $html[] = '<a href="index.php?logout=true">Logout</a>';
        }
        
        //		$html[] = '<div class="note">';
        //		$html[] = '</div>';
        

        return implode("\n", $html);
    
    }

    function handle_login_failed()
    {
        $message = Translation :: get("InvalidId");
        if ($this->get_platform_setting('allow_registration', 'admin') == 'true')
            $message = Translation :: get("InvalidForSelfRegistration");
        return "<div id=\"login_fail\">" . $message . "</div>";
    }

    function display_login_form()
    {
        $form = new FormValidator('formLogin');
        $renderer = & $form->defaultRenderer();
        $renderer->setElementTemplate('<div>{label}&nbsp;<!-- BEGIN required --><span style="color: #ff0000">*</span><!-- END required --></div><div>{element}</div>');
        $renderer->setElementTemplate('<div>{element}</div>', 'submitAuth');
        $form->addElement('text', 'login', Translation :: get('UserName'), array('size' => 15));
        $form->addRule('login', Translation :: get('ThisFieldIsRequired'), 'required');
        $form->addElement('password', 'password', Translation :: get('Pass'), array('size' => 15));
        $form->addRule('password', Translation :: get('ThisFieldIsRequired'), 'required');
        $form->addElement('submit', 'submitAuth', Translation :: get('Ok'));
        return $form->toHtml();
    }
}
?>