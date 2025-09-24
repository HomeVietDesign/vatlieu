window.addEventListener('DOMContentLoaded', function(){
	jQuery(function($){

		let phone_number_el = $('#fw-option-_phone_number'),
			ajax_check = null;

		phone_number_el.parent().append('<span id="_phone_number_error" class="required"></span>');

		console.log(phone_number_el);
		
		function check_contractor_exists() {
			let phone_number = phone_number_el.val(),
				id = $('#post_ID').val(),
				msg_phone_number_el = phone_number_el.next('#_phone_number_error'),
				not_exists = false;

			phone_number = sanitize_phone_number(phone_number);
		
			if(ajax_check!=null) ajax_check.abort();
			ajax_check = $.ajax({
				url: ajaxurl,
				type: 'post',
				dataType: 'json',
				async: false,
				data: {action:'check_contractor_exists', phone_number:phone_number, id:id},
				beforeSend: function(xhr) {
					msg_phone_number_el.html('Đang kiểm tra...');
				},
				success: function(response) {
					if(response) {
						msg_phone_number_el.focus();
						msg_phone_number_el.html('Số điện thoại đã tồn tại!');
					} else {
						msg_phone_number_el.html('');
						not_exists = true;
					}
				}
			});

			return not_exists;
		}

		$('#publish').on('click', function(e){
			let not_exists = check_contractor_exists();
			if(!not_exists) {
				$('html,body').scrollTop($('#fw-option-_phone_number').closest('.postbox-container').offset().top);
			}
			return not_exists;
		});
		phone_number_el.on('keyup change', function(e){
			check_contractor_exists();
		});

		$('<input id="find-occupationchecklist" type="text" placeholder="Tìm..." style="width:100%">').insertBefore($('#occupation-tabs'));
		$(document).on('keyup', '#find-occupationchecklist', function(){
			let that = $(this),
				s = that.val().toLowerCase();
			$('#occupationchecklist li').filter(function() {
				$(this).toggle($(this).text().toLowerCase().indexOf(s) > -1);
			});
		});

		$(document).on('click', '.row-actions .editinline', function(e){
			let _this = $(this),
				tr = _this.parents('tr'),
				id = tr.find('input[name="post[]"]').val();

			setTimeout(function(){
				let $occupation_checklist = $('body').find('tr#edit-'+id).find('ul.occupation-checklist');
				$('<span class="input-text-wrap"><input class="find-list-occupation" type="text" placeholder="Tìm..."></span>').insertBefore($occupation_checklist);
			}, 100);
		});

		$(document).on('keyup change', 'input.find-list-occupation', function(e){
			let that = $(this),
				s = that.val().toLowerCase();
			$('ul.occupation-checklist li').filter(function() {
				$(this).toggle($(this).text().toLowerCase().indexOf(s) > -1);
			});
		});

	});
});