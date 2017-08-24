<?php


if (TL_MODE == 'FE')
{
	$GLOBALS['TL_HOOKS']['generatePage'][] = array('Css2page','myGeneratePage');
    $GLOBALS['TL_HOOKS']['getCacheKey'][] = array('Css2page', 'modifyCacheKey');


}