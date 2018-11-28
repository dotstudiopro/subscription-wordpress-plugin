<?php

if (!class_exists("Subscription_Listing_Template")) {

    /**
     * Subscription Listing Class
     */
    class Subscription_Listing_Template {

        public function locate_template($template_file, $parameters = array()) {
            if (!empty($parameters)) {
                extract($parameters);
            }

            if (file_exists(get_stylesheet_directory() . "/page-templates/" . $template_file . ".php")) { // check for child theme template file
                include( get_stylesheet_directory() . "/page-templates/" . $template_file . ".php" );
            } elseif (file_exists(get_template_directory() . "/page-templates/" . $template_file . ".php")) { // check for theme template file
                include( get_template_directory() . "/page-templates/" . $template_file . ".php" );
            } elseif (file_exists(plugin_dir_path(__DIR__) . "page-templates/" . $template_file . ".php")) { // include default template
                include( plugin_dir_path(__DIR__) . "page-templates/" . $template_file . ".php" );
            } else {
                echo "Your Subscription Listings plugin is missing the " . sanitize_text_field($template_file) . " file template, please upload a fresh copy of the plugin to fix this.<br>\n";
            }

            return;
        }

    }

}