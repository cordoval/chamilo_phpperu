<?php
require_once dirname(__FILE__) . '/../wiki_reporting_block.class.php';
require_once PATH :: get_reporting_path() . '/lib/reporting_data.class.php';

class WikiPageUsersContributionsReportingBlock extends WikiReportingBlock
{

    public function count_data()
    {        
        $reporting_data = new ReportingData();
        $reporting_data->set_rows(array(Translation :: get('Username'), Translation :: get('NumberOfContributions')));
        
    	$dm = RepositoryDataManager :: get_instance();
        $complex_content_object_item = $dm->retrieve_complex_content_object_item(Request :: get(ComplexDisplay :: PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID));
        $wiki_page = $dm->retrieve_content_object($complex_content_object_item->get_ref());
        $versions = $dm->retrieve_content_object_versions($wiki_page);
        $users = array();
        foreach ($versions as $version)
        {
            $users[$version->get_default_property(ContentObject :: PROPERTY_OWNER_ID)] ++;
        }
        arsort($users);
        foreach ($users as $user => $number)
        {
            if ($count < 5)
            {
                $user = UserDataManager :: get_instance()->retrieve_user($user);
                $reporting_data->add_category(0);
            	$reporting_data->add_data_category_row(0, Translation :: get('Username'), $user->get_username());
            	$reporting_data->add_data_category_row(0, Translation :: get('NumberOfContributions'), $number);
            	$reporting_data->hide_categories();
                $count ++;
            }
        }        
        return $reporting_data;
    }

    public function retrieve_data()
    {
        return $this->count_data();
    }

    public function get_available_displaymodes()
    {
        $modes = array();
        $modes[ReportingFormatter :: DISPLAY_TABLE] = Translation :: get('Table');
        return $modes;
    }
}
?>