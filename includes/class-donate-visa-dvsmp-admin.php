<?php
/**
 * Created by PhpStorm.
 * User: smp
 * Date: 18/06/18
 * Time: 05:03 PM
 */

class Donate_Visa_DVSMP_Admin
{
    public function __construct()
    {
        $this->name = donate_visa_dvsmp()->name;
        $this->plugin_url = donate_visa_dvsmp()->plugin_url;
        $this->version = donate_visa_dvsmp()->plugin_url;
        $this->assets = donate_visa_dvsmp()->assets;
        add_action('admin_menu', array($this, 'donate_visa_dvsmp_menu'));
    }

    public function donate_visa_dvsmp_menu()
    {
        add_menu_page($this->name, $this->name, 'manage_options', 'menus' . donate_visa_dvsmp()->nameClean(), array($this,'menu' . donate_visa_dvsmp()->nameClean()), $this->assets .'img/favicon.png');
        $config = add_submenu_page('menus' . donate_visa_dvsmp()->nameClean(), __('Configuration', 'donate-visa'), __('Configuration', 'donate-visa'), 'manage_options', 'config-' . donate_visa_dvsmp()->nameClean(), array($this,'configInit'));
        $donations = add_submenu_page('menus' . donate_visa_dvsmp()->nameClean(), __('Donations', 'donate-visa'), __('Donations', 'donate-visa'), 'manage_options', 'donate-' . donate_visa_dvsmp()->nameClean(), array($this,'contentDoante'));
        remove_submenu_page('menus' . donate_visa_dvsmp()->nameClean(), 'menus' . donate_visa_dvsmp()->nameClean());
        add_action( 'admin_footer', array( $this, 'enqueue_scripts_admin' ) );
    }

    public function configInit()
    {
        $enviroment = get_option('donate-visa-enviroment-dvsmp');
        $apikey = get_option('donate-visa-apikey-dvsmp');
        $currency = get_option('donate-visa-currency-dvsmp');

        ?>
        <div class="wrap about-wrap">
            <form id="donate-visa-config">
                <table>
                    <tbody>
                    <tr>
                        <th><?php _e('Environment', 'donate-visa'); ?></th>
                        <td>
                            <select name="donate-visa-enviroment-dvsmp" id="donate-visa-enviroment-dvsmp">
                                <option value="sandbox" <?php if ($enviroment == 'sandbox'){ echo 'selected'; } ?>><?php _e('Sandbox','donate-visa'); ?></option>
                                <option value="live" <?php if ($enviroment == 'live'){ echo 'selected'; } ?>><?php _e('Live','donate-visa'); ?></option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th><?php _e('Currency', 'donate-visa'); ?></th>
                        <td>
                            <select name="donate-visa-currency-dvsmp" id="donate-visa-enviroment-dvsmp">
                                <option value="ARS" <?php if ($currency == 'ARS'){ echo 'selected'; } ?>><?php _e('Argentine peso','donate-visa'); ?></option>
                                <option value="AUD" <?php if ($currency == 'AUD'){ echo 'selected'; } ?>><?php _e('Australian dollar','donate-visa'); ?></option>
                                <option value="BRL" <?php if ($currency == 'BRL'){ echo 'selected'; } ?>><?php _e('Brazilian real','donate-visa'); ?></option>
                                <option value="CAD" <?php if ($currency == 'CAD'){ echo 'selected'; } ?>><?php _e('Canadian dollar','donate-visa'); ?></option>
                                <option value="CNY" <?php if ($currency == 'CNY'){ echo 'selected'; } ?>><?php _e('Chinese Yuan','donate-visa'); ?></option>
                                <option value="CLP" <?php if ($currency == 'CLP'){ echo 'selected'; } ?>><?php _e('Chilean peso','donate-visa'); ?></option>
                                <option value="COP" <?php if ($currency == 'COP'){ echo 'selected'; } ?>><?php _e('Colombian peso','donate-visa'); ?></option>
                                <option value="CFP" <?php if ($currency == 'CFP'){ echo 'selected'; } ?>><?php _e('French franc','donate-visa'); ?></option>
                                <option value="HKD" <?php if ($currency == 'HKD'){ echo 'selected'; } ?>><?php _e('Hong Kong dollar','donate-visa'); ?></option>
                                <option value="INR" <?php if ($currency == 'INR'){ echo 'selected'; } ?>><?php _e('Indian Rupee','donate-visa'); ?></option>
                                <option value="EUR" <?php if ($currency == 'EUR'){ echo 'selected'; } ?>><?php _e('Ireland euro','donate-visa'); ?></option>
                                <option value="MYR" <?php if ($currency == 'MYR'){ echo 'selected'; } ?>><?php _e('Malaysian Ringgit','donate-visa'); ?></option>
                                <option value="MXN" <?php if ($currency == 'MXN'){ echo 'selected'; } ?>><?php _e('Mexican peso','donate-visa'); ?></option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                            <th><?php _e('Apikey', 'donate-visa'); ?></th>
                            <td>
                                <input type="text" name="donate-visa-apikey-dvsmp" value="<?php echo $apikey; ?>">
                            </td
                    </tr>
                    </tbody>
                </table>
                <?php submit_button(); ?>
            </form>
        </div>
        <?php
    }


    public function contentDoante()
    {
        $payments = get_option('donate-visa-dvsmp-payments');
        ?>
        <div class="wrap about-wrap">
        <?php
        if (empty($payments)) {
            ?>
            <div class="about-text">
                <h2><?php _e('No donations made to show', 'donate-visa'); ?></h2>
            </div>
            <?php
        } else {
            ?>
            <table class="widefat fixed" cellspacing="0">
                <thead>
                <tr>
                    <th><?php _e('Name', 'donate-visa'); ?></th>
                    <th><?php _e('Company', 'donate-visa'); ?></th>
                    <th><?php _e('Email', 'donate-visa'); ?></th>
                    <th><?php _e('Price', 'donate-visa'); ?></th>
                    <th><?php _e('Currency', 'donate-visa'); ?></th>
                </tr>
                </thead>
                <?php
                for ($i = 0; $i < count($payments); ++$i) {
                    echo '<tr class="alternate">
<th>' . $payments[$i]['name'] . '</th>
<th>' . $payments[$i]['company'] . '</th>
<th>' . $payments[$i]['email'] . '</th>
<th>' . $payments[$i]['price'] . '</th>
<th>' . $payments[$i]['currencyCode'] . '</th>
</tr>';
                }
                ?>
            </table>
            </div>
            <?php

        }
    }

    public function enqueue_scripts_admin()
    {
        wp_enqueue_script( 'donate-visadvsmp', $this->plugin_url . 'assets/js/config.js', array( 'jquery' ), $this->version, true );
    }
}