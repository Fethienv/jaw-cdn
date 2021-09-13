<?php
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function monsterinsights_eu_compliance_require_optin() {
    if ( ( function_exists( 'cookiebot_active' ) && cookiebot_active() ) || class_exists( 'Cookie_Notice' ) ) {
        return apply_filters( 'monsterinsights_eu_compliance_require_optin', false );
    }
    return false;
}

function monsterinsights_eu_compliance_settings( $settings ) {
    $settings['eucompliance']['euoverview'] = array( 
        'id' => 'euoverview',
        'name'  => esc_html__( 'EU Compliance Overview:', 'monsterinsights-eu-compliance' ),
        'no_label' => true,
        'type' => 'descriptive_text',
        'desc' => esc_html__( 'MonsterInsights\'s EU Compliance addon can help you comply with the latest EU regulations like GDPR by automatically performing configuration changes and integrating with compatible plugins and services.', 'monsterinsights-eu-compliance' )
    );

    if ( ! monsterinsights_eu_compliance_require_optin() ) {
        $settings['eucompliance']['euip'] = array( 
            'id'    => 'euip',
            'name'  => esc_html__( 'Anonymize IP addresses for Google Analytics hits:', 'monsterinsights-eu-compliance' ),
            'type'  => 'checkbox',
            'faux'  => true,
            'std'   => true,
            'field_class' => 'monsterinsights-large-checkbox',
            'desc'  => esc_html__( 'The EU Compliance Addon is automatically anonymizing all IP addresses for Google Analytics hits, eCommerce hits, and form tracking hits.', 'monsterinsights-eu-compliance' )
        );
        $settings['eucompliance']['eudemographics'] = array( 
            'id'    => 'eudemographics',
            'name'  => esc_html__( 'Disable Demographics and Interests Reports for Remarketing and Advertising:', 'monsterinsights-eu-compliance' ),
            'type'  => 'checkbox',
            'faux'  => true,
            'std'   => true,
            'field_class' => 'monsterinsights-large-checkbox',
            'desc'  => sprintf( esc_html__( 'The EU Compliance Addon has automatically disabled Demographics and Interests Reports for Remarketing and Advertising tracking on Google Analytics hits. You will want to make sure you have also %1$sdisabled data sharing in your Google Analytics account%2$s.', 'monsterinsights-eu-compliance' ), '<a href="https://www.monsterinsights.com/docs/getting-started-with-the-eu-compliance-addon" target="_blank" rel="noopener noreferrer" referrer="no-referrer">', '</a>' )
        );
        $settings['eucompliance']['euuserid'] = array( 
            'id'    => 'euuserid',
            'name'  => esc_html__( 'Disable UserID tracking:', 'monsterinsights-eu-compliance' ),
            'type'  => 'checkbox',
            'faux'  => true,
            'std'   => true,
            'field_class' => 'monsterinsights-large-checkbox',
            'desc'  => esc_html__( 'The EU Compliance Addon has automatically disabled UserID tracking on Google Analytics hits, eCommerce hits, form tracking hits, and the UserID dimension in the Custom Dimensions addon.', 'monsterinsights-eu-compliance' )
        );
        $settings['eucompliance']['euauthor'] = array( 
            'id'    => 'euauthor',
            'name'  => esc_html__( 'Disable Author tracking:', 'monsterinsights-eu-compliance' ),
            'type'  => 'checkbox',
            'faux'  => true,
            'std'   => true,
            'field_class' => 'monsterinsights-large-checkbox',
            'desc'  => esc_html__( 'The EU Compliance Addon has automatically disabled the author dimension in the Custom Dimensions addon.', 'monsterinsights-eu-compliance' )
        );
    }
    $settings['eucompliance']['eugacompat'] = array( 
        'id'    => 'eugacompat',
        'name'  => esc_html__( 'Enable ga() Compatibility Mode:', 'monsterinsights-eu-compliance' ),
        'type'  => 'checkbox',
        'faux'  => true,
        'std'   => true,
        'field_class' => 'monsterinsights-large-checkbox',
        'desc'  => esc_html__( 'The EU Compliance Addon has automatically enabled the ga() compatibility feature so plugins integrating for cookie consent can use GA to store consent as a GA event.', 'monsterinsights-eu-compliance' )
    );
    if ( class_exists( 'MonsterInsights_AMP' ) ) {
        $settings['eucompliance']['require_amp_consent'] = array(
            'id'          => 'require_amp_consent',
            'name'        => esc_html__( 'Wait for AMP consent box?', 'monsterinsights-eu-compliance' ),
            'desc'        => esc_html__( 'If you have implemented an AMP Consent Box, enabling this option will tell MonsterInsights to not track on AMP pages until consent is given via that box (or if you have the fallback configured to true, on fallback as well). Important: If you do not have an AMP Consent Box implemented (requires custom code), enabling this option will prevent MonsterInsights from tracking all AMP users.', 'monsterinsights-eu-compliance' ),
            'type'        => 'checkbox',
        );
    }

    if ( function_exists( 'cookiebot_active' ) && cookiebot_active() ) {
        $settings['eucompliance']['cookiebot'] = array(
            'id'    => 'cookiebot',
            'name'  => esc_html__( 'Integrate with the CookieBot or Cookie Notice plugin:', 'monsterinsights-eu-compliance' ),
            'type'  => 'checkbox',
            'std'   => true,
            'faux'  => true,
            'field_class' => 'monsterinsights-large-checkbox',
            'desc'  => esc_html__( 'The EU Compliance Addon has detected you have the CookieBot plugin installed and active, and has automatically performed all required tasks to make our tracking code compatible with it for you. No code changes are required to the MonsterInsights plugin code. Note: having MonsterInsights wait till consent is given to load Google Analytics might alter your Google Analytics data completeness and accuracy.', 'monsterinsights-eu-compliance' )
        );
    } else if ( function_exists( 'cookiebot_active' ) && ! cookiebot_active() ) {
        $settings['eucompliance']['cookiebot'] = array(
            'id'    => 'cookiebot',
            'name'  => esc_html__( 'Integrate with the CookieBot or Cookie Notice plugin:', 'monsterinsights-eu-compliance' ),
            'type'  => 'checkbox',
            'faux'  => true,
            'std'   => false,
            'field_class' => 'monsterinsights-large-checkbox',
            'desc'  => esc_html__( 'The EU Compliance Addon has detected you have the CookieBot plugin installed but you have not activated it in their settings panel. Once you activate it, the EU Compliance addon will automatically perform all required tasks to make our tracking code compatible with it for you. No code changes are required to the MonsterInsights plugin code. Note: having MonsterInsights wait till consent is given to load Google Analytics might alter your Google Analytics data completeness and accuracy.', 'monsterinsights-eu-compliance' )
        );
    } else if ( class_exists( 'Cookie_Notice' ) ) {
        $settings['eucompliance']['cookie_notice'] = array(
            'id'          => 'cookie_notice',
            'name'        => esc_html__( 'Integrate with the CookieBot or Cookie Notice plugin:', 'monsterinsights-eu-compliance' ),
            'desc'        => esc_html__( 'The EU Compliance Addon has detected you have the Cookie Notice plugin by dFactory plugin installed and active, and has automatically performed all required tasks to make our tracking code compatible with it for you. No code changes are required to the MonsterInsights plugin code. Note: having MonsterInsights wait till consent is given to load Google Analytics might alter your Google Analytics data completeness and accuracy', 'monsterinsights-eu-compliance' ),
            'type'        => 'checkbox',
            'faux'        => true,
            'std'         => true,
        );
    } else {
        $settings['eucompliance']['cookiebot'] = array(
            'id'    => 'cookiebot',
            'name'  => esc_html__( 'Integrate with the CookieBot or Cookie Notice plugin:', 'monsterinsights-eu-compliance' ),
            'type'  => 'checkbox',
            'faux'  => true,
            'std'   => false,
            'field_class' => 'monsterinsights-large-checkbox',
            'desc'  => sprintf( esc_html__( 'The EU Compliance Addon allows for integration with either the %1$sCookieBot plugin%2$s or the %3$sCookie Notice plugin%2$s to have MonsterInsights wait to track a user into Google Analytics until consent is given. If you install either %1$sCookieBot%2$s or %3$sCookie Notice by dFactory%2$s, and activate it, the  EU Compliance addon will automatically perform all required tasks to make our tracking code compatible with it for you. No code changes are required to the MonsterInsights plugin code. Note: having MonsterInsights wait until consent is given to load Google Analytics might alter your Google Analytics data completeness and accuracy.', 'monsterinsights-eu-compliance' ), '<a href="https://wordpress.org/plugins/cookiebot/" target="_blank" rel="noopener noreferrer" referrer="no-referrer">',
                                    '</a>','<a href="https://wordpress.org/plugins/cookie-notice/" target="_blank" rel="noopener noreferrer" referrer="no-referrer">'
                )
        );
    }
    $settings['eucompliance']['eugasettings'] = array( 
        'id' => 'eugasettings',
        'name'  => esc_html__( 'Manual Google Analytics Account Changes Required:', 'monsterinsights-eu-compliance' ),
        //'no_label' => true,
        'type' => 'descriptive_text',
        'desc' => sprintf( esc_html__( 'This addon automates a lot of the needed changes for EU compliance, however it cannot alter your Google Analytics account, and some configuration changes might be needed. For the latest recommendations, we recommend reading the EU Compliance addon %1$sGetting Started Guide%2$s for step by step directions on any needed configuration changes.', 'monsterinsights-eu-compliance' ), '<a href="https://www.monsterinsights.com/docs/getting-started-with-the-eu-compliance-addon/" target="_blank" rel="noopener noreferrer" referrer="no-referrer">', '</a>' )
    );
    $settings['eucompliance']['eulegal'] = array( 
        'id' => 'eulegal',
        'name'  => esc_html__( 'Legal Disclaimer:', 'monsterinsights-eu-compliance' ),
        //'no_label' => true,
        'type' => 'descriptive_text',
        'desc' => sprintf( esc_html__( '%1$sThis addon is designed to automate some of the settings change required to be in compliance with various EU laws however due to the dynamic nature of websites, no plugin can offer 100 percent legal compliance. Please consult a specialist internet law attorney to determine if you are in compliance with all applicable laws for your jurisdictions and your use cases.%2$s%1$sAs a website operator, it is solely your responsibility to ensure that you are in compliance with all applicable laws and regulations governing your use of our plugin.%2$s%1$sMonsterInsights, it’s employees/contractors, and other affiliated parties are not lawyers. Any advice given in our support, documentation, website, other mediums or through our services/products should not be considered legal advice and is for informational and/or educational purposes only and are not guaranteed to be correct, complete or up-to-date, and do not constitute creating/entering an Attorney-Client relationship.%2$s', 'monsterinsights-eu-compliance' ), '<p>', '</p>' )
    );

    if ( ! monsterinsights_eu_compliance_require_optin() ) {
        $settings['demographics']['anonymize_ips']['faux'] = true;
        $settings['demographics']['anonymize_ips']['std']  = true;
        $settings['demographics']['anonymize_ips']['desc'] = esc_html__( 'Because you have the EU Compliance addon installed, MonsterInsights is automatically anonymizing the last octet of all IP addresses', 'monsterinsights-eu-compliance' );
        $settings['demographics']['anonymize_ips']['field_class'] = 'monsterinsights-large-checkbox';

        $settings['demographics']['userid']['faux'] = true;
        $settings['demographics']['userid']['std']  = false;
        $settings['demographics']['userid']['desc'] = esc_html__( 'Because you have the EU Compliance addon installed, MonsterInsights has disabled the userID tracking feature.', 'monsterinsights-eu-compliance' );
        $settings['demographics']['userid']['field_class'] = 'monsterinsights-large-checkbox';

        $settings['demographics']['demographics']['faux'] = true;
        $settings['demographics']['demographics']['std']  = false;
        $settings['demographics']['demographics']['desc'] = sprintf( esc_html__( 'Because you have the EU Compliance addon installed, MonsterInsights has disabled the demographics and interest reports for remarketing and advertising tracking feature. You will want to make sure you have also %1$sdisabled data sharing in your Google Analytics account%2$s.', 'monsterinsights-eu-compliance' ), '<a href="https://www.monsterinsights.com/docs/getting-started-with-the-eu-compliance-addon" target="_blank" rel="noopener noreferrer" referrer="no-referrer">', '</a>' );
        $settings['demographics']['demographics']['field_class'] = 'monsterinsights-large-checkbox';

        $settings['compatibility']['gatracker_compatibility_mode']['faux'] = true;
        $settings['compatibility']['gatracker_compatibility_mode']['std']  = true;
        $settings['compatibility']['gatracker_compatibility_mode']['desc'] = esc_html__( 'Because you have the EU Compliance addon installed, MonsterInsights has automatically enabled the ga() compatibility feature.', 'monsterinsights-eu-compliance' );
        $settings['compatibility']['gatracker_compatibility_mode']['field_class'] = 'monsterinsights-large-checkbox';
    }
    return $settings;
}
add_filter( 'monsterinsights_registered_settings', 'monsterinsights_eu_compliance_settings', 9999 );

