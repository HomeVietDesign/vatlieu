<?php
namespace HomeViet;

class Ajax {

	public static function add_to_removed() {
		$response = [
			'code' => 0,
			'msg' => '',
			'data' => [],
			'html' => ''
		];

		if((current_user_can('edit_contractors')) && check_ajax_referer( 'global', 'nonce', false )) {
			$texture = isset($_POST['texture'])?absint($_POST['texture']):0;
			$project = isset($_POST['project'])?absint($_POST['project']):0;
			$contractor = isset($_POST['contractor'])?absint($_POST['contractor']):0;

			$texture_obj = \HomeViet\Texture::get_instance($texture);

			$project_obj = Project::get_instance(get_term_by('term_id', $project, 'project'));
			$project_obj->remove_contractor($contractor);
			$project_obj->remove_local_contractor($contractor);
			Template_Tags::add_to_removed_contractors($contractor);

			$response['code'] = 200;
			$response['msg'] = 'OK';
			$response['data'] = [
				'texture' => $texture,
				'project' => $project,
				'contractor' => $contractor,
			];

			$response['html'] = self::get_project_contractors($texture_obj, $project_obj, $contractor);

		} else {
			$response['code'] = 403;
			$response['msg'] = 'Forbiden.';
		}

		wp_send_json( $response );
	}

	public static function remove_from_removed() {
		$response = [
			'code' => 0,
			'msg' => '',
			'data' => [],
			'html' => ''
		];

		if((current_user_can('edit_contractors')) && check_ajax_referer( 'global', 'nonce', false )) {
			$texture = isset($_POST['texture'])?absint($_POST['texture']):0;
			$project = isset($_POST['project'])?absint($_POST['project']):0;
			$contractor = isset($_POST['contractor'])?absint($_POST['contractor']):0;

			$texture_obj = \HomeViet\Texture::get_instance($texture);
			
			$project_obj = Project::get_instance(get_term_by('term_id', $project, 'project'));
			Template_Tags::remove_from_removed_contractors($contractor);

			$response['code'] = 200;
			$response['msg'] = 'OK';
			$response['data'] = [
				'texture' => $texture,
				'project' => $project,
				'contractor' => $contractor,
			];

			$response['html'] = self::get_project_contractors($texture_obj, $project_obj, $contractor);
			
		} else {
			$response['code'] = 403;
			$response['msg'] = 'Forbiden.';
		}

		wp_send_json( $response );
	}

	public static function add_new_location() {
		$response = [
			'code' => 0,
			'msg' => '',
			'data' => []
		];

		if((current_user_can('manage_locations')) && check_ajax_referer( 'add-new-location', 'nonce', false )) {
			$location_name = isset($_POST['location_name'])?sanitize_text_field($_POST['location_name']):'';
			
			if($location_name!='') {
				$insert = wp_insert_term( $location_name, 'location' );
				if($insert instanceof \WP_Error) {
					$response['code'] = 406;
					$response['msg'] = '<p class="text-danger">'.esc_html($insert->get_error_message()).'</p>';
				} else {
					if(function_exists('customtaxorder_set_db_term_order')) {
						//debug_log($insert);
						customtaxorder_set_db_term_order( $insert['term_id'], 99, 'location' );
					}
					$response['code'] = 200;
					$response['msg'] = '<p class="text-success">Đã tạo</p>';
				}
			}

		} else {
			$response['code'] = 403;
			$response['msg'] = '<p class="text-danger">Không đủ quyền hạn.</p>';
		}

		wp_send_json( $response );
	}

