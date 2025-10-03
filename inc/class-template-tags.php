<?php
/**
 * 
 * 
 */
namespace HomeViet;

class Template_Tags {

	const OUT_PROJECT = 0; // chưa được đề cử

	const IN_PROJECT = -1; // đề cử cho dự án

	const IN_LOCATION = -2; // đề cử tại địa phương

	const IN_REMOVED = -3; // loại bỏ

	const IN_CATEGORY = -4; // loại bỏ

	public static function remove_from_removed_contractors($contractor_id) {
		$removed_contractors = self::get_removed_contractors();
		if(in_array($contractor_id, $removed_contractors)) {
			unset($removed_contractors[array_search($contractor_id, $removed_contractors)]);
			update_option( 'removed_contractors', $removed_contractors );
		}
	}

	public static function add_to_removed_contractors($contractor_id) {
		$removed_contractors = self::get_removed_contractors();
		if(!in_array($contractor_id, $removed_contractors)) {
			$removed_contractors[] = $contractor_id;
			debug_log($removed_contractors);
			update_option( 'removed_contractors', $removed_contractors );
		}
	}

	public static function get_removed_contractors() {
		return get_option('removed_contractors', []);
	}

	public static function texture_contractors($args) {
		global $post, $texture;
		if($args['project']) {
			
			$design_cat_url = home_url();
			
			$sub_taxs = [];
			if($args['design_cat']) {
				$design_cat_url = get_term_link( $args['design_cat']->id, 'design_cat' );
				$sub_taxs = fw_get_db_term_option( $args['design_cat']->id, 'design_cat', 'tax_active' ); // loại thư mục con
			}
			
			$project_url = add_query_arg('pro', $args['project']->id, $design_cat_url);
			?>
			<div class="occupation">
				<div class="d-flex justify-content-between pt-3 sticky-top border-bottom pb-3 px-3 bg-white align-items-center flex-wrap position-relative">
					<input type="hidden" id="project" value="<?=$args['project']->id?>">
					<div class="position-relative z-3">
					<?php
					if($args['design_cat']) {
						?>
						<div class="design-cat my-2 d-flex align-items-center">
							<strong>
								<?php
								if(current_user_can( 'edit_textures' )) {
									echo '<a href="'.esc_url($project_url).'">'.esc_html($args['design_cat']->term->name).'</a>';
								} else {
									echo esc_html($args['design_cat']->term->name);
								}
								?>
							</strong>
							<strong class="d-block ms-2">»</strong>
							<?php
							$sub_cats = [];
							if($sub_taxs) {
								foreach ($sub_taxs as $tax) {
									$_sub_cats = get_the_terms( $post, $tax ); // Thư mục con
									if($_sub_cats) {
										foreach ($_sub_cats as $key => $value) {
											if($tax=='design_interior') {
												$url = add_query_arg( 'int', $value->term_id, $project_url );
											}elseif($tax=='design_exterior') {
												$url = add_query_arg( 'ext', $value->term_id, $project_url );
											} else {
												$url = $project_url;
											}

											$sub_cats[] = [
												'url' => $url,
												'title' => $value->name
											];
										}	
									}
								}
							}

							if($sub_cats) {
								if(count($sub_cats)>1) {
									?>
									<div class="btn-group">
										<button class="btn btn-sm dropdown-toggle fw-bold" type="button" data-bs-toggle="dropdown" aria-expanded="false">Các không gian</button>
										<ul class="dropdown-menu">
										<?php
										foreach ($sub_cats as $key => $value) {
											if(current_user_can( 'edit_textures' )) {
												?>
												<li><a class="dropdown-item fw-bold" href="<?=esc_url($value['url'])?>"><?=esc_html($value['title'])?></a></li>
												<?php
											} else {
												?>
												<li><span class="dropdown-item fw-bold"><?=esc_html($value['title'])?></span></li>
												<?php
											}
										}	
										?>
										</ul>
									</div>
									<?php
								} else {
									if(current_user_can( 'edit_textures' )) {
									?>
									<a class="fw-bold ms-2" href="<?=esc_url($sub_cats[0]['url'])?>"><?=esc_html($sub_cats[0]['title'])?></a>
									<?php
									} else {
									?>
									<span class="fw-bold ms-2"><?=esc_html($sub_cats[0]['title'])?></span>
									<?php
									}
								}
							}
							?>
						</div>
						<?php
					}
					?>
					</div>
					<div class="d-flex justify-content-center position-absolute w-100 start-0 z-1">
						<h5 class="text-uppercase mx-0 my-2 p-0"><a class="d-block" href="<?php the_permalink(); ?>" title="Tải lại">Nhà cung cấp<?php
						if(!is_user_logged_in() || !current_user_can( 'edit_contractors' )) {
							if(!empty($args['occupation'])) {
							?>
							&nbsp;<?php echo esc_html('"'.$args['occupation']->term->name.'"'); ?>
							<?php
							}
						}
						?></a></h5>
						<?php
						if(current_user_can( 'edit_contractors' )) {
							?>
							<form id="frm-occupation-selection" class="ms-5 d-flex align-items-center" action="" method="post">
								<input type="hidden" name="texture" id="texture" value="<?=$post->ID?>">
								<?php
								wp_dropdown_categories([
									'taxonomy' => 'occupation',
									'name' => 'occupation',
									'id' => 'occupation-selection',
									'class' => 'select2-hidden-accessible',
									'hide_empty' => false,
									'selected' => ($args['occupation'])?$args['occupation']->id:0,
									'hierarchical' => true,
									'show_option_all'   => '--Hạng mục--',
								]);
								?>
								<button type="submit" class="btn btn-sm btn-primary ms-3">Lưu</button>
							</form>

							<div class="d-flex align-items-center ms-2">
								<button type="button" class="btn btn-sm btn-danger ms-2" data-bs-toggle="modal" data-bs-target="#add-new-occupation"><span class="dashicons dashicons-plus-alt2"></span> hạng mục</button>
								<button type="button" class="btn btn-sm btn-danger ms-2" data-bs-toggle="modal" data-bs-target="#add-new-contractor"><span class="dashicons dashicons-plus-alt2"></span> nhà thầu</button>
							</div>
						<?php
						} ?>
						
					</div>
				</div>
				<?php

				if($args['occupation']) { // có hạng mục

					$project_contractors = $args['project']->get_contractors();
					$project_local_contractors = $args['project']->get_local_contractors();
					$removed_contractors = self::get_removed_contractors();

					//$excludes = array_unique(array_merge($project_contractors, $project_local_contractors,$removed_contractors));
					
					?>
					<section id="project-contractors">
					<?php
					if(!empty($project_contractors)) {
						\HomeViet\Template_Tags::contractors([ 
							'texture' => $texture, 
							'project' => $args['project'], 
							'occupation'=>$args['occupation'], 
							'type'=>self::IN_PROJECT, 
							//'contractors' => array_diff($project_contractors, $removed_contractors),
							'contractors' => $project_contractors,
							'local_contractors' => $project_local_contractors, 
							'removed_contractors' => $removed_contractors, 
						]);
					}
					?>
					</section>

					<section id="project-local-contractors">
					<?php
					if(!empty($project_local_contractors)) {
						\HomeViet\Template_Tags::contractors([ 
							'texture' => $texture, 
							'project' => $args['project'], 
							'occupation'=>$args['occupation'], 
							'type'=>self::IN_LOCATION, 
							'contractors' => $project_contractors,
							'local_contractors' => $project_local_contractors, 
							'removed_contractors' => $removed_contractors,  
							// 'local_contractors' => array_diff(
							// 	$project_local_contractors, 
							// 	array_unique(array_merge(, $removed_contractors))
							// ), 
						]);
					}
					?>
					</section>
					<?php
					if(current_user_can( 'edit_contractors' )) {
						?>
						<section id="contractors-cat-0">
						<?php
						\HomeViet\Template_Tags::contractors([ 
							'texture' => $texture, 
							'project' => $args['project'], 
							'occupation'=>$args['occupation'], 
							'type'=>self::OUT_PROJECT, 
							'contractors' => $project_contractors,
							'local_contractors' => $project_local_contractors, 
							'removed_contractors' => $removed_contractors,  
						]);
						?>
						</section>
						<?php
						/*
						$contractor_cats = get_terms([
							'taxonomy' => 'contractor_cat',
							'hide_empty' => true
						]);

						if($contractor_cats) {
							foreach ($contractor_cats as $index => $cat) {
								?>
								<section class="contractors-cat" id="contractors-cat-<?=$cat->term_id?>">
								<?php
								\HomeViet\Template_Tags::contractors([
									'texture' => $texture, 
									'project' => $args['project'], 
									'occupation'=>$args['occupation'],
									'type'=>self::IN_CATEGORY,  
									'contractor_cat'=>$cat, 
									'contractors' => [], 
									'local_contractors' => [], 
									'excludes' => $excludes 
								]);
								?>
								</section>
								<?php
							}
						}
						*/
					}
					?>
					<section id="removed-contractors">
					<?php
					if(!empty($removed_contractors)) {
						\HomeViet\Template_Tags::contractors([ 
							'texture' => $texture, 
							'project' => $args['project'], 
							'occupation'=>$args['occupation'], 
							'type'=>self::IN_REMOVED, 
							'contractors' => $project_contractors,
							'local_contractors' => $project_local_contractors, 
							'removed_contractors' => $removed_contractors,
						]);
					}
					?>
					</section>
					<?php
				} else { // end occupations
					?>
					<div class="p-3 text-center text-secondary">Chưa có hạng mục</div>
					<?php
				}
				?>
			</div>
			<?php
		
		} // end if project
		else {
			?>
			<div class="p-3 text-center text-secondary">Chưa có dự án</div>
			<?php
		}
		
	}

