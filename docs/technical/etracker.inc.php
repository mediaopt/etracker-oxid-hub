<?php
/**
 * This material may not be reproduced, displayed, modified or distributed
 * without the express prior written permission of the copyright holder.
 * 
 * Copyright (c) etracker GmbH. All Rights Reserved
 */

abstract class etracker
{
	const CODE_VERSION = '4.0';
	const CNT_HOST = 'www.etracker.de';
	const STATIC_HOST = 'static.etracker.com';

	/**
	 * @var array
	 */
	private static $defaultParameters = [
		'et_easy' 			=> true,	// easy code
		'et_pagename'		=> '',		// pagename
		'et_areas'			=> '',		// slash delimited area names
		'et_ilevel'			=> 0,		// advanced services: level of interest
		'et_target'			=> '',		// advanced services: comma delimited target names
		'et_tval'			=> 0.0,		// advanced services: target value
		'et_tsale'			=> 0,		// advanced services: lead (0), sale (1) or storno(2)
		'et_tonr'			=> '',		// advanced services: target order number
		'et_lpage'			=> 0,		// advanced services: landing page id
		'et_trig'			=> '',		// advanced services: trigger id
		'et_se'				=> 0,		// advanced services: campaign id
		'et_cust'			=> 0,		// advanced services: status of customer
		'et_basket'			=> '',		// advanced services: shopping cart
		'et_url'			=> '',		// url of the page
		'et_tag'			=> '',		// tag for segmentation
		'et_sub'			=> '',		// sub channel
		'et_organisation' 	=> '',		// special
		'et_demographic'	=> ''		// special
	];

	/**
	 * @param string $secureCode crypted statistic id (secure code)
	 * @param array $parameters counting parameters
	 * @param bool $noScript show noscript part
	 * @param bool $free code for free accounts
	 * @param bool $showAll show all parameters in output
	 * @param array $slaveCodes master/slave tracking
	 * @return string
	 */
	public static function getCode(
		$secureCode,
		$parameters = [],
		$noScript = false,
		$free = false,
		$showAll = false,
		$slaveCodes = []
	)
	{
		if(!preg_match("/^[0-9a-zA-Z]+$/", $secureCode))
			return '';

		$parameters = self::checkParameter(array_merge(self::$defaultParameters, $parameters));

		$code  = "<!-- Copyright (c) 2000-".date("Y")." etracker GmbH. All rights reserved. -->\n";
		$code .= "<!-- This material may not be reproduced, displayed, modified or distributed -->\n";
		$code .= "<!-- without the express prior written permission of the copyright holder. -->\n";
		$code .= "<!-- etracker tracklet ".self::CODE_VERSION." -->\n";
		$code .= self::getParameters( $showAll, $parameters);
		$code .= '<script id="_etLoader" type="text/javascript" charset="UTF-8" data-secure-code="'.$secureCode.'"'.self::getSlaveCodesString($slaveCodes).' src="//'.self::STATIC_HOST.'/code/e.js"></script>'."\n";

		if($noScript)
		{
			$code .= self::getNoScriptTag($secureCode, $free, $parameters);
		}
		$code .= "<!-- etracker tracklet ".self::CODE_VERSION." end -->\n\n";

		return $code;
	}

	/**
	 * @param array $slaveCodes
	 * @return string
	 */
	private static function getSlaveCodesString(array $slaveCodes)
	{
		return $slaveCodes
			? ' data-slave-codes="' . implode(',', $slaveCodes) . '"'
			: '';
	}

	/**
	 * @param bool $showAll
	 * @param array $parameters
	 * @return string
	 */
	private static function getParameters($showAll = false, array $parameters = [])
	{
		$code = '';	

		foreach($parameters as $name => $value)
		{
			if(('et_easy' == $name && $value) || $value || $showAll)
			{
				if ('cc_attributes' != $name)
				{
					$code .= 'var '.$name.' = '.json_encode($value).";\n";
				} else{
					$code .= 'var '.$name.' = '.$value.";\n";
				}

			}
		}

		$ret = '';
		if($code)
		{
			$ret .= "<script type=\"text/javascript\">\n";
			$ret .= $code;
			$ret .= "</script>\n";
		}
		return $ret;
	}
	
	/**
	 * @param string $secureCode
	 * @param bool $free
	 * @param array $parameters
	 * @return string
	 */
	private static function getNoScriptTag($secureCode, $free = false, array $parameters)
	{
		$script = $free ? 'fcnt_css.php' : 'cnt_css.php';
		$code = '<noscript><link rel="stylesheet" media="all" href="//'.self::CNT_HOST."/$script?et=$secureCode&amp;v=".self::CODE_VERSION.'&amp;java=n';
		$code .= self::getNoScriptParameters($parameters);
		$code .= "\" /></noscript>\n";

		return $code;
	}

	/**
	 * @param array $parameters
	 * @return string
	 */
	private static function getNoScriptParameters(array $parameters)
	{
		$parameters['et_target'] = $parameters['et_target']. ',' .$parameters['et_tval'] . ',' . $parameters['et_tonr'] . ',' . $parameters['et_tsale'];
		unset($parameters['et_tval']);
		unset($parameters['et_tonr']);
		unset($parameters['et_tsale']);

		$ret = '';
		foreach($parameters as $key => $value)
		{
			$ret .= '&amp;'.$key.'='.$value;
		}
		return $ret;
	}

	/**
	 * @param array
	 * @return array
	 */
	public static function checkParameter(array $parameter = [])
	{
		$result = [];
		
		foreach($parameter as $name => $value)
		{
			switch($name)
			{
				case 'et_easy':
				case 'et_cust':
					$result[$name] = $value ? 1 : 0;
					break;
				
				case 'et_pagename':
				case 'et_areas':
				case 'et_target':
				case 'et_basket':
				case 'et_organisation':
				case 'et_demographic':
					$result[$name] = rawurlencode($value);
					break;
				
				case 'et_ilevel':
				case 'et_tsale':
				case 'et_lpage':
				case 'et_se':
					$result[$name] = (int) $value;
					break;
				case 'et_tval':
					$result[$name] = (float) str_replace(',', '.', $parameter[$name]);
					break;
				case 'et_trig':
					$trigger = preg_replace("/\s+/", '', $parameter[$name]);
					$result[$name] = preg_match("/^[0-9,]+$/", $trigger) ? $trigger : '';
					break;
				case 'et_url':
				case 'et_tag':
				case 'et_sub':
				case 'et_tonr':
					$result[$name] = str_replace('"', '', $parameter[$name]);
					break;
				case 'cc_attributes':
					$result[$name] = $value;
					break;
			}
		}
		return $result;
	}
}
