<?php
// Add button to comment row actions
function aireply_add_button_to_comment_row_actions($actions, $comment) {
    $actions['chatgpt_reply'] = '<a href="#" class="openai-reply">Reply with ChatGPT</a>';
    return $actions;
}
add_filter('comment_row_actions', 'aireply_add_button_to_comment_row_actions', 10, 2);