	public static function contractors($args) {
		//debug($args);

		$query_args = [
			'post_type' => 'contractor',
			'posts_per_page' => -1,
			'post_status' => 'publish',
			'orderby' => [
				'menu_order' => 'DESC',
				'date' => 'DESC'
			]
		];

		$loc = isset($_GET['loc']) ? absint($_GET['loc']) : 0;

		//$default_location = (int) get_option( 'default_term_location', -1 );

		$tax_query = [
			'occupation' => [
				'taxonomy' => 'occupation',
				'field' => 'term_id',
				'terms' => [$args['occupation']->id]
			]
		];

		switch ($args['type']) {
			case self::OUT_PROJECT:
				// $tax_query['contractor_cat'] = [
				// 	'taxonomy' => 'contractor_cat',
				// 	'operator' => 'NOT EXISTS'
				// ];
				if($loc) {
					$tax_query['location'] = [
						'taxonomy' => 'location',
						'field' => 'term_id',
						'terms' => [$loc]
					];
				}

				$query_args['post__not_in'] = array_unique(array_merge($args['contractors'],$args['local_contractors'],$args['removed_contractors']));
				break;
			
			case self::IN_PROJECT:
				$query_args['post__in'] = (!empty($args['contractors']))?$args['contractors']:[0];
				//$query_args['post__in'] = (!empty($args['contractors']))?array_diff(array_diff($args['contractors'], $args['local_contractors']),$args['removed_contractors']):[0];
				break;

			case self::IN_LOCATION:
				$query_args['post__in'] = (!empty($args['local_contractors']))?$args['local_contractors']:[0];
				break;

			case self::IN_REMOVED:
				$query_args['post__in'] = (!empty($args['removed_contractors']))?$args['removed_contractors']:[0];
				break;

			case self::IN_CATEGORY:
				// 	$tax_query['contractor_cat'] = [
				// 		'taxonomy' => 'contractor_cat',
				// 		'field' => 'term_id',
				// 		'terms' => [$args['contractor_cat']->term_id]
				// 	];
				break;
		}

		$query_args['tax_query'] = $tax_query;

		//debug($query_args);

		$query = new \WP_Query($query_args);
		
		//debug_log($query);

		//$contractors = get_posts($query_args);

		
		?>
		<div class="section-contractors mt-4 <?php echo (!$query->have_posts() && $args['type']!==self::OUT_PROJECT)?'hide':''; ?>">
			<div class="section-contractors-heading fw-bold text-center text-uppercase">
				<?php
				switch ($args['type']) {
					case self::OUT_PROJECT:
						// echo 'Chưa phân nhóm';
						$locations = get_terms([
							'taxonomy' => 'location',
							'hide_empty' => false
						]);

						if($locations) {
							$current_url = remove_query_arg('loc', fw_current_url());
							?>
							<div class="locations d-flex flex-wrap">
								<a href="<?=esc_url($current_url)?>" class="btn btn-sm btn-<?php echo ($loc==0)?'danger':'primary'; ?> m-1">Tất cả</a>
								<?php
								foreach ($locations as $key => $value) {
									$href = add_query_arg('loc', $value->term_id, $current_url);
									?>
									<a href="<?=esc_url($href)?>" class="btn btn-sm btn-<?php echo ($loc==$value->term_id)?'danger':'primary'; ?> m-1"><?=esc_html($value->name)?></a>
									<?php
								}
								?>
								<button type="button" class="btn btn-sm btn-secondary m-1" data-bs-toggle="modal" data-bs-target="#add-new-location"><span class="dashicons dashicons-plus-alt2"></span> địa điểm</button>
							</div>
							<?php
						}
						break;
					
					case self::IN_PROJECT:
						echo esc_html(fw_get_db_settings_option('recommend_label', 'Được đề cử'));
						break;

					case self::IN_LOCATION:
						echo esc_html(fw_get_db_settings_option('recommend_label_local', 'Tại địa phương'));
						break;

					case self::IN_REMOVED:
						echo esc_html(fw_get_db_settings_option('recommend_label_removed', 'Đã loại bỏ'));
						break;

					case self::IN_CATEGORY:
						// if($args['contractor_cat'] instanceof \WP_Term) {
						// 	echo esc_html($args['contractor_cat']->name);
						// }
						break;
				}
				?>
			</div>
			<?php
			if($query->have_posts()) {
				?>
				<div class="contractors p-3">
					<div class="row g-3<?php echo (current_user_can( 'edit_contractors' ))?' contractors-sortable':''; ?> justify-content-center">
					<?php
					while ($query->have_posts()) {
						$query->the_post();
						global $post;

						$contractor = \HomeViet\Contractor::get_instance($post->ID);
						$phone_number = fw_get_db_post_option($post->ID, '_phone_number');

						//$contractor_locations = get_the_terms( $post, 'location' );

						//$occupations = get_the_terms( $post, 'occupation' );
						$segments = get_the_terms( $post, 'segment' );
						$sources = get_the_terms( $post, 'contractor_source' );

						?>
						<div class="contractor-item contractor contractor-<?=$post->ID?><?php
						if(in_array($post->ID, $args['contractors'])) {
							echo ' in-project';
						} else {
							echo ' out-project';
						}

						if(in_array($post->ID, $args['local_contractors'])) {
							echo ' in-local-project';
						} else {
							echo ' out-local-project';
						}

						if(in_array($post->ID, $args['removed_contractors'])) {
							echo ' in-removed-project';
						} else {
							echo ' out-removed-project';
						}

						?> col-md-6 col-xl-4 col-xxl-3" data-id="<?=$post->ID?>">
							<div class="border h-100 bg-white">
								<div class="thumbnail position-relative border-bottom bg-secondary-subtle">
									<div class="position-absolute bottom-0 end-0 d-flex z-3 p-1">
									<?php
									if(current_user_can( 'edit_contractors' )) {
										if(in_array($post->ID, $args['removed_contractors'])) {
											?>
											<button type="button" class="btn btn-sm btn-warning btn-shadow fw-bold ms-2 remove-from-removed" data-texture="<?=(($args['texture'])?$args['texture']->id:0)?>" data-project="<?=$args['project']->id?>" data-contractor="<?=$post->ID?>" title="Sử dụng nhà thầu"><span class="dashicons dashicons-arrow-up-alt"></span></button>
											<?php
										} else {

											?>
											<button type="button" class="btn btn-sm btn-warning btn-shadow fw-bold ms-2 add-to-removed" data-texture="<?=(($args['texture'])?$args['texture']->id:0)?>" data-project="<?=$args['project']->id?>" data-contractor="<?=$post->ID?>" title="Loại bỏ nhà thầu"><span class="dashicons dashicons-arrow-down-alt"></span></button>
											<?php
										}

										if(in_array($post->ID, $args['contractors'])) {
											?>
											<button type="button" class="btn btn-sm btn-danger btn-shadow fw-bold ms-2 remove-from-project" data-texture="<?=(($args['texture'])?$args['texture']->id:0)?>" data-project="<?=$args['project']->id?>" data-contractor="<?=$post->ID?>" title="Loại khỏi dự án"><span class="dashicons dashicons-minus"></span></button>
											<?php
										} else {
											?>
											<button type="button" class="btn btn-sm btn-danger btn-shadow fw-bold ms-2 add-to-project" data-texture="<?=(($args['texture'])?$args['texture']->id:0)?>" data-project="<?=$args['project']->id?>" data-contractor="<?=$post->ID?>" title="Đề cử cho dự án"><span class="dashicons dashicons-plus-alt2"></span></button>
											<?php
										}
										
										if(in_array($post->ID, $args['local_contractors'])) {
											?>
											<button type="button" class="btn btn-sm btn-primary btn-shadow fw-bold ms-2 remove-from-local-project position-relative" data-texture="<?=(($args['texture'])?$args['texture']->id:0)?>" data-project="<?=$args['project']->id?>" data-contractor="<?=$post->ID?>" title="Loại khỏi địa phương dự án">
												<span class="position-absolute top-50 start-50 translate-middle dashicons dashicons-location"></span>
												<span class="position-absolute d-block icon-minus"></span>
											</button>
											<?php
										} else {
											?>
											<button type="button" class="btn btn-sm btn-primary btn-shadow fw-bold ms-2 add-to-local-project" data-texture="<?=(($args['texture'])?$args['texture']->id:0)?>" data-project="<?=$args['project']->id?>" data-contractor="<?=$post->ID?>" title="Thêm vào địa phương dự án"><span class="dashicons dashicons-location"></span></button>
											<?php
										}
									
										?>
										
										<a class="btn btn-sm btn-primary btn-shadow fw-bold ms-2" href="<?php echo get_edit_post_link( $post ); ?>" target="_blank"><span class="dashicons dashicons-edit-page"></span></a>
										
										<a href="#edit-contractor" class="btn btn-sm btn-danger btn-shadow fw-bold ms-2" data-bs-toggle="modal" data-texture="<?=(($args['texture'])?$args['texture']->id:0)?>" data-project="<?=$args['project']->id?>" data-contractor="<?=$post->ID?>" data-contractor-title="<?php echo esc_attr($post->post_title); ?>" title="Sửa nhanh"><span class="dashicons dashicons-edit"></span></a>
										<?php
									}
									?>
									</div>
									<div class="position-absolute top-0 start-0 d-flex z-3">
										<?php
										/*
										if(!empty($cgroups)) {
											echo '<div class="cgroups d-flex">';
											foreach ($cgroups as $cg) {
												?>
												<span class="m-1 btn btn-sm btn-<?=esc_attr($cg->description)?> btn-shadow"><?=esc_html($cg->name)?></span>
												<?php
											}
											echo '</div>';
										}
										
										if(!empty($contractor_locations)) {
											echo '<div class="locations d-flex">';
											foreach ($contractor_locations as $loc) {
												?>
												<span class="m-1 btn btn-sm btn-primary btn-shadow"><?=esc_html($loc->name)?></span>
												<?php
											}
											echo '</div>';
										}
										*/
										?>
									</div>
									<?php if(current_user_can('edit_contractors')) { ?>
									<div class="position-absolute top-0 end-0 d-flex z-3">
										<div class="segments d-flex">
										<?php
										if(!empty($segments)) {
											foreach ($segments as $seg) {
												?>
												<span class="m-1 btn btn-sm btn-danger btn-shadow"><?=esc_html($seg->name)?></span>
												<?php
											}
										}
										?>
										</div>
									</div>
									<div class="position-absolute top-0 start-0 d-flex z-3">
										<div class="sources d-flex">
										<?php
										if(!empty($sources)) {
											foreach ($sources as $s) {
												?>
												<span class="m-1 btn btn-sm btn-success btn-shadow"><?=esc_html($s->name)?></span>
												<?php
											}
										}
										?>
										</div>
									</div>
									<?php } ?>
									<div class="ratio ratio-4x3">
										<div><?php echo $contractor->get_image('medium_large'); ?></div>
									</div>
									
								</div>
								<div class="info py-3 px-1">
									<h3 class="text-center text-uppercase fs-6 m-0 fw-bold">
										<span><?php echo esc_html($args['occupation']->term->name); ?></span>
										&nbsp;-&nbsp;
										<span><?php echo esc_html($post->post_title); ?></span>
									</h3>
									<div class="btn-contacts d-flex p-2 justify-content-center">
										<?php if($phone_number) { ?>
										<div class="bg-success text-white py-1 px-2 m-1 rounded"><?php echo esc_html($phone_number); ?></div>
										<a class="bg-danger text-white py-1 px-2 m-1 rounded" href="tel:<?php echo esc_attr($phone_number); ?>">Gọi điện</a>
										<a class="bg-primary text-white py-1 px-2 m-1 rounded" href="https://zalo.me/<?php echo esc_attr($phone_number); ?>">Nhắn Zalo</a>
										<?php } ?>
									</div>
									<div class="desc text-center"><?php the_content(); ?></div>
								</div>
							</div>
						</div>
						<?php
					}
					?>
					</div>
				</div>
				<?php
				wp_reset_postdata();
			} else {
				?>
				<div class="text-center mt-3 text-secondary">Không có</div>
				<?php
			}
			?>
		</div>
		<?php
		
	}

