<?php
/**
 * 
 */

get_header();
?>
<div class="container py-5">
	<div class="row g-4 justify-content-center">
		<?php
		$design_cats = get_terms([
			'taxonomy' => 'design_cat',
			'hide_empty' => false,
		]);
		//debug($design_cats);
		if(!($design_cats instanceof \WP_Error) && !empty($design_cats)) {
			foreach ($design_cats as $key => $value) {
				$url = get_term_link( $value, $value->taxonomy );
				$thumbnail = fw_get_db_term_option($value->term_id, $value->taxonomy, 'image');
				//debug($thumbnail);
				?>
				<a class="col-lg-4 d-block p-3 m-3 fs-1 text-uppercase bg-secondary fw-bold shadow rounded text-warning" href="<?=esc_url($url)?>">
					<span class="thumbnail d-block ratio ratio-16x9 mb-3">
					<?php
					if(!empty($thumbnail)) {
						echo wp_get_attachment_image( $thumbnail['attachment_id'], 'large' );
					}
					?>
					</span>
					<span class="d-block text-center"><?=esc_html($value->name)?></span>
				</a>
				<?php
			}
		}
		?>
	</div>
</div>
<?php
get_footer();