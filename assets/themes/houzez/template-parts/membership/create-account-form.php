
<div class="row">
    <div class="col-md-6 col-sm-12">
        <div class="mb-3">
            <label class="form-label"><?php esc_html_e('First Name', 'houzez'); ?></label>
            <input type="text" name="first_name" class="form-control" placeholder="<?php esc_html_e('Enter your first name', 'houzez'); ?>">
        </div>
    </div><!-- col-md-6 col-sm-12 -->
    <div class="col-md-6 col-sm-12">
        <div class="mb-3">
            <label class="form-label"><?php esc_html_e('Last Name', 'houzez'); ?></label>
            <input type="text" name="last_name" class="form-control" placeholder="<?php esc_html_e('Enter your last name', 'houzez'); ?>">
        </div>
    </div><!-- col-md-6 col-sm-12 -->
    <div class="col-md-6 col-sm-12">
        <div class="mb-3">
            <label class="form-label"><?php esc_html_e('Username *', 'houzez'); ?> </label>
            <input type="text" name="username" class="form-control" placeholder="<?php esc_html_e('Enter username', 'houzez'); ?> ">
        </div>
    </div><!-- col-md-6 col-sm-12 -->
    <div class="col-md-6 col-sm-12">
        <div class="mb-3">
            <label class="form-label"><?php esc_html_e('Email *', 'houzez'); ?> </label>
            <input type="email" name="useremail" class="form-control" placeholder="<?php esc_html_e('Enter your email address', 'houzez'); ?>">
        </div>
    </div><!-- col-md-6 col-sm-12 -->
    <div class="col-md-6 col-sm-12">
        <div class="mb-3">
            <label class="form-label"><?php esc_html_e('Password *', 'houzez'); ?> </label>
            <input type="password" name="register_pass" class="form-control" placeholder="<?php esc_html_e('Password', 'houzez'); ?>">
        </div>
    </div><!-- col-md-6 col-sm-12 -->
    <div class="col-md-6 col-sm-12">
        <div class="mb-3">
            <label class="form-label"><?php esc_html_e('Confirm Password *', 'houzez'); ?> </label>
            <input type="password" name="register_pass_retype" class="form-control" placeholder="<?php esc_html_e('Confirm Password', 'houzez'); ?>">
        </div>
    </div><!-- col-md-6 col-sm-12 -->
</div><!-- row -->
<?php do_action( 'houzez_register_form_fields'); ?>
<?php wp_nonce_field( 'houzez_register_nonce2', 'houzez_register_security2' ); ?>
<input type="hidden" name="action" value="houzez_register_user_with_membership">

