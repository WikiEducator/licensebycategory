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
	'path'           => __FILE__,
	'name'           => 'LicenseByCategory',
	'version'        => '1.1.3',
	'url'            => 'http://WikiEducator.org/Extension:LicenseByCategory',
	'author'         => '[http://WikiEducator.org/User:JimTittsler Jim Tittsler]',
	'description'    => 'Change header(link) and footer license based on category',
	'license'	 => 'GPL-2.0'
);

$wgHooks['OutputPageMakeCategoryLinks'][] = 'weCategoryLinks';
$wgHooks['SkinTemplateOutputPageBeforeExec'][] = 'weLicenseByCategory';

function weCategoryLinks( &$out, $categories, &$links ) {
	global $weLicense;
	$weLicense = 'CC-BY-SA';
	if ( array_key_exists( 'CC0', $categories ) || array_key_exists( 'CC-0', $categories ) ) {
		$weLicense = 'CC0';
	} elseif ( array_key_exists( 'Public_Domain', $categories ) ) {
		$weLicense = 'PD';
	} else {
		foreach ( $categories as $cat => $v ) {
			if ( preg_match( "/^cc-by((_pages)|(-[0-9.]+))?$/i",
					$cat ) ) {
				$weLicense = 'CC-BY';
				break;
			}
		}
	}
	return true;
}

function weLicenseByCategory ( &$templateEngine, &$tpl ) {
	global $weLicense;
	$weCopyrights = array(
		'CC-BY' => 'Content is available under the <a rel="license" href="http://creativecommons.org/licenses/by/3.0" class="external" title="http://creativecommons.org/licenses/by/3.0/">Creative Commons Attribution License</a>.',
		'CC0' => 'Content is available under a <a rel="license" href="http://creativecommons.org/publicdomain/zero/1.0/" class="external" title="http://creativecommons.org/publicdomain/zero/1.0/">CC0 Public Domain Dedication</a>.',
		'PD' => 'Content has been released under a <a rel="license" href="http://WikiEducator.org/WikiEducator:Public_Domain" class="external" title="http://WikiEducator.org/WikiEducator.org/Public_Domain">Public Domain dedication</a>.'
	);
	$weCopyrightIcons = array(
		'CC-BY' => array(
			'url' => 'http://creativecommons.org/licenses/by/3.0/',
			'src' => '/extensions/LicenseByCategory/icons/cc-by.png',
			'alt' => 'Creative Commons Attribution (CC-BY)',
		),
		'CC0' => array(
			'url' => 'http://creativecommons.org/publicdomain/zero/1.0/',
			'src' => '/extensions/LicenseByCategory/icons/cc0.png',
			'alt' => 'CC0 Public Domain Dedication',
		),
		'PD' => array(
			'url' => 'http://WikiEducator.org/WikiEducator:Public_Domain',
			'src' => '/extensions/LicenseByCategory/icons/pd.png',
			'alt' => 'Public Domain dedication',
		),
	);

	if ( array_key_exists( $weLicense, $weCopyrightIcons ) ) {
		$tpl->set( 'copyright', $weCopyrights[$weLicense] );

		# replace both the old and new forms of the copyright icon
		$i = $weCopyrightIcons[$weLicense];
		$tpl->set( 'copyrightico',
		       "<a href=\"{$i['url']}\"><img src=\"{$i['src']}\" alt=\"{$i['alt']}\" /></a>" );
		$fi = $tpl->data['footericons'];
		$fi['copyright']['copyright'] = $weCopyrightIcons[$weLicense];
		$tpl->setRef( 'footericons', $fi );

		# replace the old copyright link from the head
		$he = $tpl->data['headelement'];
		$he = preg_replace( '/(<link rel="copyright" href=")[^"]+/',
			'$1'.$weCopyrightIcons[$weLicense]['url'], $he );
		$tpl->setRef( 'headelement', $he );
	}
	return true;
}
