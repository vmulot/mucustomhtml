<?php
class MuCustomHtmlModel extends ObjectModel
{
	public $htmlcontent;
	public $blockname;
	public $link;
	public $active = 1;
	public $position;

	public function __construct($id_mucustomhtml = null, $id_lang = null)
	{
		parent::__construct($id_mucustomhtml);
		$this->id_image = ($this->id && file_exists( _PS_IMG_DIR_.'mu/'.(int)$this->id.'.jpg')) ? (int)$this->id : false;
		$this->image_dir =  _PS_IMG_DIR_.'mu/';
	}

	public static $definition = array(
		'table' => 'mucustomhtml',
		'primary' => 'id_mucustomhtml',
		'multilang' => true,
		'fields' => array(
			'blockname' => array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'required'=> true),
			'active' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool', 'required' => true),
			'position' => array('type' => self::TYPE_INT),
			'htmlcontent' => array('type' => self::TYPE_HTML, 'lang' => true, 'validate' => 'isCleanHtml', 'size' => 3999999999999),
			'link' => array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isUrl', 'required' => true, 'size' => 255),
		),
	);
			
	public static function findAllbyIdLang($id_lang) {
    	$sql = '
    	SELECT * 
    	FROM ' . _DB_PREFIX_ . 'mucustomhtml m
    	LEFT JOIN '._DB_PREFIX_.'mucustomhtml_lang l ON(m.id_mucustomhtml = l.id_mucustomhtml)
    	WHERE l.id_lang='.(int)$id_lang.' 
    	AND m.active = 1
    	ORDER BY m.position
    	LIMIT 6';

    	if ($rows = Db :: getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS($sql)) {
    		$blocks = array();
    		foreach($rows as $block){
    			$block['has_picture'] = file_exists(_PS_IMG_DIR_.'mu/'.(int)($block['id_mucustomhtml']).'.jpg');
    			$blocks[] = $block;
    		}
        	return $blocks;
        }
        return array();
    }
    
    
	public function copyFromPost()
	{
		/* Classical fields */
		foreach ($_POST AS $key => $value)
			if (key_exists($key, $this) AND $key != 'id_'.$this->table)
				$this->{$key} = $value;

		/* Multilingual fields */
		if (sizeof($this->fieldsValidateLang))
		{
			$languages = Language::getLanguages(false);
			foreach ($languages AS $language)
				foreach ($this->fieldsValidateLang AS $field => $validation)
					if (isset($_POST[$field.'_'.(int)($language['id_lang'])]))
						$this->{$field}[(int)($language['id_lang'])] = $_POST[$field.'_'.(int)($language['id_lang'])];
		}
	}
	
	/**
	 * Moves a html block
	 *
	 * @since 1.5.0
	 * @param boolean $way Up (1) or Down (0)
	 * @param integer $position
	 * @return boolean Update result
	 */
	public function updatePosition($way, $position)
	{
		if (!$res = Db::getInstance()->executeS('
			SELECT `id_mucustomhtml`, `position`
			FROM `'._DB_PREFIX_.'mucustomhtml`
			ORDER BY `position` ASC'
		))
		return false;
		
		foreach ($res as $htmlblock)
			if ((int)$htmlblock['id_mucustomhtml'] == (int)$this->id)
				$moved_block = $htmlblock;

		if (!isset($moved_block) || !isset($position))
			return false;

		// < and > statements rather than BETWEEN operator
		// since BETWEEN is treated differently according to databases
		return (Db::getInstance()->execute('
			UPDATE `'._DB_PREFIX_.'mucustomhtml`
			SET `position`= `position` '.($way ? '- 1' : '+ 1').'
			WHERE `position`
			'.($way
				? '> '.(int)$moved_block['position'].' AND `position` <= '.(int)$position
				: '< '.(int)$moved_block['position'].' AND `position` >= '.(int)$position))
		&& Db::getInstance()->execute('
			UPDATE `'._DB_PREFIX_.'mucustomhtml`
			SET `position` = '.(int)$position.'
			WHERE `id_mucustomhtml` = '.(int)$moved_block['id_mucustomhtml']));
	}

	/**
	 * Reorders html blocks positions.
	 * Called after deleting a carrier.
	 *
	 * @since 1.5.0
	 * @return bool $return
	 */
	public static function cleanPositions()
	{
		$return = true;

		$sql = '
		SELECT `id_mucustomhtml`
		FROM `'._DB_PREFIX_.'mucustomhtml`
		ORDER BY `position` ASC';
		$result = Db::getInstance()->executeS($sql);

		$i = 0;
		foreach ($result as $value)
			$return = Db::getInstance()->execute('
			UPDATE `'._DB_PREFIX_.'mucustomhtml`
			SET `position` = '.(int)$i++.'
			WHERE `id_mucustomhtml` = '.(int)$value['id_mucustomhtml']);
		return $return;
	}
}