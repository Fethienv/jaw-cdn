<?php
namespace AIOSEO\Plugin\Pro\Options;

use AIOSEO\Plugin\Common\Options as CommonOptions;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class DynamicBackup extends CommonOptions\DynamicBackup {
	/**
	 * Checks whether data from the backup has to be restored.
	 *
	 * @since 4.1.3
	 *
	 * @return void
	 */
	public function init() {
		parent::init();

		$this->restoreRoles();
	}

	/**
	 * Restores the dynamic Roles options.
	 *
	 * @since 4.1.3
	 *
	 * @return void
	 */
	private function restoreRoles() {
		$customRoles = aioseo()->helpers->getCustomRoles();
		foreach ( $customRoles as $customRoleName => $customRoleLabel ) {
			if ( ! empty( $this->backup['roles'][ $customRoleName ] ) ) {
				$this->restoreOptions( $this->backup['roles'][ $customRoleName ], [ 'accessControl', $customRoleName ] );
				unset( $this->backup['roles'][ $customRoleName ] );
				$this->shouldBackup = true;
			}
		}
	}

	/**
	 * Maybe backup the options if it has disappeared.
	 *
	 * @since 4.1.3
	 *
	 * @param  array $newOptions An array of options to check.
	 * @return void
	 */
	public function maybeBackup( $newOptions ) {
		$this->maybeBackupRoles( $newOptions['accessControl'] );
		$this->maybeBackupTaxonomy( $newOptions['searchAppearance']['taxonomies'], $newOptions['social']['facebook']['general']['taxonomies'] );

		parent::maybeBackup( $newOptions );
	}

	/**
	 * Maybe backup the roles.
	 *
	 * @since 4.1.3
	 *
	 * @param  array $dynamicRoles An array of dynamic roles to check.
	 * @return void
	 */
	private function maybeBackupRoles( $dynamicRoles ) {
		$customRoles = aioseo()->helpers->getCustomRoles();

		// Remove the skipped roles.
		$roles = apply_filters( 'aioseo_access_control_excluded_roles', array_merge( [
			'subscriber'
		], aioseo()->helpers->isWooCommerceActive() ? [ 'customer' ] : [] ) );

		foreach ( $roles as $role ) {
			if ( array_key_exists( $role, $dynamicRoles ) ) {
				unset( $dynamicRoles[ $role ] );
			}
		}

		$missing = [];
		foreach ( $dynamicRoles as $role => $data ) {
			if ( empty( $customRoles[ $role ] ) ) {
				$missing[ $role ] = $data;
			}
		}

		foreach ( $missing as $roleName => $roleSettings ) {
			$this->backup['roles'][ $roleName ] = aioseo()->dynamicOptions->convertOptionsToValues( $roleSettings, 'value' );
			$this->shouldBackup = true;
		}
	}

	/**
	 * Maybe backup the Taxonomies.
	 *
	 * @since 4.1.4
	 *
	 * @param  array $dynamicTaxonomies   An array of dynamic taxonomy from Search Appearance to check.
	 * @param  array $dynamicTaxonomiesOG An array of dynamic taxonomy from Social Facebook to check.
	 * @return void
	 */
	protected function maybeBackupTaxonomy( $dynamicTaxonomies, $dynamicTaxonomiesOG = [] ) {
		parent::maybeBackupTaxonomy( $dynamicTaxonomies, [] );

		$taxonomies = aioseo()->helpers->getPublicTaxonomies();
		$taxonomies = $this->normalizeObjectName( $taxonomies );
		foreach ( $dynamicTaxonomiesOG as $dynamicTaxonomyNameOG => $dynamicTaxonomySettingsOG ) {
			$found = wp_list_filter( $taxonomies, [ 'name' => $dynamicTaxonomyNameOG ] );
			if ( count( $found ) === 0 ) {
				$this->backup['taxonomies'][ $dynamicTaxonomyNameOG ]['social']['facebook'] = $dynamicTaxonomySettingsOG;
				$this->shouldBackup = true;
			}
		}
	}
}