	public static function extract_template_args($slug, $name, $args) {

		$templates = array();
		$name      = (string) $name;
		if ( '' !== $name ) {
			$templates[] = "{$slug}-{$name}.php";
		}

		$templates[] = "{$slug}.php";

		do_action( 'get_template_part', $slug, $name, $templates, $args );

		$_template_file = locate_template( $templates, false, true );

		if($_template_file) {

			if(!empty($args)) {
				extract( $args, EXTR_SKIP );
			}

			include_once $_template_file;

			return true;
		}

		return false;
	}

	public static function site_main_open() {
		?>
		<main class="main" id="top">
		<?php
	}

	public static function header_html() {
		// body open custom code 5
		add_action('wp_body_open', [__CLASS__, 'site_main_open'], 15);
		if(!is_singular('texture')) {
			add_action('wp_body_open', [__CLASS__, 'sidebar'], 20);
			add_action('wp_body_open', [__CLASS__, 'navbar_top'], 30);
			add_action('wp_body_open', [__CLASS__, 'site_content_open'], 40);
		}
		
	}

	public static function sidebar() {
		get_sidebar();
	}

	public static function navbar_top() {
		$queried = get_queried_object();
		$current_url = preg_replace('/page\/\d+/', '', fw_current_url());

		$cate = isset($_GET['cate']) ? absint($_GET['cate']) : 0;
		$pro = isset($_GET['pro']) ? absint($_GET['pro']) : 0;

		?>
		<nav class="navbar navbar-top fixed-top navbar-expand" id="navbarDefault">
			<div class="collapse navbar-collapse justify-content-between">
				<div class="navbar-logo">
					<button class="btn navbar-toggler navbar-toggler-humburger-icon hover-bg-transparent" type="button" data-bs-toggle="collapse" data-bs-target="#navbarVerticalCollapse" aria-controls="navbarVerticalCollapse" aria-expanded="false" aria-label="Toggle Navigation">
						<span class="navbar-toggle-icon">
							<span class="toggle-line"></span>
						</span>
					</button>
					<a class="navbar-brand me-1 me-sm-3" href="<?php echo home_url(); ?>">
						<div class="d-flex align-items-center">
							<div class="d-flex align-items-center">
								<h5 class="logo-text ms-2 d-none d-sm-block text-primary-emphasis"><?php bloginfo( 'name' ); ?></h5>
							</div>
						</div>
					</a>
				</div>
				<?php
				$design_cats = get_terms([
					'taxonomy' => 'design_cat',
					'hide_empty' => false,
				]);
				//debug($design_cats);
				if(!($design_cats instanceof \WP_Error) && !empty($design_cats)) {
				?>
				<ul class="navbar-nav navbar-nav-icons flex-row">
				<?php
				foreach ($design_cats as $key => $value) {
					$tax_active = fw_get_db_term_option($value->term_id, 'design_cat', 'tax_active', []);
					$url = get_term_link( $value, $value->taxonomy );

					if(is_tax()) {
						if(!is_tax('design_cat')) {
							if(is_tax('design_interior') && !in_array('design_interior', $tax_active)) {
								if($pro) $url = add_query_arg(['pro'=>$pro], $url);
							}elseif(is_tax('design_exterior') && !in_array('design_exterior', $tax_active)) {
								if($pro) $url = add_query_arg(['pro'=>$pro], $url);
							}elseif(!in_array('design_interior', $tax_active)) {
								$url = remove_query_arg( 'int', $current_url );
								$url = add_query_arg(['cate'=>$value->term_id], $url);
							}elseif(!in_array('design_exterior', $tax_active)) {
								$url = remove_query_arg( 'ext', $current_url );
								$url = add_query_arg(['cate'=>$value->term_id], $url);

							}
						} else {
							if($pro) $url = add_query_arg(['pro'=>$pro], $url);
						}
					}

					$class = ((is_tax() && !is_tax('design_cat') && $cate==$value->term_id)||(is_tax('design_cat') && $queried->term_id==$value->term_id))?' current-menu-item':'';
					?>
					<li id="menu-item-<?=$value->term_id?>" class="menu-item menu-item-type-custom menu-item-object-custom nav-item<?=$class?>">
						<a href="<?=esc_url($url)?>" class="nav-link"><?=esc_html($value->name)?></a>
					</li>
					<?php
				}
				?>
				</ul>
				<?php
				}
				// wp_nav_menu([
				// 	'theme_location' => 'primary',
				// 	'container' => false,
				// 	'echo' => true,
				// 	'fallback_cb' => '',
				// 	'depth' => 1,
				// 	'walker' => new \HomeViet\Walker_Primary_Menu(),
				// 	'items_wrap' => '<ul class="navbar-nav navbar-nav-icons flex-row">%3$s</ul>',
				// ]);
				?>
				<div class="search-box navbar-top-search-box d-none d-lg-block position-relative" style="width:25rem;">
					<form class="position-relative" action="<?=esc_url(home_url())?>" method="GET">
						<input class="form-control search-input rounded-pill form-control-sm" type="search" name="s" placeholder="Search..." aria-label="Search" value="<?php the_search_query(); ?>">
						<button type="submit" class="position-absolute btn btn-sm top-50 translate-middle-y end-0 border-0 me-1"><span class="dashicons dashicons-search"></span></button>
					</form>
				</div>

				<?php echo do_shortcode( '[user_info_block]' ); ?>
			</div>
		</nav>
		<?php
	}

