<?php
/*
Plugin Name: Donate Visa
Description: Donation form with Visa payment method.
Version: 1.0.0
Author: Saul Morales Pacheco
Author URI: https://saulmoralespa.com
License: GNU General Public License v3.0
License URI: http://www.gnu.org/licenses/gpl-3.0.html
Domain Path: /languages/
*/

if (!defined( 'ABSPATH' )) exit;
if(!defined('DONATE_VISA_DVSMP_VERSION')){
    define('DONATE_VISA_DVSMP_VERSION', '1.0.0');
}

add_action('plugins_loaded','donate_visa_dvsmp_init',0);

function donate_visa_dvsmp_init(){

    load_plugin_textdomain('donate-visa', FALSE, dirname(plugin_basename(__FILE__)) . '/languages');

    if (!requeriments_donate_visa_dvsmp()){
        return;
    }

    donate_visa_dvsmp()->run_donate_visa();

    if(get_option('donate_visa_dvsmp_activation_redirect', false)){
        delete_option('donate_visa_dvsmp_activation_redirect');
        wp_redirect(admin_url('admin.php?page=config-donatevisa'));
    }

}

add_action('notices_donate_visa_dvsmp', 'notices_donate_visa_dvsmp_action', 10, 1);

function notices_donate_visa_dvsmp_action($notice){
    ?>
    <div class="error notice">
        <p><?php echo $notice; ?></p>
    </div>
    <?php
}

function requeriments_donate_visa_dvsmp(){

    if ( version_compare( '5.6.0', PHP_VERSION, '>' ) ) {
        if ( is_admin() && ! defined( 'DOING_AJAX' ) ) {
            $php = __( 'Donate Visa: Requires php version 5.6.0 or higher.', 'donate-visa' );
            do_action('notices_donate_visa_dvsmp', $php);
        }
        return false;
    }
    return true;
}

function donate_visa_dvsmp()
{
    static $plugin;
    if (!isset($plugin)){
        require_once ('includes/class-donate-visa-dvsmp-plugin.php');
        $plugin = new Donate_Visa_DVSMP_Plugin(__FILE__,DONATE_VISA_DVSMP_VERSION, 'Donate visa');
    }
    return $plugin;
}

function donate_visa_dvsmp_activation(){
    add_option('donate_visa_dvsmp_activation_redirect', true);
}

register_activation_hook(__FILE__,'donate_visa_dvsmp_activation');