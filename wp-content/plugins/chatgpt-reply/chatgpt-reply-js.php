<?php
// Add JavaScript code to handle ChatGPT reply
function add_chatgpt_js_to_comment_page() {
	$openai_api_key = get_option( 'openai_api_key' );
	$max_tokens = empty(get_option( 'openai_max_tokens' )) ? 100 : get_option( 'openai_max_tokens' );
	$temperature = empty(get_option( 'openai_temperature' )) ? 0.5 : get_option( 'openai_temperature' );
    ?>
    <script>
    jQuery(document).ready(function($) {
        // Add click handler to "Reply with ChatGPT" button
        $('.openai-reply').click(function() {
			if(!"<?php echo esc_attr( $openai_api_key ); ?>")
			{
				alert("OpenAI API Key is not set up. \nPlease go to WordPress admin ChatGPT Settings page.\nFollow instructions to set up OpenAI API Key.")
			}
			else{
				var commentId = $(this).closest('tr').attr('id').replace('comment-', '');
				$('#comment-' + commentId + ' .row-actions .reply button').click();
				openai_reply(commentId);
			}
            return false;
        });
		
		function openai_reply(comment_id) {
			
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
