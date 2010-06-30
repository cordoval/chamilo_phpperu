<?php
/**
 * $Id: default_user_table_cell_renderer.class.php 211 2009-11-13 13:28:39Z vanpouckesven $
 * @package user.lib.user_table
 */


class DefaultInternshipOrganizerUserTableCellRenderer extends ObjectTableCellRenderer
{

    /**
     * Constructor
     */
    function DefaultInternshipOrganizerUserTableCellRenderer()
    {
    }

    /**
     * Renders a table cell
     * @param UserTableColumnModel $column The column which should be
     * rendered
     * @param User $user The User to render
     * @return string A HTML representation of the rendered table cell
     */
    function render_cell($column, $user)
    {
        switch ($column->get_name())
        {
            case User :: PROPERTY_ID :
                return $user->get_id();
            case User :: PROPERTY_LASTNAME :
                return $user->get_lastname();
            case User :: PROPERTY_FIRSTNAME :
                return $user->get_firstname();
            case User :: PROPERTY_USERNAME :
                return $user->get_username();
            case User :: PROPERTY_EMAIL :
                return $user->get_email();
            case User :: PROPERTY_STATUS :
                return $user->get_status();
            case User :: PROPERTY_PLATFORMADMIN :
                return $user->get_platformadmin();
            case User :: PROPERTY_OFFICIAL_CODE :
                return $user->get_official_code();
//            case User :: PROPERTY_LANGUAGE :
//                return $user->get_language();
            case User :: PROPERTY_VERSION_QUOTA :
                return $user->get_version_quota();
            case User :: PROPERTY_PICTURE_URI :
                return $this->render_picture($user);
            default :
                return '&nbsp;';
        }
    }

    function render_id_cell($object)
    {
        return $object->get_id();
    }

    private function render_picture($user)
    {
        if ($user->has_picture())
        {
            $picture = $user->get_full_picture_path();
            $thumbnail_path = $this->get_thumbnail_path($picture);
            $thumbnail_url = Path :: get(WEB_TEMP_PATH) . basename($thumbnail_path);
            return '<span style="display:none;">1</span><img src="' . $thumbnail_url . '" alt="' . htmlentities($user->get_fullname()) . '" border="0"/>';
        }
        else
        {
            return '<span style="display:none;">0</span>';
        }
    }

    private function get_thumbnail_path($image_path)
    {
        $thumbnail_path = Path :: get(WEB_TEMP_PATH) . Hashing :: hash($image_path) . basename($image_path);
        if (! is_file($thumbnail_path))
        {
            $thumbnail_creator = ImageManipulation :: factory($image_path);
            $thumbnail_creator->create_thumbnail(20);
            $thumbnail_creator->write_to_file($thumbnail_path);
        }
        return $thumbnail_path;
    }
}
?>