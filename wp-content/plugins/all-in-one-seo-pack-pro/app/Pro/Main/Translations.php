<?php
namespace AIOSEO\Plugin\Pro\Main;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use DateTime;

/**
 * Translations class.
 *
 * @since 4.0.0
 */
class Translations {
	/**
	 * Installed translations.
	 *
	 * @since 4.0.0
	 *
	 * @var array
	 */
	static private $installedTranslations;

	/**
	 * Available languages.
	 *
	 * @since 4.0.0
	 *
	 * @var array
	 */
	static private $availableLanguages;

	/**
	 * Class Constructor
	 *
	 * @param string $type   Project type. Either plugin or theme.
	 * @param string $slug   Project directory slug.
	 * @param string $apiUrl Full GlotPress API URL for the project.
	 */
	public function __construct( $type, $slug, $apiUrl ) {
		$this->type   = $type;
		$this->slug   = $slug;
		$this->apiUrl = $apiUrl;
	}

	/**
	 * Adds a new project to load translations for.
	 *
	 * @since 4.0.0
	 *
	 * @param  string $type   Project type. Either plugin or theme.
	 * @param  string $slug   Project directory slug.
	 * @param  string $apiUrl Full GlotPress API URL for the project.
	 * @return void
	 */
	public function init() {
		if ( ! has_action( 'init', [ $this, 'registerCleanTranslationsCache' ] ) ) {
			add_action( 'init', [ $this, 'registerCleanTranslationsCache' ], 9999 );
		}

		// Short-circuits translations API requests for private projects.
		add_filter(
			'translations_api',
			function ( $result, $requestedType, $args ) {
				if ( $this->type . 's' === $requestedType && $this->slug === $args['slug'] ) {
					return $this->getTranslations( $this->type, $args['slug'], $this->apiUrl );
				}

				return $result;
			},
			10,
			3
		);

		// Filters the translations transients to include the private plugin or theme. @see wp_get_translation_updates().
		add_filter(
			'site_transient_update_' . $this->type . 's',
			function ( $value ) {
				if ( ! $value ) {
					$value = new \stdClass();
				}

				if ( ! isset( $value->translations ) ) {
					$value->translations = [];
				}

				$translations = $this->getTranslations( $this->type, $this->slug, $this->apiUrl );

				if ( ! isset( $translations->{ $this->slug }['translations'] ) ) {
					return $value;
				}

				if ( null === self::$installedTranslations ) {
					self::$installedTranslations = wp_get_installed_translations( $this->type . 's' );
				}

				if ( null === self::$availableLanguages ) {
					self::$availableLanguages = get_available_languages();
				}

				foreach ( (array) $translations->{ $this->slug }['translations'] as $translation ) {
					if ( in_array( $translation['language'], self::$availableLanguages, true ) ) {
						if ( isset( self::$installedTranslations[ $this->slug ][ $translation['language'] ] ) && $translation['updated'] ) {
							$local  = new DateTime( self::$installedTranslations[ $this->slug ][ $translation['language'] ]['PO-Revision-Date'] );
							$remote = new DateTime( $translation['updated'] );

							if ( $local >= $remote ) {
								continue;
							}
						}

						$translation['type'] = $this->type;
						$translation['slug'] = $this->slug;

						$value->translations[] = $translation;
					}
				}
				return $value;
			}
		);
	}

	/**
	 * Registers actions for clearing translation caches.
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	public function registerCleanTranslationsCache() {
		$clearPluginTranslations = function() {
			$this->cleanTranslationsCache( 'plugin' );
		};

		$clearThemeTranslations = function() {
			$this->cleanTranslationsCache( 'theme' );
		};

		add_action( 'set_site_transient_update_plugins', $clearPluginTranslations );
		add_action( 'delete_site_transient_update_plugins', $clearPluginTranslations );

		add_action( 'set_site_transient_update_themes', $clearThemeTranslations );
		add_action( 'delete_site_transient_update_themes', $clearThemeTranslations );
	}

	/**
	 * Clears existing translation cache for a given type.
	 *
	 * @since 4.0.0
	 *
	 * @param  string $type Project type. Either plugin or theme.
	 * @return void
	 */
	public function cleanTranslationsCache( $type ) {
		$transientKey = '_aioseo_translations_' . $this->slug . '_' . $type;
		$translations = get_site_transient( $transientKey );

		if ( ! is_object( $translations ) ) {
			return;
		}

		/*
		 * Don't delete the cache if the transient gets changed multiple times
		 * during a single request. Set cache lifetime to maximum 15 seconds.
		 */
		$cacheLifespan  = 15;
		$timeNotChanged = isset( $translations->_last_checked ) && ( time() - $translations->_last_checked ) > $cacheLifespan;

		if ( ! $timeNotChanged ) {
			return;
		}

		delete_site_transient( $transientKey );
	}

	/**
	 * Gets the translations for a given project.
	 *
	 * @since 4.0.0
	 *
	 * @param  string $type Project type. Either plugin or theme.
	 * @param  string $slug Project directory slug.
	 * @param  string $url  Full GlotPress API URL for the project.
	 * @return array        Translation data.
	 */
	public function getTranslations( $type, $slug, $url ) {
		$transientKey = '_aioseo_translations_' . $slug . '_' . $type;
		$translations = get_site_transient( $transientKey );

		if ( false !== $translations ) {
			return $translations;
		}

		if ( ! is_object( $translations ) ) {
			$translations = new \stdClass();
		}

		if ( isset( $translations->{ $slug } ) && is_array( $translations->{ $slug } ) ) {
			return $translations->{ $slug };
		}

		$result = json_decode( wp_remote_retrieve_body( wp_remote_get( $url, [ 'timeout' => 2 ] ) ), true );
		if ( ! is_array( $result ) ) {
			$result = [];
		}

		$translations->{ $slug }     = $result;
		$translations->_last_checked = time();

		set_site_transient( $transientKey, $translations );
		return $result;
	}
}