	public static function add_new_contractor() {
		global $wpdb;

		$response = [
			'code' => 0,
			'msg' => '',
			'data' => []
		];

		if((current_user_can('edit_contractors')) && check_ajax_referer( 'add-new-contractor', 'nonce', false )) {
			$contractor_title = isset($_POST['contractor_title'])?sanitize_text_field($_POST['contractor_title']):'';
			
			
			if($contractor_title!='') {
				$contractor_description = isset($_POST['contractor_description'])?sanitize_textarea_field($_POST['contractor_description']):'';
				$contractor_phone_number = isset($_POST['contractor_phone_number'])?sanitize_textarea_field($_POST['contractor_phone_number']):'';
				$contractor_occupation = isset($_POST['contractor_occupation'])?absint($_POST['contractor_occupation']):0;
				$contractor_source = isset($_POST['contractor_source'])?absint($_POST['contractor_source']):0;
				$location = isset($_POST['location'])?absint($_POST['location']):0;

				if( $contractor_phone_number!='' && \HomeViet\Admin\Contractor::contractor_exists($contractor_phone_number) ) {
					$response['code'] = 406;
					$response['msg'] = '<p class="text-danger">Số điện thoại đã tồn tại.</p>';
				} else {
					$insert = wp_insert_post( [
						'post_type' => 'contractor',
						'post_title' => $contractor_title,
						'post_content' => $contractor_description,
						'post_status' => 'publish'
					], true );

					if($insert instanceof \WP_Error) {
						$response['code'] = 406;
						$response['msg'] = '<p class="text-danger">'.esc_html($insert->get_error_message()).'</p>';
					} else if(is_int($insert)) {
						
						fw_set_db_post_option($insert, '_phone_number', $contractor_phone_number);
						update_post_meta( $insert, '_phone_number', $contractor_phone_number );

						$wpdb->update( $wpdb->posts, ['post_excerpt' => phone_8420($contractor_phone_number).' '.phone_0284($contractor_phone_number),'post_name' => 'c'.$insert], ['ID' => $insert] );

						if($contractor_occupation) {
							wp_set_object_terms( $insert, $contractor_occupation, 'occupation', false );
						}

						if($contractor_source) {
							wp_set_object_terms( $insert, $contractor_source, 'contractor_source', false );
						}
						
						if($location) {
							wp_set_object_terms( $insert, $location, 'location', false );
						}

						$response['code'] = 200;
						$response['msg'] = '<p class="text-success">Đã tạo</p>';
					}
				}
			} else {
				$response['code'] = 406;
				$response['msg'] = '<p class="text-danger">Phải có tiêu đề</p>';
			}

		} else {
			$response['code'] = 403;
			$response['msg'] = '<p class="text-danger">Không đủ quyền hạn.</p>';
		}

		wp_send_json( $response );
	}

	public static function add_new_occupation() {
		$response = [
			'code' => 0,
			'msg' => '',
			'data' => []
		];

		if((current_user_can('manage_occupations')) && check_ajax_referer( 'add-new-occupation', 'nonce', false )) {
			$occupation_name = isset($_POST['occupation_name'])?sanitize_text_field($_POST['occupation_name']):'';
			
			if($occupation_name!='') {
				$insert = wp_insert_term( $occupation_name, 'occupation' );
				if($insert instanceof \WP_Error) {
					$response['code'] = 406;
					$response['msg'] = '<p class="text-danger">'.esc_html($insert->get_error_message()).'</p>';
				} else {
					$response['code'] = 200;
					$response['msg'] = '<p class="text-success">Đã tạo</p>';
				}
			}

		} else {
			$response['code'] = 403;
			$response['msg'] = '<p class="text-danger">Không đủ quyền hạn.</p>';
		}

		wp_send_json( $response );
	}

