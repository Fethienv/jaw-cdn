<?php
namespace AIOSEO\Plugin\Pro\Migration;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use AIOSEO\Plugin\Common\Models;

// phpcs:disable WordPress.Arrays.ArrayDeclarationSpacing.AssociativeArrayFound

/**
 * Migrates the Local Business SEO settings from V3.
 *
 * @since 4.0.0
 */
class LocalBusiness {
	/**
	 * The old V3 options.
	 *
	 * @since 4.0.0
	 *
	 * @var array
	 */
	protected $oldOptions = [];

	/**
	 * Class constructor.
	 *
	 * @since 4.0.0
	 */
	public function __construct() {
		$this->oldOptions = aioseo()->migration->oldOptions;

		if ( empty( $this->oldOptions['modules']['aiosp_schema_local_business_options'] ) ) {
			return;
		}

		$this->migrateLocalBusinessAddress();
		$this->migrateLocalBusinessPhoneNumber();
		$this->migrateLocalBusinessOpeningHours();
		$this->migrateLocalBusinessPriceRange();

		$settings = [
			'aiosp_schema_local_business_aioseo_business_name'  => [ 'type' => 'string', 'newOption' => [ 'localBusiness', 'locations', 'business', 'name' ] ],
			'aiosp_schema_local_business_aioseo_business_type'  => [ 'type' => 'string', 'newOption' => [ 'localBusiness', 'locations', 'business', 'businessType' ] ],
			'aiosp_schema_local_business_aioseo_business_image' => [ 'type' => 'string', 'newOption' => [ 'localBusiness', 'locations', 'business', 'image' ] ],
		];

		aioseo()->migration->helpers->mapOldToNew( $settings, $this->oldOptions['modules']['aiosp_schema_local_business_options'] );
	}

