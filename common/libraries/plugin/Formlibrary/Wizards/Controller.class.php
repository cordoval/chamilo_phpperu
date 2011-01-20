<?php 
require_once Path:: get_plugin_path() . 'FormLibrary/Wizards/Storages/PageStorage.class.php';
require_once Path:: get_plugin_path() . 'FormLibrary/Wizards/Storages/ActionStorage.class.php';
require_once Path:: get_plugin_path() . 'FormLibrary/Wizards/Page.class.php';
require_once Path:: get_plugin_path() . 'FormLibrary/Wizards/Action.interface.php';

/*
 * Controller class, it will take care of the flow in a wizard
 * Via this class you can add, remove pages from a wizard
 */
class Controller
{
	protected $pagestorage;		//Page storage that holds all the pages of the wizard
	protected $actionstorage;	//Action storages that holds all the actions like the process that has to be performed when the wizard got ended succesfully
	protected $currentpage;		//Number that represents the current page
	
	/*
	 * Constructor of the class
	 * The page storage and action storage get instanciated and the current page starts from 0
	 */
	public function Controller($name)
	{
		$this->pagestorage = new PageStorage($this);
		$this->actionstorage = new ActionStorage($this);
		$this->currentpage = 0;
	}
	
	/*
	 * Getter to get the number of the current page
	 */
	public function get_current_page()
	{
		return $this->currentpage;
	}
	
	/*
	 * Via this function you can add a page to the  wizard
	 */
	public function add_page($page)
	{
		if(!is_null($page))
		{
			$this->pagestorage->add_page($page);
		}
	}
	
	/*
	 * Via this function you can remove a page from the wizard
	 */
	public function delete_page($page)
	{
		if(!is_null($page))
		{
			$this->pagestorage->delete_page($page);
		}
	}
	
	/*
	 * This function returns the pagestorage
	 */
	public function get_pagestorage()
	{
		return $this->pagestorage;
	}
	
	/*
	 * Via this function you can retrieve a page via the name
	 */
	public function retrieve_page($page)
	{
		if(!is_null($page))
			return $this->pagestorage->retrieve_page($page);		
	}
	
	/*
	 * Via this function you can add an action to the wizard
	 */
	public function add_action($action)
	{
		if(!is_null($action))
			$this->actionstorage->add_action($action);
	}	
	
	/*
	 * This function returns the actionstorage
	 */
	public function get_actionstorage()
	{
		return $this->actionstorage;
	}	
	
	public function run()
	{
		$pages = $this->pagestorage->get_pages();
		$this->handle();
	}	
	
	public function show($page)
	{
		$page->buildForm();
		$page->get_parent()->display_header($trail, false);
		echo $page->render();
		$page->get_parent()->display_footer();
	}
	
	public function handle()
	{
		$pages = $this->pagestorage->get_pages();
		if($pages[$this->currentpage]->is_valid())
		{
			if(isset($_POST['next']))
			{				
				$this->currentpage = $_POST['next'];	
				$curr = $this->currentpage - 1;
				$_SESSION[$pages[$curr]->get_name()] = $pages[$curr]->get_values();
			}
		}
		if(isset($_POST['previous']))
		{
			$this->currentpage = $_POST['previous'];			
		}	
		if(isset($_POST['finish']))
		{
			$actions = $this->get_actionstorage()->get_actions();
			foreach($actions as $action)
			{
				$action->perform();
			}				
		}		
		unset($_POST['next']);
		unset($_POST['previous']);

		$this->show($pages[$this->currentpage]);
	}
	
	public function reset_sessions()
	{
		$pages = $this->pagestorage->get_pages();
		for($i=0; $i<count($pages);$i++)
		{
			unset($_SESSION[$pages[$i]->get_name()]);
		}
	}
}
?>