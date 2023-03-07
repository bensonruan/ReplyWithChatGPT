<?php
/*
Plugin Name: ChatGPT Reply
Description: Adds a "Reply with ChatGPT" opion to the wp-admin comment page. call OpenAI API to auto generate text to reply 
Version: 1.0
Author: Benson Ruan
Author URI: https://bensonruan.com/
License: GPLv2 or later
*/

define( 'CHATGPT_REPLY__PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

require_once( CHATGPT_REPLY__PLUGIN_DIR . 'chatgpt-reply-js.php' );
require_once( CHATGPT_REPLY__PLUGIN_DIR . 'chatgpt-reply-option.php' );
require_once( CHATGPT_REPLY__PLUGIN_DIR . 'chatgpt-reply-settings.php' );