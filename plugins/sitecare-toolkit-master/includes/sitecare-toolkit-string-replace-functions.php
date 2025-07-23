<?php

/**
 * The file that defines the plugin's helper functions for replacing strings.
 *
 * @link       https://www.sitecare.com
 * @since      0.0.1
 *
 * @package    SiteCare_Toolkit
 * @subpackage SiteCare_Toolkit/includes
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Replace characters in a phone number
 *
 * @param string $input (required) - the phone number to be modified.
 * @param string $replacement (required) - the character to replace the $input's character.
 * @return string
 */
if ( ! function_exists( 'sctk_replace_phone' ) ) {
    function sctk_replace_phone( $input, $replacement = '' ) {
        // String replace.
        $output = preg_replace('/\D+/', $replacement, $input );

        return $output;
    }
}


/**
 * Replace the state name with the state abbreviation.
 *
 * @param string $name (required) - the full state name.
 * @return string
 */
if ( ! function_exists( 'sctk_replace_state_name' ) ) {
    function sctk_replace_state_name( $name ) {
        $states = array(
            array( 'name' => 'Alabama', 'abbr' => 'AL' ),
            array( 'name' => 'Alaska', 'abbr' => 'AK' ),
            array( 'name' => 'Arizona', 'abbr' => 'AZ' ),
            array( 'name' => 'Arkansas', 'abbr' => 'AR' ),
            array( 'name' => 'California', 'abbr' => 'CA' ),
            array( 'name' => 'Colorado', 'abbr' => 'CO' ),
            array( 'name' => 'Connecticut', 'abbr' => 'CT' ),
            array( 'name' => 'Delaware', 'abbr' => 'DE' ),
            array( 'name' => 'Florida', 'abbr' => 'FL' ),
            array( 'name' => 'Georgia', 'abbr' => 'GA' ),
            array( 'name' => 'Hawaii', 'abbr' => 'HI' ),
            array( 'name' => 'Idaho', 'abbr' => 'ID' ),
            array( 'name' => 'Illinois', 'abbr' => 'IL' ),
            array( 'name' => 'Indiana', 'abbr' => 'IN' ),
            array( 'name' => 'Iowa', 'abbr' => 'IA' ),
            array( 'name' => 'Kansas', 'abbr' => 'KS' ),
            array( 'name' => 'Kentucky', 'abbr' => 'KY' ),
            array( 'name' => 'Louisiana', 'abbr' => 'LA' ),
            array( 'name' => 'Maine', 'abbr' => 'ME' ),
            array( 'name' => 'Maryland', 'abbr' => 'MD' ),
            array( 'name' => 'Massachusetts', 'abbr' => 'MA' ),
            array( 'name' => 'Michigan', 'abbr' => 'MI' ),
            array( 'name' => 'Minnesota', 'abbr' => 'MN' ),
            array( 'name' => 'Mississippi', 'abbr' => 'MS' ),
            array( 'name' => 'Missouri', 'abbr' => 'MO' ),
            array( 'name' => 'Montana', 'abbr' => 'MT' ),
            array( 'name' => 'Nebraska', 'abbr' => 'NE' ),
            array( 'name' => 'Nevada', 'abbr' => 'NV' ),
            array( 'name' => 'New Hampshire', 'abbr' => 'NH' ),
            array( 'name' => 'New Jersey', 'abbr' => 'NJ' ),
            array( 'name' => 'New Mexico', 'abbr' => 'NM' ),
            array( 'name' => 'New York', 'abbr' => 'NY' ),
            array( 'name' => 'North Carolina', 'abbr' => 'NC' ),
            array( 'name' => 'North Dakota', 'abbr' => 'ND' ),
            array( 'name' => 'Ohio', 'abbr' => 'OH' ),
            array( 'name' => 'Oklahoma', 'abbr' => 'OK' ),
            array( 'name' => 'Oregon', 'abbr' => 'OR' ),
            array( 'name' => 'Pennsylvania', 'abbr' => 'PA' ),
            array( 'name' => 'Rhode Island', 'abbr' => 'RI' ),
            array( 'name' => 'South Carolina', 'abbr' => 'SC' ),
            array( 'name' => 'South Dakota', 'abbr' => 'SD' ),
            array( 'name' => 'Tennessee', 'abbr' => 'TN' ),
            array( 'name' => 'Texas', 'abbr' => 'TX' ),
            array( 'name' => 'Utah', 'abbr' => 'UT' ),
            array( 'name' => 'Vermont', 'abbr' => 'VT' ),
            array( 'name' => 'Virginia', 'abbr' => 'VA' ),
            array( 'name' => 'Washington', 'abbr' => 'WA' ),
            array( 'name' => 'West Virginia', 'abbr' => 'WV' ),
            array( 'name' => 'Wisconsin', 'abbr' => 'WI' ),
            array( 'name' => 'Wyoming', 'abbr' => 'WY' ),
            array( 'name' => 'Virgin Islands', 'abbr' => 'V.I.' ),
            array( 'name' => 'Guam', 'abbr' => 'GU' ),
            array( 'name' => 'Puerto Rico', 'abbr' => 'PR')
        );

        $return = false;   
        $strlen = strlen( $name );

        foreach ( $states as $state) :
            if ( 2 > $strlen ) {
                return false;
            } else if ( 2 == $strlen ) {
                if ( strtolower( $state['abbr'] ) == strtolower( $name ) ) {
                    $return = $state['name'];
                    break;
                }
            } else {
                if ( strtolower( $state['name'] ) == strtolower( $name ) ) {
                    $return = strtoupper( $state['abbr'] );
                    break;
                }
            }
        endforeach;

        return $return;
    }
}


