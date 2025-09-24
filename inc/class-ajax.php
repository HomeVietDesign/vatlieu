<?php
namespace HomeViet;

class Ajax {

	public static function get_project_contractors($texture, $project, $contractor=0) {
		$occupation = null;
		$occupations = get_the_terms( $texture->post, 'occupation' ); // hạng mục
		if($occupations) {
			$occupation = \HomeViet\Occupation::get_instance($occupations[0]);
		}

		if($contractor>0) {
			$contractor_cats = get_the_terms( $contractor, 'contractor_cat' ); // phân nhóm
		} else {
			$contractor_cats = get_terms([
				'taxonomy'=>'contractor_cat',
				'hide_empty' => true
			]); // phân nhóm
		}
		
		//debug_log($contractor_cats);

		$excludes = array_merge($project->get_contractors(), $project->get_local_contractors());

		$return = [
			'html' => '',
			'html_local' => '',
			'html_cats' => [],
		];

		ob_start();
		
		\HomeViet\Template_Tags::contractors( [ 'texture' => $texture, 'project' => $project, 'occupation'=>$occupation, 'contractor_cat'=>-1, 'contractors' => $project->get_contractors(), 'local_contractors' => $project->get_local_contractors(), 'excludes' => [] ] );

		$return['html'] = ob_get_clean();

		ob_start();
			
		\HomeViet\Template_Tags::contractors( [ 'texture' => $texture, 'project' => $project, 'occupation'=>$occupation, 'contractor_cat'=>'', 'contractors'=>[], 'local_contractors' => $project->get_local_contractors(), 'excludes' => $project->get_contractors() ] );

		$return['html_local'] = ob_get_clean();

		if($contractor_cats) {
			foreach ($contractor_cats as $key => $value) {
				ob_start();
		
				\HomeViet\Template_Tags::contractors( [ 'texture' => $texture, 'project' => $project, 'occupation'=>$occupation, 'contractor_cat'=>$value, 'contractors' => [], 'local_contractors' => [], 'excludes' => $excludes ] );

				$return['html_cats']['contractors-cat-'.$value->term_id] = ob_get_clean();
			}
		} else {
			ob_start();
		
			\HomeViet\Template_Tags::contractors( [ 'texture' => $texture, 'project' => $project, 'occupation'=>$occupation, 'contractor_cat'=>0, 'contractors' => [], 'contractors' => [], 'excludes' => $excludes ] );

			$return['html_cats']['contractors-cat-0'] = ob_get_clean();
		}

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
			$texture = isset($_POST['texture'])?absint($_POST['texture']):0;
			$project = isset($_POST['project'])?absint($_POST['project']):0;
			$contractor = isset($_POST['contractor'])?absint($_POST['contractor']):0;

			if($texture && $project && $contractor) {
				
				if(isset($_POST['add_to_project'])) {
					$project_contractors = fw_get_db_term_option($project, 'project', 'contractors', []);
					if(!in_array($contractor, $project_contractors)) {
						$project_contractors[] = $contractor;
						fw_set_db_term_option($project, 'project', 'contractors', $project_contractors);
					}
				}

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
		$texture = isset($_GET['texture'])?absint($_GET['texture']):0;
		$project = isset($_GET['project'])?absint($_GET['project']):0;
		$contractor = isset($_GET['contractor'])?absint($_GET['contractor']):0;

		if($contractor && $texture && $project) {
			// $cgroups = get_the_terms( $contractor, 'cgroup' );
			// if(!empty($cgroups)) {
			// 	$cgroups = array_map(function($t){
			// 		return $t->term_id;
			// 	}, $cgroups);
			// } else {
			// 	$cgroups = [];
			// }
			// $_cgroups = get_terms([
			// 	'taxonomy' => 'cgroup',
			// 	'hide_empty' => false
			// ]);
			?>
			<form id="frm-edit-contractor" method="POST" action="">
				<input type="hidden" name="texture" value="<?=$texture?>">
				<input type="hidden" name="project" value="<?=$project?>">
				<input type="hidden" name="contractor" value="<?=$contractor?>">
				<?php wp_nonce_field( 'edit-contractor', 'nonce' ); ?>
				<div id="edit-contractor-response"></div>
				<div class="mb-3">
					<!-- <div class="form-check">
						<input class="form-check-input" type="checkbox" name="add_to_project" value="on" id="add-to-project">
						<label class="form-check-label" for="add-to-project">Đề cử cho dự án</label>
					</div> -->
				<?php
					// wp_dropdown_categories([
					// 	'taxonomy' => 'cgroup',
					// 	'name' => 'cgroup',
					// 	'id' => 'cgroup-selection',
					// 	'class' => 'select2-hidden-accessible',
					// 	'hide_empty' => false,
					// 	'selected' => (!empty($cgroups))?$cgroups[0]->term_id:0,
					// 	'hierarchical' => true,
					// 	'show_option_all'   => '--Phân nhóm--',
					// ]);
					/*
					if(!empty($_cgroups)) {
						foreach ($_cgroups as $key => $value) {
							?>
							<div class="form-check-wrap">
								<div class="form-check">
									<input class="form-check-input cgroup-check-input" type="checkbox" name="cgroups[]" value="<?=$value->term_id?>" id="check-cgroup-<?=$value->term_id?>" <?php checked( in_array($value->term_id, $cgroups), true ); ?>>
									<label class="form-check-label" for="check-cgroup-<?=$value->term_id?>"><?=esc_html($value->name)?></label>
								</div>
							</div>
							<?php
						}
					}
					*/
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
