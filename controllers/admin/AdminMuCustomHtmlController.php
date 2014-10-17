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
		$this->bootstrap = true;
		$this->table = 'mucustomhtml';
		$this->className = 'MuCustomHtmlModel';
		$this->lang = true;
		$this->deleted = false;
		
		$this->explicitSelect = true;
		$this->_defaultOrderBy = 'position';

		$this->context = Context::getContext();
		$this->bulk_actions = array(
			'delete' => array(
				'text' => $this->l('Delete selected'),
				'confirm' => $this->l('Delete selected items?'),
				'icon' => 'icon-trash'
			)
		);

		$this->fieldImageSettings = array(
			'name' => 'blockpicture',
			'dir' => 'mu'
		);

		$this->block_img_path = _PS_IMG_DIR_.'mu/';

		$this->fields_list = array(
			'id_mucustomhtml' => array(
				'title' => $this->l('ID'),
				'align' => 'center',
				'class' => 'fixed-width-xs'
			),
			'blockname' => array(
				'title' => $this->l('Block Title'),
			),
			'image' => array(
  				'title' => $this->l('Main Picture'),
  				'width' => 70,
  				'image' => $this->fieldImageSettings["dir"]
  			),
			'position' => array(
				'title' => $this->l('Position'),
				'align' => 'center',
				'class' => 'fixed-width-sm',
				'position' => 'position'
			),
			'active' => array(
				'title' => $this->l('Displayed'),
				'active' => 'status',
				'align' => 'center',
				'type' => 'bool',
				'class' => 'fixed-width-sm',
				'orderby' => false
			)
		);
		parent::__construct();
	}
	public function renderList()
	{
		$this->addRowAction('edit');
		$this->addRowAction('delete');

		return parent::renderList();
	}

	public function initPageHeaderToolbar()
	{	

		$this->page_header_toolbar_title = $this->l('Custom html blocks');
		if ($this->display != 'edit' || $this->display != 'add')
			$this->page_header_toolbar_btn['addmucustomhtml'] = array(
				'href' => $this->context->link->getAdminLink('AdminMuCustomHtml').'&addmucustomhtml',
				'desc' => $this->l('Add new html block', null, null, false),
				'icon' => 'process-icon-new'
			);

		parent::initPageHeaderToolbar();
	}
	
	public function initToolbar()
	{
		if (empty($this->display))
		{
			$this->toolbar_btn['new'] = array(
				'href' => self::$currentIndex.'&amp;add'.$this->table.'&amp;token='.$this->token,
				'desc' => $this->l('Add New')
			);
		}
		if (Tools::getValue('id_mucustomhtml') && !Tools::isSubmit('updatecategory'))
		{
			$this->toolbar_btn['edit'] = array(
				'href' => self::$currentIndex.'&amp;update'.$this->table.'&amp;id_cmucustomhtml='.(int)Tools::getValue('id_mucustomhtml').'&amp;token='.$this->token,
				'desc' => $this->l('Edit')
			);
		}
		parent::initToolbar();
	}

	public function initContent()
	{
		if ($this->action == 'select_delete')
			$this->context->smarty->assign(array(
				'delete_form' => true,
				'url_delete' => htmlentities($_SERVER['REQUEST_URI']),
				'boxes' => $this->boxes,
			));

		parent::initContent();
	}

	public function renderForm()
	{
		if (!($obj = $this->loadObject(true)))
			return;

		$image = $this->block_img_path.$obj->id.'.jpg';
		$image_url = ImageManager::thumbnail($image, $this->table.'_'.(int)$obj->id.'.'.$this->imageType, 350, $this->imageType, true, true);
		$image_size = file_exists($image) ? filesize($image) / 1000 : false;
		
		$this->fields_form = array(
			'tinymce'=> true,
			'legend' => array(
				'title' => $this->l('Custom html block'),
				'icon' => 'icon-cogs',
			),
			'input' => array(
				array(
					'type' => 'text',
					'name' => 'blockname',
					'label' =>  $this->l('Name'),
					'size' => 40
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
					'type' => 'text',
					'label' => $this->l('Target URL'),
					'name' => 'link',
					'lang' => true,
				),
				array(
					'type' => 'switch',
					'label' => $this->l('Displayed:'),
					'name' => 'active',
					'required' => false,
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
					),
				),
				array(
					'type' => 'file',
					'label' => $this->l('Picture'),
					'name' => 'blockpicture',
					'display_image' => true,
					'image' => $image_url ? $image_url : false,
					'size' => $image_size,
					'hint' => $this->l('Block picture.')
				),
			),
			'submit' => array(
				'title' => $this->l('Save'),
				'name' => 'submitAdd'.$this->table
			)
		);

		return parent::renderForm();
	}

	protected function postImage($id)
	{

		$ret = parent::postImage($id);

		if (($id_mucustomhtml = (int)Tools::getValue('id_mucustomhtml')) && isset($_FILES) && count($_FILES) && file_exists($this->block_img_path.$id_mucustomhtml.'.jpg'))
		{
			$images_types = ImageType::getImagesTypes('stores');
			foreach ($images_types as $k => $image_type)
			{
				ImageManager::resize($this->block_img_path.$id_mucustomhtml.'.jpg',
							$this->block_img_path.$id_mucustomhtml.'-'.stripslashes($image_type['name']).'.jpg',
							(int)$image_type['width'], (int)$image_type['height']
				);
			}
		}
		return $ret;
	}
}