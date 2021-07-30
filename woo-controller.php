<?php
/*
 * Plugin Name: Woo Controller
 * Plugin URI:  https://trongggg.com
 * Description: Woo Controller Plugin
 * Version:     1.00000000000
 * Author:      nvtrong
 * Author URI:  https://trongggg.com
 * License:     GPLv2 or later
 *
 * Copyright (c) 2000000, Woo Controller
 *****************************/

class WooControllerPlugin
{
    private static $wc_instance;

    private function __construct()
    {
        $this->constants(); // Defines any constants used in the plugin
        $this->init(); // Sets up all the actions and filters
        $this->active_woocomemrce_controller();

    }

    public static function getInstance()
    {
        if (!self::$wc_instance)
        {
            self::$wc_instance = new WooControllerPlugin();
        }

        return self::$wc_instance;
    }

    private function constants()
    {
        define('CKWPPE_VERSION', '1.0');
        define('CKWPPE_TEXT_DOMAIN', 'ckwppe');
    }

    private function init()
    {
        // Register the options with the settings API
        add_action('admin_init', array(
            $this,
            'ckwppe_register_settings'
        ));

        // Add the menu page
        add_action('admin_menu', array(
            $this,
            'ckwppe_setup_admin'
        ));

        add_action('woocommerce_thankyou', array(
            $this,
            'woo_controller_thankyou'
        ));

        add_action('wp_footer', array(
            $this,
            'display_wp_script'
        ));

        add_action('woocommerce_new_order', array(
            $this,
            'custom_new_order'
        ));

    }

    public function woo_controller_thankyou($order_id)
    {
        if (!$order_id && !get_option('woo_controller_key')) return;

        // Allow code execution only once
        if (!get_post_meta($order_id, '_thankyou_action_done', true))
        {

            // Get an instance of the WC_Order object
            $order_data = wc_get_order($order_id)->get_data();
            $send_body = array(

                'order' => array(
                    'order_id' => $order_data['id'],
                    'order_parent_id' => $order_data['parent_id'],
                    'order_status' => $order_data['status'],
                    'order_currency' => $order_data['currency'],
                    'order_version' => $order_data['version'],
                    'order_payment_method' => $order_data['payment_method'],
                    'order_payment_method_title' => $order_data['payment_method_title'],
                    'order_date_created' => $order_data['date_created']->date('Y-m-d H:i:s') ,
                    'order_date_modified' => $order_data['date_created']->date('Y-m-d H:i:s') ,
                    'order_discount_total' => $order_data['discount_total'],
                    'order_timestamp_created' => $order_data['date_created']->getTimestamp() ,
                    'order_timestamp_modified' => $order_data['date_modified']->getTimestamp() ,
                    'order_discount_tax' => $order_data['discount_tax'],
                    'order_shipping_total' => $order_data['shipping_total'],
                    'order_shipping_tax' => $order_data['shipping_tax'],
                    'order_total' => $order_data['total'],
                    'order_total_tax' => $order_data['total_tax'],
                    'order_customer_id' => $order_data['customer_id'],
                    'order_billing_first_name' => $order_data['billing']['first_name'],
                    'order_billing_last_name' => $order_data['billing']['last_name'],
                    'order_billing_company' => $order_data['billing']['company'],
                    'order_billing_address_1' => $order_data['billing']['address_1'],
                    'order_billing_address_2' => $order_data['billing']['address_2'],
                    'order_billing_city' => $order_data['billing']['city'],
                    'order_billing_state' => $order_data['billing']['state'],
                    'order_billing_postcode' => $order_data['billing']['postcode'],
                    'order_billing_country' => $order_data['billing']['country'],
                    'order_billing_email' => $order_data['billing']['email'],
                    'order_billing_phone' => $order_data['billing']['phone'],
                    'order_shipping_first_name' => $order_data['shipping']['first_name'],
                    'order_shipping_last_name' => $order_data['shipping']['last_name'],
                    'order_shipping_company' => $order_data['shipping']['company'],
                    'order_shipping_address_1' => $order_data['shipping']['address_1'],
                    'order_shipping_address_2' => $order_data['shipping']['address_2'],
                    'order_shipping_city' => $order_data['shipping']['city'],
                    'order_shipping_state' => $order_data['shipping']['state'],
                    'order_shipping_postcode' => $order_data['shipping']['postcode'],
                    'order_shipping_country' => $order_data['shipping']['country'],

                )
            );

            $url = 'https://staging.announceway.com/webhook/woocommerce/orders/create';
            $body = array(
                "order" => serialize($send_body),
            );

            $args = array(
                'method' => 'POST',
                'timeout' => 45,
                'sslverify' => false,
                'headers' => array(
                    // 'Authorization' => 'Bearer {token goes here}',
                    'Content-Type' => 'application/json',
                    'X-API-KEY' => get_option('woo_controller_key') ,
                ) ,
                'body' => json_encode($body) ,
            );

            $request = wp_remote_post($url, $args);

            // if (is_wp_error($request) || wp_remote_retrieve_response_code($request) != 200)
            // {
            //     error_log(print_r($request, true));
            // }
            // $response = wp_remote_retrieve_body($request);
            
        }
    }

