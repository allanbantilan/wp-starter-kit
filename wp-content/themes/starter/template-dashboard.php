<?php
/* Template Name: Dashboard */
// ponytail: front-end account page. The real admin dashboard is /wp-admin —
// auth itself is WordPress core (wp-login.php), not reinvented here.
get_header();

if (is_user_logged_in()) :
    $u = wp_get_current_user(); ?>
    <div class="card stack">
        <h1><?php printf(esc_html__('Welcome, %s', 'starter'), esc_html($u->display_name)); ?></h1>
        <dl class="dl">
            <dt><?php esc_html_e('Username', 'starter'); ?></dt><dd><?php echo esc_html($u->user_login); ?></dd>
            <dt><?php esc_html_e('Email', 'starter'); ?></dt><dd><?php echo esc_html($u->user_email); ?></dd>
            <dt><?php esc_html_e('Role', 'starter'); ?></dt><dd><?php echo esc_html(implode(', ', $u->roles)); ?></dd>
        </dl>
        <div class="actions">
            <?php if (current_user_can('manage_options')) : ?>
                <a class="button" href="<?php echo esc_url(admin_url()); ?>"><?php esc_html_e('WP Admin', 'starter'); ?></a>
            <?php endif; ?>
            <a class="button" href="<?php echo esc_url(wp_logout_url(home_url('/'))); ?>"><?php esc_html_e('Log out', 'starter'); ?></a>
        </div>
    </div>
<?php else : ?>
    <div class="card stack">
        <h1><?php esc_html_e('Dashboard', 'starter'); ?></h1>
        <p class="muted"><?php esc_html_e('Log in to view your account.', 'starter'); ?></p>
        <div class="actions">
            <a class="button" href="<?php echo esc_url(wp_login_url(get_permalink())); ?>"><?php esc_html_e('Log in', 'starter'); ?></a>
        </div>
    </div>
<?php endif;

get_footer();