// Allow for AMP to wait for AMP consent
function monsterinsights_amp_add_analytics_consent( $analytics ) {
    $consent = monsterinsights_get_option( 'require_amp_consent', false ) ;
    if ( $consent ) {
        $analytics['monsterinsights-googleanalytics']['attributes'] = array( 'data-block-on-consent' );
    }
    return $analytics;
}
add_filter( 'monsterinsights_amp_add_analytics', 'monsterinsights_amp_add_analytics_consent' );

// override demographics to false
add_filter( 'monsterinsights_get_option_demographics', 'monsterinsights_eu_compliance_addon_option_false' );

// override anonymize_ips to true
add_filter( 'monsterinsights_get_option_anonymize_ips', 'monsterinsights_eu_compliance_addon_option_true' );

// override gatracker_compatibility_mode to true
add_filter( 'monsterinsights_get_option_gatracker_compatibility_mode', '__return_true' );

// override userID to false
add_filter( 'monsterinsights_get_option_userid', 'monsterinsights_eu_compliance_addon_option_false' );

function monsterinsights_eu_compliance_addon_option_false( $value ){
    if ( monsterinsights_eu_compliance_require_optin() ) {
        return $value;
    }
    return false;
}

function monsterinsights_eu_compliance_addon_option_true( $value ){
    if ( monsterinsights_eu_compliance_require_optin() ) {
        return $value;
    }
    return true;
}

