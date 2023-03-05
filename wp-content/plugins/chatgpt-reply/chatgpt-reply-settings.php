<?php
// Define plugin settings page
function chatgpt_reply_settings() {
    add_menu_page(
        'Reply with ChatGPT Settings', 
        'ChatGPT Settings', 
        'manage_options', 
        'openai-comment-reply-settings', 
        'openai_comment_reply_settings_page', 
        'dashicons-format-chat' 
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
    <h1>Reply with ChatGPT Settings</h1>
    <form method="post" action="options.php">
      <?php settings_fields( 'openai_settings' ); ?>
      <?php do_settings_sections( 'openai_settings' ); ?>
      <table class="form-table">
        <tr valign="top">
          <th scope="row">OpenAI API Key</th>
          <td>
			<ul style="margin-top:5px">
				<li>Go to the <a href="https://openai.com/product" target="_blank">https://openai.com/product </a></li>
				<li>Click on the “Get started” button.</li>
				<li>Create an account or just Continue with your Google or Microsoft Account</li>
				<li>Once your registration is complete, you will receive an email with instructions on how to activate your account.</li>
				<li>Once your account is activated, log in to the OpenAI API dashboard.</li>
				<li>Click on the “API Keys” tab and then click the “Create new secret key” button.</li>
				<li>Copy the API key and paste it here.</li>
			</ul>
		  <input type="password" required name="openai_api_key" class="regular-text" value="<?php echo esc_attr( $openai_api_key ); ?>" /></td>
        </tr>
		<tr valign="top">
          <th scope="row">Maximum  Words</th>
          <td><input type="number" min="1" max="2048" step="1" name="openai_max_tokens" class="small-text" value="<?php echo esc_attr( $max_tokens ); ?>" />
			Range from 1 to 2048, recommend value of 100 - 500 for generating short replies to comments
		  </td>
        </tr>
		<tr valign="top">
          <th scope="row">Creativity</th>
          <td><input type="number" min="0" max="1" step="0.1" name="openai_temperature" class="small-text" value="<?php echo esc_attr( $temperature ); ?>" />
			Range from 0 to 1, lower value (e.g. 0.1 to 0.3) for more conservative and predictable, higher value (e.g. 0.7 to 1.0).) for more creative and unpredictable
		  </td>
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
