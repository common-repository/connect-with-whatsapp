<?php

namespace AtlantisConnectWithWhatsApp\includes;

if (!defined('ABSPATH')) {
    exit;
}

$includes = ["settings"];
foreach ($includes as $includeFileName) {
    require_once __DIR__ . '/connectwithwhatsapp_' . $includeFileName . '.php';
}

use AtlantisConnectWithWhatsApp\includes\AtlantisConnectWithWhatsApp_Settings;

class AtlantisConnectWithWhatsApp_Admin
{
    // settings for whatsapp button styling
    private static $options = [
        "telephone" => [
            "title" => "Default Whatsapp-number",
            "type" => "tel",
            "default" => "+4906666666",
        ],
        "buttontext" => [
            "title" => "Default Text for Button",
            "type" => "text",
            "default" => "Write us on WhatsApp",
        ],
        "message" => [
            "title" => "Default Message to send",
            "type" => "text_long",
            "default" => "Hello there, \n i used the whatsappbutton on your page.\n Please send me more info to your product.\n\n Thanks a lot.",
        ],
        "fontStyleSection" => [
            "title" => "Fontstyle",
            "type" => "section",
        ],
        "textDecoration" => [
            "title" => "Textdecoration",
            "type" => "select",
            "default" => "none",
            "options" => [
                "none",
                "line-through",
                "underline",
                "overline",
            ],
        ],
        "fontFamily" => [
            "title" => "Fontfamily",
            "type" => "text",
            "default" => "Arial",
        ],
        "fontSize" => [
            "title" => "Fontsize (in px)",
            "type" => "number",
            "default" => 15,
        ],
        "fontWeight" => [
            "title" => "Fontweight",
            "type" => "select",
            "default" => 200,
            "options" => [100, 200, 300, 400, 500, 600, 700, 800, 900],
        ],
        "colorStyleSection" => [
            "title" => "ColorStyle",
            "type" => "section",
        ],
        "backgroundColor" => [
            "title" => "Backgroundcolor",
            "type" => "color",
            "default" => "#1fb7a2",
        ],
        "textColor" => [
            "title" => "Textcolor",
            "type" => "color",
            "default" => "#ffffff",
        ],
        "buttonStyleSection" => [
            "title" => "Spacing",
            "type" => "section",
        ],
        "paddingTop" => [
            "title" => "Padding top (in px)",
            "type" => "number",
            "default" => 15,
        ],
        "paddingRight" => [
            "title" => "Padding right (in px)",
            "type" => "number",
            "default" => 15,
        ],
        "paddingBottom" => [
            "title" => "Padding bottom (in px)",
            "type" => "number",
            "default" => 15,
        ],
        "paddingLeft" => [
            "title" => "Padding left (in px)",
            "type" => "number",
            "default" => 15,
        ],
        "borderStyleSection" => [
            "title" => "Borderstyle",
            "type" => "section",
        ],
        "borderColor" => [
            "title" => "Bordercolor",
            "type" => "color",
            "default" => "#1fb7a2",
        ],
        "borderWidth" => [
            "title" => "Borderwidth (in px)",
            "type" => "number",
            "default" => 2,
        ],
        "borderRadius" => [
            "title" => "Borderradius (in px)",
            "type" => "number",
            "default" => 90,
        ],
        "hoverStyleSection" => [
            "title" => "Hoversytle",
            "type" => "section",
        ],
        "hoverBackgroundColor" => [
            "title" => "Backgroundcolor hover",
            "type" => "color",
            "default" => "#1fb7a2",
        ],
        "hoverColor" => [
            "title" => "Textcolor hover",
            "type" => "color",
            "default" => "#ffffff",
        ],
    ];
    private static $prefix = "lantis_whatsappbutton-";

    public static function lantis_register_whatsappbutton_setting()
    {
        // add all options to make them savable
        foreach (self::$options as $option => $optionSettings) {
            register_setting(AtlantisConnectWithWhatsApp_Settings::$optionPrefix . 'group', AtlantisConnectWithWhatsApp_Settings::$optionPrefix . $option);
        }
    }

    public static function lantis_whatsappbutton_config()
    {
        if (!is_admin()) {
            return;
        }
        // generate settings HTML
        $elements = "";
        foreach (self::$options as $name => $optionSettings) {
            $options = [];
            if (isset($optionSettings["options"])) {
                $options = $optionSettings["options"];
            }
            // build rows
            $elements .= "<tr>" . self::getElement(AtlantisConnectWithWhatsApp_Settings::$optionPrefix . $name, $optionSettings["type"], $optionSettings["title"], $optionSettings["default"], $options) . "</tr>";

        }
        self::displaySettings($elements);
    }

    /**
     * Show form, Option elements, xsrf validations and etc.
     */
    private static function displaySettings($elements)
    {
        echo "<div class=\"wrap\">
        <h1>Whatsappbutton Options</h1>
        <form method=\"post\"  action=\"options.php\">";
        settings_fields(AtlantisConnectWithWhatsApp_Settings::$optionPrefix . 'group');
        do_settings_sections(AtlantisConnectWithWhatsApp_Settings::$optionPrefix . 'group');
        echo "<table class=\"form-table\">$elements</table>";
        submit_button();
        echo "</form></div>";
    }

    private static function getElement($name, $type, $title, $default, $options)
    {
        switch ($type) {
            case "text_long":
                return "<th scope=\"row\">$title</th><td><textarea rows=\"6\" cols=\"45\" type=\"text\" name=\"$name\">" . esc_attr(get_option($name, $default)) . "</textarea></td>";
                break;
            case "select":
                $element = "<th scope=\"row\">$title</th><td><select name=\"$name\" style=\"min-width: 159px\">";
                foreach ($options as $option) {
                    $element .= "<option" . (esc_attr(get_option($name, $default)) == $option ? ' selected' : '') . ">$option</option>";
                }
                return $element . "</select></td>";
                break;
            case "section":
                return "<th scope=\"row\" style=\"padding: 0; padding-top: 20px; padding-left: 20px\">$title</th><td style=\"padding: 0;\"></td></tr><tr><th style=\"padding: 0; padding-left: 20px\"><hr></th><td style=\"padding: 0;\"><hr></td>";
                break;
            default:
                return "<th scope=\"row\">$title</th><td><input type=\"$type\" name=\"$name\" value=\"" . esc_attr(get_option($name, $default)) . "\"></td></textarea>";
                break;
        }
    }
}