	public static function get_project_contractors($texture, $project, $contractor=0) {
		$occupation = null;
		$occupations = get_the_terms( $texture->post, 'occupation' ); // hạng mục
		if($occupations) {
			$occupation = \HomeViet\Occupation::get_instance($occupations[0]);
		}

		// if($contractor>0) {
		// 	$contractor_cats = get_the_terms( $contractor, 'contractor_cat' ); // phân nhóm
		// } else {
		// 	$contractor_cats = get_terms([
		// 		'taxonomy'=>'contractor_cat',
		// 		'hide_empty' => true
		// 	]); // phân nhóm
		// }

		$removed_contractors = Template_Tags::get_removed_contractors();
		
		//debug_log($contractor_cats);

		$return = [
			'html' => '',
			'html_local' => '',
			'html_cats' => [],
			'html_removed' => '',
		];

		ob_start();
		
		\HomeViet\Template_Tags::contractors([
			'texture' => $texture, 
			'project' => $project, 
			'occupation'=>$occupation, 
			'type'=>Template_Tags::IN_PROJECT,
			'contractors' => $project->get_contractors(), 
			'local_contractors' => $project->get_local_contractors(), 
			'removed_contractors' => $removed_contractors, 
		]);

		$return['html'] = ob_get_clean();

		ob_start();
			
		\HomeViet\Template_Tags::contractors([ 
			'texture' => $texture, 
			'project' => $project, 
			'occupation'=>$occupation, 
			'type'=>Template_Tags::IN_LOCATION,
			'contractors' => $project->get_contractors(), 
			'local_contractors' => $project->get_local_contractors(), 
			'removed_contractors' => $removed_contractors, 
		]);

		$return['html_local'] = ob_get_clean();

		// if($contractor_cats) {
		// 	foreach ($contractor_cats as $key => $value) {
		// 		ob_start();
		
		// 		\HomeViet\Template_Tags::contractors([ 
		// 			'texture' => $texture, 
		// 			'project' => $project, 
		// 			'occupation'=>$occupation, 
		// 			'contractor_cat'=>$value, 
		// 			'contractors' => $project->get_contractors(), 
		// 			'local_contractors' => $project->get_local_contractors(), 
		// 			'removed_contractors' => $removed_contractors, 
		// 		]);

		// 		$return['html_cats']['contractors-cat-'.$value->term_id] = ob_get_clean();
		// 	}
		// } else {
			ob_start();
		
			\HomeViet\Template_Tags::contractors([ 
				'texture' => $texture, 
				'project' => $project, 
				'occupation'=>$occupation, 
				'type'=>Template_Tags::OUT_PROJECT,
				'contractors' => $project->get_contractors(), 
				'local_contractors' => $project->get_local_contractors(), 
				'removed_contractors' => $removed_contractors, 
			]);

			$return['html_cats']['contractors-cat-0'] = ob_get_clean();
		//}

		ob_start();
			
		\HomeViet\Template_Tags::contractors([ 
			'texture' => $texture, 
			'project' => $project, 
			'occupation'=>$occupation, 
			'type'=>Template_Tags::IN_REMOVED,
			'contractors' => $project->get_contractors(), 
			'local_contractors' => $project->get_local_contractors(), 
			'removed_contractors' => $removed_contractors, 
		]);

		$return['html_removed'] = ob_get_clean();

		return $return;
	}

	public static function add_to_local_project() {
		$response = [
			'code' => 0,
			'msg' => '',
			'data' => [],
			'html' => ''
		];

		if((current_user_can('edit_contractors')) && check_ajax_referer( 'global', 'nonce', false )) {
			$texture = isset($_POST['texture'])?absint($_POST['texture']):0;
			$project = isset($_POST['project'])?absint($_POST['project']):0;
			$contractor = isset($_POST['contractor'])?absint($_POST['contractor']):0;

			$texture_obj = \HomeViet\Texture::get_instance($texture);

			$project_obj = Project::get_instance(get_term_by('term_id', $project, 'project'));
			$project_obj->remove_contractor($contractor);
			Template_Tags::remove_from_removed_contractors($contractor);
			$project_obj->add_local_contractor($contractor);

			$response['code'] = 200;
			$response['msg'] = 'OK';
			$response['data'] = [
				'texture' => $texture,
				'project' => $project,
				'contractor' => $contractor,
			];

			$response['html'] = self::get_project_contractors($texture_obj, $project_obj, $contractor);

		} else {
			$response['code'] = 403;
			$response['msg'] = 'Forbiden.';
		}

		wp_send_json( $response );
	}