// Force DisplayFeatures off, even if they are turned on in the GA settings (override account settings)
function monsterinsights_eu_compliance_force_displayfeatures_off( $options ) {
    if ( monsterinsights_eu_compliance_require_optin() ) {
        return $options;
    }
    $options['demographics'] = "'set', 'displayFeaturesTask', null";
    return $options;
}
add_filter( 'monsterinsights_frontend_tracking_options_analytics_before_pageview', 'monsterinsights_eu_compliance_force_displayfeatures_off' );

// Hide userID and author custom dimension
function monsterinsights_eu_compliance_custom_dimensions( $dimensions ) {
    if ( monsterinsights_eu_compliance_require_optin() ) {
        return $dimensions;
    }
    $dimensions['user_id']['enabled'] = false;
    $dimensions['author']['enabled']  = false;
    return $dimensions;
}
add_filter( 'monsterinsights_available_custom_dimensions', 'monsterinsights_eu_compliance_custom_dimensions' );

// Remove user_id and author from being used even if already set to be used
function monsterinsights_eu_compliance_custom_dimensions_option( $dimensions ) {
    if ( monsterinsights_eu_compliance_require_optin() ) {
        return $dimensions;
    }
     if ( ! empty( $dimensions ) && is_array( $dimensions ) ) {
        foreach ( $dimensions as $key => $row ) {
            if ( ! empty( $row['type'] ) && ( $row['type'] === 'user_id' || $row['type'] === 'author' ) ) {
                unset( $dimensions[$key] );
            }
        }
    }
    return $dimensions;
}
add_filter( 'monsterinsights_get_option_custom_dimensions', 'monsterinsights_eu_compliance_custom_dimensions_option' );

