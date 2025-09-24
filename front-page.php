<?php
/**
 * 
 */
//echo __FILE__;

//get_template_part( 'archive' );

get_header();
/*
while (have_posts()) {
	the_post();
	the_content();
}
*/
?>
<div class="container py-5">
	<div class="d-flex justify-content-center">
		<?php
		$design_cats = get_terms([
			'taxonomy' => 'design_cat',
			'hide_empty' => false,
		]);
		//debug($design_cats);
		if(!($design_cats instanceof \WP_Error) && !empty($design_cats)) {
			foreach ($design_cats as $key => $value) {
				$url = get_term_link( $value, $value->taxonomy );
				?>
				<a class="d-block p-5 m-3 fs-1 text-uppercase text-bg-dark fw-bold shadow rounded" href="<?=esc_url($url)?>"><?=esc_html($value->name)?></a>
				<?php
			}
		}
		?>
	</div>
</div>
<?php
get_footer();