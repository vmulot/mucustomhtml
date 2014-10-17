<?php
if (!defined('_PS_VERSION_'))
  exit;

require_once(_PS_MODULE_DIR_ . 'mucustomhtml/models/MuCustomHtmlModel.php');

class MuCustomHtml extends Module {
	
	public function __construct()
	{
	    $this->name = 'mucustomhtml';
	    $this->tab = 'front_office_features';
	    $this->version = 1.0;
	    $this->author = 'Vincent Mulot';
	    $this->need_instance = 0;
	    $this->bootstrap = true;
	    parent::__construct();
	    
	    $this->displayName = $this->l('Mu Custom Html');
	    $this->description = $this->l('Create html blocks, and hook them');
		
		if ($this->active && Configuration::get('MU_NB_BLOCK') == '')
				$this->warning = $this->l('You have to configure your module');
	}
 
	public function install()
	{
		// Install SQL
		include(dirname(__FILE__).'/sql/install.php');
		foreach ($sql as $s)
			if (!Db::getInstance()->execute($s))
				return false;
	
		// Install Module  
		return parent::install() && $this->registerHook('displayHome') && $this->registerHook('displayHeader') && $this->registerHook('displayTopColumn');
    }
    
	public function uninstall()
	{
		// Uninstall SQL
		include(dirname(__FILE__).'/sql/uninstall.php');
		foreach ($sql as $s)
			if (!Db::getInstance()->execute($s))
				return false;
		
		Configuration::deleteByName('MU_NB_BLOCK');
		
		// Uninstall Module
		if (!parent::uninstall())
			return false;

		return true;
	}
	
	public function getContent()
	{
		$output = '';
		if(Tools::isSubmit('submitMuCustomHtml'))
		{
			$nbr = (int)Tools::getValue('MU_NB_BLOCK');
			if (!$nbr || $nbr <= 0 || !Validate::isInt($nbr))
				$errors[] = $this->l('An invalid number of block has been specified.');
			else
			{
				Configuration::updateValue('MU_NB_BLOCK', (int)$nbr);
			}
			
			if (isset($errors) && count($errors))
				$output .= $this->displayError(implode('<br />', $errors));
			else
				$output .= $this->displayConfirmation($this->l('Your settings have been updated.')); 
		}

		return $output.$this->renderForm();
	}

	public function renderForm()
	{
		$fields_form = array(
			'form' => array(
				'legend' => array(
					'title' => $this->l('Settings'),
					'icon' => 'icon-cog'
				),
				'input' => array(
					array(
						'type' => 'text',
						'label' => $this->l('Number of block to display'),
						'name' => 'MU_NB_BLOCK',
						'desc' => $this->l('Display X blocks where X is the number entered'),
						'class' => 'fixed-width-xs',
					)
				),
				'submit' => array(
					'title' => $this->l('Save'),
				)
			)
		);
		
		$helper = new HelperForm();
		$helper->show_toolbar = true;
		$helper->table = $this->table;
		$lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
		$helper->default_form_language = $lang->id;
		$helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
		$this->fields_form = array();

		$helper->identifier = $this->identifier;
		$helper->submit_action = 'submitMuCustomHtml';
		$helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
		$helper->token = Tools::getAdminTokenLite('AdminModules');
		$helper->tpl_vars = array(
			'fields_value' => array('MU_NB_BLOCK' => Tools::getValue('MU_NB_BLOCK', Configuration::get('MU_NB_BLOCK'))),
			'languages' => $this->context->controller->getLanguages(),
			'id_language' => $this->context->language->id
		);

		return $helper->generateForm(array($fields_form));
	}

	public function hookDisplayHeader()
	{
		$this->context->controller->addCSS($this->_path.'views/css/mucustomhtml.css');
	}
	
	public function hookDisplayHome($params)
	{
		$id_lang = (int)$this->context->language->id;
		$nb_blocks = (int)Configuration::get('MU_NB_BLOCK');
		$taille_col = round(12/$nb_blocks);

		$mucustomhtml = MuCustomHtmlModel::findAllbyIdLang($id_lang);
		$this->smarty->assign(array(
			'img_mu_dir' => _PS_IMG_.'mu/',
			'taille' => $taille_col,
			'customhtmlblocks' => $mucustomhtml
		));
		return $this->display(__FILE__, 'views/templates/hooks/mu-home.tpl');
	}

	public function hookDisplayTopColumn($params)
	{
		if (!isset($this->context->controller->php_self) || $this->context->controller->php_self != 'index')
			return;
		return $this->hookDisplayHome($params);
	}
} 
?>