// filter IPs in the Measurement Protocol calls
function monsterinsights_eu_compliance_mp_api_call_ip( $ip ) {
    if ( monsterinsights_eu_compliance_require_optin() ) {
        return $ip;
    }
    /**
        SRC: https://stackoverflow.com/questions/48767382/anonymize-ipv4-and-ipv6-addresses-with-php-preg-replace
        Pattern Explanations:

        IPv4:

            /         #Pattern delimiter
            \.        #Match dot literally
            \d*       #Match zero or more digits
            $         #Match the end of the string
            /         #Pattern delimiter
        IPv6

            /         #Pattern delimiter
            [\da-f]*  #Match zero or more digits or a b c d e f
            :         #Match colon
            [\da-f]*  #Match zero or more digits or a b c d e f
            $         #Match the end of the string
            /         #Pattern delimiter
    **/
    $ipv4 = '.' .(string) mt_rand(100,999);
    $ipv6 = (string) mt_rand(1000,9999) . ':' . (string) mt_rand(1000,9999);

    return preg_replace(
        array(
            '/\.\d*$/',
            '/[\da-f]*:[\da-f]*$/'
        ),
        array(
            $ipv4,
            $ipv6
        ),
        $ip
    );
}
add_filter( 'monsterinsights_mp_api_call_ip', 'monsterinsights_eu_compliance_mp_api_call_ip' );

