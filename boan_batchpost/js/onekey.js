var I64BIT_TABLE =
'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789_-'.split('');
function hash(input){
	var hash = 5381;
	var i = input.length - 1;
	if(typeof input == 'string'){
	for (; i > -1; i--)
	hash += (hash << 5) + input.charCodeAt(i);
	}
	else{
	for (; i > -1; i--)
	hash += (hash << 5) + input[i];
	}
	var value = hash & 0x7FFFFFFF;
	var retValue = '';
	do{
	retValue += I64BIT_TABLE[value & 0x3F];
	}
	while(value >>= 6);
	return retValue;
}


var STATUSMSG = {
	'-1' : '内部服务器错误',
	'0' : '上传成功',
	'1' : '不支持此类扩展名',
	'2' : '服务器限制无法上传那么大的附件',
	'3' : '用户组限制无法上传那么大的附件',
	'4' : '不支持此类扩展名',
	'5' : '文件类型限制无法上传那么大的附件',
	'6' : '今日您已无法上传更多的附件',
	'8' : '附件文件无法保存',
	'9' : '没有合法的文件被上传',
	'10' : '非法操作',
	'11' : '今日您已无法上传那么大的附件'
};

function itemstatus(file_id,status,title){
	boan_jq('#dt_'+file_id+' img').css('display','block');
	if(status == 0){
		boan_jq('#dt_'+file_id+' img').attr('src','source/plugin/boan_batchpost/images/loading.gif');
	}else if(status==1){
		boan_jq('#dt_'+file_id+' img').attr('src','source/plugin/boan_batchpost/images/data_valid.gif');
	}else{
		boan_jq('#dt_'+file_id+' img').attr('src','source/plugin/boan_batchpost/images/data_invalid.gif');
	}
	if(title){
		boan_jq('#dt_'+file_id+' img').attr('title',title);
	}

}


boan_attlist = new Array();

if(pmethod == 3){
	setTimeout("boan_jq('.webuploader-element-invisible').attr('webkitdirectory', '')",200);
}

var boan_wrap = boan_jq('#uploader');
var boan_queue = boan_jq('<ul class="filelist"></ul>')
            .appendTo( boan_wrap.find('.queueList') ),
	
			// 状态栏，包括进度和控制按钮
			boan_statusBar = boan_wrap.find('.statusBar'),
	
			// 文件总体选择信息。
			boan_info = boan_statusBar.find('.info'),
	
			// 上传按钮
			boan_upload = boan_wrap.find('.uploadBtn'),
	
			// 没选择文件之前的内容。
			boan_placeHolder = boan_wrap.find('.placeholder'),
	
			// 总体进度条
			boan_progress = boan_statusBar.find('.progress').hide(),
	
			// 添加的文件数量
			fileCount = 0,
	
			// 添加的文件总大小
			fileSize = 0,
	
			// 可能有pedding, ready, uploading, confirm, done.
			state = 'pedding',
	
			// 所有文件的进度信息，key为file id
			percentages = {};

function updateTotalProgress() {
	var loaded = 0,

		total = 0,
		spans = boan_progress.children(),
		percent;

	boan_jq.each( percentages, function( k, v ) {
		total += v[ 0 ];
		loaded += v[ 0 ] * v[ 1 ];
	} );

	percent = total ? loaded / total : 0;

	spans.eq( 0 ).text( Math.round( percent * 100 ) + '%' );
	spans.eq( 1 ).css( 'width', Math.round( percent * 100 ) + '%' );
	updateStatus();
}

