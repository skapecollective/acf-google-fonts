<?php

namespace AcfGoogleFonts\Admin;

use AcfGoogleFonts\Admin\Notice;
use AcfGoogleFonts\Contracts\StaticInitiator;

class Notices {

	use StaticInitiator;

	/**
	 * @var array Store notices to display to the user.
	 */
	public static $notices = [];

	/**
	 * Notices constructor.
	 */
	function __construct() {
		add_action( 'admin_notices', [ self::class, 'renderNotices' ] );
	}

	/**
	 * Render any admin notices.
	 *
	 * @link https://codex.wordpress.org/Plugin_API/Action_Reference/admin_notices
	 * @return void
	 */
	public static function renderNotices() {
		if ( $notices = static::getNotices() ) {
			foreach ( $notices as $notice ) {
				$notice->render();
			}
		}
	}

	/**
	 * Add notice data.
	 *
	 * @param string $text
	 * @param array $args
	 * @return Notice
	 */
	public static function addNotice( string $text, array $args = [] ) {
		$notice = new Notice( $text, $args );
		static::$notices[] = $notice;
		return $notice;
	}

	/**
	 * Add success notice.
	 *
	 * @param string $text
	 * @param array	$args
	 * @return Notice
	 */
	public static function addSuccessNotice( string $text, array $args = [] ) {
		$args[ 'type' ] = 'success';
		return static::addNotice( $text, $args );
	}

	/**
	 * Add warning notice.
	 *
	 * @param string $text
	 * @param array	$args
	 * @return Notice
	 */
	public static function addWarningNotice( string $text, array $args = [] ) {
		$args[ 'type' ] = 'warning';
		return static::addNotice( $text, $args );
	}

	/**
	 * Add error notice.
	 *
	 * @param string $text
	 * @param array	$args
	 * @return Notice
	 */
	public static function addErrorNotice( string $text, array $args = [] ) {
		$args[ 'type' ] = 'error';
		return static::addNotice( $text, $args );
	}

	/**
	 * Return an array of registered notices.
	 *
	 * @return array
	 */
	public static function getNotices() {
		return static::$notices;
	}
}
