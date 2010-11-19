<?php
namespace application\portfolio;
use common\libraries\PublicationRSS;

class PortfolioPublicationRSS extends PublicationRSS
{
	function __construct()
	{
		parent :: PublicationRSS('Chamilo Portfolio', htmlspecialchars(Path :: get(WEB_PATH)), 'Portfolio publications', htmlspecialchars(Path :: get(WEB_PATH)));
	}
	
	function retrieve_items($user, $min_date = '')
	{
		$pubs = PortfolioDataManager :: get_instance()->retrieve_portfolio_publications(null, null, 20);//, array('id', SORT_DESC));
		$publications = array();
		while ($pub = $pubs->next_result())
		{
			if ($this->is_visible_for_user($user, $pub))
			{
				$publications[] = $pub;
			}
		}
		return $publications;
	}
	
	function get_url($pub)
	{
		$params[Application :: PARAM_ACTION] = PortfolioManager :: ACTION_VIEW_PORTFOLIO;
		$params[PortfolioManager :: PARAM_PORTFOLIO_OWNER_ID] = $pub->get_publisher();
		$params[PortfolioManager :: PARAM_PORTFOLIO_PUBLICATION] = $pub->get_id();
		return Path :: get(WEB_PATH).Redirect :: get_link(PortfolioManager :: APPLICATION_NAME, $params);
	}
	
	function is_visible_for_user($user, $pub)
	{
		return $pub->is_visible_for_target_user($user->get_id());
	}
}

?>