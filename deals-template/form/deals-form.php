<?php do_action('deals_before_customer_login_form'); ?>

        <h2><?php _e('Login', 'wpdeals'); ?></h2>
        <form method="post" id="deals-loginform" class="deals-user login">
                <p class="form-row form-row-first">
                        <label for="username"><?php _e('Username', 'wpdeals'); ?> <span class="required">*</span></label>
                        <input type="text" class="input-text input" name="username" id="username" />
                </p>
                <p class="form-row form-row-last">
                        <label for="password"><?php _e('Password', 'wpdeals'); ?> <span class="required">*</span></label>
                        <input class="input-text input" type="password" name="password" id="password" />
                </p>
                <div class="clear"></div>

                <p class="form-row">
                        <?php wp_nonce_field('login', 'login', false); ?>
                        <input type="hidden" value="<?php echo $_SERVER['HTTP_REFERER']; ?>" name="_wp_http_referer" />
                        <input type="submit" id="wp-submit" class="button button-primary" name="login" value="<?php _e('Login', 'wpdeals'); ?>" />
                        <a class="lost_password" href="<?php echo esc_url( wp_lostpassword_url( home_url() ) ); ?>"><?php _e('Lost Password?', 'wpdeals'); ?></a>
                </p>
        </form>

<?php if (get_option('users_can_register') == 1) : ?>	
	
        <h2><?php _e('Register', 'wpdeals'); ?></h2>
        <form method="post" class="deals-user register" autocomplete="off">

                <p class="form-row form-row-first">
                        <label for="reg_username"><?php _e('Username', 'wpdeals'); ?> <span class="required">*</span></label>
                        <input type="text" class="input-text" name="username" id="reg_username" value="<?php if (isset($_POST['username'])) echo $_POST['username']; ?>" />
                </p>
                <p class="form-row form-row-last">
                        <label for="reg_email"><?php _e('Email', 'wpdeals'); ?> <span class="required">*</span></label>
                        <input type="email" class="input-text" name="email" id="reg_email" <?php if (isset($_POST['email'])) echo $_POST['email']; ?> />
                </p>
                <div class="clear"></div>

                <p class="form-row form-row-first">
                        <label for="reg_password"><?php _e('Password', 'wpdeals'); ?> <span class="required">*</span></label>
                        <input type="password" class="input-text" name="password" id="reg_password" />
                </p>
                <p class="form-row form-row-last">
                        <label for="reg_password2"><?php _e('Re-enter password', 'wpdeals'); ?> <span class="required">*</span></label>
                        <input type="password" class="input-text" name="password2" id="reg_password2" />
                </p>
                <div class="clear"></div>

                <!-- Spam Trap -->
                <div style="left:-999em; position:absolute;"><label for="trap">Anti-spam</label><input type="text" name="email_2" id="trap" /></div>

                <p class="form-row">
                        <?php wp_nonce_field('register', 'register') ?>
                        <input type="submit" class="button" name="register" value="<?php _e('Register', 'wpdeals'); ?>" />
                </p>

        </form>
        
<?php endif; ?>

<?php do_action('deals_after_customer_login_form'); ?>