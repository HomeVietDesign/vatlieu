<?php
/**
 * 
 */
get_header();

$cate = get_queried_object();

//global $wp_query;
//debug($cate);

$pro = isset($_GET['pro']) ? absint($_GET['pro']) : 0;
$occ = isset($_GET['occ']) ? absint($_GET['occ']) : 0;
$int = isset($_GET['int']) ? absint($_GET['int']) : 0;
$ext = isset($_GET['ext']) ? absint($_GET['ext']) : 0;

$crum_pro = '';
$crum_int = '';
$crum_ext = '';


$crum_cate = '<span class="mx-2">/</span><a href="'.esc_url(get_term_link($cate,'design_cat')).'">'.esc_html($cate->name).'</a>';


if($pro) {
	$crum_pro = '<span class="mx-2">/</span><span>'.esc_html(strip_tags(get_term_field( 'name', $pro, 'project' ))).'</span>';
}

if($int) {
	$crum_int = '<span class="mx-2">/</span><span>'.esc_html(get_term_field( 'name', $int, 'design_interior' )).'</span>';
}elseif($ext) {
	$crum_ext = '<span class="mx-2">/</span><span>'.esc_html(get_term_field( 'name', $ext, 'design_exterior' )).'</span>';
}

?>
<section class="mb-6">
	<div class="border-bottom mb-4">
	<div class="page-header position-sticky row g-3 justify-content-between align-items-center z-3">
		<div class="col-auto">
			<h6 class="my-1 text-uppercase d-flex fw-bold">
				<?php
				echo '<a href="'.esc_url(get_term_link($cate, $cate->taxonomy)).'">';
				
				the_archive_title();
				
				echo '</a>';

				if($pro) {
					echo $crum_pro;
				}
				
				if($crum_ext) {
					echo $crum_ext;
				}
				else if($crum_int) {
					echo $crum_int;
				}
				?>
			</h6>
		</div>
		<div class="col">
			<?php
			$occupations = get_terms([
				'taxonomy' => 'occupation',
				'hide_empty' => false,
			]);
			//debug($occupations);
			if(!($occupations instanceof \WP_Error) && !empty($occupations)) {
			?>
			<select id="occupation-select" class="d-none" style="width: 200px;">
				<option value="0" <?php selected( $occ, 0 ); ?>>--Hạng mục--</option>
			<?php
			foreach ($occupations as $key => $value) {
				?>
				<option value="<?=$value->term_id?>" <?php selected( $occ, $value->term_id ); ?>><?=esc_html($value->name)?></option>
				<?php
			}
			?>
			</select>
			<?php
			}
			?>
		</div>
		<?php
		if($pro && ($int || $ext)) {
			echo do_shortcode('[texture_upload_button ttl="0" cache="private"]');
		}
		?>
	</div>
	</div>
	
	<?php
	if($pro) {
		if(have_posts()) { //global $wp_query; debug($wp_query->request); ?>
		<div class="row g-3 grid-textures justify-content-center">
			<?php
			while (have_posts()) {
				the_post();
				get_template_part( 'parts/texture-loop' );
			}
			?>
		</div>
		<?php
		$paginate_links = paginate_links([
			'end_size'           => 3,
			'mid_size'           => 2,
			'prev_text'          => '<span class="dashicons dashicons-arrow-left"></span>',
			'next_text'          => '<span class="dashicons dashicons-arrow-right"></span>',
		]);

		if($paginate_links) {
			?>
			<div class="paginate-links d-flex justify-content-center align-items-stretch my-3">
				<?php echo $paginate_links; ?>
			</div>
			<?php
		}

		wp_reset_postdata();
		?>
		<?php } else { ?>
			<div class="alert alert-info" role="alert">Chưa có</div>
		<?php
		}
	}
	?>
</section>
<?php
get_footer();