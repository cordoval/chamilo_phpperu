<?php

namespace application\portfolio;

use common\libraries\WebApplication;
use common\libraries\Utilities;


class PortfolioAutoloader
{
	static function load($classname)
	{
            $classname_parts = explode('\\', $classname);

        if (count($classname_parts) == 1)
        {
                return false;
        }
        else
        {
                $classname = $classname_parts[count($classname_parts) - 1];
                array_pop($classname_parts);
            if (implode('\\', $classname_parts) != __NAMESPACE__)
            {
                    return false;
                }
            }

            $list = array(
                'portfolio_publication' => 'portfolio_publication.class.php',
                'portfolio_data_manager_interface' => 'portfolio_data_manager_interface.class.php',
                'portfolio_data_manager' => 'portfolio_data_manager.class.php',
                'portfolio_location' => 'portfolio_location.class.php',
                'portfolio_rights' => 'rights/portfolio_rights.class.php',
                'portfolio_information' => 'portfolio_information.class.php',
                'portfolio_manager' => 'portfolio_manager/portfolio_manager.class.php');
            $lower_case = Utilities :: camelcase_to_underscores($classname);
        if (key_exists($lower_case, $list))
        {
                $url = $list[$lower_case];
                require_once WebApplication :: get_application_class_lib_path('portfolio') . $url;
                return true;
            }

            return false;
        }
    }

?>