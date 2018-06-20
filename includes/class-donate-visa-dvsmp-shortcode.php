<?php
/**
 * Created by PhpStorm.
 * User: smp
 * Date: 19/06/18
 * Time: 09:14 AM
 */

class Donate_Visa_DVSMP_Shortcode
{
    public function __construct()
    {
        add_shortcode( 'donate_visa', array( $this,'donate_visa_dvsmp_shortcode' ));
    }

    public function donate_visa_dvsmp_shortcode()
    {
        $img = donate_visa_dvsmp()->enviroment()  ? 'https://secure.checkout.visa.com/wallet-services-web/xo/button.png'  : 'https://sandbox.secure.checkout.visa.com/wallet-services-web/xo/button.png';

        $html = "<div id='container-form-donate-visa'>
<form id='donate_visa-frontend'>
<h2 id='donate-visa-title'>" . __('Donate your support', 'donate-visa') . "</h2>
<div class='donate-visa-alert'>
</div>
<label class='donate-visa-label' for='donate-visa-price'>" . __('Amount', 'donate-visa') . "</label>
<input type='number' min='20' name='donate-visa-price' id='donate-visa-price' required=''>
<label class='donate-visa-label' for='donate-visa-name-last'>" . __('Name and last name', 'donate-visa') . "</label>
<input type='text' id='donate-visa-name-last' name='donate-visa-name-last' required=''>
<label class='donate-visa-label' for='donate-visa-company'>" . __('Company Name', 'donate-visa') . "</label>
<input type='text' id='donate-visa-company' name='donate-visa-company' required=''>
<label class='donate-visa-label' for='donate-visa-email'>" . __('Email Address', 'donate-visa') . "</label>
<input type='email' id='donate-visa-email' name='donate-visa-email' required=''>
<button type='submit' id='donate-visa-button'>" . __('Donate your support', 'donate-visa') . "</button>
<div class='v-checkout-wrapper' style='display: none;'>
<img
class='v-button' role='button' alt='Visa Checkout'
src='" . $img . "'>
<a class='v-learn v-learn-default' href='#' data-locale='" . get_locale() . "'>" . __('Tell Me More', 'donate-visa') . "</a>
</div>
</form>
</div>";
        return $html;

    }
}