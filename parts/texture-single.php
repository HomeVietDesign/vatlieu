<?php
global $post, $texture;

$projects = get_the_terms( $post, 'project' ); // Dự án
$occupations = get_the_terms( $post, 'occupation' ); // hạng mục
$design_cats = get_the_terms( $post, 'design_cat' ); // loại thiết kế

$locations = [];
$segments = [];

$project = null;
$occupation = null;
$design_cat = null;

if($projects) {
	$project = \HomeViet\Project::get_instance($projects[0]);
	
	$locations = fw_get_db_term_option($project->id, 'project', 'location');
	$segments = fw_get_db_term_option($project->id, 'project', 'segment');
}

if($occupations) {
	$occupation = \HomeViet\Occupation::get_instance($occupations[0]);
}

if($design_cats) {
	$design_cat = \HomeViet\Occupation::get_instance($design_cats[0]);
}
?>
<article id="texture-<?php echo $texture->get_id(); ?>">
	<div class="container-fluid">
		<div class="row justify-content-center">
			<div class="texture-image col-lg-3 col-xxl-2 text-center bg-white">
				<div class="sticky-top">
					<h2 class="text-uppercase py-3 mb-0"><?php echo esc_html($post->post_name); ?></h2>
					<?php
					$src_full = wp_get_attachment_image_src( $texture->image_id, 'full' );
					?>
					<div class="pswp-gallery mb-3"><a class="d-block border border-secondary-subtle" href="<?=esc_url($src_full[0])?>" data-pswp-width="<?=$src_full[1]?>" data-pswp-height="<?=$src_full[2]?>">
						<?php echo $texture->get_image(); ?>
					</a></div>
					<div class="text-start">
					<?php
					if($occupation) {
					?>
					<div class="occupations my-2">
						<span>Hạng mục: </span>
						<strong><?php echo esc_html($occupation->term->name); ?></strong>
					</div>
					<?php
					}

					if( current_user_can( 'edit_textures' ) ) {
						if($project) {
							$project_url = add_query_arg('pro', $project->id, get_term_link( $design_cat->id, 'design_cat' ));
						?>
						<div class="projects my-2">
							<span>Dự án: </span>
							<strong>
								<a href="<?=esc_url($project_url)?>" title="<?php echo esc_attr($project->term->description); ?>"><?php echo esc_html($project->term->name); ?></a>
							</strong>
						</div>
						<?php
						}

						
						if($locations) {
						?>
						<div class="locations my-2">
							<span>Địa điểm: </span>
							<?php
							foreach ($locations as $key => $value) {

								?>
								<strong>
									<?php if($key>0) echo ', '; ?>
									<?php echo esc_html(get_term_field( 'name', $value, 'location' )); ?>
								</strong>
								<?php
							}
							?>
						</div>
						<?php
						}


						if($segments) {
						?>
						<div class="segments my-2">
							<span>Phân khúc: </span>
							<?php
							foreach ($segments as $key => $value) {

								?>
								<strong>
									<?php if($key>0) echo ', '; ?>
									<?php echo esc_html(get_term_field( 'name', $value, 'segment' )); ?>
								</strong>
								<?php
							}
							?>
						</div>
						<?php
						}

					}
					?>
					</div>
				</div>
			</div>
			<div class="col-lg-9 col-xxl-10 p-0">
				<?php

				\HomeViet\Template_Tags::texture_contractors( ['project'=>$project, 'occupation' => $occupation, 'design_cat' => $design_cat] );
				
				?>
				<div class="pb-5"></div>
			</div>
		</div>
	</div>
</article>
	
