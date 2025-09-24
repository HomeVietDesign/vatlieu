window.addEventListener('DOMContentLoaded', function(){
	const popoverTriggerList = document.querySelectorAll('[data-bs-toggle="popover"]');
	const popoverList = [...popoverTriggerList].map(popoverTriggerEl => new bootstrap.Popover(popoverTriggerEl));
	const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
	const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));

	jQuery(function($){

		function do_contractors_sortable() {
			$(".contractors-sortable").sortable({
				zIndex: 1022,
				//placeholder: "ui-state-highlight",
				cancel: ".no-drag",
				update: function( event, ui ) {
					let $list = $(this).closest('.occupation'),
						ids = [];
					$list.find('.contractor.ui-sortable-handle').each(function(i,e){
						ids.push($(e).data('id'));
					});

					//console.log(ids);
					
					$.ajax({
						url: theme.ajax_url,
						type: 'POST',
						data: {
							action: 'sort_contractors',
							nonce: theme.nonce,
							ids:ids
						},
						beforeSend: function(xhr) {
							
						},
						success: function(response) {
							//console.log(response);
						},
						error: function() {
							
						},
						complete: function() {
							
						}
					});
					
				}
			});
		}

		$(document).on('click', '.add-to-local-project', function(e){
			if(confirm('Thêm vào địa phương dự án này?')) {
				let $button = $(this)
					,texture = $button.data('texture')
					,project = $button.data('project')
					,contractor = $button.data('contractor')
					;
				$.ajax({
					url: theme.ajax_url,
					type: 'POST',
					data: {
						action: 'add_to_local_project',
						nonce: theme.nonce,
						texture:texture,
						project:project,
						contractor:contractor
					},
					beforeSend: function(xhr) {
						
					},
					success: function(response) {
						//console.log(response);
						if(response['code']==200) {
							if(response['html']!='') {
								$('#project-contractors').html(response['html']['html']);
								$('#project-local-contractors').html(response['html']['html_local']);
								Object.entries(response['html']['html_cats']).forEach(([key, value]) => {
									$('#'+key).html(value);
								});

								do_contractors_sortable();
							}
						}
					},
					error: function() {
						
					},
					complete: function() {
						
					}
				});
			}
		});

		$(document).on('click', '.remove-from-local-project', function(e){
			if(confirm('Loại khỏi địa phương dự án này?')) {
				let $button = $(this)
					,texture = $button.data('texture')
					,project = $button.data('project')
					,contractor = $button.data('contractor')
					;
				$.ajax({
					url: theme.ajax_url,
					type: 'POST',
					data: {
						action: 'remove_from_local_project',
						nonce: theme.nonce,
						texture:texture,
						project:project,
						contractor:contractor
					},
					beforeSend: function(xhr) {
						
					},
					success: function(response) {
						//console.log(response);
						if(response['code']==200) {
							if(response['html']!='') {
								$('#project-contractors').html(response['html']['html']);
								$('#project-local-contractors').html(response['html']['html_local']);
								Object.entries(response['html']['html_cats']).forEach(([key, value]) => {
									$('#'+key).html(value);
								});
								do_contractors_sortable();
							}
						}
					},
					error: function() {
						
					},
					complete: function() {
						
					}
				});
			}
		});

		$(document).on('click', '.add-to-project', function(e){
			if(confirm('Đề cử cho dự án này?')) {
				let $button = $(this)
					,texture = $button.data('texture')
					,project = $button.data('project')
					,contractor = $button.data('contractor')
					;
				$.ajax({
					url: theme.ajax_url,
					type: 'POST',
					data: {
						action: 'add_to_project',
						nonce: theme.nonce,
						texture:texture,
						project:project,
						contractor:contractor
					},
					beforeSend: function(xhr) {
						
					},
					success: function(response) {
						if(response['code']==200) {
							if(response['html']!='') {
								$('#project-contractors').html(response['html']['html']);
								$('#project-local-contractors').html(response['html']['html_local']);
								Object.entries(response['html']['html_cats']).forEach(([key, value]) => {
									$('#'+key).html(value);
								});

								do_contractors_sortable();
							}
						}
					},
					error: function() {
						
					},
					complete: function() {
						
					}
				});
			}
		});

		$(document).on('click', '.remove-from-project', function(e){
			if(confirm('Loại khỏi dự án này?')) {
				let $button = $(this)
					,texture = $button.data('texture')
					,project = $button.data('project')
					,contractor = $button.data('contractor')
					;
				$.ajax({
					url: theme.ajax_url,
					type: 'POST',
					data: {
						action: 'remove_from_project',
						nonce: theme.nonce,
						texture:texture,
						project:project,
						contractor:contractor
					},
					beforeSend: function(xhr) {
						
					},
					success: function(response) {
						//console.log(response);
						if(response['code']==200) {
							if(response['html']!='') {
								$('#project-contractors').html(response['html']['html']);
								$('#project-local-contractors').html(response['html']['html_local']);
								Object.entries(response['html']['html_cats']).forEach(([key, value]) => {
									$('#'+key).html(value);
								});

								do_contractors_sortable();
							}
						}
					},
					error: function() {
						
					},
					complete: function() {
						
					}
				});
			}
		});

		// edit contractor
		$('#edit-contractor').on('show.bs.modal', function (event) {
			let $modal = $(this),
				$button = $(event.relatedTarget)
				,$body = $modal.find('.modal-body')
				,texture = $button.data('texture')
				,project = $button.data('project')
				,contractor = $button.data('contractor')
				,contractor_title = $button.data('contractor-title')
				;

			$('#edit-contractor-label').text(contractor_title);

			$.ajax({
				url: theme.ajax_url,
				type: 'GET',
				data: {
					action: 'get_edit_contractor_form',
					texture:texture,
					project:project,
					contractor:contractor
				},
				beforeSend: function(xhr) {
					$body.text('Đang tải..');
				},
				success: function(response) {
					$body.html(response);
					// $('#cgroup-selection').select2({
					// 	width: '100%',
					// 	dropdownCssClass: 'cgroup-selection-dropdown'
					// });
				},
				error: function() {
					$body.text('Lỗi khi tải. Tắt mở lại.');
				},
				complete: function() {
					
				}
			});
			
		}).on('hidden.bs.modal', function (e) {
			let $modal = $(this),
				$body = $modal.find('.modal-body');

			$('#edit-contractor-label').text('');
			$body.text('');
		});

		// $(document).on('change', '#frm-edit-contractor .cgroup-check-input', function(e){
		// 	$(this).closest('.form-check-wrap').siblings().find('.cgroup-check-input').prop('checked', false);
		// });

		$(document).on('submit', '#frm-edit-contractor', function(e){
			e.preventDefault();
			let $form = $(this)
				//,formData = new FormData($form[0])
				,$button = $form.find('[type="submit"]')
				,$response = $('#edit-contractor-response')
				,data = $form.serialize()
				,formData = unserialize(data)
				;
			$button.prop('disabled', true);

			$.ajax({
				url: theme.ajax_url+'?action=update_contractor',
				type: 'POST',
				//data: formData,
				data: data,
				dataType: 'json',
				beforeSend: function() {
					$response.html('<p class="text-primary">Đang xử lý...</p>');
				},
				success: function(response) {
					if(response['code']==200) {
						$.ajax({
							url: theme.ajax_url+'?action=get_contractor_info',
							type: 'GET',
							dataType: 'json',
							data: data,
							success: function(response) {
								//$('.contractor-' + formData.contractor + ' .cgroups').html(response['cgroups']);
								$('#edit-contractor .btn-close').trigger('click');
							}
						});

						//$('#edit-contractor .btn-close').trigger('click');
					} else if(response['code']==0) {
						$('#edit-contractor .btn-close').trigger('click');
					}

					$response.html(response['msg']);
				},
				error: function(xhr) {
					$response.html('<p class="text-danger">Có lỗi xảy ra. Xin vui lòng thử lại.</p>');
				},
				complete: function() {
					$button.prop('disabled', false);
				}
			});
		});

		//---------------------------------------------------------

		$('a[href$="#"]').on('click', function(e){
			e.preventDefault();
			return false;
		});

		$('#search-project').on('input', debounce(function(event){
			let $input = $(this),
				kw = $input.val().toLowerCase(),
				$search_list = $('#project-list-search'),
				$list = $('#project-list');

			if(kw!='') {
				$search_list.removeClass('hidden');
				$list.addClass('hidden');

				let html = $('<div class="nav-item-wrapper"></div>');

				$list.find('.nav-link-text').filter(function(){
					let $link_text = $(this);
					if($link_text.text().toLowerCase().indexOf(kw) > -1) {
						let a = '<a class="nav-link dropdown-indicator label-1" href="'+$link_text.closest('a').attr('href')+'"><div class="d-flex align-items-center"><span class="nav-link-text-wrapper"><span class="nav-link-icon"></span><span class="nav-link-text">'+$link_text.text()+'</span></span></div></a>';
						html.append(a);
					}
				});
				
				$search_list.html(html);

			} else {
				$search_list.addClass('hidden');
				$list.removeClass('hidden');
			}
		}));

		$('#occupation-select').select2({
			dropdownCssClass: 'occupation-dropdown',
			selectionCssClass: 'occupation-selection'
		}).on('change', function(e){
			const occ =  parseInt($(this).val());
			const url = new URL(window.location.href);
			if (occ>0) {
				url.searchParams.set('occ', occ); // thêm hoặc cập nhật
			} else {
				url.searchParams.delete('occ'); // xóa nếu chọn rỗng
			}

			window.location.href = url.toString(); // chuyển hướng với URL mới
		});

		const theme_toast = document.getElementById('theme-toast');
		const toastBootstrap = bootstrap.Toast.getOrCreateInstance(theme_toast);

		$('.texture-copy-url').on('click', function(e){
			let url = $(this).data('url'), code = $(this).data('code');
			CopyToClipboard(url);
			let $toast = $(toastBootstrap._element);
			$toast.find('.toast-body').html('Đã sao chép URL của "'+code+'"');
			toastBootstrap.show();
		});


		var lightbox = new PhotoSwipeLightbox({
			gallery: '.pswp-gallery',
			children: 'a',
			pswpModule: PhotoSwipe 
		});
		lightbox.init();


		$('#occupation-selection').select2({
			width: '200px',
			dropdownCssClass: 'occupation-selection-dropdown'
		});

		$('#frm-occupation-selection').on('submit', function(e){
			let $frm = $(this),
				$btn = $frm.find('button[type="submit"]'),
				btn_text = $btn.text(),
				data = $frm.serialize();

			$.ajax({
				url: theme.ajax_url+'?action=occupation_selection_save&nonce='+theme.nonce,
				type: "POST",
				data: data,
				dataType: 'json',
				beforeSend: function() {
					$btn.text(btn_text+'...');
					$btn.prop('disabled', true);
				},
				success: function(data){
					$btn.text(btn_text);
					location.reload();
				},
				error: function() {
					
				},
				complete: function() {
					$btn.prop('disabled', false);
				}
			});

			return false;
		});
	});
});