<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2016 Leo Feyer
 *
 * @license LGPL-3.0+
 */


/**
 * System configuration
 */
$GLOBALS['TL_DCA']['tl_page']['palettes']['__selector__'][] = 'stylesheet_enable';
$GLOBALS['TL_DCA']['tl_page']['subpalettes']['stylesheet_enable'] ='stylesheet';

$GLOBALS['TL_DCA']['tl_page']['palettes']['regular'] = str_replace('includeLayout;','includeLayout;{stylesheet_legend},stylesheet_enable;',$GLOBALS['TL_DCA']['tl_page']['palettes']['regular']);


$GLOBALS['TL_DCA']['tl_page']['fields']['stylesheet_enable'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_page']['stylesheet_enable'],
	'exclude'                 => true,
	'inputType'               => 'checkbox',
	'eval'                    => array('submitOnChange'=>true, 'tl_class'=>'long'),
	'sql'                     => "char(1) NOT NULL default ''"
);

$GLOBALS['TL_DCA']['tl_page']['fields']['stylesheet'] = array
		(
			'label'                   => &$GLOBALS['TL_LANG']['tl_page']['stylesheet'],
			'exclude'                 => true,
			'filter'                  => true,
			'inputType'               => 'checkboxWizard',
			'foreignKey'              => 'tl_style_sheet.name',
			'options_callback'        => array('tl_page_chibko', 'getStyleSheets'),
			'eval'                    => array('multiple'=>true),
			'xlabel' => array
			(
				array('tl_page_chibko', 'styleSheetLink')
			),
			'sql'                     => "blob NULL",
			'relation'                => array('type'=>'hasMany', 'load'=>'lazy')
		);
class tl_page_chibko extends tl_page
{

	
	/**
	 * Return all style sheets of the current theme
	 *
	 * @param DataContainer $dc
	 *
	 * @return array
	 */
	public function getStyleSheets(DataContainer $dc)
	{
		// Récupère le layout de la page
        $id=$dc->activeRecord->id;
        $pid=$dc->activeRecord->pid;
        $pageObj=\PageModel::findById($id);
        
        if ($dc->activeRecord->includeLayout) {
            $layout=$dc->activeRecord->layout;
        } else {
            // Load all parent pages
            $objParentPage = \PageModel::findParentsById($id);
            if ($objParentPage !== null)
			{
				
                /*echo "Test : ".$pageObj->rootId."<br>";
                echo "<pre>";
                print_r($pageObj);
                echo "</pre>";*/
                while ($objParentPage->next())
				{
                    // Layout
					if ($objParentPage->includeLayout) {
				        $layout = $objParentPage->layout;
                        break;
					}
				}
			}
        
        }
       
        // récupère le template de page
        $layoutModel=LayoutModel::findById($layout);
        
        // Récupère le theme ID
        $themeId=$layoutModel->pid;
        
        $objStyleSheet = $this->Database->prepare("SELECT id, name FROM tl_style_sheet WHERE pid=?")
										->execute($themeId);

		if ($objStyleSheet->numRows < 1)
		{
			return array();
		}

		$return = array();

		while ($objStyleSheet->next())
		{
			$return[$objStyleSheet->id] = $objStyleSheet->name;
		}

		return $return;
	}


	



	/**
	 * Add a link to edit the stylesheets of the theme
	 *
	 * @param DataContainer $dc
	 *
	 * @return string
	 */
	public function styleSheetLink(DataContainer $dc)
	{
		return ' <a href="contao/main.php?do=themes&amp;table=tl_style_sheet&amp;id=' . $dc->activeRecord->pid . '&amp;popup=1&amp;rt=' . REQUEST_TOKEN . '" title="' . specialchars($GLOBALS['TL_LANG']['tl_layout']['edit_styles']) . '" onclick="Backend.openModalIframe({\'width\':768,\'title\':\''.specialchars(str_replace("'", "\\'", $GLOBALS['TL_LANG']['tl_layout']['edit_styles'])).'\',\'url\':this.href});return false">' . Image::getHtml('edit.gif', '', 'style="vertical-align:text-bottom"') . '</a>';
	}


	/**
	 * Auto-select layout.css if responsive.css is selected (see #8222)
	 *
	 * @param mixed $value
	 *
	 * @return string
	 */
	public function checkFramework($value)
	{
		if (empty($value))
		{
			return '';
		}

		$array = deserialize($value);

		if (empty($array) || !is_array($array))
		{
			return $value;
		}

		if (($i = array_search('responsive.css', $array)) !== false && array_search('layout.css', $array) === false)
		{
			array_insert($array, $i, 'layout.css');
		}

		return $array;
	}
}