/**
 * Replace the country name with the country abbreviation.
 *
 * @param string $name (required) - the full country name.
 * @return string
 */
if ( ! function_exists( 'sctk_replace_country_name' ) ) {
    function sctk_replace_country_name( $name ) {
        $countries = array(
            'AF' => esc_attr__( 'Afghanistan', 'sitecare-toolkit' ), 
            'AL' => esc_attr__( 'Albania', 'sitecare-toolkit' ), 
            'DZ' => esc_attr__( 'Algeria', 'sitecare-toolkit' ), 
            'AS' => esc_attr__( 'American Samoa', 'sitecare-toolkit' ), 
            'AD' => esc_attr__( 'Andorra', 'sitecare-toolkit' ), 
            'AO' => esc_attr__( 'Angola', 'sitecare-toolkit' ), 
            'AI' => esc_attr__( 'Anguilla', 'sitecare-toolkit' ), 
            'AQ' => esc_attr__( 'Antarctica', 'sitecare-toolkit' ), 
            'AG' => esc_attr__( 'Antigua and Barbuda', 'sitecare-toolkit' ), 
            'AR' => esc_attr__( 'Argentina', 'sitecare-toolkit' ), 
            'AM' => esc_attr__( 'Armenia', 'sitecare-toolkit' ), 
            'AW' => esc_attr__( 'Aruba', 'sitecare-toolkit' ), 
            'AU' => esc_attr__( 'Australia', 'sitecare-toolkit' ), 
            'AT' => esc_attr__( 'Austria', 'sitecare-toolkit' ), 
            'AZ' => esc_attr__( 'Azerbaijan', 'sitecare-toolkit' ), 
            'BS' => esc_attr__( 'Bahamas', 'sitecare-toolkit' ), 
            'BH' => esc_attr__( 'Bahrain', 'sitecare-toolkit' ), 
            'BD' => esc_attr__( 'Bangladesh', 'sitecare-toolkit' ), 
            'BB' => esc_attr__( 'Barbados', 'sitecare-toolkit' ), 
            'BY' => esc_attr__( 'Belarus', 'sitecare-toolkit' ), 
            'BE' => esc_attr__( 'Belgium', 'sitecare-toolkit' ), 
            'BZ' => esc_attr__( 'Belize', 'sitecare-toolkit' ), 
            'BJ' => esc_attr__( 'Benin', 'sitecare-toolkit' ), 
            'BM' => esc_attr__( 'Bermuda', 'sitecare-toolkit' ), 
            'BT' => esc_attr__( 'Bhutan', 'sitecare-toolkit' ), 
            'BO' => esc_attr__( 'Bolivia', 'sitecare-toolkit' ), 
            'BA' => esc_attr__( 'Bosnia and Herzegovina', 'sitecare-toolkit' ), 
            'BW' => esc_attr__( 'Botswana', 'sitecare-toolkit' ), 
            'BV' => esc_attr__( 'Bouvet Island', 'sitecare-toolkit' ), 
            'BR' => esc_attr__( 'Brazil', 'sitecare-toolkit' ), 
            'BQ' => esc_attr__( 'British Antarctic Territory', 'sitecare-toolkit' ), 
            'IO' => esc_attr__( 'British Indian Ocean Territory', 'sitecare-toolkit' ), 
            'VG' => esc_attr__( 'British Virgin Islands', 'sitecare-toolkit' ), 
            'BN' => esc_attr__( 'Brunei', 'sitecare-toolkit' ), 
            'BG' => esc_attr__( 'Bulgaria', 'sitecare-toolkit' ), 
            'BF' => esc_attr__( 'Burkina Faso', 'sitecare-toolkit' ), 
            'BI' => esc_attr__( 'Burundi', 'sitecare-toolkit' ), 
            'KH' => esc_attr__( 'Cambodia', 'sitecare-toolkit' ), 
            'CM' => esc_attr__( 'Cameroon', 'sitecare-toolkit' ), 
            'CA' => esc_attr__( 'Canada', 'sitecare-toolkit' ), 
            'CT' => esc_attr__( 'Canton and Enderbury Islands', 'sitecare-toolkit' ), 
            'CV' => esc_attr__( 'Cape Verde', 'sitecare-toolkit' ), 
            'KY' => esc_attr__( 'Cayman Islands', 'sitecare-toolkit' ), 
            'CF' => esc_attr__( 'Central African Republic', 'sitecare-toolkit' ), 
            'TD' => esc_attr__( 'Chad', 'sitecare-toolkit' ), 
            'CL' => esc_attr__( 'Chile', 'sitecare-toolkit' ), 
            'CN' => esc_attr__( 'China', 'sitecare-toolkit' ), 
            'CX' => esc_attr__( 'Christmas Island', 'sitecare-toolkit' ), 
            'CC' => esc_attr__( 'Cocos [Keeling] Islands', 'sitecare-toolkit' ), 
            'CO' => esc_attr__( 'Colombia', 'sitecare-toolkit' ), 
            'KM' => esc_attr__( 'Comoros', 'sitecare-toolkit' ), 
            'CG' => esc_attr__( 'Congo - Brazzaville', 'sitecare-toolkit' ), 
            'CD' => esc_attr__( 'Congo - Kinshasa', 'sitecare-toolkit' ), 
            'CK' => esc_attr__( 'Cook Islands', 'sitecare-toolkit' ), 
            'CR' => esc_attr__( 'Costa Rica', 'sitecare-toolkit' ), 
            'HR' => esc_attr__( 'Croatia', 'sitecare-toolkit' ), 
            'CU' => esc_attr__( 'Cuba', 'sitecare-toolkit' ), 
            'CY' => esc_attr__( 'Cyprus', 'sitecare-toolkit' ), 
            'CZ' => esc_attr__( 'Czech Republic', 'sitecare-toolkit' ), 
            'CI' => esc_attr__( 'Côte d’Ivoire', 'sitecare-toolkit' ), 
            'DK' => esc_attr__( 'Denmark', 'sitecare-toolkit' ), 
            'DJ' => esc_attr__( 'Djibouti', 'sitecare-toolkit' ), 
            'DM' => esc_attr__( 'Dominica', 'sitecare-toolkit' ), 
            'DO' => esc_attr__( 'Dominican Republic', 'sitecare-toolkit' ), 
            'NQ' => esc_attr__( 'Dronning Maud Land', 'sitecare-toolkit' ), 
            'DD' => esc_attr__( 'East Germany', 'sitecare-toolkit' ), 
            'EC' => esc_attr__( 'Ecuador', 'sitecare-toolkit' ), 
            'EG' => esc_attr__( 'Egypt', 'sitecare-toolkit' ), 
            'SV' => esc_attr__( 'El Salvador', 'sitecare-toolkit' ), 
            'GQ' => esc_attr__( 'Equatorial Guinea', 'sitecare-toolkit' ), 
            'ER' => esc_attr__( 'Eritrea', 'sitecare-toolkit' ), 
            'EE' => esc_attr__( 'Estonia', 'sitecare-toolkit' ), 
            'ET' => esc_attr__( 'Ethiopia', 'sitecare-toolkit' ), 
            'FK' => esc_attr__( 'Falkland Islands', 'sitecare-toolkit' ), 
            'FO' => esc_attr__( 'Faroe Islands', 'sitecare-toolkit' ), 
            'FJ' => esc_attr__( 'Fiji', 'sitecare-toolkit' ), 
            'FI' => esc_attr__( 'Finland', 'sitecare-toolkit' ), 
            'FR' => esc_attr__( 'France', 'sitecare-toolkit' ), 
            'GF' => esc_attr__( 'French Guiana', 'sitecare-toolkit' ), 
            'PF' => esc_attr__( 'French Polynesia', 'sitecare-toolkit' ), 
            'TF' => esc_attr__( 'French Southern Territories', 'sitecare-toolkit' ), 
            'FQ' => esc_attr__( 'French Southern and Antarctic Territories', 'sitecare-toolkit' ), 
            'GA' => esc_attr__( 'Gabon', 'sitecare-toolkit' ), 
            'GM' => esc_attr__( 'Gambia', 'sitecare-toolkit' ), 
            'GE' => esc_attr__( 'Georgia', 'sitecare-toolkit' ), 
            'DE' => esc_attr__( 'Germany', 'sitecare-toolkit' ), 
            'GH' => esc_attr__( 'Ghana', 'sitecare-toolkit' ), 
            'GI' => esc_attr__( 'Gibraltar', 'sitecare-toolkit' ), 
            'GR' => esc_attr__( 'Greece', 'sitecare-toolkit' ), 
            'GL' => esc_attr__( 'Greenland', 'sitecare-toolkit' ), 
            'GD' => esc_attr__( 'Grenada', 'sitecare-toolkit' ), 
            'GP' => esc_attr__( 'Guadeloupe', 'sitecare-toolkit' ), 
            'GU' => esc_attr__( 'Guam', 'sitecare-toolkit' ), 
            'GT' => esc_attr__( 'Guatemala', 'sitecare-toolkit' ), 
            'GG' => esc_attr__( 'Guernsey', 'sitecare-toolkit' ), 
            'GN' => esc_attr__( 'Guinea', 'sitecare-toolkit' ), 
            'GW' => esc_attr__( 'Guinea-Bissau', 'sitecare-toolkit' ), 
            'GY' => esc_attr__( 'Guyana', 'sitecare-toolkit' ), 
            'HT' => esc_attr__( 'Haiti', 'sitecare-toolkit' ), 
            'HM' => esc_attr__( 'Heard Island and McDonald Islands', 'sitecare-toolkit' ), 
            'HN' => esc_attr__( 'Honduras', 'sitecare-toolkit' ), 
            'HK' => esc_attr__( 'Hong Kong SAR China', 'sitecare-toolkit' ), 
            'HU' => esc_attr__( 'Hungary', 'sitecare-toolkit' ), 
            'IS' => esc_attr__( 'Iceland', 'sitecare-toolkit' ), 
            'IN' => esc_attr__( 'India', 'sitecare-toolkit' ), 
            'ID' => esc_attr__( 'Indonesia', 'sitecare-toolkit' ), 
            'IR' => esc_attr__( 'Iran', 'sitecare-toolkit' ), 
            'IQ' => esc_attr__( 'Iraq', 'sitecare-toolkit' ), 
            'IE' => esc_attr__( 'Ireland', 'sitecare-toolkit' ), 
            'IM' => esc_attr__( 'Isle of Man', 'sitecare-toolkit' ), 
            'IL' => esc_attr__( 'Israel', 'sitecare-toolkit' ), 
            'IT' => esc_attr__( 'Italy', 'sitecare-toolkit' ), 
            'JM' => esc_attr__( 'Jamaica', 'sitecare-toolkit' ), 
            'JP' => esc_attr__( 'Japan', 'sitecare-toolkit' ), 
            'JE' => esc_attr__( 'Jersey', 'sitecare-toolkit' ), 
            'JT' => esc_attr__( 'Johnston Island', 'sitecare-toolkit' ), 
            'JO' => esc_attr__( 'Jordan', 'sitecare-toolkit' ), 
            'KZ' => esc_attr__( 'Kazakhstan', 'sitecare-toolkit' ), 
            'KE' => esc_attr__( 'Kenya', 'sitecare-toolkit' ), 
            'KI' => esc_attr__( 'Kiribati', 'sitecare-toolkit' ), 
            'KW' => esc_attr__( 'Kuwait', 'sitecare-toolkit' ), 
            'KG' => esc_attr__( 'Kyrgyzstan', 'sitecare-toolkit' ), 
            'LA' => esc_attr__( 'Laos', 'sitecare-toolkit' ), 
            'LV' => esc_attr__( 'Latvia', 'sitecare-toolkit' ), 
            'LB' => esc_attr__( 'Lebanon', 'sitecare-toolkit' ), 
            'LS' => esc_attr__( 'Lesotho', 'sitecare-toolkit' ), 
            'LR' => esc_attr__( 'Liberia', 'sitecare-toolkit' ), 
            'LY' => esc_attr__( 'Libya', 'sitecare-toolkit' ), 
            'LI' => esc_attr__( 'Liechtenstein', 'sitecare-toolkit' ), 
            'LT' => esc_attr__( 'Lithuania', 'sitecare-toolkit' ), 
            'LU' => esc_attr__( 'Luxembourg', 'sitecare-toolkit' ), 
            'MO' => esc_attr__( 'Macau SAR China', 'sitecare-toolkit' ), 
            'MK' => esc_attr__( 'Macedonia', 'sitecare-toolkit' ), 
            'MG' => esc_attr__( 'Madagascar', 'sitecare-toolkit' ), 
            'MW' => esc_attr__( 'Malawi', 'sitecare-toolkit' ), 
            'MY' => esc_attr__( 'Malaysia', 'sitecare-toolkit' ), 
            'MV' => esc_attr__( 'Maldives', 'sitecare-toolkit' ), 
            'ML' => esc_attr__( 'Mali', 'sitecare-toolkit' ), 
            'MT' => esc_attr__( 'Malta', 'sitecare-toolkit' ), 
            'MH' => esc_attr__( 'Marshall Islands', 'sitecare-toolkit' ), 
            'MQ' => esc_attr__( 'Martinique', 'sitecare-toolkit' ), 
            'MR' => esc_attr__( 'Mauritania', 'sitecare-toolkit' ), 
            'MU' => esc_attr__( 'Mauritius', 'sitecare-toolkit' ), 
            'YT' => esc_attr__( 'Mayotte', 'sitecare-toolkit' ), 
            'FX' => esc_attr__( 'Metropolitan France', 'sitecare-toolkit' ), 
            'MX' => esc_attr__( 'Mexico', 'sitecare-toolkit' ), 
            'FM' => esc_attr__( 'Micronesia', 'sitecare-toolkit' ), 
            'MI' => esc_attr__( 'Midway Islands', 'sitecare-toolkit' ), 
            'MD' => esc_attr__( 'Moldova', 'sitecare-toolkit' ), 
            'MC' => esc_attr__( 'Monaco', 'sitecare-toolkit' ), 
            'MN' => esc_attr__( 'Mongolia', 'sitecare-toolkit' ), 
            'ME' => esc_attr__( 'Montenegro', 'sitecare-toolkit' ), 
            'MS' => esc_attr__( 'Montserrat', 'sitecare-toolkit' ), 
            'MA' => esc_attr__( 'Morocco', 'sitecare-toolkit' ), 
            'MZ' => esc_attr__( 'Mozambique', 'sitecare-toolkit' ), 
            'MM' => esc_attr__( 'Myanmar [Burma]', 'sitecare-toolkit' ), 
            'NA' => esc_attr__( 'Namibia', 'sitecare-toolkit' ), 
            'NR' => esc_attr__( 'Nauru', 'sitecare-toolkit' ), 
            'NP' => esc_attr__( 'Nepal', 'sitecare-toolkit' ), 
            'NL' => esc_attr__( 'Netherlands', 'sitecare-toolkit' ), 
            'AN' => esc_attr__( 'Netherlands Antilles', 'sitecare-toolkit' ), 
            'NT' => esc_attr__( 'Neutral Zone', 'sitecare-toolkit' ), 
            'NC' => esc_attr__( 'New Caledonia', 'sitecare-toolkit' ), 
            'NZ' => esc_attr__( 'New Zealand', 'sitecare-toolkit' ), 
            'NI' => esc_attr__( 'Nicaragua', 'sitecare-toolkit' ), 
            'NE' => esc_attr__( 'Niger', 'sitecare-toolkit' ), 
            'NG' => esc_attr__( 'Nigeria', 'sitecare-toolkit' ), 
            'NU' => esc_attr__( 'Niue', 'sitecare-toolkit' ), 
            'NF' => esc_attr__( 'Norfolk Island', 'sitecare-toolkit' ), 
            'KP' => esc_attr__( 'North Korea', 'sitecare-toolkit' ), 
            'VD' => esc_attr__( 'North Vietnam', 'sitecare-toolkit' ), 
            'MP' => esc_attr__( 'Northern Mariana Islands', 'sitecare-toolkit' ), 
            'NO' => esc_attr__( 'Norway', 'sitecare-toolkit' ), 
            'OM' => esc_attr__( 'Oman', 'sitecare-toolkit' ), 
            'PC' => esc_attr__( 'Pacific Islands Trust Territory', 'sitecare-toolkit' ), 
            'PK' => esc_attr__( 'Pakistan', 'sitecare-toolkit' ), 
            'PW' => esc_attr__( 'Palau', 'sitecare-toolkit' ), 
            'PS' => esc_attr__( 'Palestinian Territories', 'sitecare-toolkit' ), 
            'PA' => esc_attr__( 'Panama', 'sitecare-toolkit' ), 
            'PZ' => esc_attr__( 'Panama Canal Zone', 'sitecare-toolkit' ), 
            'PG' => esc_attr__( 'Papua New Guinea', 'sitecare-toolkit' ), 
            'PY' => esc_attr__( 'Paraguay', 'sitecare-toolkit' ), 
            'YD' => esc_attr__( 'People\'s Democratic Republic of Yemen', 'sitecare-toolkit' ), 
            'PE' => esc_attr__( 'Peru', 'sitecare-toolkit' ), 
            'PH' => esc_attr__( 'Philippines', 'sitecare-toolkit' ), 
            'PN' => esc_attr__( 'Pitcairn Islands', 'sitecare-toolkit' ), 
            'PL' => esc_attr__( 'Poland', 'sitecare-toolkit' ), 
            'PT' => esc_attr__( 'Portugal', 'sitecare-toolkit' ), 
            'PR' => esc_attr__( 'Puerto Rico', 'sitecare-toolkit' ), 
            'QA' => esc_attr__( 'Qatar', 'sitecare-toolkit' ), 
            'RO' => esc_attr__( 'Romania', 'sitecare-toolkit' ), 
            'RU' => esc_attr__( 'Russia', 'sitecare-toolkit' ), 
            'RW' => esc_attr__( 'Rwanda', 'sitecare-toolkit' ), 
            'BL' => esc_attr__( 'Saint Barthélemy', 'sitecare-toolkit' ), 
            'SH' => esc_attr__( 'Saint Helena', 'sitecare-toolkit' ), 
            'KN' => esc_attr__( 'Saint Kitts and Nevis', 'sitecare-toolkit' ), 
            'LC' => esc_attr__( 'Saint Lucia', 'sitecare-toolkit' ), 
            'MF' => esc_attr__( 'Saint Martin', 'sitecare-toolkit' ), 
            'PM' => esc_attr__( 'Saint Pierre and Miquelon', 'sitecare-toolkit' ), 
            'VC' => esc_attr__( 'Saint Vincent and the Grenadines', 'sitecare-toolkit' ), 
            'WS' => esc_attr__( 'Samoa', 'sitecare-toolkit' ), 
            'SM' => esc_attr__( 'San Marino', 'sitecare-toolkit' ), 
            'SA' => esc_attr__( 'Saudi Arabia', 'sitecare-toolkit' ), 
            'SN' => esc_attr__( 'Senegal', 'sitecare-toolkit' ), 
            'RS' => esc_attr__( 'Serbia', 'sitecare-toolkit' ), 
            'CS' => esc_attr__( 'Serbia and Montenegro', 'sitecare-toolkit' ), 
            'SC' => esc_attr__( 'Seychelles', 'sitecare-toolkit' ), 
            'SL' => esc_attr__( 'Sierra Leone', 'sitecare-toolkit' ), 
            'SG' => esc_attr__( 'Singapore', 'sitecare-toolkit' ), 
            'SK' => esc_attr__( 'Slovakia', 'sitecare-toolkit' ), 
            'SI' => esc_attr__( 'Slovenia', 'sitecare-toolkit' ), 
            'SB' => esc_attr__( 'Solomon Islands', 'sitecare-toolkit' ), 
            'SO' => esc_attr__( 'Somalia', 'sitecare-toolkit' ), 
            'ZA' => esc_attr__( 'South Africa', 'sitecare-toolkit' ), 
            'GS' => esc_attr__( 'South Georgia and the South Sandwich Islands', 'sitecare-toolkit' ), 
            'KR' => esc_attr__( 'South Korea', 'sitecare-toolkit' ), 
            'ES' => esc_attr__( 'Spain', 'sitecare-toolkit' ), 
            'LK' => esc_attr__( 'Sri Lanka', 'sitecare-toolkit' ), 
            'SD' => esc_attr__( 'Sudan', 'sitecare-toolkit' ), 
            'SR' => esc_attr__( 'Suriname', 'sitecare-toolkit' ), 
            'SJ' => esc_attr__( 'Svalbard and Jan Mayen', 'sitecare-toolkit' ), 
            'SZ' => esc_attr__( 'Swaziland', 'sitecare-toolkit' ), 
            'SE' => esc_attr__( 'Sweden', 'sitecare-toolkit' ), 
            'CH' => esc_attr__( 'Switzerland', 'sitecare-toolkit' ), 
            'SY' => esc_attr__( 'Syria', 'sitecare-toolkit' ), 
            'ST' => esc_attr__( 'São Tomé and Príncipe', 'sitecare-toolkit' ), 
            'TW' => esc_attr__( 'Taiwan', 'sitecare-toolkit' ), 
            'TJ' => esc_attr__( 'Tajikistan', 'sitecare-toolkit' ), 
            'TZ' => esc_attr__( 'Tanzania', 'sitecare-toolkit' ), 
            'TH' => esc_attr__( 'Thailand', 'sitecare-toolkit' ), 
            'TL' => esc_attr__( 'Timor-Leste', 'sitecare-toolkit' ), 
            'TG' => esc_attr__( 'Togo', 'sitecare-toolkit' ), 
            'TK' => esc_attr__( 'Tokelau', 'sitecare-toolkit' ), 
            'TO' => esc_attr__( 'Tonga', 'sitecare-toolkit' ), 
            'TT' => esc_attr__( 'Trinidad and Tobago', 'sitecare-toolkit' ), 
            'TN' => esc_attr__( 'Tunisia', 'sitecare-toolkit' ), 
            'TR' => esc_attr__( 'Turkey', 'sitecare-toolkit' ), 
            'TM' => esc_attr__( 'Turkmenistan', 'sitecare-toolkit' ), 
            'TC' => esc_attr__( 'Turks and Caicos Islands', 'sitecare-toolkit' ), 
            'TV' => esc_attr__( 'Tuvalu', 'sitecare-toolkit' ), 
            'UM' => esc_attr__( 'U.S. Minor Outlying Islands', 'sitecare-toolkit' ), 
            'PU' => esc_attr__( 'U.S. Miscellaneous Pacific Islands', 'sitecare-toolkit' ), 
            'VI' => esc_attr__( 'U.S. Virgin Islands', 'sitecare-toolkit' ), 
            'UG' => esc_attr__( 'Uganda', 'sitecare-toolkit' ), 
            'UA' => esc_attr__( 'Ukraine', 'sitecare-toolkit' ), 
            'SU' => esc_attr__( 'Union of Soviet Socialist Republics', 'sitecare-toolkit' ), 
            'AE' => esc_attr__( 'United Arab Emirates', 'sitecare-toolkit' ), 
            'GB' => esc_attr__( 'United Kingdom', 'sitecare-toolkit' ), 
            'US' => esc_attr__( 'United States', 'sitecare-toolkit' ), 
            'ZZ' => esc_attr__( 'Unknown or Invalid Region', 'sitecare-toolkit' ), 
            'UY' => esc_attr__( 'Uruguay', 'sitecare-toolkit' ), 
            'UZ' => esc_attr__( 'Uzbekistan', 'sitecare-toolkit' ), 
            'VU' => esc_attr__( 'Vanuatu', 'sitecare-toolkit' ), 
            'VA' => esc_attr__( 'Vatican City', 'sitecare-toolkit' ), 
            'VE' => esc_attr__( 'Venezuela', 'sitecare-toolkit' ), 
            'VN' => esc_attr__( 'Vietnam', 'sitecare-toolkit' ), 
            'WK' => esc_attr__( 'Wake Island', 'sitecare-toolkit' ), 
            'WF' => esc_attr__( 'Wallis and Futuna', 'sitecare-toolkit' ), 
            'EH' => esc_attr__( 'Western Sahara', 'sitecare-toolkit' ), 
            'YE' => esc_attr__( 'Yemen', 'sitecare-toolkit' ), 
            'ZM' => esc_attr__( 'Zambia', 'sitecare-toolkit' ), 
            'ZW' => esc_attr__( 'Zimbabwe', 'sitecare-toolkit' ), 
            'AX' => esc_attr__( 'Åland Islands', 'sitecare-toolkit' ), 
        );

        $return = false;   
        $strlen = strlen( $name );

        foreach ( $countries as $country_abbr=>$country_name ) :
            if ( 2 > $strlen ) {
                return false;
            } else if ( 2 == $strlen ) {
                if ( strtolower( $country_abbr ) == strtolower( $name ) ) {
                    $return = $country_name;
                    break;
                }
            } else {
                if ( strtolower( $country_name ) == strtolower( $name ) ) {
                    $return = strtoupper( $country_abbr );
                    break;
                }
            }
        endforeach;

        return $return;
    }
}

if ( ! function_exists( 'sctk_hide_email' ) ) {
    /**
     * Hide email from Spam Bots.
     *
     * @param string $email The email address.
     *
     * @return string The obfuscated email address.
     * @since  0.2.0
     */
    function sctk_hide_email( $email = null ) {
        if ( ! is_email( $content ) ) {
            return;
        }

        $content = antispambot( $content );

        $email_link = sprintf( 'mailto:%s', $content );

        return sprintf( '<a href="%s">%s</a>', esc_url( $email_link, array( 'mailto' ) ), esc_html( $content ) );
    }
}
