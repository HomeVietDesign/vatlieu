window.addEventListener('DOMContentLoaded', function(){
	jQuery(function($){
		$(document).on('click', '.row-actions .editinline', function(e){
			let _this = $(this),
				tr = _this.parents('tr'),
				id = tr.find('input[name="post[]"]').val();

			setTimeout(function(){
				let $occupation_checklist = $('body').find('tr#edit-'+id).find('ul.occupation-checklist');
				$('<div class="input-text-wrap" style="margin-bottom:5px;"><input class="find-list-occupation" type="text" placeholder="Tìm..."></div>').insertBefore($occupation_checklist);

				let $design_interior_checklist = $('body').find('tr#edit-'+id).find('ul.design_interior-checklist');
				$('<div class="input-text-wrap" style="margin-bottom:5px;"><input class="find-list-design_interior" type="text" placeholder="Tìm..."></div>').insertBefore($design_interior_checklist);

				let $design_exterior_checklist = $('body').find('tr#edit-'+id).find('ul.design_exterior-checklist');
				$('<div class="input-text-wrap" style="margin-bottom:5px;"><input class="find-list-design_exterior" type="text" placeholder="Tìm..."></div>').insertBefore($design_exterior_checklist);

			}, 100);
		});

		$(document).on('keyup change click', 'input.find-list-design_exterior', function(e){
			let that = $(this),
				s = that.val().toLowerCase();
			$('ul.design_exterior-checklist li').filter(function() {
				$(this).toggle($(this).text().toLowerCase().indexOf(s) > -1);
			});

		});

		$(document).on('keyup change click', 'input.find-list-design_interior', function(e){
			let that = $(this),
				s = that.val().toLowerCase();
			$('ul.design_interior-checklist li').filter(function() {
				$(this).toggle($(this).text().toLowerCase().indexOf(s) > -1);
			});

		});

		$(document).on('keyup change click', 'input.find-list-occupation', function(e){
			let that = $(this),
				s = that.val().toLowerCase();
			$('ul.occupation-checklist li').filter(function() {
				$(this).toggle($(this).text().toLowerCase().indexOf(s) > -1);
			});

		});
	});
});