<?php
/**
 * Created by PhpStorm.
 * User: smp
 * Date: 18/06/18
 * Time: 03:18 PM
 */

class Donate_Visa_DVSMP_Plugin
{
    /**
     * Filepath of main plugin file.
     *
     * @var string
     */
    public $file;
    /**
     * Plugin version.
     *
     * @var string
     */
    public $version;
    /**
     * Absolute plugin path.
     *
     * @var string
     */
    public $plugin_path;
    /**
     * Absolute plugin URL.
     *
     * @var string
     */
    public $plugin_url;
    /**
     * Absolute path to plugin includes dir.
     *
     * @var string
     */
    public $includes_path;
    /**
     * @var bool
     */
    private $_bootstrapped = false;
    /**
     * @var string
     */
    public $assets;

    public function __construct($file, $version, $name)
    {
        $this->file = $file;
        $this->version = $version;
        $this->name = $name;

        $this->plugin_path   = trailingslashit( plugin_dir_path( $this->file ) );
        $this->plugin_url    = trailingslashit( plugin_dir_url( $this->file ) );
        $this->includes_path = $this->plugin_path . trailingslashit( 'includes' );
        $this->assets = $this->plugin_url . trailingslashit( 'assets');

        add_filter( 'plugin_action_links_' . plugin_basename( $this->file), array( $this, 'plugin_action_links' ) );
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
        add_action( 'wp_ajax_donate_visa_dvsmp',array($this,'donate_visa_dvsmp_ajax'));
        add_action( 'wp_ajax_nopriv_donate_visa_dvsmp',array($this,'donate_visa_dvsmp_ajax'));
    }


    public function run_donate_visa()
    {
        try{
            if ($this->_bootstrapped){
                throw new Exception( __( 'Donate visa can only be called once', 'donate-visa'));
            }
            $this->_run();
            $this->_bootstrapped = true;
        }catch (Exception $e){
            if ( is_admin() && ! defined( 'DOING_AJAX' ) ) {
                do_action('notices_donate_visa_dvsmp', 'Donate Visa: ' . $e->getMessage());
            }
        }
    }

    protected function _run()
    {
        $this->_load_handlers();
    }

    protected function _load_handlers()
    {
        require_once ($this->includes_path . 'class-donate-visa-dvsmp-admin.php');
        require_once ($this->includes_path . 'class-donate-visa-dvsmp-shortcode.php');
        $this->admin = new Donate_Visa_DVSMP_Admin();
        $this->shortcode = new Donate_Visa_DVSMP_Shortcode();
    }

    public function plugin_action_links($links)
    {
        $plugin_links = array();
        $plugin_links[] = '<a href="'.admin_url( 'admin.php?page=config-donatevisa').'">' . esc_html__( 'Settings', 'donate-visa' ) . '</a>';
        return array_merge( $plugin_links, $links );
    }

    public function enqueue_scripts()
    {

        if ($this->enviroment()){
            $urlSrcVisa = 'https://assets.secure.checkout.visa.com/
checkout-widget/resources/js/integration/v1/sdk.js';
        }else{
            $urlSrcVisa = 'https://sandbox-assets.secure.checkout.visa.com/
checkout-widget/resources/js/integration/v1/sdk.js';
        }

        wp_enqueue_style('donate_visa_dvsmp_css', $this->plugin_url . 'assets/css/donate-visa-dvsmp.css', array(), $this->version, null);
        wp_enqueue_script( 'donate_visa_src', $urlSrcVisa, array( 'jquery' ), $this->version, true );
        wp_enqueue_script( 'donate_visa_dvsmp', $this->plugin_url . 'assets/js/donate-visa-dvsmp.js', array( 'jquery' ), $this->version, true );
        wp_localize_script( 'donate_visa_dvsmp', 'donatevisadvsmp', array(
            'ajaxurl' => admin_url( 'admin-ajax.php' ),
            'apikey' => get_option('donate-visa-apikey-dvsmp'),
            'currency' => get_option('donate-visa-currency-dvsmp'),
            'locale' => get_locale(),
            'apikeyMsj' => __('The api key is required', 'donate-visa'),
            'successMsj' => __('The payment has been successful', 'donate-visa'),
            'cancelMsj' => __('You have canceled the payment', 'donate-visa'),
            'errorMsj' => __('Error when making the payment, please try again', 'donate-visa')
        ) );

    }

    public function donate_visa_dvsmp_ajax()
    {
        if (!empty($_POST)){

            if(!empty($_POST['donate-visa-enviroment-dvsmp'])){
                update_option('donate-visa-enviroment-dvsmp', sanitize_text_field($_POST['donate-visa-enviroment-dvsmp']));
                update_option('donate-visa-currency-dvsmp', sanitize_text_field($_POST['donate-visa-currency-dvsmp']));
                update_option('donate-visa-apikey-dvsmp', sanitize_text_field($_POST['donate-visa-apikey-dvsmp']));
            }

            if (!empty($_POST['company'])){

                $payments = get_option('donate-visa-dvsmp-payments');
                $transaction = array(array('name' => sanitize_text_field($_POST['name']), 'company' => sanitize_text_field($_POST['company']), 'price' => sanitize_text_field($_POST['price']), 'currencyCode' => sanitize_text_field($_POST['currencyCode']),  'email' => sanitize_text_field($_POST['email'])));

                if (empty($payments)){
                    update_option('donate-visa-dvsmp-payments', $transaction);
                }else{
                   $array = array_merge($payments, $transaction);
                    update_option('donate-visa-dvsmp-payments', $array);
                }

            }
        }
        die();
    }


    public function nameClean($domain = false)
    {
        $name = ($domain) ? str_replace(' ', '-', $this->name)  : str_replace(' ', '', $this->name);
        return strtolower($name);
    }

    public function enviroment()
    {
        $enviroment = get_option('donate-visa-enviroment-dvsmp');
        $enviroment = (empty($enviroment) || $enviroment == 'sandbox') ? false : true;

        return $enviroment;
    }
}