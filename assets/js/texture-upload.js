window.addEventListener('DOMContentLoaded', function(){
	jQuery(function($){
		const texture_upload_toast = document.getElementById('texture-upload-toast');
		const toastBootstrapUpload = bootstrap.Toast.getOrCreateInstance(texture_upload_toast);

		function _sendFileToServer(i=0, sendData) {
			var uploadURL = theme.ajax_url+'?action=texture_upload'; //Upload URL
			//var extraData = {}; //Extra Data.
			var formData = sendData[i].formData, status = sendData[i].status;

			status.showAbort();

			var jqXHR = $.ajax({
				xhr: function() {
					var xhrobj = $.ajaxSettings.xhr();
					if (xhrobj.upload) {
						xhrobj.upload.addEventListener('progress', function(event) {
							var percent = 0;
							var position = event.loaded || event.position;
							var total = event.total;
							if (event.lengthComputable) {
								percent = Math.ceil(position / total * 100);
							}
							//Set progress
							status.setProgress(percent);
						}, false);
					}
					return xhrobj;
				},
				url: uploadURL,
				type: "POST",
				contentType:false,
				processData: false,
				cache: false,
				data: formData,
				dataType: 'json',
				beforeSend: function() {
					
				},
				success: function(data){
					//console.log(data);
					status.setProgress(100);
					status.progressBar.addClass('completed');
					status.progressBar.text(data);
				},
				error: function(jqXHR, textStatus, errorThrown) {
					status.setProgress(0);
					status.progressBar.addClass('error');
					status.progressBar.text(JSON.parse(jqXHR.responseText));
				},
				complete: function() {
					if(i<sendData.length-1) {
						_sendFileToServer(i+1, sendData);
					} else {
						setTimeout(function(){
							toastBootstrapUpload.hide();
						}, 5000);
					}
				}
			}); 

			status.setAbort(jqXHR);
		}

		var rowCount=0;
		function createStatusbar() {
			rowCount++;
			var row="odd";
			
			if(rowCount %2 ==0) row ="even";
			
			this.statusbar = $("<div class='d-flex p-1 border justify-content-between mb-1 statusbar "+row+"'></div>");
			this.filename = $("<div class='filename text-truncate'></div>").appendTo(this.statusbar);
			this.size = $("<div class='filesize'></div>").appendTo(this.statusbar);
			this.abort_wrap = $("<div class='abort-wrap'></div>").appendTo(this.statusbar);
			this.abort = $("<button type='button' class='abort' disabled>Hủy</button>");
			this.abort_wrap.html(this.abort);
			this.progressBar = $("<div class='progressBar text-center'><div></div></div>").appendTo(this.statusbar);
			
			$('#texture-upload-statusbars').append(this.statusbar);

			this.setFileNameSize = function(name,size) {
				var sizeStr="";
				var sizeKB = size/1024;
				if(parseInt(sizeKB) > 1024) {
					var sizeMB = sizeKB/1024;
					sizeStr = sizeMB.toFixed(2)+" MB";
				} else {
					sizeStr = sizeKB.toFixed(2)+" KB";
				}

				this.filename.html(name);
				this.size.html(sizeStr);
			}

			this.setProgress = function(progress) {       
				var progressBarWidth = progress*this.progressBar.width() / 100;  
				this.progressBar.find('div').animate({ width: progressBarWidth }, 10).html(progress + "% ");
				if(parseInt(progress) >= 100) {
					this.abort.hide();
				}
			}

			this.setAbort = function(jqxhr) {
				var sb = this.statusbar;
				this.abort.click(function() {
					jqxhr.abort();
					sb.hide();
				});
			}

			this.showAbort = function() {
				this.abort.prop('disabled', false);
			}

		}

		function handleFileUpload(files) {
			var ucate = $('#ucate').val(),
				uocc = $('#uocc').val(),
				upro = $('#upro').val(),
				uint = $('#uint').val(),
				uext = $('#uext').val(),
				unonce = $('#unonce').val(),
				data = [];

			for (var i = 0; i < files.length; i++) {
				var fd = new FormData();
				fd.append('ucate', ucate);
				fd.append('uocc', uocc);
				fd.append('upro', upro);
				fd.append('uint', uint);
				fd.append('uext', uext);
				fd.append('unonce', unonce);
				fd.append('image', files[i]);

				var status = new createStatusbar(); //Using this we can set progress.
				status.setFileNameSize(files[i].name,files[i].size);

				data.push({
					formData: fd,
					status: status
				});

			}

			_sendFileToServer(0, data);

			toastBootstrapUpload.show();
		}

		var $dragandrop = $("#texture-upload-dragandrop-handler");
		$dragandrop.on('dragenter', function (e) {
			e.stopPropagation();
			e.preventDefault();

			$(this).addClass('border-danger');

		}).on('dragover', function (e) {
			e.stopPropagation();
			e.preventDefault();

		}).on('drop', function (e) {
			e.preventDefault();

			$(this).removeClass('border-danger');
			$(this).addClass('border-primary');
			
			var files = e.originalEvent.dataTransfer.files;

			if(files.length>0) {
				//We need to send dropped files to Server
				handleFileUpload(files);
			}
		});
		
		$(document).on('dragenter', function (e) {
			e.stopPropagation();
			e.preventDefault();
		}).on('dragover', function (e) {
			e.stopPropagation();
			e.preventDefault();
			$dragandrop.removeClass('border-danger');
			$dragandrop.addClass('border-primary');
		}).on('drop', function (e) {
			e.stopPropagation();
			e.preventDefault();
		});

		$('input#umap').on('input', function(e){
			//console.log($(this));
			var files = $(this).prop('files');

			if(files.length>0) {
				//We need to send dropped files to Server
				handleFileUpload(files);
			}
		});
	});
});