<?php
defined('ABSPATH') or die('Direct access not allowed.');
?>
<div id="publishpress-future-classic-metabox"></div>

<input type="hidden" name="future_action_enabled" value="<?php echo $enabled ? 1 : 0; ?>" />
<input type="hidden" name="future_action_date" value="<?php echo esc_attr($date); ?>" />
<input type="hidden" name="future_action_action" value="<?php echo esc_attr($action); ?>" />
<input type="hidden" name="future_action_terms" value="<?php echo esc_attr(implode(',', $terms)); ?>" />
<input type="hidden" name="future_action_taxonomy" value="<?php echo esc_attr($taxonomy); ?>" />
<input type="hidden" name="future_action_browser_timezone_offset" value="0" />

<input name="future_action_view" value="classic-metabox" type="hidden" />
<?php
wp_nonce_field('__future_action', '_future_action_nonce');