	public static function site_content_open() {
		?>
		<div class="content">
		<?php
	}

	public static function site_content_close() {
		?>
		</div>
		<?php
	}

	public static function right_sidebar() {
		if(is_singular( 'texture' )) {
			//get_sidebar('right');
		}
	}

	public static function footer_html() {
		if(!is_singular('texture')) {
			add_action('wp_footer', [__CLASS__, 'site_content_close'], 8);
		}
		add_action('wp_footer', [__CLASS__, 'site_main_close'], 9);
		// enqueue scripts 10
		// modal 50
		// custom code 100
	}

	public static function site_main_close() {
		?>
		</main>
		<?php
	}

	public static function modals() {
		?>
		<div class="toast-container position-fixed bottom-0 end-0 p-3">
			<div id="theme-toast" class="toast text-bg-info align-items-center" role="alert" aria-live="assertive" aria-atomic="true">
				<div class="d-flex">
					<div class="toast-body"></div>
					<button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
				</div>
			</div>
			<div id="texture-upload-toast" class="toast" role="alert" aria-live="assertive" aria-atomic="true" data-bs-autohide="false">
				<div class="toast-header">
					Upload
					<button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
				</div>
				<div class="toast-body overflow-y-auto">
					<div id="texture-upload-statusbars"></div>
				</div>
			</div>
		</div>

		<div class="modal fade" id="searchBoxModal">
			<div class="modal-dialog">
				<div class="modal-content mt-15 rounded-pill">
					<div class="modal-body p-0">
						<form class="position-relative" action="<?=esc_url(home_url())?>" method="GET">
							<input class="form-control search-input rounded-pill" type="search" name="s" placeholder="Search..." aria-label="Search" value="<?php the_search_query(); ?>">
							<button type="submit" class="position-absolute btn btn-sm top-50 translate-middle-y end-0 border-0 me-1"><span class="dashicons dashicons-search"></span></button>
						</form>
					</div>
				</div>
			</div>
		</div>

		<div class="modal fade" id="edit-contractor" tabindex="-1" role="dialog" aria-labelledby="edit-contractor-label">
			<div class="modal-dialog modal-lg modal-dialog-scrollable">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title" id="edit-contractor-label">Sửa nhà thầu</h5>
						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
					</div>
					<div class="modal-body"></div>
				</div>
			</div>
		</div>

		<div class="modal fade" id="add-new-location" tabindex="-1" role="dialog" aria-labelledby="add-new-location-label">
			<div class="modal-dialog modal-md modal-dialog-scrollable">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title" id="add-new-location-label">Tạo hạng mục</h5>
						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
					</div>
					<div class="modal-body">
						<form id="frm-add-new-location" method="POST" action="">
							<?php wp_nonce_field( 'add-new-location', 'nonce' ); ?>
							<div id="add-new-location-response"></div>
							<div class="mb-3">
								Tiêu đề
								<input type="text" class="form-control" name="location_name" required>
							</div>
							<div class="mb-3">
								<button type="submit" class="btn btn-danger text-uppercase fw-bold text-yellow text-nowrap d-block w-100">Tạo</button>
							</div>
							
						</form>
					</div>
				</div>
			</div>
		</div>

		<div class="modal fade" id="add-new-occupation" tabindex="-1" role="dialog" aria-labelledby="add-new-occupation-label">
			<div class="modal-dialog modal-md modal-dialog-scrollable">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title" id="add-new-occupation-label">Tạo hạng mục</h5>
						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
					</div>
					<div class="modal-body">
						<form id="frm-add-new-occupation" method="POST" action="">
							<?php wp_nonce_field( 'add-new-occupation', 'nonce' ); ?>
							<div id="add-new-occupation-response"></div>
							<div class="mb-3">
								Tiêu đề
								<input type="text" class="form-control" name="occupation_name" required>
							</div>
							<div class="mb-3">
								<button type="submit" class="btn btn-danger text-uppercase fw-bold text-yellow text-nowrap d-block w-100">Tạo</button>
							</div>
							
						</form>
					</div>
				</div>
			</div>
		</div>
		<?php
		global $occupation;	
		?>
		<div class="modal fade" id="add-new-contractor" tabindex="-1" role="dialog" aria-labelledby="add-new-contractor-label">
			<div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title" id="add-new-contractor-label" required>Tạo nhà thầu</h5>
						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
					</div>
					<div class="modal-body">
						<form id="frm-add-new-contractor" method="POST" action="">
							<?php wp_nonce_field( 'add-new-contractor', 'nonce' ); ?>
							<input type="hidden" name="contractor_occupation" value="<?=absint(($occupation)?$occupation->id:0)?>">
							<div id="add-new-contractor-response"></div>
							<div class="row">
								<div class="col-lg-6">
									<div class="mb-3">
										Tiêu đề
										<input type="text" class="form-control" name="contractor_title">
									</div>
									<div class="mb-3">
										Số điện thoại
										<input type="text" class="form-control" name="contractor_phone_number">
									</div>
									<div class="mb-3">
										Nguồn nhà thầu
										<div>
										<?php
										wp_dropdown_categories([
											'taxonomy' => 'contractor_source',
											'name' => 'contractor_source',
											'id' => 'contractor_source-selection',
											'class' => 'select2-hidden-accessible w-100',
											'hide_empty' => false,
											'hierarchical' => true,
											'show_option_all'   => '--Không có--',
										]);
										?>
										</div>
									</div>
									<div class="mb-3">
										Địa điểm
										<div>
										<?php
										wp_dropdown_categories([
											'taxonomy' => 'location',
											'name' => 'location',
											'id' => 'location-selection',
											'class' => 'select2-hidden-accessible',
											'hide_empty' => false,
											'hierarchical' => true,
											'show_option_all'   => '--Không có--',
										]);
										?>
										</div>
									</div>
								</div>
								<div class="col-lg-6">
									<div class="mb-3">
										Mô tả thêm
										<textarea class="form-control" rows="11" name="contractor_description"></textarea>
									</div>
								</div>
							</div>
							<div class="mb-3">
								<button type="submit" class="btn btn-danger text-uppercase fw-bold text-yellow text-nowrap d-block w-100">Tạo</button>
							</div>
							
						</form>
					</div>
				</div>
			</div>
		</div>
		<?php
	}

