<?php /* ponytail: header.php + footer.php are the only partials; add single.php, page.php, archive.php when a route needs its own design. */ ?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="skip-link" href="#main-content"><?php esc_html_e('Skip to content', 'starter'); ?></a>
<header class="site-header">
  <div class="site">
    <a class="brand" href="<?php echo esc_url(home_url('/')); ?>"><?php bloginfo('name'); ?></a>
    <div class="header-actions">
      <?php if (has_nav_menu('primary')) : ?>
        <nav class="site-nav" aria-label="<?php esc_attr_e('Primary', 'starter'); ?>">
          <?php wp_nav_menu(['theme_location' => 'primary', 'container' => false, 'fallback_cb' => false, 'depth' => 1]); ?>
        </nav>
      <?php endif; ?>
      <?php if (is_user_logged_in()) : ?>
        <a class="button" href="<?php echo esc_url(wp_logout_url(home_url('/'))); ?>"><?php esc_html_e('Log out', 'starter'); ?></a>
      <?php else : ?>
        <a class="button" href="<?php echo esc_url(wp_login_url(home_url('/'))); ?>"><?php esc_html_e('Log in', 'starter'); ?></a>
      <?php endif; ?>
    </div>
  </div>
</header>
<main id="main-content" class="site" tabindex="-1">
