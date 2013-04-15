<?php
if (!defined('_PS_VERSION_'))
  exit;

// Loading Models
require_once(_PS_MODULE_DIR_ . 'mucustomhtml/models/MuCustomHtmlModel.php');

class MuCustomHtml extends Module {

	private $_html = '';
	private $limit = 6;
	
	public function __construct()
	{
	    $this->name = 'mucustomhtml';
	    $this->tab = 'front_office_features';
	    $this->version = 1.0;
	    $this->author = ' vmulot';
	    $this->ps_versions_compliancy['min'] = '1.5.0.1';
	    $this->need_instance = 0;
	    
	    parent::__construct();
	    
	    $this->displayName = $this->l('Mu Custom Html');
	    $this->description = $this->l('Create html blocks, hook on Home page and Customer Account');
	    $this->confirmUninstall = $this->l('Are you sure you want to delete this module ?');
	}
 
	public function install()
	{
		// Install SQL
		include(dirname(__FILE__).'/sql/install.php');
		foreach ($sql as $s)
			if (!Db::getInstance()->execute($s))
				return false;
		
		// Install Module  
		if (parent::install() == false OR !$this->registerHook('displayHome') OR !$this->registerHook('displayHeader') OR !$this->registerHook('displayCustomerAccount'))
			return false;
		return true;

    }
    
	public function uninstall()
	{
		// Uninstall SQL
		include(dirname(__FILE__).'/sql/uninstall.php');
		foreach ($sql as $s)
			if (!Db::getInstance()->execute($s))
				return false;

		if (!parent::uninstall())
			return false;

		return true;
	}

	public function hookDisplayHeader()
	{
		$this->context->controller->addCSS($this->_path.'views/css/mucustomhtml.css');
	}
	
	public function hookDisplayHome()
	{
		$id_lang = (int)$this->context->language->id;
		
		$mucustomhtml = MuCustomHtmlModel::findAllbyIdLang($id_lang);
		
		$this->smarty->assign(array(
			'customhtmlblocks' => $mucustomhtml
		));
		return $this->display(__FILE__, 'views/templates/hooks/mucustomhtml.tpl');
	}
	
	public function hookDisplayCustomerAccount(){
		return $this->hookDisplayHome();
	}
} 
?>