	public static function remove_from_local_project() {
		$response = [
			'code' => 0,
			'msg' => '',
			'data' => [],
			'html' => ''
		];

		if((current_user_can('edit_contractors')) && check_ajax_referer( 'global', 'nonce', false )) {
			$texture = isset($_POST['texture'])?absint($_POST['texture']):0;
			$project = isset($_POST['project'])?absint($_POST['project']):0;
			$contractor = isset($_POST['contractor'])?absint($_POST['contractor']):0;

			$texture_obj = \HomeViet\Texture::get_instance($texture);
			
			$project_obj = Project::get_instance(get_term_by('term_id', $project, 'project'));
			$project_obj->remove_local_contractor($contractor);

			$response['code'] = 200;
			$response['msg'] = 'OK';
			$response['data'] = [
				'texture' => $texture,
				'project' => $project,
				'contractor' => $contractor,
			];

			$response['html'] = self::get_project_contractors($texture_obj, $project_obj, $contractor);
			
		} else {
			$response['code'] = 403;
			$response['msg'] = 'Forbiden.';
		}

		wp_send_json( $response );
	}

	public static function add_to_project() {
		$response = [
			'code' => 0,
			'msg' => '',
			'data' => [],
			'html' => ''
		];

		if((current_user_can('edit_contractors')) && check_ajax_referer( 'global', 'nonce', false )) {
			$texture = isset($_POST['texture'])?absint($_POST['texture']):0;
			$project = isset($_POST['project'])?absint($_POST['project']):0;
			$contractor = isset($_POST['contractor'])?absint($_POST['contractor']):0;

			$texture_obj = \HomeViet\Texture::get_instance($texture);

			$project_obj = Project::get_instance(get_term_by('term_id', $project, 'project'));
			$project_obj->remove_local_contractor($contractor);
			Template_Tags::remove_from_removed_contractors($contractor);
			$project_obj->add_contractor($contractor);

			$response['code'] = 200;
			$response['msg'] = 'OK';
			$response['data'] = [
				'texture' => $texture,
				'project' => $project,
				'contractor' => $contractor,
			];

			$response['html'] = self::get_project_contractors($texture_obj, $project_obj, $contractor);
			
		} else {
			$response['code'] = 403;
			$response['msg'] = 'Forbiden.';
		}

		wp_send_json( $response );
	}

	public static function remove_from_project() {
		$response = [
			'code' => 0,
			'msg' => '',
			'data' => [],
			'html' => ''
		];

		if((current_user_can('edit_contractors')) && check_ajax_referer( 'global', 'nonce', false )) {
			$texture = isset($_POST['texture'])?absint($_POST['texture']):0;
			$project = isset($_POST['project'])?absint($_POST['project']):0;
			$contractor = isset($_POST['contractor'])?absint($_POST['contractor']):0;

			$texture_obj = \HomeViet\Texture::get_instance($texture);
			
			$project_obj = Project::get_instance(get_term_by('term_id', $project, 'project'));
			$project_obj->remove_contractor($contractor);

			$response['code'] = 200;
			$response['msg'] = 'OK';
			$response['data'] = [
				'texture' => $texture,
				'project' => $project,
				'contractor' => $contractor,
			];

			$response['html'] = self::get_project_contractors($texture_obj, $project_obj, $contractor);
			
		} else {
			$response['code'] = 403;
			$response['msg'] = 'Forbiden.';
		}

		wp_send_json( $response );
	}

	public static function sort_contractors() {
		$response = [
			'code' => 0,
			'msg' => '',
			'data' => []
		];

		if((current_user_can('edit_contractors')) && check_ajax_referer( 'global', 'nonce', false )) {
			$ids = isset($_POST['ids'])?array_map('absint', $_POST['ids']):[];

			if($ids) {
				$total = count($ids);
				foreach ($ids as $key => $id) {
					wp_update_post( ['ID'=>$id, 'menu_order'=>($total-$key)] );
				}

			}

			$response['code'] = 200;
			$response['msg'] = 'OK';
			$response['data'] = $ids;
		} else {
			$response['code'] = 403;
			$response['msg'] = 'Forbiden.';
		}

		wp_send_json( $response );
	}

