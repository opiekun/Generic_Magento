<?php
/**
 * Magezon
 *
 * This source file is subject to the Magezon Software License, which is available at https://www.magezon.com/license
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to https://www.magezon.com for more information.
 *
 * @category  Magezon
 * @package   Magezon_Core
 * @copyright Copyright (C) 2019 Magezon (https://www.magezon.com)
 */

namespace Magezon\Core\Plugin\View\Result;

class Layout
{
	public function aroundRenderResult(
		$subject,
		callable $proceed,
		$httpResponse
	) {
		$result = $proceed($httpResponse);
		$html   = $httpResponse->getBody();
		$html   = $this->minify($html);
        $httpResponse->setBody($html);
		return $result;
    }

    public function minify($html)
    {
    	$regex  = '@(?:<style class="mgz-style">)(.*)</style>@msU';
		preg_match_all($regex, $html, $matches);
        if ($matches[0]) {
        	$stylesHtml = '';
        	foreach ($matches[0] as $_style) {
				$stylesHtml .= str_replace(['<style class="mgz-style">', '</style>'], [], $_style);
        	}
        	$stylesHtml = $this->minifyCss($stylesHtml);
        	$html = preg_replace($regex, '', $html);
        	$html = str_replace('</head>', '<style>' . $stylesHtml . '</style>' . '</head>', $html);
        }
        return $html;
    }

	 /**
	 * https://gist.github.com/webgefrickel/3339063
	 * This function takes a css-string and compresses it, removing
	 * unneccessary whitespace, colons, removing unneccessary px/em
	 * declarations etc.
	 *
	 * @param string $css
	 * @return string compressed css content
	 * @author Steffen Becker
	 */
	function minifyCss($css) {
	  // some of the following functions to minimize the css-output are directly taken
	  // from the awesome CSS JS Booster: https://github.com/Schepp/CSS-JS-Booster
	  // all credits to Christian Schaefer: http://twitter.com/derSchepp
	  // remove comments
	  $css = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $css);
	  // backup values within single or double quotes
	  preg_match_all('/(\'[^\']*?\'|"[^"]*?")/ims', $css, $hit, PREG_PATTERN_ORDER);
	  for ($i=0; $i < count($hit[1]); $i++) {
	    $css = str_replace($hit[1][$i], '##########' . $i . '##########', $css);
	  }
	  // remove traling semicolon of selector's last property
	  $css = preg_replace('/;[\s\r\n\t]*?}[\s\r\n\t]*/ims', "}\r\n", $css);
	  // remove any whitespace between semicolon and property-name
	  $css = preg_replace('/;[\s\r\n\t]*?([\r\n]?[^\s\r\n\t])/ims', ';$1', $css);
	  // remove any whitespace surrounding property-colon
	  $css = preg_replace('/[\s\r\n\t]*:[\s\r\n\t]*?([^\s\r\n\t])/ims', ':$1', $css);
	  // remove any whitespace surrounding selector-comma
	  $css = preg_replace('/[\s\r\n\t]*,[\s\r\n\t]*?([^\s\r\n\t])/ims', ',$1', $css);
	  // remove any whitespace surrounding opening parenthesis
	  $css = preg_replace('/[\s\r\n\t]*{[\s\r\n\t]*?([^\s\r\n\t])/ims', '{$1', $css);
	  // remove any whitespace between numbers and units
	  $css = preg_replace('/([\d\.]+)[\s\r\n\t]+(px|em|pt|%)/ims', '$1$2', $css);
	  // shorten zero-values
	  $css = preg_replace('/([^\d\.]0)(px|em|pt|%)/ims', '$1', $css);
	  // constrain multiple whitespaces
	  $css = preg_replace('/\p{Zs}+/ims',' ', $css);
	  // remove newlines
	  $css = str_replace(array("\r\n", "\r", "\n"), '', $css);
	  // Restore backupped values within single or double quotes
	  for ($i=0; $i < count($hit[1]); $i++) {
	    $css = str_replace('##########' . $i . '##########', $hit[1][$i], $css);
	  }
	  return $css;
	}
}