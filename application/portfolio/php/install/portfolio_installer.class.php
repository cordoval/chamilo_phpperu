<?php

namespace application\portfolio;

use common\libraries\Installer;
use common\libraries\Translation;
use common\libraries\Utilities;

require_once dirname(__FILE__). '/../lib/portfolio_data_manager.class.php';
require_once dirname(__FILE__). '/../lib/rights/portfolio_rights.class.php';
/**
 * This installer can be used to create the storage structure for the
 * portfolio application.
 * @author Sven Vanpoucke
 */
class PortfolioInstaller extends Installer
{

    /**
     * Constructor
     */
    function PortfolioInstaller($values)
    {
        parent :: __construct($values, PortfolioDataManager :: get_instance());
    }

    function get_path()
    {
        return dirname(__FILE__);
    }

    /**
	 * Runs the install-script.
	 */
	function install_extra()
	{
		if (! $this->create_default_location())
		{
			return false;
		}
		else
		{
			$this->add_message(self :: TYPE_NORMAL, Translation :: get('DefaultLocationCreated'), null, Utilities::COMMON_LIBRARIES);
		}



		return true;
	}

        /**
         * create a location with tree identifier 0 to apply
         * default settings for the portfolio
         */
        function create_default_location()
        {
            return PortfolioRights::create_default_location();


        }
}
?>