	public static function update_contractor_sort() {
		$response = [
			'code' => 0,
			'msg' => '',
			'data' => []
		];

		if((current_user_can('edit_contractors')) && check_ajax_referer( 'global', 'nonce', false )) {
			$ids = isset($_POST['ids'])?array_map('absint', $_POST['ids']):[];
			$project = isset($_POST['project'])?absint($_POST['project']):0;
			$project_contractors = fw_get_db_term_option($project, 'project', 'contractors', []);
			$project_contractors_sort = [];

			if($ids) {
				foreach ($ids as $key => $id) {
					if(in_array($id, $project_contractors)) {
						$project_contractors_sort[] = $id;
						unset($project_contractors[array_search($id, $project_contractors)]);
					}
				}

				$project_contractors = array_merge($project_contractors_sort, $project_contractors);


				fw_set_db_term_option($project, 'project', 'contractors', $project_contractors);
			}
			
			if($project_contractors) {
				$total = count($project_contractors);
				foreach ($project_contractors as $key => $id) {
					wp_update_post( ['ID'=>$id, 'menu_order'=>($total-$key)] );
				}
			}

			$response['code'] = 200;
			$response['msg'] = 'OK';
			$response['data'] = $ids;
		} else {
			$response['code'] = 403;
			$response['msg'] = 'Forbiden.';
		}

		wp_send_json( $response );
	}

	public static function get_contractor_info() {
		$texture = isset($_GET['texture'])?absint($_GET['texture']):0;
		$project = isset($_GET['project'])?absint($_GET['project']):0;
		$contractor = isset($_GET['contractor'])?absint($_GET['contractor']):0;

		$response = [
			'cgroups' => '',
		];
		
		if($contractor) {
			// $cgroups = get_the_terms( $contractor, 'cgroup' );
			// if($cgroups) {
			// 	foreach ($cgroups as $cg) {
			// 		$response['cgroups'] .= '<span class="m-1 btn btn-sm btn-'.esc_attr($cg->description).' btn-shadow">'.esc_html($cg->name).'</span>';
			// 	}
			// }

			//$response['zalo'] = ($estimate['zalo'])?'<a class="btn btn-sm btn-shadow fw-bold" href="'.esc_url($estimate['zalo']).'" target="_blank">Zalo</a>':'';
		}

		wp_send_json($response);
	}

	public static function update_contractor() {
		$response = [
			'code' => 0,
			'msg' => '',
			'data' => []
		];

		if((current_user_can('edit_contractors')) && check_ajax_referer( 'edit-contractor', 'nonce', false )) {
			$contractor = isset($_POST['contractor'])?absint($_POST['contractor']):0;

			if($contractor) {
				$contractor_title = isset($_POST['contractor_title'])?sanitize_text_field($_POST['contractor_title']):0;
				$contractor_location = isset($_POST['contractor_location'])?array_map('absint',$_POST['contractor_location']):[];

				wp_update_post(['ID'=>$contractor, 'post_title'=>$contractor_title]);
				wp_set_object_terms( $contractor, $contractor_location, 'location', false );

				$response['code'] = 200;
				$response['msg'] = '<p class="text-success">Đã lưu</p>';

				// if(isset($_POST['cgroups'])) {
				// 	$cgroups = array_map('absint', $_POST['cgroups']);
				// 	//debug_log($cgroups);
				// 	$set = wp_set_object_terms( $contractor, $cgroups, 'cgroup', false );
				// 	if($set instanceof \WP_Error) {
				// 		$response['code'] = 404;
				// 		$response['msg'] = '<p class="text-danger"></p>';
				// 	} else {
				// 		$response['code'] = 200;
				// 		$response['msg'] = '<p class="text-success">Đã lưu</p>';
				// 	}
				// }
			}

		} else {
			$response['code'] = 403;
			$response['msg'] = '<p class="text-danger">Không đủ quyền hạn.</p>';
		}

		wp_send_json( $response );
	}

