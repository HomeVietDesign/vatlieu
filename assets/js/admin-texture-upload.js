window.addEventListener('DOMContentLoaded', function(){
	jQuery(function($){
		
		function _sendFileToServer(i=0, sendData) {
			var uploadURL = ajaxurl+'?action=admin_texture_upload'; //Upload URL
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
				beforeSend: function() {
					
				},
				success: function(data){
					status.setProgress(100);
					if(i<sendData.length-1) {
						_sendFileToServer(i+1, sendData);
					} else {
						$('#texture-upload-status').html('Done!');
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
			
			this.statusbar = $("<div class='statusbar "+row+"'></div>");
			this.filename = $("<div class='filename'></div>").appendTo(this.statusbar);
			this.size = $("<div class='filesize'></div>").appendTo(this.statusbar);
			this.progressBar = $("<div class='progressBar'><div></div></div>").appendTo(this.statusbar);
			this.abort = $("<button type='button' class='abort' disabled>Abort</button>").appendTo(this.statusbar);
			
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
			var project = $('#upload_to_project').val(),
				design_type = $('#upload_to_design_type').val(),
				nonce = $('#upload_nonce').val(),
				data = [];

			for (var i = 0; i < files.length; i++) {
				var fd = new FormData();
				fd.append('project', project);
				fd.append('design_type', design_type);
				fd.append('nonce', nonce);
				fd.append('image', files[i]);

				var status = new createStatusbar(); //Using this we can set progress.
				status.setFileNameSize(files[i].name,files[i].size);

				data.push({
					formData: fd,
					status: status
				});

				//sendFileToServer(fd,status);
			}

			_sendFileToServer(0, data);
		}
		
		var $dragandrop = $("#texture-upload-dragandrop-handler");
		$dragandrop.on('dragenter', function (e) {
			e.stopPropagation();
			e.preventDefault();

			$(this).css('border', '2px solid #0B85A1');

		}).on('dragover', function (e) {
			e.stopPropagation();
			e.preventDefault();

		}).on('drop', function (e) {
			e.preventDefault();

			$(this).css('border', '2px dotted #0B85A1');
			
			var files = e.originalEvent.dataTransfer.files;

			//$('#texture-upload-statusbars').html('');

			$('#texture-upload-status').html('');

			//We need to send dropped files to Server
			handleFileUpload(files);
		});
		
		$(document).on('dragenter', function (e) {
			e.stopPropagation();
			e.preventDefault();
		}).on('dragover', function (e) {
			e.stopPropagation();
			e.preventDefault();
			$dragandrop.css('border', '2px dotted #0B85A1');
		}).on('drop', function (e) {
			e.stopPropagation();
			e.preventDefault();
		});

	});
});