<?php

namespace AcfGoogleFonts\Admin;

use AcfGoogleFonts\Admin\View;
use AcfGoogleFonts\Contracts\Prefixer;

/**
 * @method setSlug(string $slug)
 * @method setTitle(string $title)
 * @method setParent(string $parent)
 * @method setPosition(int $parent)
 * @method setIcon(string $icon)
 * @method setCapabilites(string $capabilities)
 * @method setView(View $param)
 */
class Page {

	use Prefixer;

	public $slug = null;

	public $title = null;

	public $parent = null;

	public $position = 99;

	public $icon = 'dashicons-admin-generic';

	public $capabilities = 'manage_options';

	public $view;

	/**
	 * @var int The `admin_menu` hook priority.
	 */
	public $priority = 20;

	/**
	 * Page constructor.
	 *
	 * @param string $title
	 * @param string|null $icon
	 * @param string|null $parent
	 */
	public function __construct( string $title, ?string $icon = 'dashicons-admin-generic', ?string $parent = null ) {
		$this->title = $title;
		$this->icon = $icon;
		$this->parent = $parent;
	}

	/**
	 * Magic method override properties
	 *
	 * @param  string $name
	 * @param  array $arguments
	 * @return void
	 */
	public function __call( $name, $arguments ) {
		if ( preg_match( '/^set/', $name ) ) {
			$name = lcfirst( preg_replace( '/^set/', '', $name ) );
			if ( property_exists( $this, $name ) ) {
				$this->$name = array_shift( $arguments );
			}
		}
	}

	/**
	 * Register everything with WordPress.
	 *
	 * @return void
	 */
	public function register() {
		add_action( 'admin_menu', [ $this, 'adminMenu' ], $this->priority );
	}

	/**
	 * @return string Get the prefixed page slug.
	 */
	public function getSlug() {
		return self::prefix( $this->slug ?: $this->title, 'sanitize_title' );
	}

	/**
	 * Get full admin URL.
	 *
	 * @return string
	 */
	public function getUrl() {
		return add_query_arg( 'page', $this->getSlug(), admin_url( $this->parent ?: 'admin.php' ) );
	}

	/**
	 * Get the page title.
	 *
	 * @return string
	 */
	public function getTitle() {
		return $this->title;
	}

	/**
	 *
	 * @link  https://developer.wordpress.org/reference/hooks/admin_menu/
	 * @link  https://developer.wordpress.org/reference/functions/add_submenu_page/
	 * @link  https://developer.wordpress.org/reference/functions/add_menu_page/
	 *
	 * @param  string $context
	 * @return void
	 */
	public function adminMenu( $context ) {

		$title = $this->getTitle();
		$slug = $this->getSlug();

		if ( $this->parent ) {
			add_submenu_page( $this->parent, $title, $title, $this->capabilities, $slug, [ $this, 'renderAdmin' ], $this->position );
		} else {
			add_menu_page( $title, $title, $this->capabilities, $slug, [ $this, 'renderAdmin' ], $this->icon, $this->position );
		}
	}

	/**
	 * Render admin page.
	 *
	 * @return void
	 */
	public function renderAdmin() {
		if ( $this->view instanceof View ) {
			$this->view->render();
		}
	}


}
