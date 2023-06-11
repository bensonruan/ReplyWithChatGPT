<?php
// Add JavaScript code to handle AI reply
function aireply_add_js_to_comment_page() {
	$openai_api_key = get_option( 'openai_api_key' );
	$max_tokens = empty(get_option( 'openai_max_tokens' )) ? 100 : get_option( 'openai_max_tokens' );
	$temperature = empty(get_option( 'openai_temperature' )) ? 0.5 : get_option( 'openai_temperature' );
	$model = get_option( 'openai_model' );
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
				aireply_request_openai_api(commentId);
			}
            return false;
        });
		
		function aireply_request_openai_api(comment_id) {
			
			rowData = $('#inline-'+comment_id);
			comment_text = 'Reply to comment: ' + $('textarea.comment', rowData).val();
			editRow = $('#replyrow');
			$( '#replysubmit .spinner' ).addClass( 'is-active' );
			apiKey = "<?php echo esc_attr( $openai_api_key ); ?>";	
			model = "<?php echo esc_attr( $model ); ?>";
			apiurl = "";
			data = "";
			if(model.includes("gpt")){
				apiurl = "https://api.openai.com/v1/chat/completions"
				data = JSON.stringify({
				  "model": model,
				  "messages": [
								{
								  "role": "system", 
								  "content": "Act like you are a wordpress website admin, and you are replying comments"
								},
								{
								  "role": "user", 
								  "content": comment_text
								}
							  ],
				  "max_tokens": <?php echo esc_attr( $max_tokens ); ?>,
				  "temperature": <?php echo esc_attr( $temperature ); ?>
				})
			}
			else {
				apiurl = "https://api.openai.com/v1/completions";
				data = JSON.stringify({
				  "model": model,
				  "prompt":  comment_text,
				  "max_tokens": <?php echo esc_attr( $max_tokens ); ?>,
				  "temperature": <?php echo esc_attr( $temperature ); ?>
				})
			}
			$.ajax({
				type: "POST",
				url: apiurl,
				headers: {
				  "Content-Type": "application/json",
				  "Authorization": "Bearer " + apiKey
				},
				data: data,
				success: function(response) {
				  var choices = response.choices;
				  if( choices.length > 0) {
					var choice = choices[0];
					var reply_text = ""
					if(model.includes("gpt")){
						reply_text = choice.message.content;
					}else{
						reply_text = choice.text;
					}
					if (reply_text && reply_text.startsWith("\n\n")) {
						 reply_text = reply_text.replace(/^\n+/, "");
					}
					$('#replycontent', editRow).val( reply_text );
					$( '#replysubmit .spinner' ).removeClass( 'is-active' );
				  }
				},
				error: function (request, status, error) {
					var apiError = request.responseJSON.error.message;
					alert("Reply with ChatGPT not working due to error below\nOpenAI API error : \n"+apiError);
					$( '#replysubmit .spinner' ).removeClass( 'is-active' );
				}
			});
		}
    });

    
    </script>
    <?php
}
add_action('admin_footer-edit-comments.php', 'aireply_add_js_to_comment_page');
