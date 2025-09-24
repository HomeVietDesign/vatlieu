<?php
global $post, $texture;
//$texture->refresh();
//$texture = new \HomeViet\Texture($post);

?>
<article <?php post_class('col-sm-6 col-md-4 col-xl-2'); ?>>
	<div class="inner border bg-white h-100">
		<div class="images position-relative border-bottom">
			<!-- <button class="texture-download position-absolute end-0 top-0 z-2 p-1 border-0 bg-transparent" type="button" data-id="<?=$texture->id?>"><span class="dashicons dashicons-download"></span></button> -->
			<button class="texture-copy-url position-absolute end-0 top-0 z-2 p-1 border-0 bg-info" type="button" data-url="<?=esc_url(get_permalink( $post ))?>" data-code="<?=esc_attr(strtoupper($post->post_name))?>" title="Sao chép URL"><span class="dashicons dashicons-admin-page"></span></button>
			<div class="ratio ratio-1x1 bg-dark-subtle z-1">
				<div class="texture-thumbnail position-absolute w-100 h-100 start-0 end-0">
					<a href="<?php the_permalink(); ?>"><?php the_post_thumbnail( 'large' ); ?></a>
				<?php
					/*
					if(has_post_thumbnail()) {
						$src_full = wp_get_attachment_image_src( get_post_thumbnail_id(), 'full' );
						?>
						<a class="d-block ratio ratio-1x1" href="<?=esc_url($src_full[0])?>" data-pswp-width="<?=$src_full[1]?>" data-pswp-height="<?=$src_full[2]?>">
							<?php the_post_thumbnail( 'large' ); ?>
						</a>
						<?php
					}
					*/
				?>
				</div>
			</div>
			<?php
			if(is_litespeed_cache_active()) {
				echo do_shortcode( '[esi texture_edit_button ttl="0" cache="private"]' );
			} else {
				echo do_shortcode( '[texture_edit_button ttl="0" cache="private"]' );
			}
			?>
		</div>
		
		<div class="info text-center">
			<h6 class="title m-0">
				<a class="code d-block px-2 py-3 text-uppercase" href="<?php the_permalink(); ?>">
					<?php echo esc_html($post->post_name); ?>
				</a>
			</h6>
		</div>
	</div>
</article>