<div id="lf_form_container">
    <form method="post" action="<?php echo (isset($_SERVER['REQUEST_URI']) ? esc_url(sanitize_text_field(wp_unslash($_SERVER['REQUEST_URI']))) : ''); ?>">
    <?php wp_nonce_field( 'grow_form_action' ); ?>
        <?php
            $nonce_verified = isset($_POST['_wpnonce']) && wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['_wpnonce'])), 'grow_form_action');
        ?>
        <h3><?php echo esc_attr(get_option('lf_form_heading')); ?></h3>
        <p id="lf_first_name_block">
            <label class="description" for="lf_first_name"><?php echo esc_attr(get_option('lf_firstname_field_label')); ?></label>
            <input id="lf_first_name" name="lf_first_name" type="text" maxlength="255" value="<?php echo ($nonce_verified && isset($_POST['lf_first_name']) ? esc_attr(sanitize_text_field(wp_unslash($_POST['lf_first_name']))) : '' ); ?>" required='required' placeholder="<?php echo esc_attr(get_option('lf_firstname_placeholder_label')); ?>" />
        </p>
        <p id="lf_last_name_block">
            <label class="description" for="lf_last_name"><?php echo esc_attr(get_option('lf_lastname_field_label')); ?></label>
            <input id="lf_last_name" name="lf_last_name" type="text" maxlength="255" value="<?php echo (isset($_POST['lf_last_name']) ? esc_attr(sanitize_text_field(wp_unslash($_POST['lf_last_name']))) : '' ); ?>" required='required' placeholder="<?php echo esc_attr(get_option('lf_lastname_placeholder_label')); ?>" />
        </p>
        <p id="lf_email_block">
            <label class="description" for="lf_email"><?php echo esc_attr(get_option('lf_email_field_label')); ?></label>
            <input id="lf_email" name="lf_email" type="email" maxlength="255" value="<?php echo (isset($_POST['lf_email']) ? esc_attr(sanitize_email(wp_unslash($_POST['lf_email']))) : '' ); ?>" required='required' placeholder="<?php echo esc_attr(get_option('lf_email_placeholder_label')); ?>" />
        </p>
        <p id="lf_phone_block">
            <label class="description" for="lf_phone"><?php echo esc_attr(get_option('lf_phone_field_label')); ?></label>
            <input id="lf_phone" name="lf_phone" type="text" maxlength="24" value="<?php echo (isset($_POST['lf_phone']) ? esc_attr(sanitize_text_field(wp_unslash($_POST['lf_phone']))) : '' ); ?>" placeholder="<?php echo esc_attr(get_option('lf_phone_placeholder_label')); ?>" />
        </p>
        <p id="lf_message_block">
            <label class="description" for="lf_message"><?php echo esc_attr(get_option('lf_message_field_label')); ?></label>
			<textarea id="lf_message" name="lf_message" required='required' placeholder="<?php echo esc_attr(get_option('lf_message_placeholder_label')) ?>"><?php echo (isset($_POST['lf_message']) ? esc_attr(sanitize_text_field(wp_unslash($_POST['lf_message']))) : '' ); ?></textarea>
        </p>

		<?php $disclaimer_text = esc_attr(get_option('lf_disclaimer_text')); if(!empty($disclaimer_text)): ?>
		<p id="lf_disclaimer">
			<input type="checkbox" id="lf_disclaimer" name="lf_disclaimer_checkbox" <?php echo esc_attr($this->is_selected('i_agree', isset($_POST['lf_disclaimer_checkbox']) ? sanitize_text_field(wp_unslash($_POST['lf_disclaimer_checkbox'])) : '','checkbox')) ?> value="i_agree" required='true'/>
			<label class="description" for="lf_disclaimer"><?php echo esc_html($disclaimer_text); ?> *</label>
		</p>
		<?php endif; ?>

        <div id="lf_wrap" style="display:none;">
            <label class="description" for="leave_this_blank">Leave this Blank if are sentient </label>
            <input name="leave_this_blank_url" type="text" value="" id="leave_this_blank"/>
        </div>

        <!-- Load reCAPTCHA site key on frontend only (admin preview doesn't need reCAPTCHA) -->
		<?php if($this->is_recaptcha_v3_configured() && !is_admin()) { ?>
			<!-- reCAPTCHA v3 (invisible) -->
			<input type="hidden" name="recaptcha_v3_token" id="recaptcha_v3_token" />
			<script>
				window.RECAPTCHA_V3_SITE_KEY = "<?php echo esc_attr(get_option('lf_recaptcha_v3_site_key')); ?>";
			</script>
		<?php } elseif($this->is_recaptcha_v2_configured() && !is_admin()) { ?>
			<!-- reCAPTCHA v2 (checkbox) -->
			<p class="g-recaptcha" data-sitekey="<?php echo esc_attr(get_option('lf_recaptcha_site_key')); ?>"></p>
		<?php } ?>

        <input type="hidden" name="leave_this_alone" value="<?php echo esc_attr(base64_encode(time())); ?>"/>
        <p class="buttons">
            <input type="hidden" name="form_id" value="1044046" />
            <input id="saveForm" class="button_text" type="submit" name="lf_submit" value="<?php echo esc_attr(get_option('lf_submit_button_text')); ?>" style="background-color: <?php echo esc_attr(get_option('lf_submit_button_color')); ?>; color:<?php echo esc_attr(get_option('lf_submit_button_text_color')); ?>" />
        </p>
    </form>
</div>

<!-- Load reCAPTCHA JavaScript only on frontend (library not available in admin) -->
<?php if($this->is_recaptcha_v3_configured() && !is_admin()) { ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('#lf_form_container form');
    const submitButton = form.querySelector('input[type="submit"]');
    
    if (!form || !submitButton) return;
    
    // Prevent infinite submission loops
    let recaptchaProcessed = false;
    
    form.addEventListener('submit', function(e) {
        // Allow normal submission if reCAPTCHA already processed
        if (recaptchaProcessed) return true;
        
        // Intercept submission to process reCAPTCHA first
        e.preventDefault();
        
        if (typeof grecaptcha === 'undefined') {
            alert('reCAPTCHA not loaded. Please refresh and try again.');
            return false;
        }
        
        grecaptcha.ready(function() {
            grecaptcha.execute(window.RECAPTCHA_V3_SITE_KEY, {action: 'contact_form_submit'})
                .then(function(token) {
                    document.getElementById('recaptcha_v3_token').value = token;
                    recaptchaProcessed = true;
                    // Simulate user clicking submit button to preserve HTML5 validation
                    // form.submit() would bypass required field checks and other validation
                    submitButton.click();
                })
                .catch(function(error) {
                    alert('reCAPTCHA verification failed. Please try again.');
                });
        });
    });
});
</script>
<?php } ?>
