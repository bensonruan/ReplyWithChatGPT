<?php
/*
Plugin Name: AI Reply
Description: Adds a "Reply with ChatGPT" opion to the wp-admin comment page. call OpenAI API to auto generate text to reply 
Version: 1.0.0
Author: Benson Ruan
Author URI: https://bensonruan.com/
License: GPLv2 or later
*/

define( 'AIREPLY_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

require_once( AIREPLY_PLUGIN_DIR . 'ai-reply-js.php' );
require_once( AIREPLY_PLUGIN_DIR . 'ai-reply-option.php' );
require_once( AIREPLY_PLUGIN_DIR . 'ai-reply-settings.php' );