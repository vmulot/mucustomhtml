<?php
/**
 * Tab Example - Controller Admin Example
 *
 * @category   	Module / checkout
 * @author     	PrestaEdit <j.danse@prestaedit.com>
 * @copyright  	2012 PrestaEdit
 * @version   	1.0	
 * @link       	http://www.prestaedit.com/
 * @since      	File available since Release 1.0
*/

class AdminMuCustomHtmlController extends ModuleAdminController
{
	protected $position_identifier = 'id_mucustomhtml';
	
	public function __construct()
	{
		$this->table = 'mucustomhtml';
		$this->className = 'MuCustomHtmlModel';
		$this->lang = true;
		$this->deleted = false;
		$this->colorOnBackground = false;
		$this->bulk_actions = array('delete' => array('text' => $this->l('Delete selected'), 'confirm' => $this->l('Delete selected items?')));
		$this->context = Context::getContext();
		
		$this->_defaultOrderBy = 'position';

		parent::__construct();
	}
	
	/**
	 * Function used to render the list to display for this controller
	 */
	public function renderList()
	{
		$this->addRowAction('edit');
		$this->addRowAction('delete');
		
		$this->bulk_actions = array(
			'delete' => array(
				'text' => $this->l('Delete selected'),
				'confirm' => $this->l('Delete selected items?')
				)
			);
		
		$this->fields_list = array(
			'id_mucustomhtml' => array(
				'title' => $this->l('ID'),
				'align' => 'center',
				'width' => 25
			),
			'blockname' => array(
				'title' => $this->l('Name'),
				'width' => 'auto'
			),
			'position' => array(
				'title' => $this->l('Position'),
				'width' => 40,
				'align' => 'center',
				'position' => 'position'
			),
			'active' => array(
				'title' => $this->l('Displayed'),
				'active' => 'status',
				'align' => 'center',
				'type' => 'bool',
				'width' => 70,
				'orderby' => false
			)
		);
		$lists = parent::renderList();
		
		parent::initToolbar();
		
		return $lists;
	}
				
	public function renderForm()
	{
		
		$this->fields_form = array(
			'tinymce' => true,
			'legend' => array(
				'title' => $this->l('Custom html block'),
				'image' => '../img/admin/cog.gif'
			),
			'input' => array(
				array(
					'type' => 'text',
					'name' => 'blockname',
					'label' =>  $this->l('Name'),
					'size' => 40
				),
				array(
					'type' => 'text',
					'name' => 'cssclass',
					'label' =>  $this->l('Css class'),
					'size' => 40,
					'lang' => true,
					'desc' => $this->l('Add a css class to block element')
				),
				array(
					'type' => 'textarea',
					'name' => 'htmlcontent',
					'cols' => 70,
					'rows' => 30,
					'lang' => true,
					'autoload_rte' => true,
					'label' => $this->l('Custom html:'),
				),
				array(
					'type' => 'radio',
					'label' => $this->l('Displayed:'),
					'name' => 'active',
					'required' => false,
					'class' => 't',
					'is_bool' => true,
					'values' => array(
						array(
							'id' => 'active_on',
							'value' => 1,
							'label' => $this->l('Enabled')
						),
						array(
							'id' => 'active_off',
							'value' => 0,
							'label' => $this->l('Disabled')
						)
					)
				),
			),
			'submit' => array(
				'title' => $this->l('Save'),
				'class' => 'button'
			)
		);

		if (!($obj = $this->loadObject(true)))
			return;

		return parent::renderForm();
	}
}