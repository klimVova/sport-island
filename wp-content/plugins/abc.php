<?php
/*
Plugin//Name: ABC
Description: Плагин-шаблон
License:     GPL2
 
ABC is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.
 
ABC is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.
 
You should have received a copy of the GNU General Public License
along with ABC. If not, see https://abc.abc/.
*/

register_activation_hook(__FILE__, 'si_activation');
register_deactivation_hook(__FILE__, 'si_deactivation');
register_uninstall_hook(__FILE__, 'si_delete');

function si_activation()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'si_settings';
    if ($wpdb->get_var("SHOW TABLE LIKE $table_name") !== $table_name) {
        $sql = "CREATE TABLE IF NOT EXISTS `$table_name`(
            `msg_text` text NOT NULL 
        ) CHARSET=utf8;";
        $wpdb->query($sql);
        $wpdb->query("INSERT INTO `$table_name` (`msg_text`) VALUES ('Hello!');");
    }
}

function si_deactivation()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'si_settings';
    $wpdb->query("DELETE FROM $table_name");
}

function si_delete()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'si_settings';
    $wpdb->query("DROP TABLE $table_name");
}

add_action('wp_footer', function () {
    echo '<p class="ABC-plugin"></p>';
});
