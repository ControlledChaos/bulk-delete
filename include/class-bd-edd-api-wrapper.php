<?php
/**
 * Wrapper for EDD API
 *
 * @package    Bulk_Delete
 * @subpackage EDD
 * @author     Sudar
 * @since      5.0
 */
class BD_EDD_API_Wrapper {

    /**
     * Store url
     *
     * @since 5.0
     */
    const STORE_URL = 'http://bulkwp.com';

    /**
     * Check license
     *
     * @since  5.0
     * @static
     * @param  string $addon   Addon name
     * @param  string $license The license code
     * @return bool|array      False if request fails, API response otherwise
     */
    public static function check_license( $addon, $license ) {
        $api_params = array(
            'edd_action'=> 'check_license',
            'license' 	=> trim( $license ),
            'item_name' => urlencode( $addon ),
			'url'       => home_url()
        );

        $response = self::call_edd_api( $api_params );

        //TODO Encapsulate the below code into a separate function
        if ( $response && isset( $response->license ) ) {
            if ( 'valid' == $response->license ) {
                return array(
                    'license'    => $license,
                    'validity'   => 'valid',
                    'expires'    => $response->expires,
                    'addon-name' => $response->item_name
                );
            } elseif ( 'invalid' == $response->license ) {
                return array(
                    'validity'   => 'invalid'
                );
            }
        }

        return FALSE;
    }

    /**
     * Activate license
     *
     * @since  5.0
     * @static
     * @param  string    $addon   The addon that needs to be activated
     * @param  string    $license The license code
     * @return bool|array         False if request fails, License info otherwise
     */
    public static function activate_license( $addon, $license ) {
        $api_params = array(
            'edd_action'=> 'activate_license',
            'license' 	=> trim( $license ),
            'item_name' => urlencode( $addon ),
			'url'       => home_url()
        );

        $response = self::call_edd_api( $api_params );

        if ( $response && isset( $response->license ) ) {
            if ( 'valid' == $response->license ) {
                return array(
                    'license'    => $license,
                    'validity'   => 'valid',
                    'expires'    => $response->expires,
                    'addon-name' => $response->item_name
                );
            } elseif ( 'invalid' == $response->license ) {
                return array(
                    'validity'   => 'invalid'
                );
            }
        }

        return FALSE;
    }

    /**
     * Deactivate License
     *
     * @since  5.0
     * @static
     * @param  string $addon   The addon that needs to be deactivated
     * @param  string $license The license code
     * @return bool            True if deactivated, False otherwise
     */
    public static function deactivate_license( $addon, $license ) {
        $api_params = array(
            'edd_action'=> 'deactivate_license',
            'license' 	=> trim( $license ),
            'item_name' => urlencode( $addon ),
			'url'       => home_url()
        );

        $response = self::call_edd_api( $api_params );

        if ( $response && isset( $response->license ) ) {
            if ( 'deactivated' == $response->license ) {
                return TRUE;
            }
        }

        return FALSE;
    }

    /**
     * Call the EDD API
     *
     * @since  5.0
     * @static
     * @access private
     * @param  array      $api_params   Parameters for API
     * @return bool|array $license_data False if request fails, API response otherwise
     */
    private static function call_edd_api( $api_params ) {
        $response = wp_remote_get( add_query_arg( $api_params, self::STORE_URL ), array( 'timeout' => 15, 'sslverify' => false ) );

        // make sure the response came back okay
        if ( is_wp_error( $response ) ) {
            return false;
        }

        $license_object = json_decode( wp_remote_retrieve_body( $response ) );
        return $license_object;
    }
}
?>