	public static function display_widgets() {
		?>
		<div class="site-footer-inner container-xl">
			<div class="row">
				<?php if(is_active_sidebar( 'footer-1' )) { ?>
				<div class="site-footer-col col-lg-4 py-3">
					<div class="col-inner"><?php dynamic_sidebar('footer-1'); ?></div>
				</div>
				<?php } ?>
				<?php if(is_active_sidebar( 'footer-2' )) { ?>
				<div class="site-footer-col col-lg-4 py-3">
					<div class="col-inner"><?php dynamic_sidebar('footer-2'); ?></div>
				</div>
				<?php } ?>
				<?php if(is_active_sidebar( 'footer-3' )) { ?>
				<div class="site-footer-col col-lg-4 py-3">
					<div class="col-inner"><?php dynamic_sidebar('footer-3'); ?></div>
				</div>
				<?php } ?>
			</div>
		</div>
		<?php
	}

	public static function footer_custom_scripts() {
		global $theme_setting;
		$custom_script = $theme_setting->get('footer_code', '');
		if(''!=$custom_script) {
			echo $custom_script;
		}

	}

	public static function body_open_custom_code() {
		global $theme_setting;
		$custom_script = $theme_setting->get('body_code', '');
		if(''!=$custom_script) {
			echo $custom_script;
		}
	}

