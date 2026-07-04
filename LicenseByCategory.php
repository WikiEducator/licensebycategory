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

class LicenseByCategoryHooks {

	private const LICENSE_ICONS = array(
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

	private const LICENSE_TEXT = array(
		'CC-BY' => 'Content is available under the <a rel="license" href="http://creativecommons.org/licenses/by/3.0" class="external" title="http://creativecommons.org/licenses/by/3.0/">Creative Commons Attribution License</a>.',
		'CC0' => 'Content is available under a <a rel="license" href="http://creativecommons.org/publicdomain/zero/1.0/" class="external" title="http://creativecommons.org/publicdomain/zero/1.0/">CC0 Public Domain Dedication</a>.',
		'PD' => 'Content has been released under a <a rel="license" href="http://WikiEducator.org/WikiEducator:Public_Domain" class="external" title="http://WikiEducator.org/WikiEducator.org/Public_Domain">Public Domain dedication</a>.',
	);

	public static function onOutputPageMakeCategoryLinks( $out, $categories, &$links ) {
		$weLicense = 'CC-BY-SA';
		if ( array_key_exists( 'CC0', $categories ) || array_key_exists( 'CC-0', $categories ) ) {
			$weLicense = 'CC0';
		} elseif ( array_key_exists( 'Public_Domain', $categories ) ) {
			$weLicense = 'PD';
		} else {
			foreach ( $categories as $cat => $type ) {
				if ( preg_match( "/^cc-by((_pages)|(-[0-9.]+))?$/i",
						$cat ) ) {
					$weLicense = 'CC-BY';
					break;
				}
			}
		}

		if ( !array_key_exists( $weLicense, self::LICENSE_ICONS ) ) {
			return true;
		}

		# Remember which license applies to this output, for onSkinCopyrightFooter
		$out->setProperty( 'weLicense', $weLicense );

		$icon = self::LICENSE_ICONS[$weLicense];

		# Modern replacement for rewriting <link rel="copyright"> in the page head
		$out->setCopyrightUrl( $icon['url'] );

		# There is no hook for the footer copyright icon; $wgFooterIcons is read
		# fresh from config each request, so updating it here is picked up when
		# the skin builds the footer.
		global $wgFooterIcons;
		$wgFooterIcons['copyright']['copyright'] = array(
			'url' => $icon['url'],
			'src' => $icon['src'],
			'alt' => $icon['alt'],
		);

		return true;
	}

	public static function onSkinCopyrightFooter( $title, $type, &$msg, &$link ) {
		$weLicense = RequestContext::getMain()->getOutput()->getProperty( 'weLicense' );
		if ( $weLicense !== null && array_key_exists( $weLicense, self::LICENSE_TEXT ) ) {
			$msg = 'licensebycategory-copyright';
			$link = self::LICENSE_TEXT[$weLicense];
		}
		return true;
	}
}
