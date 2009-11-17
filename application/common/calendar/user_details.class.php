<?php
/**
 * $Id: user_details.class.php 205 2009-11-13 12:57:33Z vanpouckesven $
 * @package application.common
 */
class UserDetails
{
    /**
     * The user
     */
    private $user;

    /**
     * Constructor
     * @param User $user
     */
    public function UserDetails($user)
    {
        $this->user = $user;
    }

    /**
     * Returns a HTML representation of the user details
     * @return string
     * @todo Implement further details
     */
    public function toHtml()
    {
        $html[] = '<div class="user_details" style="clear: both;background-image: url(' . Theme :: get_common_image_path() . 'content_object/profile.png);">';
        $html[] = '<img src="' . $this->user->get_full_picture_url() . '" alt="' . $this->user->get_fullname() . '" style="margin: 10px;max-height: 150px; border:1px solid black;float: right; display: inline;"/>';
        $html[] = '<div class="title">';
        $html[] = $this->user->get_fullname();
        $html[] = '</div>';
        $html[] = '<div class="description">';
        $html[] = Translation :: get('Email') . ': ' . Display :: encrypted_mailto_link($this->user->get_email());
        $html[] = '<br />' . Translation :: get('Username') . ': ' . $this->user->get_username();
        $html[] = '<br />' . Translation :: get('Status') . ': ' . ($this->user->get_status() == 1 ? Translation :: get('Teacher') : Translation :: get('Student'));
        if ($this->user->is_platform_admin())
        {
            $html[] = ', ' . Translation :: get('PlatformAdmin');
        }
        $html[] = '</div>';
        $html[] = '<div style="clear:both;"><span></span></div>';
        $html[] = '</div>';
        return implode("\n", $html);
    }
}
?>