	public static function noindex() {
		?>
		<meta name="robots" content="noindex, nofollow" />
		<?php
	}

	public static function head_youtube_scripts() {
		?>
		<script>
			// This code loads the IFrame Player API code asynchronously.
			var tag = document.createElement('script');
			tag.src = "https://www.youtube.com/iframe_api";
			var firstScriptTag = document.getElementsByTagName('script')[0];
			firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);
		 </script>
		<?php
	}

	public static function head_scripts() {
		global $theme_setting;
		?>
		<style type="text/css">
			.grecaptcha-badge {
				right: -999999px!important;
			}
			/* PART 1 - Before Lazy Load */
			img[data-lazyloaded]{
				opacity: 0;
			}
			/* PART 2 - Upon Lazy Load */
			img.litespeed-loaded{
				-webkit-transition: opacity .3s linear 0.1s;
				-moz-transition: opacity .3s linear 0.1s;
				transition: opacity .3s linear 0.1s;
				opacity: 1;
			}
			/*@media (min-width: 576px) {
				
			}*/
		</style>
		<script type="text/javascript">
			/*
			window.addEventListener('DOMContentLoaded', function(){
				const root = document.querySelector(':root');
				root.style.setProperty('--footer-buttons-fixed--height', document.getElementById('footer-buttons-fixed').clientHeight+'px');
				root.style.setProperty('--site-header--height', document.getElementById('site-header').clientHeight+'px');
				window.addEventListener('resize', function(){
					root.style.setProperty('--footer-buttons-fixed--height', document.getElementById('footer-buttons-fixed').clientHeight+'px');
					root.style.setProperty('--site-header--height', document.getElementById('site-header').clientHeight+'px');
				});
			});
			*/
		</script>
		<?php
		$custom_script = $theme_setting->get('head_code', '');
		if(''!=$custom_script) {
			echo $custom_script;
		}
	}
}