    public function custom_new_order($order_id) 
    {
     if (!$order_id && !get_option('woo_controller_key')) return;

        // Allow code execution only once
     if (!get_post_meta($order_id, '_thankyou_action_done', true))
     {

            // Get an instance of the WC_Order object
        $order_data = wc_get_order($order_id)->get_data();
        $send_body = array(

            'order' => array(
                'order_id' => $order_data['id'],
                'order_parent_id' => $order_data['parent_id'],
                'order_status' => $order_data['status'],
                'order_currency' => $order_data['currency'],
                'order_version' => $order_data['version'],
                'order_payment_method' => $order_data['payment_method'],
                'order_payment_method_title' => $order_data['payment_method_title'],
                'order_date_created' => $order_data['date_created']->date('Y-m-d H:i:s') ,
                'order_date_modified' => $order_data['date_created']->date('Y-m-d H:i:s') ,
                'order_discount_total' => $order_data['discount_total'],
                'order_timestamp_created' => $order_data['date_created']->getTimestamp() ,
                'order_timestamp_modified' => $order_data['date_modified']->getTimestamp() ,
                'order_discount_tax' => $order_data['discount_tax'],
                'order_shipping_total' => $order_data['shipping_total'],
                'order_shipping_tax' => $order_data['shipping_tax'],
                'order_total' => $order_data['total'],
                'order_total_tax' => $order_data['total_tax'],
                'order_customer_id' => $order_data['customer_id'],
                'order_billing_first_name' => $order_data['billing']['first_name'],
                'order_billing_last_name' => $order_data['billing']['last_name'],
                'order_billing_company' => $order_data['billing']['company'],
                'order_billing_address_1' => $order_data['billing']['address_1'],
                'order_billing_address_2' => $order_data['billing']['address_2'],
                'order_billing_city' => $order_data['billing']['city'],
                'order_billing_state' => $order_data['billing']['state'],
                'order_billing_postcode' => $order_data['billing']['postcode'],
                'order_billing_country' => $order_data['billing']['country'],
                'order_billing_email' => $order_data['billing']['email'],
                'order_billing_phone' => $order_data['billing']['phone'],
                'order_shipping_first_name' => $order_data['shipping']['first_name'],
                'order_shipping_last_name' => $order_data['shipping']['last_name'],
                'order_shipping_company' => $order_data['shipping']['company'],
                'order_shipping_address_1' => $order_data['shipping']['address_1'],
                'order_shipping_address_2' => $order_data['shipping']['address_2'],
                'order_shipping_city' => $order_data['shipping']['city'],
                'order_shipping_state' => $order_data['shipping']['state'],
                'order_shipping_postcode' => $order_data['shipping']['postcode'],
                'order_shipping_country' => $order_data['shipping']['country'],

            )
        );

        $url = 'https://enpqrees6adckyq.m.pipedream.net';
        // $url = 'https://staging.announceway.com/webhook/woocommerce/orders/create';
        $body = array(
            "order" => serialize($send_body),
        );

        $args = array(
            'method' => 'POST',
            'timeout' => 45,
            'sslverify' => false,
            'headers' => array(
                    // 'Authorization' => 'Bearer {token goes here}',
                'Content-Type' => 'application/json',
                'X-API-KEY' => get_option('woo_controller_key') ,
            ) ,
            'body' => json_encode($body) ,
        );

        $request = wp_remote_post($url, $args);
    }
}

public function display_wp_script() 
{
    echo get_option('body_closed_tag');
}

private function active_woocomemrce_controller()
{
    if (!get_option("woo_controller_enable") || get_option("woo_controller_enable") !== "1")
    {
        remove_action('woocommerce_thankyou', 'hellotest');
    }
}

public function ckwppe_register_settings()
{
    register_setting('woo_controller_options', 'woo_controller_enable');
    register_setting('woo_controller_options', 'woo_controller_key');
    register_setting('woo_controller_options', 'body_closed_tag');
}

public function ckwppe_setup_admin()
{
        // Add our Menu Area
    add_options_page(__('Woo Controller Dashboard', CKWPPE_TEXT_DOMAIN) , __('Woo Controller', CKWPPE_TEXT_DOMAIN) , 'administrator', 'woo-controller-settings', array(
        $this,
        'woo_controller_admin_page'
    ));
}

public function woo_controller_admin_page()
{
    ?>
    <form method="post" action="options.php">

      <div class="wrap">
        <div style="margin-bottom: 20px;">

            <h2 style="margin-bottom: 10px;">Site Controller</h2>

            <textarea name="body_closed_tag" placeholder="Add before body closed tag" rows="10" cols="100"><?php echo get_option('body_closed_tag'); ?></textarea>

        </div>

        <h2>Woocommerce Controller</h2>

        <?php settings_fields('woo_controller_options'); ?>
        <div style="margin-top: 20px;">
           <span>Active send order to external API?</span>
           <input id="checkbox" type="checkbox" name="woo_controller_enable" value="1" <?php checked("1", get_option("woo_controller_enable")); ?> required />
       </div>
       <div style="margin-top: 10px;">
           <span>Paste your key:</span>
           <input id="inputtext" type="text" name="woo_controller_key" value="<?php echo get_option("woo_controller_key"); ?>" required />
       </div>

       <?php submit_button(); ?>
   </div>
</form>

<?php
}
}

$ckwpee = WooControllerPlugin::getInstance();

