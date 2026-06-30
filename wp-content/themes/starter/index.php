<?php
// Universal template. WP falls back to index.php for every route it can't find
// a more specific template for, so this single file renders the whole site.
get_header();

if (have_posts()) :
    while (have_posts()) : the_post(); ?>
        <article <?php post_class(); ?>>
            <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
            <?php is_singular() ? the_content() : the_excerpt(); ?>
        </article>
    <?php endwhile;
    the_posts_pagination();
else : ?>
    <p><?php esc_html_e('Nothing here yet.', 'starter'); ?></p>
<?php endif;

get_footer();
