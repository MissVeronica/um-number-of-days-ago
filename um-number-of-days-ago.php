<?php
/**
 * Plugin Name:     Ultimate Member - Number of Days ago
 * Description:     Extension to Ultimate Member for display of dates in the Members Directory either as WP human time difference or only as number of days difference. A Shortcode for these time differences in the User Profile page.
 * Version:         1.1.0
 * Requires PHP:    7.4
 * Author:          Miss Veronica
 * License:         GPL v2 or later
 * License URI:     https://www.gnu.org/licenses/gpl-2.0.html
 * Author URI:      https://github.com/MissVeronica
 * Text Domain:     ultimate-member
 * Domain Path:     /languages
 * UM version:      2.7.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; 
if ( ! class_exists( 'UM' ) ) return;

Class UM_Number_of_Days_ago {

    public $um_meta_keys = array();

    function __construct() {

        add_filter( 'um_ajax_get_members_data', array( $this, 'um_ajax_get_members_number_of_days_ago' ), 10, 3 );
        add_shortcode( 'number_of_days_ago',    array( $this, 'number_of_days_ago_shortcode' ), 10, 2 );
        add_filter( 'um_settings_structure',    array( $this, 'um_settings_structure_number_of_days_ago' ), 10, 1 );
    }

    public function number_of_days_ago_shortcode( $atts, $content = null ) {

        if ( ! empty( $atts['meta_key'] )) {

            $meta_key = sanitize_text_field( $atts['meta_key'] );
            $type = sanitize_text_field( $atts['type'] );

            return $this->do_number_of_days_ago( $meta_key, $type );
        }

        return '';
    }

    public function um_ajax_get_members_number_of_days_ago( $data_array, $user_id, $directory_data ) {

        if ( is_array( $data_array )  && ! empty( $user_id )) {

            if ( empty( $this->um_meta_keys )) {

                $settings = UM()->options()->get( 'um_wp_number_of_days_ago_meta_keys' );

                $this->um_meta_keys['WP'] = array();
                if ( ! empty( $settings ) && is_array( $settings )) {
                    $this->um_meta_keys['WP']   = array_map( 'sanitize_text_field', $settings );
                }

                $settings = UM()->options()->get( 'um_number_of_days_ago_meta_keys' );

                $this->um_meta_keys['days'] = array();
                if ( ! empty( $settings ) && is_array( $settings )) {
                    $this->um_meta_keys['days'] = array_map( 'sanitize_text_field', $settings );
                }
            }

            foreach( $this->um_meta_keys as $type => $meta_keys ) {
                if ( ! empty( $meta_keys )) {

                    foreach( $meta_keys as $meta_key ) {
                        if ( isset( $data_array[$meta_key] )) {
                            $data_array[$meta_key] = $this->do_number_of_days_ago( $meta_key, $type );
                        }
                    }
                }
            }
        }

        return $data_array;
    }

    public function do_number_of_days_ago( $meta_key, $type_human_time_diff = 'WP' ) {

        $value = um_user( $meta_key );

        if ( ! is_numeric( $value )) {
            $value = strtotime( str_replace( '/', '-', $value ));
        }

        if ( $type_human_time_diff == 'WP' ) {

            if ( $meta_key = 'birth_date' ) {

                $diff = (int) abs( current_time( 'timestamp' ) - $value );

                $value = 'less than one year';
                if ( $diff >= YEAR_IN_SECONDS ) {
                    $value = floor( $diff / YEAR_IN_SECONDS ) . ' years';
                }

            } else {

                $value = human_time_diff( $value, current_time( 'timestamp' ));
            }

            if ( empty( $this->um_meta_keys )) {
                return $value;
            }

            switch ( $meta_key ) {
                case 'birth_date': $string = __( '%s old', 'ultimate-member' ); break;
                default:           $string = __( '%s ago', 'ultimate-member' );
            }
        } 

        if ( $type_human_time_diff == 'days' ) {

            $time_diff = current_time( 'timestamp' ) - $value;
            $value = intval( $time_diff/DAY_IN_SECONDS );

            if ( $value == 0 ) {
                $value = intval( $time_diff/HOUR_IN_SECONDS );
                $string = ( $value == 1 ) ? __( 'one hour ago', 'ultimate-member' ) : __( '%d hours ago', 'ultimate-member' );

                if ( $value == 0 ) {
                    $value = intval( $time_diff/MINUTE_IN_SECONDS );
                    $string = ( $value == 1 ) ? __( 'one minute ago', 'ultimate-member' ) : __( '%d minutes ago', 'ultimate-member' );

                    if ( $value == 0 ) {
                        $string = __( 'less than one minute ago', 'ultimate-member' );
                    }
                }

            } else {

                $string = ( $value == 1 ) ? __( 'one day ago', 'ultimate-member' ) : __( '%d days ago', 'ultimate-member' );
            }
        }

        return sprintf( $string, $value );
    }

    public function um_settings_structure_number_of_days_ago( $settings_structure ) {

        $um_forms = get_posts( array( 'post_type' => 'um_form', 'numberposts' => -1, 'post_status' => array( 'publish' )));
        $date_meta_keys = array();

        if ( ! empty( $um_forms )) {
            foreach ( $um_forms as $um_form ) {
                $fields = UM()->query()->get_attr( 'custom_fields', $um_form->ID );

                foreach( $fields as $field ) {
                    if ( $field['type'] == 'date' ) {
                        $date_meta_keys[$field['metakey']] = esc_attr( $field['metakey'] . ' - ' . $field['label'] );
                    }
                }
            }
        }

        if ( ! isset( $date_meta_keys['user_registered'] )) {
            $date_meta_keys['user_registered'] = __( 'user_registered - User Registration date', 'ultimate-member' );
        }
        if ( ! isset( $date_meta_keys['_um_last_login'] )) {
            $date_meta_keys['_um_last_login'] = __( '_um_last_login - Last User Login date', 'ultimate-member' );
        }

        $settings_structure['misc']['fields'][] = array(
            'id'            => 'um_wp_number_of_days_ago_meta_keys',
            'type'          => 'select',
            'multi'         => true,
            'size'          => 'medium',
            'options'       => $date_meta_keys,
            'label'         => __( 'Number of Days ago - Meta Keys WP human', 'ultimate-member' ),
            'tooltip'       => __( 'Select the date meta_key fields to use for the WP human time difference in Members Directory.', 'ultimate-member' ),
            );

        $settings_structure['misc']['fields'][] = array(
            'id'            => 'um_number_of_days_ago_meta_keys',
            'type'          => 'select',
            'multi'         => true,
            'size'          => 'medium',
            'options'       => $date_meta_keys,
            'label'         => __( 'Number of Days ago - Meta Keys Days ago', 'ultimate-member' ),
            'tooltip'       => __( 'Select the date meta_key fields to use for "x days ago" in Members Directory.', 'ultimate-member' ),
            );

        return $settings_structure;
    }
}

new UM_Number_of_Days_ago();
