<?php

namespace xCss2page;

class Css2page {

	public function myGeneratePage($objPage, $objLayout, $objPageRegular) {
		$arrStyleSheetLayout=deserialize($objLayout->stylesheet,true);
        $arrStyleSheetPage=deserialize($objPage->stylesheet,true);
        $arrStyleSheet= array_unique (array_merge($arrStyleSheetLayout,$arrStyleSheetPage));
        $objLayout->stylesheet=serialize($arrStyleSheet);
	}
    
    /**
	 * Modify the cached key
	 *
	 * @param string $key
	 *
	 * @return string
	 */
	public function modifyCacheKey($key)
	{
		$key.= $this->isStyleSheetEnabled() ? '_css2page_' : '';
		
		return $key;
	}
    
    
    
	protected function isStyleSheetEnabled()
	{
        if ($GLOBALS['objPage']->stylesheet_enable)
        {
			return true;
		}
		return false;
	}
}