function updateStatus() {
	var text = '', stats;

	if ( state === 'ready' ) {
		text = '选中' + fileCount + '个文件，共' +
				WebUploader.formatSize( fileSize ) + '。';
	} else if ( state === 'confirm' ) {
		stats = Uploader.getStats();
		if ( stats.uploadFailNum ) {
			text = '已成功上传' + stats.successNum+ '个文件，'+
				stats.uploadFailNum + '个文件上传失败。'
		}

	} else {
		stats = Uploader.getStats();
		text = '共' + fileCount + '个文件（' +
				WebUploader.formatSize( fileSize )  +
				'），已上传' + stats.successNum + '个文件';

		if ( stats.uploadFailNum ) {
			text += '，失败' + stats.uploadFailNum + '个文件';
		}
	}

	boan_info.html( text );
}

 // 当有文件添加进来时执行，负责view的创建
    function addFile( file ) {
		var  index = file.name.lastIndexOf('.');
		var basefilename  = index >= 0 ? file.name.substring(0,index) : file.name;
		var re =new RegExp(separate+"\\d+","i");
		index = basefilename.search(re);
		var before = after = 0;
		if( pmethod == '3'){
			var arr = file.source.source.webkitRelativePath.split('/');
			basefilename = arr[0];
			if(arr.length>2){
				before = arr[1];
				basefilename += arr[1];
			}
		}
		if(index >= 0 && separate != '' && pmethod == '2'){
			before = basefilename.substring(0,index);
			after = basefilename.substring(index+1);
			after = parseInt(after);
			index = hash(before);
		}else{
			index = ((pmethod == 2 || pmethod == 3) ? hash(basefilename) : hash(file.name));
		}
		file.attindex = index;
		if(boan_attlist[index]){
			var boan_li =boan_jq('#'+file.attindex);
			boan_attlist[index].push({id:file.id,order:after,attid:0});
			percentages[ file.id ] = [ file.size, 0 ];
			boan_jq('<dt id="dt_'+file.id+'">'+file.name+'<img/ style="float:right"/></dt>').appendTo(boan_li.find('dl'));
			return;
		}else{
			boan_attlist[index] = new Array({id:file.id,order:after,attid:0});
			
		}
        var boan_li = boan_jq( '<li id="' + file.attindex + '" title=' + file.name + '>' +
                '<p class="upload_title">' + (before ? before : basefilename) + '</p>' +
                '<div class="imgWrap"><dl></dl></div>'+
                '<p class="progress"><span></span></p>' +
				'<p class="info"><span></span></p>' +
                '</li>' ),

            boan_btns = boan_jq('<div class="file-panel">' +
                '<span class="cancel">删除</span></div>').appendTo( boan_li ),
            boan_prgress = boan_li.find('p.progress span'),
            boan_wrap = boan_li.find( 'div.imgWrap' ),
            boan_info = boan_jq('<p class="error"></p>'),
			boan_filelist = boan_li.find('dl'),
			boan_info1 =  boan_li.find('p.info span'),
            showError = function( code ) {
                switch( code ) {
                    case 'exceed_size':
                        text = '文件大小超出';
                        break;

                    case 'interrupt':
                        text = '上传暂停';
                        break;

                    default:
                        text = '上传失败，请重试';
                        break;
                }

                boan_info.text( text ).appendTo( boan_li );
				boan_info1.css('display','none');
				
            };

        if ( file.getStatus() === 'invalid' ) {
            showError( file.statusText );
        } else {
			boan_jq('<dt id="dt_'+file.id+'">'+file.name+'<img style="float:right"/></dt>').appendTo(boan_filelist);
            percentages[ file.id ] = [ file.size, 0 ];
        }

        file.on('statuschange', function( cur, prev ) {
            if ( prev === 'progress' ) {
                //boan_prgress.hide().width(0);
            } else if ( prev === 'queued' ) {
				boan_info1.css('color','blue');
				boan_info1.text('上传附件中...');
                boan_btns.remove();
            }

            // 成功
            if ( cur === 'error' || cur === 'invalid' ) {
                console.log( file.statusText );
                showError( file.statusText );
                percentages[ file.id ][ 1 ] = 1;
            } else if ( cur === 'interrupt' ) {
                showError( 'interrupt' );
            } else if ( cur === 'queued' ) {
                percentages[ file.id ][ 1 ] = 0;
            } else if ( cur === 'progress' ) {
				boan_info.remove();
                boan_prgress.css('display', 'block');
            } else if ( cur === 'complete' ) {
               // boan_li.append( '<span class="success"></span>' );
            }

            boan_li.removeClass( 'state-' + prev ).addClass( 'state-' + cur );
        });
       boan_btns.on( 'click', 'span', function() {
            var index = boan_jq(this).index();

            switch ( index ) {
                case 0:
					for(i=0; i<boan_attlist[file.attindex].length;i++){
						var f = Uploader.getFile(boan_attlist[file.attindex][i].id);
						 Uploader.removeFile( f );
					}
                    delete boan_attlist[file.attindex];
                    return;
            }
        });

       boan_li.appendTo( boan_queue );
    }

    // 负责view的销毁
    function removeFile( file ) {
        var boan_li =boan_jq('#'+file.attindex);

        delete percentages[ file.id ];
        updateTotalProgress();
        boan_li.off().find('.file-panel').off().end().remove();
    }

  function setState( val ) {
        var file, stats;

        if ( val === state ) {
            return;
        }

        boan_upload.removeClass( 'state-' + state );
        boan_upload.addClass( 'state-' + val );
        state = val;

        switch ( state ) {
            case 'pedding':
                boan_placeHolder.removeClass( 'element-invisible' );
                boan_queue.parent().removeClass('filled');
                boan_queue.hide();
                boan_statusBar.addClass( 'element-invisible' );
                Uploader.refresh();
                break;

            case 'ready':
                boan_placeHolder.addClass( 'element-invisible' );
                boan_jq( '#filePicker2' ).removeClass( 'element-invisible');
                boan_queue.parent().addClass('filled');
                boan_queue.show();
                boan_statusBar.removeClass('element-invisible');
                Uploader.refresh();
                break;

            case 'uploading':
                boan_jq( '#filePicker2' ).addClass( 'element-invisible' );
                boan_progress.show();
                boan_upload.text( '暂停上传' );
                break;

            case 'paused':
                boan_progress.show();
                boan_upload.text( '继续上传' );
                break;

            case 'confirm':
                boan_progress.hide();
                boan_upload.text( '开始上传' ).addClass( 'disabled' );

                stats = Uploader.getStats();
               
                    setState( 'finish' );
                    return;
                
                break;
            case 'finish':
                stats = Uploader.getStats();
				boan_jq( '#filePicker2' ).removeClass( 'element-invisible' );
				boan_upload.text( '开始上传' ).removeClass( 'disabled' );
              
                break;
        }

        updateStatus();
 }


	function fileQueueError(errorCode,obj) {
		try {
			var err = '';
			switch (errorCode) {
			case 'F_EXCEED_SIZE':
				err = '单个文件大小不得超过' + WebUploader.Base.formatSize(obj.options['fileSingleSizeLimit']) + '！';
				break;
			case 'Q_EXCEED_NUM_LIMIT':
				err = '最多只能上传' + obj.options['fileNumLimit'] +'个！';
				break;
			case 'Q_EXCEED_SIZE_LIMIT':
				err = '上传文件总大小超出' + WebUploader.Base.formatSize(obj.options['fileSizeLimit']) + '！';
				break;
			case 'Q_TYPE_DENIED':
				err = '无效文件类型，请上传正确的文件类型';
				break;
			case 'F_DUPLICATE':
				err = '请不要重复上传相同文件';
				break;
			default:
				err = 'up_error' + code;
				break;
			}
			showDialog(err, 'notice', null, null, 0, null, null, null, null, 3);
		} catch (ex) {
			console.log(ex);
		}
	}
	
	function  get_signature(file,type){
		var obj = null;
		var url = 'plugin.php?id=boan_h5upload:ajax&oss=yes&type=forum&filename=';
		url += file.name.replace(/[\(\)'"<>]/g,'');
		var hash = boan_jq('#hash').val();
		var boan_h5upload_ossserver = boan_jq('#ossserver').val();
		url += '&hash='+ hash + '&atttype=' + type;
		boan_jq.ajax({
					type:"GET",
					url:url,
					async:false,
					success:function(data){
						if(data == -10 || data == 0){
							console.log('up error');
						}else{ 
							 if(boan_h5upload_ossserver == 'tencent'){
								 obj = data;
							 }else{
								 data = data.substr(0,data.indexOf('}')+1);
								 obj = eval ("(" + data + ")");
							 }
						}
					},
					error:function(xhr,status,error){
						console.log(error);
					},
		});
		return obj;
	}
	
	Uploader.addButton({
			id: '#filePicker2',
			label: '继续添加'
	});
	Uploader.on('beforeFileQueued',function(file){
			
		});
	Uploader.on('error', function(err_num){
		fileQueueError(err_num,this);
	});
	
	Uploader.on('fileQueued',function(file){
		if(pmethod == 3 && file.ext == 'db'){
			this.removeFile(file,true);
			return '';
			
		}
		
		fileCount++;
		fileSize += file.size;
	
		if ( fileCount === 1 ) {
			boan_placeHolder.addClass( 'element-invisible' );
			boan_statusBar.show();
		}
	
		addFile( file );
		setState( 'ready' );
		updateTotalProgress();
	});
	
	Uploader.on( 'uploadBeforeSend', function( object, data,headers ) {
	   // 修改data可以控制发送哪些携带数据。
	   data.type = 'attach';
	   delete data.lastModifiedDate;
	   delete data.name;
	   object.file.name = object.file.name.replace(/[\(\)'#"<>]/g,'');
	   if(object.file.ext.toLowerCase() == 'jpg' || object.file.ext.toLowerCase() == 'jpeg' || object.file.ext.toLowerCase() == 'png' || object.file.ext.toLowerCase() == 'gif'){
	   	 data.type = 'image';
	   }
	   if(object.file.post){
		 for(var i = 0 ; i < object.file.post.length;i++){
			 var v = object.file.post[i].split('|');
			 eval('data.' + v[0] + '="' + v[1] + '"');
		 }
	   }
	try{
		   var boan_h5upload_ossserver = boan_jq('#ossserver').val();
		   if(boan_h5upload_ossserver == 'aliyun'){
				var data1 = get_signature(object.file,data.type);		
				data = boan_jq.extend(data,{
					'key':data1.dir+data1.object,
					'policy' : data1.policy,
					'OSSAccessKeyId':data1.accessid, 
					'success_action_status':'200',
					'signature': data1.signature,
				});	
				object.file.name = object.file.name.replace(/[\(\)'"<>]/g,'');
				object.file.objectname  =  data1.dir+data1.object;
		   }else if(boan_h5upload_ossserver == 'qiniu'){
				var data1 = get_signature(object.file,data.type);	 
				data = boan_jq.extend(data,{
					'key':data1.filename,
					'token' : data1.token,
				});	
				object.file.name = object.file.name.replace(/[\(\)'"<>]/g,'');
				object.file.objectname  =  data1.filename;
		   }else if(boan_h5upload_ossserver == 'tencent'){
			   var data1 = get_signature(object.file,data.type);
			   var credentials = data1.credentials;
			   var Authorization = CosAuth({
							SecretId: credentials.tmpSecretId,
							SecretKey: credentials.tmpSecretKey,
							Method: 'POST',
							Pathname: '/',
						});
						
				
				data = boan_jq.extend(data,{
					'key' : data1.dir+data1.object,
					'x-cos-security-token' : credentials.sessionToken || '',
					'Signature' : Authorization,
					
				});	
			 
			  
			   object.file.objectname  =  data1.dir+data1.object;   
		   }
	}catch(e){
		console.log(e);
	}
	});

	Uploader.on('fileDequeued',function(file){
		fileCount--;
		fileSize -= file.size;
	
		if ( !fileCount ) {
			setState( 'pedding' );
		}
	
		removeFile( file );
		updateTotalProgress();
	});
	
	Uploader.on('uploadProgress',function(file,percentage){
		var boan_li = boan_jq('#'+file.attindex),
		avg = 0,
		boan_percent = boan_li.find('.progress span');
	    percentages[ file.id ][ 1 ] = percentage;
		for(i=0;i<boan_attlist[file.attindex].length;i++){
			var id = boan_attlist[file.attindex][i].id;
			avg += percentages[id][1]
		}
		avg /= i;
		boan_percent.css( 'width', avg * 100 + '%' );
		
		updateTotalProgress();
	});
	
	Uploader.on('uploadStart',function(file){
	   itemstatus(file.id,0);
	});
	
	Uploader.on( 'uploadSuccess', function(file,response) {
		var boan_h5upload_ossserver = boan_jq('#ossserver').val();
		var hash = boan_jq('#hash').val();
		if(boan_h5upload_ossserver ){
			var atttype = 'attach';
			var width = 0;
			if(file.ext.toLowerCase() == 'jpg' || file.ext.toLowerCase() == 'jpeg' || file.ext.toLowerCase() == 'png' || file.ext.toLowerCase() == 'gif'){
				 atttype = 'image';
				 if( typeof file._info !== 'undefined'){
				 	width = file._info.width;
				 }
			}
				var url = 'plugin.php?id=boan_h5upload:callback&type=forum&atttype=' + atttype + '&filename=' + encodeURI(file.name) + '&object=' + file.objectname +'&hash=' + hash + '&width=' + (width ? width : '');
				boan_jq.ajax({
						type:"GET",
						url:url,
						async:false,
						success:function(data){
							response = data;
						},
						error:function(xhr,status,error){
							console.log(error);
							response = -10;
						},
				});
			}
		
		var boan_li = boan_jq('#'+file.attindex);
		var boan_prgress = boan_li.find('.progress span');
		var boan_info1 =  boan_li.find('p.info span');
		var aid = parseInt(response);
		var flag = 1;
		var finish = 1;
		if(aid>0){
			itemstatus(file.id,1);
			for(i=0;i<boan_attlist[file.attindex].length;i++){
				if(boan_attlist[file.attindex][i].id == file.id){
					boan_attlist[file.attindex][i]['attid'] = aid;
					if((file.ext.toLowerCase() == 'jpg' || file.ext.toLowerCase() == 'jpeg') && (typeof file._info !== 'undefined')){
						boan_attlist[file.attindex][i]['wh'] = file._info.width + 'X' + file._info.height;
					}
					break;
				}
			}
		}else{
			itemstatus(file.id,2,'附件上传失败,出错代码:' + aid);
			for(i=0;i<boan_attlist[file.attindex].length;i++){
				if(boan_attlist[file.attindex][i].id == file.id){
					boan_attlist[file.attindex][i]['attid'] = -1;
					
				}
			}
			if(Ignore_error != '1'){
				boan_prgress.hide().width(0);
				boan_info1.css('color','red');
				boan_info1.text(STATUSMSG[-1*aid] + ',不予发贴');			
			}	
		}
		
		for(i=0;i<boan_attlist[file.attindex].length;i++){
			if(boan_attlist[file.attindex][i]['attid']<=0) {
						flag = 0;
			}
			if(boan_attlist[file.attindex][i]['attid'] == 0) {
						finish = 0;
			}
			
		}
		if(Ignore_error == '1' && finish){
			
			var arr_l = boan_attlist[file.attindex].length;
			var arr = boan_attlist[file.attindex].filter(att => att['attid'] > 0 );
			boan_attlist[file.attindex] = arr;
		
			if(boan_attlist[file.attindex].length >0 ){
				flag = 1;
			}
			
		}
		if(flag){
				boan_info1.css('color','blue');
				boan_info1.text('正在发贴...');
				var hash = boan_jq('#hash').val();
				var aids = '';
				var subject = boan_li.find('.upload_title').text();
				var setting = boan_jq('#setting').val();
				var hw = '';
				
				subject = subject.replace(/[\(\)'#"<>]/g,'')
				
				
				boan_attlist[file.attindex].sort(function(a,b){return a.order-b.order;});
				
				for(i=0;i<boan_attlist[file.attindex].length;i++){
					aids += '&aid' + i + '=' + boan_attlist[file.attindex][i]['attid'];
					if(typeof boan_attlist[file.attindex][i]['wh'] !== 'undefined' && hw == ''){
						hw = boan_attlist[file.attindex][i]['wh'];
					}
				}
				
				var url = 'plugin.php?id=boan_batchpost:ajax&op=onekey&hash='+hash + aids + '&subject='+encodeURI(subject) + '&setting=' + encodeURI(setting)  +  (hw != '' ? '&hw='+hw : '');
				boan_jq.ajax({
						type:"GET",
						url:url,
						success:function(data){
							data = eval ("(" + data + ")");
							if(data.code == 200){
								boan_li.append( '<span class="success"></span>' );
								boan_prgress.hide().width(0);
								boan_info1.hide();
							}else{
								boan_prgress.hide().width(0);
								boan_info1.css('color','red');
								boan_info1.text('发帖失败,错误信息:'+data.msg);	
								
							}
						},
						error:function(xhr,status,error){
							console.log(error);
							
						},
				});
			}
		

		 console.log(response);
		
	});
	
	Uploader.on( 'uploadError', function( file,reason ) {
		var boan_li = boan_jq('#'+file.attindex);
		var boan_prgress = boan_li.find('.progress span');
		var boan_info1 =  boan_li.find('p.info span');
		
		itemstatus(file.id,2,'附件上传失败,出错原因:' + reason);
		for(i=0;i<boan_attlist[file.attindex].length;i++){
			if(boan_attlist[file.attindex][i].id == file.id){
				boan_attlist[file.attindex][i]['attid'] = -1;
				break;
			}
			
		}
		boan_prgress.hide().width(0);
		
	});
	
	
	Uploader.on( 'startUpload', function( ) {
		 setState( 'uploading' );
	});
	
	Uploader.on( 'stopUpload', function( ) {
		 setState( 'paused' );;
	});
	
	Uploader.on( 'uploadFinished', function( ) {
		setState( 'confirm' );
	});
	
	Uploader.on( 'uploadComplete', function( file ) {
		//alert('上传完结');
	});
	boan_upload.on('click', function() {
		if ( boan_jq(this).hasClass( 'disabled' ) ) {
			return false;
		}
	
		if ( state === 'ready' ) {
			Uploader.upload();
		} else if ( state === 'paused' ) {
			Uploader.upload();
		} else if ( state === 'uploading' ) {
			Uploader.stop();
		}
	});
	
	
	boan_upload.addClass( 'state-' + state);
	updateTotalProgress();