	/**
	 * Migrates the Local Business address.
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	private function migrateLocalBusinessAddress() {
		if ( empty( $this->oldOptions['modules']['aiosp_schema_local_business_options']['aiosp_schema_local_business_aioseo_postal_address'] ) ) {
			return;
		}

		$settings = [
			'street_address'   => [ 'type' => 'string', 'newOption' => [ 'localBusiness', 'locations', 'business', 'address', 'streetLine1' ] ],
			'address_locality' => [ 'type' => 'string', 'newOption' => [ 'localBusiness', 'locations', 'business', 'address', 'city' ] ],
			'address_region'   => [ 'type' => 'string', 'newOption' => [ 'localBusiness', 'locations', 'business', 'address', 'state' ] ],
			'postal_code'      => [ 'type' => 'string', 'newOption' => [ 'localBusiness', 'locations', 'business', 'address', 'zipCode' ] ],
		];

		aioseo()->migration->helpers->mapOldToNew( $settings, $this->oldOptions['modules']['aiosp_schema_local_business_options']['aiosp_schema_local_business_aioseo_postal_address'] );

		if ( ! empty( $this->oldOptions['modules']['aiosp_schema_local_business_options']['aiosp_schema_local_business_aioseo_postal_address']['address_country'] ) ) {
			$country = $this->oldOptions['modules']['aiosp_schema_local_business_options']['aiosp_schema_local_business_aioseo_postal_address']['address_country'];

			foreach ( self::getSupportedCountries() as $value => $label ) {
				if ( $country === $label ) {
					aioseo()->options->localBusiness->locations->business->address->country = $value;
					return;
				}
			}

			$notification = Models\Notification::getNotificationByName( 'v3-migration-local-business-country' );
			if ( $notification->notification_name ) {
				return;
			}

			Models\Notification::addNotification( [
				'slug'              => uniqid(),
				'notification_name' => 'v3-migration-local-business-country',
				'title'             => __( 'Re-Enter Country in Local Business', 'all-in-one-seo-pack' ),
				'content'           => sprintf(
					// Translators: 1 - The country.
					__( 'For technical reasons, we were unable to migrate the country you entered for your Local Business schema markup. Please enter it (%1$s) again by using the dropdown menu.', 'all-in-one-seo-pack' ), // phpcs:ignore Generic.Files.LineLength.MaxExceeded
					"<strong>$country</strong>"
				),
				'type'              => 'warning',
				'level'             => [ 'all' ],
				'button1_label'     => __( 'Fix Now', 'all-in-one-seo-pack' ),
				'button1_action'    => 'http://route#aioseo-local-seo&aioseo-scroll=info-business-address-row&aioseo-highlight=info-business-address-row:locations',
				'button2_label'     => __( 'Remind Me Later', 'all-in-one-seo-pack' ),
				'button2_action'    => 'http://action#notification/v3-migration-local-business-country-reminder',
				'start'             => gmdate( 'Y-m-d H:i:s' )
			] );
		}
	}

	/**
	 * Migrates the Local Business phone number.
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	private function migrateLocalBusinessPhoneNumber() {
		if ( empty( $this->oldOptions['modules']['aiosp_schema_local_business_options']['aiosp_schema_local_business_aioseo_telephone'] ) ) {
			return;
		}

		$phoneNumber = $this->oldOptions['modules']['aiosp_schema_local_business_options']['aiosp_schema_local_business_aioseo_telephone'];
		if ( ! preg_match( '#\+\d+#', $phoneNumber ) ) {
			$notification = Models\Notification::getNotificationByName( 'v3-migration-local-business-number' );
			if ( $notification->notification_name ) {
				return;
			}

			Models\Notification::addNotification( [
				'slug'              => uniqid(),
				'notification_name' => 'v3-migration-local-business-number',
				'title'             => __( 'Invalid Phone Number for Local SEO', 'all-in-one-seo-pack' ),
				'content'           => sprintf(
					// Translators: 1 - The phone number.
					__( 'The phone number that you previously entered for your Local Business schema markup is invalid. As it needs to be internationally formatted, please enter it (%1$s) again with the country code, e.g. +1 (555) 555-1234.', 'all-in-one-seo-pack' ), // phpcs:ignore Generic.Files.LineLength.MaxExceeded
					"<strong>$phoneNumber</strong>"
				),
				'type'              => 'warning',
				'level'             => [ 'all' ],
				'button1_label'     => __( 'Fix Now', 'all-in-one-seo-pack' ),
				'button1_action'    => 'http://route#aioseo-local-seo&aioseo-scroll=info-business-contact-row&aioseo-highlight=info-business-contact-row:global-settings',
				'button2_label'     => __( 'Remind Me Later', 'all-in-one-seo-pack' ),
				'button2_action'    => 'http://action#notification/v3-migration-local-business-number-reminder',
				'start'             => gmdate( 'Y-m-d H:i:s' )
			] );
			return;
		}
		aioseo()->options->localBusiness->locations->business->contact->phone = $phoneNumber;
	}

	/**
	 * Migrates the Local Business opening hours.
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	private function migrateLocalBusinessOpeningHours() {
		if ( empty( $this->oldOptions['modules']['aiosp_schema_local_business_options']['aiosp_schema_local_business_aioseo_time_0_opening_days'] ) ) {
			return;
		}

		$openedDays = array_map( function( $day ) {
			return lcfirst( $day );
		}, $this->oldOptions['modules']['aiosp_schema_local_business_options']['aiosp_schema_local_business_aioseo_time_0_opening_days'] );

		$days = aioseo()->options->localBusiness->openingHours->days->all();
		foreach ( $days as $day => $values ) {
			if ( ! in_array( $day, $openedDays, true ) ) {
				aioseo()->options->localBusiness->openingHours->days->$day->closed = true;
			}

			aioseo()->options->localBusiness->openingHours->days->$day->openTime  = $this->oldOptions['modules']['aiosp_schema_local_business_options']['aiosp_schema_local_business_aioseo_time_0_opens']; // phpcs:ignore Generic.Files.LineLength.MaxExceeded
			aioseo()->options->localBusiness->openingHours->days->$day->closeTime = $this->oldOptions['modules']['aiosp_schema_local_business_options']['aiosp_schema_local_business_aioseo_time_0_closes']; // phpcs:ignore Generic.Files.LineLength.MaxExceeded
		}
	}

	/**
	 * Migrates the Local Business price range.
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	private function migrateLocalBusinessPriceRange() {
		if ( empty( $this->oldOptions['modules']['aiosp_schema_local_business_options']['aiosp_schema_local_business_aioseo_price_range'] ) ) {
			return;
		}

		$value = intval( $this->oldOptions['modules']['aiosp_schema_local_business_options']['aiosp_schema_local_business_aioseo_price_range'] );
		if ( ! $value ) {
			return;
		}

		$priceRange = '';
		for ( $i = 1; $i <= $value; $i++ ) {
			$priceRange .= '$';
		}
		aioseo()->options->localBusiness->locations->business->payment->priceRange = $priceRange;
	}

	/**
	 * Returns the countries from our dropdown in V4.
	 *
	 * @since 4.0.0
	 *
	 * @return array The list of supported countries.
	 */
	public static function getSupportedCountries() {
		return [
			'AF' => 'Afghanistan',
			'AL' => 'Albania',
			'DZ' => 'Algeria',
			'AS' => 'American Samoa',
			'AD' => 'Andorra',
			'AO' => 'Angola',
			'AI' => 'Anguilla',
			'AQ' => 'Antarctica',
			'AG' => 'Antigua and Barbuda',
			'AR' => 'Argentina',
			'AM' => 'Armenia',
			'AW' => 'Aruba',
			'AU' => 'Australia',
			'AT' => 'Austria',
			'AZ' => 'Azerbaijan',
			'BS' => 'Bahamas',
			'BH' => 'Bahrain',
			'BD' => 'Bangladesh',
			'BB' => 'Barbados',
			'BY' => 'Belarus',
			'BE' => 'Belgium',
			'BZ' => 'Belize',
			'BJ' => 'Benin',
			'BM' => 'Bermuda',
			'BT' => 'Bhutan',
			'BO' => 'Bolivia',
			'BQ' => 'Bonaire',
			'BA' => 'Bosnia and Herzegovina',
			'BW' => 'Botswana',
			'BV' => 'Bouvet Island',
			'BR' => 'Brazil',
			'IO' => 'British Indian Ocean Territory',
			'BN' => 'Brunei Darussalam',
			'BG' => 'Bulgaria',
			'BF' => 'Burkina Faso',
			'BI' => 'Burundi',
			'CV' => 'Cabo Verde',
			'KH' => 'Cambodia',
			'CM' => 'Cameroon',
			'CA' => 'Canada',
			'KY' => 'Cayman Islands',
			'CF' => 'Central African Republic',
			'TD' => 'Chad',
			'CL' => 'Chile',
			'CN' => 'China',
			'CX' => 'Christmas Island',
			'CC' => 'Cocos (Keeling) Islands',
			'CO' => 'Colombia',
			'KM' => 'Comoros',
			'CD' => 'Democratic Republic of the Congo',
			'CG' => 'Congo',
			'CK' => 'Cook Islands',
			'CR' => 'Costa Rica',
			'HR' => 'Croatia',
			'CU' => 'Cuba',
			'CW' => 'Curaçao',
			'CY' => 'Cyprus',
			'CZ' => 'Czechia',
			'CI' => 'Côte d\'Ivoire',
			'DK' => 'Denmark',
			'DJ' => 'Djibouti',
			'DM' => 'Dominica',
			'DO' => 'Dominican Republic',
			'EC' => 'Ecuador',
			'EG' => 'Egypt',
			'SV' => 'El Salvador',
			'GQ' => 'Equatorial Guinea',
			'ER' => 'Eritrea',
			'EE' => 'Estonia',
			'SZ' => 'Eswatini',
			'ET' => 'Ethiopia',
			'FK' => 'Falkland Islands',
			'FO' => 'Faroe Islands',
			'FJ' => 'Fiji',
			'FI' => 'Finland',
			'FR' => 'France',
			'GF' => 'French Guiana',
			'PF' => 'French Polynesia',
			'TF' => 'French Southern Territories',
			'GA' => 'Gabon',
			'GM' => 'Gambia',
			'GE' => 'Georgia',
			'DE' => 'Germany',
			'GH' => 'Ghana',
			'GI' => 'Gibraltar',
			'GR' => 'Greece',
			'GL' => 'Greenland',
			'GD' => 'Grenada',
			'GP' => 'Guadeloupe',
			'GU' => 'Guam',
			'GT' => 'Guatemala',
			'GG' => 'Guernsey',
			'GN' => 'Guinea',
			'GW' => 'Guinea-Bissau',
			'GY' => 'Guyana',
			'HT' => 'Haiti',
			'HM' => 'Heard Island and McDonald Islands',
			'VA' => 'Holy See',
			'HN' => 'Honduras',
			'HK' => 'Hong Kong',
			'HU' => 'Hungary',
			'IS' => 'Iceland',
			'IN' => 'India',
			'ID' => 'Indonesia',
			'IR' => 'Iran',
			'IQ' => 'Iraq',
			'IE' => 'Ireland',
			'IM' => 'Isle of Man',
			'IL' => 'Israel',
			'IT' => 'Italy',
			'JM' => 'Jamaica',
			'JP' => 'Japan',
			'JE' => 'Jersey',
			'JO' => 'Jordan',
			'KZ' => 'Kazakhstan',
			'KE' => 'Kenya',
			'KI' => 'Kiribati',
			'KP' => 'South Korea',
			'KR' => 'North Korea',
			'KW' => 'Kuwait',
			'KG' => 'Kyrgyzstan',
			'LA' => 'Lao People\'s Democratic Republic',
			'LV' => 'Latvia',
			'LB' => 'Lebanon',
			'LS' => 'Lesotho',
			'LR' => 'Liberia',
			'LY' => 'Libya',
			'LI' => 'Liechtenstein',
			'LT' => 'Lithuania',
			'LU' => 'Luxembourg',
			'MO' => 'Macao',
			'MG' => 'Madagascar',
			'MW' => 'Malawi',
			'MY' => 'Malaysia',
			'MV' => 'Maldives',
			'ML' => 'Mali',
			'MT' => 'Malta',
			'MH' => 'Marshall Islands',
			'MQ' => 'Martinique',
			'MR' => 'Mauritania',
			'MU' => 'Mauritius',
			'YT' => 'Mayotte',
			'MX' => 'Mexico',
			'FM' => 'Micronesia',
			'MD' => 'Moldova',
			'MC' => 'Monaco',
			'MN' => 'Mongolia',
			'ME' => 'Montenegro',
			'MS' => 'Montserrat',
			'MA' => 'Morocco',
			'MZ' => 'Mozambique',
			'MM' => 'Myanmar',
			'NA' => 'Namibia',
			'NR' => 'Nauru',
			'NP' => 'Nepal',
			'NL' => 'Netherlands',
			'NC' => 'New Caledonia',
			'NZ' => 'New Zealand',
			'NI' => 'Nicaragua',
			'NE' => 'Niger',
			'NG' => 'Nigeria',
			'NU' => 'Niue',
			'NF' => 'Norfolk Island',
			'MP' => 'Northern Mariana Islands',
			'NO' => 'Norway',
			'OM' => 'Oman',
			'PK' => 'Pakistan',
			'PW' => 'Palau',
			'PS' => 'Palestine, State of',
			'PA' => 'Panama',
			'PG' => 'Papua New Guinea',
			'PY' => 'Paraguay',
			'PE' => 'Peru',
			'PH' => 'Philippines',
			'PN' => 'Pitcairn',
			'PL' => 'Poland',
			'PT' => 'Portugal',
			'PR' => 'Puerto Rico',
			'QA' => 'Qatar',
			'MK' => 'Republic of North Macedonia',
			'RO' => 'Romania',
			'RU' => 'Russian Federation',
			'RW' => 'Rwanda',
			'RE' => 'Réunion',
			'BL' => 'Saint Barthélemy',
			'SH' => 'Saint Helena, Ascension and Tristan da Cunha',
			'KN' => 'Saint Kitts and Nevis',
			'LC' => 'Saint Lucia',
			'MF' => 'Saint Martin',
			'PM' => 'Saint Pierre and Miquelon',
			'VC' => 'Saint Vincent and the Grenadines',
			'WS' => 'Samoa',
			'SM' => 'San Marino',
			'ST' => 'Sao Tome and Principe',
			'SA' => 'Saudi Arabia',
			'SN' => 'Senegal',
			'RS' => 'Serbia',
			'SC' => 'Seychelles',
			'SL' => 'Sierra Leone',
			'SG' => 'Singapore',
			'SX' => 'Sint Maarten',
			'SK' => 'Slovakia',
			'SI' => 'Slovenia',
			'SB' => 'Solomon Islands',
			'SO' => 'Somalia',
			'ZA' => 'South Africa',
			'GS' => 'South Georgia and the South Sandwich Islands',
			'SS' => 'South Sudan',
			'ES' => 'Spain',
			'LK' => 'Sri Lanka',
			'SD' => 'Sudan',
			'SR' => 'Suriname',
			'SJ' => 'Svalbard and Jan Mayen',
			'SE' => 'Sweden',
			'CH' => 'Switzerland',
			'SY' => 'Syrian Arab Republic',
			'TW' => 'Taiwan',
			'TJ' => 'Tajikistan',
			'TZ' => 'Tanzania, United Republic of',
			'TH' => 'Thailand',
			'TL' => 'Timor-Leste',
			'TG' => 'Togo',
			'TK' => 'Tokelau',
			'TO' => 'Tonga',
			'TT' => 'Trinidad and Tobago',
			'TN' => 'Tunisia',
			'TR' => 'Turkey',
			'TM' => 'Turkmenistan',
			'TC' => 'Turks and Caicos Islands',
			'TV' => 'Tuvalu',
			'UG' => 'Uganda',
			'UA' => 'Ukraine',
			'AE' => 'United Arab Emirates',
			'GB' => 'United Kingdom of Great Britain and Northern Ireland',
			'UM' => 'United States Minor Outlying Islands',
			'US' => 'United States of America',
			'UY' => 'Uruguay',
			'UZ' => 'Uzbekistan',
			'VU' => 'Vanuatu',
			'VE' => 'Venezuela',
			'VN' => 'Vietnam',
			'VG' => 'Virgin Islands (British)',
			'VI' => 'Virgin Islands (U.S.)',
			'WF' => 'Wallis and Futuna',
			'EH' => 'Western Sahara',
			'YE' => 'Yemen',
			'ZM' => 'Zambia',
			'ZW' => 'Zimbabwe',
			'AX' => 'Åland Islands',
			'AF' => 'AF',
			'AL' => 'AL',
			'DZ' => 'DZ',
			'AS' => 'AS',
			'AD' => 'AD',
			'AO' => 'AO',
			'AI' => 'AI',
			'AQ' => 'AQ',
			'AG' => 'AG',
			'AR' => 'AR',
			'AM' => 'AM',
			'AW' => 'AW',
			'AU' => 'AU',
			'AT' => 'AT',
			'AZ' => 'AZ',
			'BS' => 'BS',
			'BH' => 'BH',
			'BD' => 'BD',
			'BB' => 'BB',
			'BY' => 'BY',
			'BE' => 'BE',
			'BZ' => 'BZ',
			'BJ' => 'BJ',
			'BM' => 'BM',
			'BT' => 'BT',
			'BO' => 'BO',
			'BQ' => 'BQ',
			'BA' => 'BA',
			'BW' => 'BW',
			'BV' => 'BV',
			'BR' => 'BR',
			'IO' => 'IO',
			'BN' => 'BN',
			'BG' => 'BG',
			'BF' => 'BF',
			'BI' => 'BI',
			'CV' => 'CV',
			'KH' => 'KH',
			'CM' => 'CM',
			'CA' => 'CA',
			'KY' => 'KY',
			'CF' => 'CF',
			'TD' => 'TD',
			'CL' => 'CL',
			'CN' => 'CN',
			'CX' => 'CX',
			'CC' => 'CC',
			'CO' => 'CO',
			'KM' => 'KM',
			'CD' => 'CD',
			'CG' => 'CG',
			'CK' => 'CK',
			'CR' => 'CR',
			'HR' => 'HR',
			'CU' => 'CU',
			'CW' => 'CW',
			'CY' => 'CY',
			'CZ' => 'CZ',
			'CI' => 'CI',
			'DK' => 'DK',
			'DJ' => 'DJ',
			'DM' => 'DM',
			'DO' => 'DO',
			'EC' => 'EC',
			'EG' => 'EG',
			'SV' => 'SV',
			'GQ' => 'GQ',
			'ER' => 'ER',
			'EE' => 'EE',
			'SZ' => 'SZ',
			'ET' => 'ET',
			'FK' => 'FK',
			'FO' => 'FO',
			'FJ' => 'FJ',
			'FI' => 'FI',
			'FR' => 'FR',
			'GF' => 'GF',
			'PF' => 'PF',
			'TF' => 'TF',
			'GA' => 'GA',
			'GM' => 'GM',
			'GE' => 'GE',
			'DE' => 'DE',
			'GH' => 'GH',
			'GI' => 'GI',
			'GR' => 'GR',
			'GL' => 'GL',
			'GD' => 'GD',
			'GP' => 'GP',
			'GU' => 'GU',
			'GT' => 'GT',
			'GG' => 'GG',
			'GN' => 'GN',
			'GW' => 'GW',
			'GY' => 'GY',
			'HT' => 'HT',
			'HM' => 'HM',
			'VA' => 'VA',
			'HN' => 'HN',
			'HK' => 'HK',
			'HU' => 'HU',
			'IS' => 'IS',
			'IN' => 'IN',
			'ID' => 'ID',
			'IR' => 'IR',
			'IQ' => 'IQ',
			'IE' => 'IE',
			'IM' => 'IM',
			'IL' => 'IL',
			'IT' => 'IT',
			'JM' => 'JM',
			'JP' => 'JP',
			'JE' => 'JE',
			'JO' => 'JO',
			'KZ' => 'KZ',
			'KE' => 'KE',
			'KI' => 'KI',
			'KP' => 'KP',
			'KW' => 'KW',
			'KG' => 'KG',
			'LA' => 'LA',
			'LV' => 'LV',
			'LB' => 'LB',
			'LS' => 'LS',
			'LR' => 'LR',
			'LY' => 'LY',
			'LI' => 'LI',
			'LT' => 'LT',
			'LU' => 'LU',
			'MO' => 'MO',
			'MG' => 'MG',
			'MW' => 'MW',
			'MY' => 'MY',
			'MV' => 'MV',
			'ML' => 'ML',
			'MT' => 'MT',
			'MH' => 'MH',
			'MQ' => 'MQ',
			'MR' => 'MR',
			'MU' => 'MU',
			'YT' => 'YT',
			'MX' => 'MX',
			'FM' => 'FM',
			'MD' => 'MD',
			'MC' => 'MC',
			'MN' => 'MN',
			'ME' => 'ME',
			'MS' => 'MS',
			'MA' => 'MA',
			'MZ' => 'MZ',
			'MM' => 'MM',
			'NA' => 'NA',
			'NR' => 'NR',
			'NP' => 'NP',
			'NL' => 'NL',
			'NC' => 'NC',
			'NZ' => 'NZ',
			'NI' => 'NI',
			'NE' => 'NE',
			'NG' => 'NG',
			'NU' => 'NU',
			'NF' => 'NF',
			'MP' => 'MP',
			'NO' => 'NO',
			'OM' => 'OM',
			'PK' => 'PK',
			'PW' => 'PW',
			'PS' => 'PS',
			'PA' => 'PA',
			'PG' => 'PG',
			'PY' => 'PY',
			'PE' => 'PE',
			'PH' => 'PH',
			'PN' => 'PN',
			'PL' => 'PL',
			'PT' => 'PT',
			'PR' => 'PR',
			'QA' => 'QA',
			'MK' => 'MK',
			'RO' => 'RO',
			'RU' => 'RU',
			'RW' => 'RW',
			'RE' => 'RE',
			'BL' => 'BL',
			'SH' => 'SH',
			'KN' => 'KN',
			'LC' => 'LC',
			'MF' => 'MF',
			'PM' => 'PM',
			'VC' => 'VC',
			'WS' => 'WS',
			'SM' => 'SM',
			'ST' => 'ST',
			'SA' => 'SA',
			'SN' => 'SN',
			'RS' => 'RS',
			'SC' => 'SC',
			'SL' => 'SL',
			'SG' => 'SG',
			'SX' => 'SX',
			'SK' => 'SK',
			'SI' => 'SI',
			'SB' => 'SB',
			'SO' => 'SO',
			'ZA' => 'ZA',
			'GS' => 'GS',
			'SS' => 'SS',
			'ES' => 'ES',
			'LK' => 'LK',
			'SD' => 'SD',
			'SR' => 'SR',
			'SJ' => 'SJ',
			'SE' => 'SE',
			'CH' => 'CH',
			'SY' => 'SY',
			'TW' => 'TW',
			'TJ' => 'TJ',
			'TZ' => 'TZ',
			'TH' => 'TH',
			'TL' => 'TL',
			'TG' => 'TG',
			'TK' => 'TK',
			'TO' => 'TO',
			'TT' => 'TT',
			'TN' => 'TN',
			'TR' => 'TR',
			'TM' => 'TM',
			'TC' => 'TC',
			'TV' => 'TV',
			'UG' => 'UG',
			'UA' => 'UA',
			'AE' => 'AE',
			'GB' => 'GB',
			'GB' => 'UK',
			'UM' => 'UM',
			'US' => 'US',
			'UY' => 'UY',
			'UZ' => 'UZ',
			'VU' => 'VU',
			'VE' => 'VE',
			'VN' => 'VN',
			'VG' => 'VG',
			'VI' => 'VI',
			'WF' => 'WF',
			'EH' => 'EH',
			'YE' => 'YE',
			'ZM' => 'ZM',
			'ZW' => 'ZW',
			'AX' => 'AX'
		];
	}
}