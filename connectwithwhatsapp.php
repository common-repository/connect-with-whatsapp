<?php

namespace AtlantisConnectWithWhatsApp;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Plugin Name: Connect with WhatsApp.
 * description: Allows you to create a <strong>WhatsApp-button</strong> using a shortcode anywhere on your Page. Makes <strong>connecting</strong> with your customers easy.
 * Version: 1.0
 * Author: Atlantis-Web UG (Haftungsbeschränkt)
 * Author URI: https://atlantis-web.de
 */

$includes = ["admin", "settings"];
foreach ($includes as $includeFileName) {
    require_once __DIR__ . '/includes/connectwithwhatsapp_' . $includeFileName . '.php';
}

use \AtlantisConnectWithWhatsApp\includes\AtlantisConnectWithWhatsApp_Settings as Settings;

class AtlantisConnectWithWhatsApp
{
    public $lantis_admin;

    public function __construct()
    {
        $this->lantis_registerEventsAndScripts();
    }

    public function lantis_registerEventsAndScripts()
    {
        $actions = [
            "admin_menu" => [
                "name" => "whatsappbutton_config_menu",
                "context" => $this,
            ],
            "init" => [
                "name" => "init_whatsappbutton_style",
                "context" => $this,
            ],
            'admin_init' => [
                "name" => "register_whatsappbutton_setting",
                "context" => "\AtlantisConnectWithWhatsApp\includes\AtlantisConnectWithWhatsApp_Admin",
            ],
            'wp_ajax_lantis_get_whatsappbutton_style' => [
                "name" => "get_whatsappbutton_style",
                "context" => $this,
            ],
            'wp_ajax_nopriv_lantis_get_whatsappbutton_style' => [
                "name" => "get_whatsappbutton_style",
                "context" => $this,
            ],
        ];

        // makes adding prefixes easy
        foreach ($actions as $actionName => $function) {
            add_action($actionName, array($function["context"], Settings::$prefix . $function["name"]));
        }

        $shortCodes = [
            "whatsappbutton" => "whatsappbutton_shortcode",
        ];
        foreach ($shortCodes as $shortCodeName => $functionName) {
            add_shortcode($shortCodeName, array($this, Settings::$prefix . $functionName));
        }
    }

    public function lantis_get_whatsappbutton_style()
    {
        // make it look like an css file
        header("Content-Type: text/css; charset=UTF-8");
        $textDecoration = get_option(Settings::$optionPrefix . 'textDecoration');
        $fontFamily = get_option(Settings::$optionPrefix . 'fontFamily');
        $fontSize = get_option(Settings::$optionPrefix . 'fontSize');
        $borderWidth = get_option(Settings::$optionPrefix . 'borderWidth');
        $borderColor = get_option(Settings::$optionPrefix . 'borderColor');
        $backgroundColor = get_option(Settings::$optionPrefix . 'backgroundColor');
        $textColor = get_option(Settings::$optionPrefix . 'textColor');
        $paddingTop = get_option(Settings::$optionPrefix . 'paddingTop');
        $paddingRight = get_option(Settings::$optionPrefix . 'paddingRight');
        $paddingBottom = get_option(Settings::$optionPrefix . 'paddingBottom');
        $paddingLeft = get_option(Settings::$optionPrefix . 'paddingLeft');
        $borderRadius = get_option(Settings::$optionPrefix . 'borderRadius');
        $hoverBackgroundColor = get_option(Settings::$optionPrefix . 'hoverBackgroundColor');
        $hoverColor = get_option(Settings::$optionPrefix . 'hoverColor');
        $fontWeight = get_option(Settings::$optionPrefix . 'fontWeight');

        echo "." . Settings::$cssButtonClassName . ",
            ." . Settings::$cssButtonClassName . ":visited,
            ." . Settings::$cssButtonClassName . ":focus {
                text-decoration: $textDecoration !important;
                font-family: $fontFamily !important;
                font-size: " . $fontSize . "px !important;
                font-weight: $fontWeight !important;
                border: " . $borderWidth . "px solid $borderColor !important;
                background-color: $backgroundColor !important;
                color: $textColor !important;
                padding: " . $paddingTop . "px " . $paddingRight . "px " . $paddingBottom . "px " . $paddingLeft . "px !important;
                border-radius: " . $borderRadius . "px !important;
                position: relative !important;
                box-shadow: none !important;
            }

            ." . Settings::$cssButtonClassName . ":hover {
                background-color: $hoverBackgroundColor !important;
                color: $hoverColor !important;
            }";
        exit;

    }

    public function lantis_whatsappbutton_config_menu()
    {
        // add menu from other class obj
        add_submenu_page('options-general.php', 'WhatsApp-button Config', 'WhatsApp-button', 'manage_options', 'lantis_whatsappbutton_settings', array("\AtlantisConnectWithWhatsApp\includes\AtlantisConnectWithWhatsApp_Admin", "lantis_whatsappbutton_config"));
    }

    public function lantis_whatsappbutton_shortcode($atts)
    {
        // temp store prefix
        list($message, $telephone, $text) = array_values(shortcode_atts(array(
            'message' => get_option(Settings::$optionPrefix . 'message'),
            'telephone' => get_option(Settings::$optionPrefix . 'telephone'),
            'text' => get_option(Settings::$optionPrefix . 'buttontext'),
        ), $atts));

        // escape everything
        $message = urlencode($message);
        $telephone = str_replace(["+", "-", "(", ")"], "", $telephone);
        return "<a target='_blank' class='" . Settings::$cssButtonClassName . "' href='https://wa.me/$telephone?text=$message'>" . esc_html($text) . "</a>";
    }

    /**
     * use WP default functions to load dynamic css
     */
    public function lantis_init_whatsappbutton_style()
    {
        wp_register_style(Settings::$prefix . 'whatsappbutton_style', admin_url('admin-ajax.php') . '?action=' . Settings::$prefix . 'get_whatsappbutton_style');
        wp_enqueue_style(Settings::$prefix . 'whatsappbutton_style');
    }

}

global $lantisConnectWithWhatsapp;
$lantisConnectWithWhatsapp = new AtlantisConnectWithWhatsApp();
