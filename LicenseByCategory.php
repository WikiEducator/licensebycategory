<?php
# ex: tabstop=8 shiftwidth=8 noexpandtab
/**
* @package MediaWiki
* @subpackage LicenseByCategory
* @author Jim Tittsler <jim@OERfoundation.org>
* @licence GPL2
*/

if( !defined( 'MEDIAWIKI' ) ) {
	die( "This file is an extension to the MediaWiki software and cannot be used standalone.\n" );
}

$wgExtensionCredits['parserhook'][] = array(
	'name'           => 'LicenseByCategory',
	'version'        => '1.0',
	'url'            => 'http://WikiEducator.org/Extension:MultiLicense',
	'author'         => '[http://WikiEducator.org/User:JimTittsler Jim Tittsler]',
        'description'    => 'Change header(link) and footer license based on category',
);

$wgHooks['OutputPageMakeCategoryLinks'][] = 'weCategoryLinks';
$wgHooks['SkinTemplateOutputPageBeforeExec'][] = 'weMultiLicense';
$wgHooks['BeforePageDisplay'][] = 'weMultiLicenseHeader';

function weCategoryLinks( &$out, $categories, &$links ) {
	global $weLicense;
	$weLicense = 'CC-BY-SA';
	if ( array_key_exists( 'CC-BY', $categories ) ) {
		$weLicense = 'CC-BY';
	} elseif ( array_key_exists('CC0', $categories ) ) {
		$weLicense = 'CC0';
	} elseif ( array_key_exists( 'Public_Domain', $categories ) ) {
		$weLicense = 'PD';
	}
	return true;
}

function weMultiLicense( &$templateEngine, &$template ) {
	global $weLicense;
	$weCopyrights = array(
		'CC-BY' => 'Content is available under <a rel="license" href="http://creativecommons.org/licenses/by/3.0" class="external" title="http://creativecommons.org/licenses/by/3.0/">Creative Commons Attribution License</a>.',
		'CC0' => 'Content is available under a <a rel="license" href="http://creativecommons.org/publicdomain/zero/1.0/" class="external" title="http://creativecommons.org/publicdomain/zero/1.0/">CC0 Public Domain Dedication</a>.',
		'PD' => 'Content has been <a rel="license" href="http://WikiEducator.org/WikiEducator:Public_Domain" class="external" title="http://WikiEducator.org/WikiEducator.org/Public_Domain">Public Domain Dedication</a>.'
	);
	$weCopyrightIcons = array(
		'CC-BY' => '<a rel="license" href="http://creativecommons.org/licenses/by/3.0/"><img src="/skins/common/images/cc-by.png"></a>',
		'CC0' => '<a rel="license" href="http://creativecommons.org/publicdomain/zero/1.0/"><img src="/skins/common/images/cc0.png"></a>',
		'PD' => '<a rel="license" href="http://WikiEducator.org/WikiEducator:Public_Domain"><img src="/skins/common/images/pd.png"></a>'
	);
	if (array_key_exists($weLicense, $weCopyrights)) {
		$template->set('copyright', $weCopyrights[$weLicense]);
	}
	if (array_key_exists($weLicense, $weCopyrightIcons)) {
		$template->set('copyrightico', $weCopyrightIcons[$weLicense]);
	}
	return true;
}

function weMultiLicenseHeader( &$out, &$sk ) {
	global $weLicense;
	$weCopyrightURL = array(
		'CC-BY' => 'http://creativecommons.org/licenses/by/3.0/',
		'CC0' => 'http://creative.commons.org/publicdomain/zero/1.0/',
		'PD' => 'http://creative.commons.org/publicdomain/zero/1.0/'
	);
	if (array_key_exists($weLicense, $weCopyrightURL)) {
		$out->addLink( array(
			'rel' => 'copyright',
			'href' => $weCopyrightURL[$weLicense]
		) );
	}
	return true;
}
