<?php
get_header();
?>
<div class="search-page py-3">
<?php

  if(have_posts()) {

  ?>
  <section class="mb-5">
    <div class="page-header mb-4 border-bottom">
      <h5 class="mb-2 text-uppercase text-center"><?php echo esc_html('"'.get_search_query( false ).'"'); ?></h5>
    </div>
    <div class="row g-3 grid-textures justify-content-center">
    <?php
      while (have_posts()) {
        the_post();
        get_template_part( 'parts/texture-loop' );
      }

      wp_reset_postdata();
    ?>
    </div>
  </section>
  <?php
  }

?>
</div>
<?php
get_footer();