// Remove userIDs from the MP calls
function monsterinsights_eu_compliance_mp_api_call_uid( $body ) {
    if ( monsterinsights_eu_compliance_require_optin() ) {
        return $body;
    }
    unset( $body['uid'] );
    return $body;
}
add_filter( 'monsterinsights_mp_api_call', 'monsterinsights_eu_compliance_mp_api_call_uid' );

function monsterinsights_eu_compliance_tracking_analytics_script_attributes( $attributes ) {
    if ( function_exists( 'cookiebot_active' ) && cookiebot_active() ) {
        $attributes['type'] = 'text/plain';
        $attributes['data-cookieconsent'] = 'statistics';
    }
    return $attributes;
}
add_filter( 'monsterinsights_tracking_analytics_script_attributes',  'monsterinsights_eu_compliance_tracking_analytics_script_attributes' );

function monsterinsights_eu_compliance_cookie_notice_integration() {
    if ( ! class_exists( 'Cookie_Notice' ) ) {
        return;
    }
    ob_start();
    ?>
    /* Compatibility with Cookie Notice */
    if ( document.cookie.indexOf( 'cookie_notice_accepted' ) === -1 ) {
        mi_track_user      = false;
        mi_no_track_reason = '<?php echo esc_js( __( "Note: You have not accepted the Cookie Notice.", "monsterinsights-eu-compliance" ) );?>';
    }
    <?php
    $output = ob_get_contents();
    ob_end_clean();
    echo $output;
}
add_action( 'monsterinsights_tracking_analytics_frontend_output_after_mi_track_user', 'monsterinsights_eu_compliance_cookie_notice_integration' );