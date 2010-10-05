<?php


class DefaultPortfolioTableCellRenderer extends ObjectTableCellRenderer
{

    
    function DefaultPortfolioTableCellRenderer($browser)
    {
    
    }

       function render_cell($column, $user)
    {
        $url = $this->browser->get_view_portfolio_url($user->get_id());
        switch ($column->get_name())
        {
            case User::PROPERTY_FIRSTNAME:
             return '<a href="' . $url . '" alt="' . $user->get_firstname() . '">' . $user->get_firstname() . '</a>';
            case USER::PROPERTY_LASTNAME:
                return '<a href="' . $url . '" alt="' . $user->get_lastname() . '">' . $user->get_lastname() . '</a>';
             case User::PROPERTY_OFFICIAL_CODE :
                return $user->get_official_code();

        }
        
        $title = $column->get_title();
        if ($title == '')
        {
            $img = Theme :: get_common_image_path() . ('treemenu_types/profile.png');
            return '<img src="' . $img . '"alt="course" />';
        }
        
        return '&nbsp;';
    }

    function render_id_cell($course)
    {
        return $course->get_id();
    }
}
?>