<?php
/*
Plugin Name: ChatGPT Reply
Description: Adds a "Reply with ChatGPT" opion to the wp-admin comment page. call OpenAI API to auto generate text to reply 
Version: 1.0
Author: Benson Ruan
*/

// Define plugin settings page
function chatgpt_reply_settings() {
    add_menu_page(
        'ChatGPT Reply Settings', // Page title
        'ChatGPT Reply', // Menu title
        'manage_options', // Capability required to access the page
        'openai-comment-reply-settings', // Menu slug
        'openai_comment_reply_settings_page', // Function to render the page
        'dashicons-admin-generic' // Icon URL or icon class name
    );
}
add_action('admin_menu', 'chatgpt_reply_settings');

// Define settings page HTML
function openai_comment_reply_settings_page() {
  if ( ! current_user_can( 'manage_options' ) ) {
    wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
  }

  $openai_api_key = get_option( 'openai_api_key' );
  $max_tokens = get_option( 'openai_max_tokens' );
  $temperature = get_option( 'openai_temperature' );
  ?>
  <div class="wrap">
    <h1>ChatGPT Reply Settings</h1>
    <form method="post" action="options.php">
      <?php settings_fields( 'openai_settings' ); ?>
      <?php do_settings_sections( 'openai_settings' ); ?>
      <table class="form-table">
        <tr valign="top">
          <th scope="row">OpenAI API Key</th>
          <td><input type="text" name="openai_api_key" class="regular-text" value="<?php echo esc_attr( $openai_api_key ); ?>" /></td>
        </tr>
		<tr valign="top">
          <th scope="row">Max Tokens</th>
          <td><input type="text" name="openai_max_tokens" class="small-text" value="<?php echo esc_attr( $max_tokens ); ?>" /></td>
        </tr>
		<tr valign="top">
          <th scope="row">Temperature</th>
          <td><input type="text" name="openai_temperature" class="small-text" value="<?php echo esc_attr( $temperature ); ?>" /></td>
        </tr>
      </table>
      <?php submit_button(); ?>
    </form>
  </div>
  <?php
}

// Define plugin settings
function openai_settings_init() {
  register_setting( 'openai_settings', 'openai_api_key' );
  register_setting( 'openai_settings', 'openai_max_tokens' );
  register_setting( 'openai_settings', 'openai_temperature' );
}
add_action( 'admin_init', 'openai_settings_init' );

// Add button to comment row actions
function add_chatgpt_button_to_comment_row_actions($actions, $comment) {
    $actions['chatgpt_reply'] = '<a href="#" class="openai-reply">Reply with ChatGPT</a>';
    return $actions;
}
add_filter('comment_row_actions', 'add_chatgpt_button_to_comment_row_actions', 10, 2);

// Add JavaScript code to handle ChatGPT reply
function add_chatgpt_js_to_comment_page() {
	$openai_api_key = get_option( 'openai_api_key' );
	$max_tokens = get_option( 'openai_max_tokens' );
	$temperature = get_option( 'openai_temperature' );
    ?>
    <script>
    jQuery(document).ready(function($) {
        // Add click handler to "Reply with ChatGPT" button
        $('.openai-reply').click(function() {
            // Toggle Reply input
			var commentId = $(this).closest('tr').attr('id').replace('comment-', '');
			$('#comment-' + commentId + ' .row-actions .reply button').click();
            openai_reply(commentId);
            return false;
        });
		
		function openai_reply(comment_id) {
			// Send the comment text to OpenAI's API using AJAX
			rowData = $('#inline-'+comment_id);
			comment_text = $('textarea.comment', rowData).val();
			editRow = $('#replyrow');
			$( '#replysubmit .spinner' ).addClass( 'is-active' );
			apiKey = "<?php echo esc_attr( $openai_api_key ); ?>";
			$.ajax({
				type: "POST",
				url: "https://api.openai.com/v1/completions",
				headers: {
				  "Content-Type": "application/json",
				  "Authorization": "Bearer " + apiKey
				},
				data: JSON.stringify({
				  "model": "text-davinci-003",
				  "prompt": 'Reply to comment: ' + comment_text,
				  "max_tokens": <?php echo esc_attr( $max_tokens ); ?>,
				  "temperature": <?php echo esc_attr( $temperature ); ?>
				}),
				success: function(response) {
				  var choices = response.choices;
				  if( choices.length > 0) {
					var choice = choices[0];
					var reply_text = choice.text;
					if (reply_text && reply_text.startsWith("\n\n")) {
						 reply_text = reply_text.replace(/^\n+/, "");
					}
					$('#replycontent', editRow).val( reply_text );
					$( '#replysubmit .spinner' ).removeClass( 'is-active' );
				  }
				}
		    });
		}
    });

    
    </script>
    <?php
}
add_action('admin_footer-edit-comments.php', 'add_chatgpt_js_to_comment_page');