	public static function get_edit_contractor_form() {
		// $texture = isset($_GET['texture'])?absint($_GET['texture']):0;
		// $project = isset($_GET['project'])?absint($_GET['project']):0;
		$contractor = isset($_GET['contractor'])?absint($_GET['contractor']):0;

		if($contractor) {
			$locations = get_the_terms( $contractor, 'location' );
			if(!empty($locations)) {
				$locations = array_map(function($t){
					return $t->term_id;
				}, $locations);
			} else {
				$locations = [];
			}

			//debug($locations);

			$_locations = get_terms([
				'taxonomy' => 'location',
				'hide_empty' => false
			]);

			?>
			<form id="frm-edit-contractor" method="POST" action="">
				<input type="hidden" name="contractor" value="<?=$contractor?>">
				<?php wp_nonce_field( 'edit-contractor', 'nonce' ); ?>
				<div id="edit-contractor-response"></div>
				<div class="mb-3">
					Tiêu đề
					<input type="text" class="form-control" name="contractor_title" value="<?=esc_attr(get_the_title($contractor))?>">
				</div>
				<div class="mb-3">
					Địa điểm
					<select id="edit-location-selection" class="select2-hidden-accessible" name="contractor_location[]" multiple>
						<option value="0">--Không có--</option>
						<?php
						if($_locations) {
							foreach ($_locations as $key => $value) {
								?>
								<option value="<?=absint($value->term_id)?>" <?php selected( in_array($value->term_id, $locations), true ); ?>><?=esc_html($value->name)?></option>
								<?php
							}
						}
						?>
					</select>
					<?php
					// ob_start();
					// 	wp_dropdown_categories([
					// 		'taxonomy' => 'location',
					// 		'name' => 'contractor_location[]',
					// 		'id' => 'edit-location-selection',
					// 		'class' => 'select2-hidden-accessible',
					// 		'hide_empty' => false,
					// 		'selected' => $locations,
					// 		'hierarchical' => true,
					// 		'show_option_all'   => '',
					// 	]);
					// echo preg_replace('/<select/', '<select multiple', ob_get_clean());
					?>
				</div>
				<div class="mb-3">
					<button type="submit" class="btn btn-danger text-uppercase fw-bold text-yellow text-nowrap d-block w-100" id="edit-contractor-submit">Lưu lại</button>
				</div>
				
			</form>
			<?php
		}
		exit;
	}

	public static function occupation_selection_save() {
		$response = [
			'status' => 500,
			'message' => ''
		];

		if(!current_user_can('edit_contractors') || !(check_ajax_referer( 'global', 'nonce', false ))) {
			$response['status'] = 403;
			$response['message'] = "Forbiden.";
			wp_send_json( $response, $response['status'] );
		}

		$occupation = isset($_POST['occupation']) ? absint($_POST['occupation']) : 0;
		$texture = isset($_POST['texture']) ? absint($_POST['texture']) : 0;

		$set = wp_set_object_terms( $texture, [$occupation], 'occupation', false );
		if($set instanceof \WP_Error) {
			$response['status'] = 404;
			$response['message'] = "Invalid.";
		} else {
			$response['status'] = 200;
			$response['message'] = "OK";
		}

		wp_send_json( $response, $response['status'] );
	}

