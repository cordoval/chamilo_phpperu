<?php
require_once Path :: get_common_path().'/rss/publication_rss.class.php';
require_once dirname(__FILE__).'/../portfolio_data_manager.class.php';
require_once dirname(__FILE__).'/../portfolio_manager/portfolio_manager.class.php';

class PortfolioPublicationRSS extends PublicationRSS
{
	function PortfolioPublicationRSS()
	{
		parent :: PublicationRSS('Chamilo Portfolio', 'http://localhost', 'Portfolio publications', 'http://localhost');
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
		$params[PortfolioManager :: PARAM_USER_ID] = $pub->get_publisher();
		$params[PortfolioManager :: PARAM_PORTFOLIO_PUBLICATION] = $pub->get_id();
		//$params = array();
		return Path :: get(WEB_PATH).Redirect :: get_link(PortfolioManager :: APPLICATION_NAME, $params);
	}
	
	function is_visible_for_user($user, $pub)
	{
		return $pub->is_visible_for_target_user($user->get_id());
	}
}

?>