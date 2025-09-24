window.addEventListener('DOMContentLoaded', function(){
	jQuery(function($){
		$('label[for="tag-name"],label[for="name"]').html('Tên gọi');
		$('label[for="tag-description"],label[for="description"]').html('Số điện thoại');
		$('#description-description').html('Số điện thoại khách hàng dùng làm số định danh dự án.');
		$('#name-description').html('Tên gọi hiển thị thay cho số điện thoại để dễ nhận biết.');
		
		/*$('.form-field.term-description-wrap').insertBefore($('.form-field.term-name-wrap'));

		const mutationObserver = new MutationObserver(mutationsList => {
			for (const mutation of mutationsList) {

				if (mutation.type === 'childList' && mutation.addedNodes.length > 0) {
					mutation.addedNodes.forEach(node => {
						// Check if the added node is an element and matches your dynamic element selector
						
						// if (node.nodeType === Node.ELEMENT_NODE && node.matches('.dynamic-element')) {
						// 	intersectionObserver.observe(node); // Start observing the newly added dynamic element
						// }

						//console.log(node);

						if (node.nodeType === Node.ELEMENT_NODE && node.matches('.inline-edit-row.inline-editor')) {
							let $node = $(node);
							const termId = $node.attr('id').replace('edit-', '');
							
							$node.find('fieldset:first-child .inline-edit-col label:first-child .title').text('Số điện thoại');
							//$node.find('.inline-edit-col label:first-child .title').text('Số điện thoại');

							$('input[name="desc"]').val($('#custom_inline_'+termId).find('.description').text());
						}
					});
				}
			}
		});

		// Start observing the target node (e.g., document.body) for childList changes
		mutationObserver.observe(document.body, { childList: true, subtree: true });*/
	});
});