	public static function texture_upload() {

		$response = [
			'status' => 500,
			'message' => ''
		];

		if(!current_user_can('edit_textures') || !(check_ajax_referer( 'texture-upload', 'unonce', false ))) {
			wp_send_json( "Không đủ quyền hạn.", 403 );
		}

		$ucate = isset($_POST['ucate']) ? absint($_POST['ucate']) : 0;
		$uocc = isset($_POST['uocc']) ? absint($_POST['uocc']) : 0;
		$upro = isset($_POST['upro']) ? absint($_POST['upro']) : 0;
		$uint = isset($_POST['uint']) ? absint($_POST['uint']) : 0;
		$uext = isset($_POST['uext']) ? absint($_POST['uext']) : 0;

		$upload = isset($_FILES['image']) ? $_FILES['image'] : null;

		if(strpos($upload['type'], 'image')===false) {
			wp_send_json( "File không phải là ảnh.", 403 );
		}

		// tải lên file dự toán
		if ( ! function_exists( 'media_handle_upload' ) ) {
			require_once(ABSPATH . "wp-admin" . '/includes/image.php');
			require_once(ABSPATH . "wp-admin" . '/includes/file.php');
			require_once(ABSPATH . "wp-admin" . '/includes/media.php');
		}

		$attachment_id = media_handle_upload( 'image', 0 );

		if ($upload['error']==0 && $attachment_id && ! is_array( $attachment_id ) ) {
			$path_parts = pathinfo($upload['full_path']);
			$insert_id = wp_insert_post([
				'post_title' => $path_parts['filename'],
				'post_type' => 'texture',
				'post_status' => 'publish',
			]);

			if(is_int($insert_id)) {

				set_post_thumbnail( $insert_id, $attachment_id );
				
				wp_update_post([
					'ID' => $attachment_id,
					'post_parent' => $insert_id
				]);
				
				if($ucate>0) {
					wp_set_object_terms( $insert_id, [$ucate], 'design_cat' );
				}
				if($uocc>0) {
					wp_set_object_terms( $insert_id, [$uocc], 'occupation' );
				}
				if($upro>0) {
					wp_set_object_terms( $insert_id, [$upro], 'project' );
				}
				if($uint>0) {
					wp_set_object_terms( $insert_id, [$uint], 'design_interior' );
				}
				if($uext>0) {
					wp_set_object_terms( $insert_id, [$uext], 'design_exterior' );
				}
				
			}

			$response['status'] = 200;
			$response['message'] = 'Hoàn thành.';
		} else {
			$response['message'] = "File error.";
			$response['status'] = 500;
		}

		wp_send_json( $response['message'], $response['status'] );
	}

	public static function texture_download() {
		//$user = wp_get_current_user();
		$id = isset($_REQUEST['id']) ? absint($_REQUEST['id']) : 0;
		$texture = \HomeViet\Texture::get_instance($id);
		if ($texture->id) {
			$upload_dir = wp_get_upload_dir();

			$image_url = $texture->get_image_src();
			//debug_log($image_url);

			if( !file_exists($upload_dir['basedir'].'/download/') ) {
				wp_mkdir_p($upload_dir['basedir'].'/download/');
			}
			
			$save_path = $upload_dir['basedir'].'/download/'.$texture->post->post_name.'.'.pathinfo(basename($image_url), PATHINFO_EXTENSION);

			if(strpos($image_url, home_url())===false) {
				// If the function it's not available, require it.
				if ( ! function_exists( 'download_url' ) ) {
					require_once ABSPATH . 'wp-admin/includes/file.php';
				}

				// Now you can use it!
				$tmp_file = download_url( $image_url );

				if(is_wp_error($tmp_file)) {
					echo 'Download failure: '.$image_url;
				} else {
					// Copies the file to the final destination and deletes temporary file.
					@copy( $tmp_file, $save_path );
					@unlink( $tmp_file );
				}
			} else {
				if($texture->get_image_file()) {
					@copy( $texture->get_image_file(), $save_path );	
				}
			}
			
			if(file_exists($save_path)) {
				// Thiết lập header để trình duyệt tải file về
				header('Content-Description: File Transfer');
				header('Content-Type: application/octet-stream');
				header('Content-Disposition: attachment; filename="' . basename($save_path) . '"');
				header('Expires: 0');
				header('Cache-Control: must-revalidate');
				header('Pragma: public');
				header('Content-Length: ' . filesize($save_path));
				ob_clean();
				flush();
				readfile($save_path);
				ignore_user_abort(true);
				unlink($save_path);

				exit;
			} else {
				echo 'File không tồn tại.';
			}
		}
		exit;
	}


	public static function texture_detail() {
		$id = isset($_REQUEST['id']) ? absint($_REQUEST['id']) : 0;
		$texture = \HomeViet\Texture::get_instance($id);
		if($texture->id) {
			if($texture->get('images')) {
			?>
			<div class="texture-detail px-3">
				<?php echo wp_get_the_content( $texture->post->post_content ); ?>
			</div>
			<?php
			}
		}
		exit;
	}


}
