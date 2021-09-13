<?php
namespace AIOSEO\Plugin\Pro\Social;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use AIOSEO\Plugin\Common\Social as CommonSocial;
use AIOSEO\Plugin\Pro\Models;

/**
 * Handles the Open Graph and Twitter Image.
 *
 * @since 4.0.0
 */
class Image extends CommonSocial\Image {
	/**
	 * Returns the Facebook or Twitter image.
	 *
	 * @since 4.0.0
	 *
	 * @param  string       $type        The type (Facebook or Twitter).
	 * @param  string       $imageSource The image source.
	 * @param  WP_Term      $term        The term object.
	 * @return string|array              The image data.
	 */
	public function getImage( $type, $imageSource, $term ) {
		if ( ! is_category() && ! is_tag() && ! is_tax() ) {
			return parent::getImage( $type, $imageSource, $term );
		}

		static $images = [];
		if ( isset( $images[ $type ] ) ) {
			return $images[ $type ];
		}

		switch ( $imageSource ) {
			case 'custom':
				$image = $this->getCustomFieldImage( $term, $type );
				break;
			case 'custom_image':
				$metaData = aioseo()->meta->metaData->getMetaData( $term );
				if ( empty( $metaData ) ) {
					break;
				}
				return ( 'facebook' === lcfirst( $type ) ) ? $metaData->og_image_custom_url : $metaData->twitter_image_custom_url;
			case 'default':
			default:
				$image = aioseo()->options->social->$type->general->defaultImageTerms;
		}

		if ( is_array( $image ) ) {
			$images[ $type ] = $image;
			return $images[ $type ];
		}

		$attachmentId    = aioseo()->helpers->attachmentUrlToPostId( aioseo()->helpers->removeImageDimensions( $image ) );
		$images[ $type ] = $attachmentId ? wp_get_attachment_image_src( $attachmentId, $this->thumbnailSize ) : $image;
		return $images[ $type ];
	}

	/**
	 * Returns the first available image.
	 *
	 * @since 4.0.0
	 *
	 * @param  WP_Term $term The term object.
	 * @param  string  $type The type of image (Facebook or Twitter).
	 * @return string  The image URL.
	 */
	public function getFirstAvailableImage( $term, $type ) {
		if ( ! is_category() && ! is_tag() && ! is_tax() ) {
			return parent::getFirstAvailableImage( $term, $type );
		}

		$image = $this->getCustomFieldImage( $term, $type );

		if ( ! $image && 'twitter' === lcfirst( $type ) ) {
			$image = aioseo()->options->social->twitter->homePage->image;
		}

		return $image ? $image : aioseo()->options->social->facebook->homePage->image;
	}

	/**
	 * Returns the image from a custom field.
	 *
	 * @since 4.0.0
	 *
	 * @param  WP_Term $post The term object.
	 * @param  string  $type The type of image (Facebook or Twitter).
	 * @return string        The image URL.
	 */
	public function getCustomFieldImage( $term, $type ) {
		if ( ! is_category() && ! is_tag() && ! is_tax() ) {
			return parent::getCustomFieldImage( $term, $type );
		}

		$prefix = ( 'facebook' === lcfirst( $type ) ) ? 'og_' : 'twitter_';

		$aioseoTerm   = Models\Term::getTerm( $term->term_id );
		$customFields = ! empty( $aioseoTerm->{ $prefix . 'image_custom_fields' } )
			? $aioseoTerm->{ $prefix . 'image_custom_fields' }
			: aioseo()->options->social->$type->general->customFieldImageTerms;

		if ( ! $customFields ) {
			return '';
		}

		$customFields = explode( ',', $customFields );
		foreach ( $customFields as $customField ) {
			$image = get_term_meta( $term->term_id, $customField, true );

			if ( ! empty( $image ) ) {
				return $image;
			}
		}
		return '';
	}
}