var urlLink = "backoffice_home.php";

$(document).ready(function() {
	var userAgent = navigator.userAgent;
	//var navBar = $("nav.navbar")[0];

	if(/Android/i.test(userAgent)){
			//$(navBar).removeClass("navloading");
		}else if(/iPhone|iPad/i.test(userAgent)){
			//$(navBar).removeClass("navloading");
		}else if(/Windows/i.test(userAgent)){
			
				$.getScript("js/bootstrap-hover-dropdown.js",function(){
					var _nv = $('[data-toggle="dropdown"]');
					//$(_nv).removeAttr("data-toggle");
					$(_nv).removeAttr("href");
					$(_nv).attr({"data-hover":"dropdown"});
					$('[data-hover="dropdown"]').dropdownHover({delay: 0}).dropdown();
				});
		}		
});

//頭部按鈕事件
function navEvent(event){ //頁首連結
	var _table,jqxhr,i=0,_mouTyp;
	$(".section").addClass("hide");
	
	$(".navbar-default .navbar-nav > li").removeClass("active");
	$(event.target).parents("li").addClass("active");
	
	if($(event.target).hasClass("home"))
	{
		$(".section.advMoudle").removeClass("hide");	
		$(".section.advMoudle table").addClass("hide");
		
		//秀出特定的table來編輯
		if($(event.target).hasClass("opt2")){
			_table = $(".section.advMoudle table")[1];
			_mouTyp = 2;
		}else if($(event.target).hasClass("opt3")) {
			_table = $(".section.advMoudle table")[2];
			_mouTyp = 3;
		}else{
			_table = $(".section.advMoudle table")[0];
			_mouTyp = 1;
		}
		
		jqxhr = $.get("jsonDataBase.php?type=moudle&moudleId=" + _mouTyp, function(callback) {
				if(callback.State == "PASS"){
					fetchImgData(_table,callback.ImgData, _mouTyp);
				};
		},"json");
	}else if($(event.target).hasClass("product")){
		$(".section.prdMoudle").removeClass("hide");
	}else if($(event.target).hasClass("custom")){
		
	}else if($(event.target).hasClass("std")){
		$(".section.stdMoudle").removeClass("hide");
		
		
	}else{
		
	}
}

//表單按鈕事件
function btnEvent(event){
	var _table = $(event.target).parents("table"),jqxhr,_tr,_ipt,i=0,_section = $(event.target).parents(".section")[0];
	
	switch(event.target.className){
		case "insertBtn" : //插入新row事件
			var _temp = $(_table).find("tr.temp");
			_tr = $(_temp).clone();
			var _td = $(_tr).find("td");
			$(_tr).removeClass('temp').addClass("insert");
			$(_temp).before(_tr);
			
			if($(_section).hasClass("stdMoudle")){
				$($(_tr).find("table td")[0]).text($(_tr).index());
			}else{
				$($(_tr).find("td")[0]).text($(_tr).index());
			}
			
			//移除insert 鍵
			if($(_table).find("tr").length >= parseInt($(_table).attr("limit")) + 3)
			{
				$(_table).find("tr.inserBtn").addClass("hide");
			}
			
			break;
		case "confirmBtn" :
			var data, _moudleid = $(event.target).parents("table").attr("moudleid");
			_tr = $(event.target).parents("tr")[0];
			if($(_tr).hasClass("modify"))
			{
				//審核修改
				var _tds = $(_tr).find("td");
				var _update = "",_able = 0;
				for(i = 1 ; i < _tds.length -1 ; i++)
				{
					if(_moudleid == "3" && i == 6){
						_update += "1,";
					}
					var _org = $(_tds[i]).find("div.org");
					var _mod =  $(_tds[i]).find(".mod")[0];
					var _new = "";
					if(!$(_mod).hasClass("sImg"))
					{
						if(_mod.value =="on"){
							_new = ($(_mod).is(":checked"))? "1":"0";
						}else{
							_new = _mod.value;
						}
						if($(_org).text()=="圖片" || $(_org).text()=="下影") 
						{
							_able = (("0" !== _new||_able)? 1:0); 
						}else if($(_org).text()=="影片" || $(_org).text()=="上架"){
							_able = (("1" !== _new||_able)? 1:0); 
						}else{
							_able = (($(_org).text() !== _new||_able)? 1:0); 
						}
						
						
						
						_update += (_new + ",");
					}
				}
				//image_data SET sys_name=?, img_kind=?, link_path=?, img_name=?, activeYN=?, img_brief=? WHERE moudle_id=? AND ID =?");
				
				if($(_tr).find("input.mod.sImg").val().length > 4){
						_able = 2;
				}
				
				if(_moudleid == "1")
				{
					_update += "null,";
				}else if(_moudleid == "2"){
					_update += "1,null";
				}

				if(_able == 1){
					//無修改圖片
					jqxhr = $.post("jsonDataBase.php", {type:"modify", modata:_update, modleid:$(_table).attr("moudleid"), imgID:$(_tr).index()} ,function(callback){
						for(i = 1 ; i < _tds.length -1 ; i++)
						{
							var _org = $(_tds[i]).find("div.org");
							var _mod =  $(_tds[i]).find(".mod")[0];
							if(!$(_mod).hasClass("sImg"))
							{
								if(_mod.value =="on"){
									$(_org).text(($(_mod).is(":checked"))? "上架":"下架");
								}else{
									if(_mod.value=="0")
									{
										$(_org).text("圖片");
									}else if(_mod.value=="1"){
										$(_org).text("影片");
									}else{
										$(_org).text(_mod.value);
									}
								}
							}
						}
					},"json");
				}else if(_able == 2){
					//有修改圖片
					var _file = $(_tr).find("input[type='file'].mod")[0];
					var _org = $(_file).attr("org");
					_file = $(_file).prop("files")[0];
					data = new FormData();
					try{
						if(_file.name !== "")
						{
							data.append('fileToUpload' ,_file);
							data.append('type','changeImage');
							data.append('orgImg', _org);
							data.append('moudleId',_moudleid);
							data.append('sliderId',$(_tr).index());
							data.append('dataArray',_update);
							$.ajax({ 
								type: 'POST', 
								processData: false, // important
								contentType: false, // important
								data: data,
								cache: false,
								url: "upload.php",
								success : function(callback){
									
									for(i = 1 ; i < _tds.length -1 ; i++)
									{
										var _org = $(_tds[i]).find("div.org");
										var _mod =  $(_tds[i]).find(".mod")[0];
										if(!$(_mod).hasClass("sImg"))
										{
											if(_mod.value =="on"){
												$(_org).text(($(_mod).is(":checked"))? "上架":"下架");
											}else{
												if(_mod.value=="0")
												{
													$(_org).text("圖片");
												}else if(_mod.value=="1"){
													$(_org).text("影片");
												}else{
													$(_org).text(_mod.value);
												}
											}
										}else{
											$(_org).html("<div class='org sImg' style=background-image:url('"+ callback + "')></div><input org='" + callback + "' type='file' class='mod sImg'/>");
										}
									}
								}
							});
						}
					}catch (e){
						alert("file error");
						return;
					}
				}
				
				$(event.target).removeClass().addClass("editBtn");
				$(_tr).removeClass("modify");
			}else{
				//確認新增
				_ipt = $(_tr).find("input").not("input[type='file']");
				var _select = $(_tr).find("select");
				var _file = $(_tr).find("input[type='file']")[0];
				_file = $(_file).prop("files")[0];
				data = new FormData();
				
				try{
					if(_file.name !== "")
					{
						data.append('fileToUpload' ,_file);
						data.append('type','upImage');
						data.append('imgId', $(_tr).index());
						data.append('moudleId',$(event.target).parents("table").attr("moudleid"));
						data.append('sliderId',$(_tr).index());
						data.append('sysName',_ipt[0].value);
						data.append('linkPath',_ipt[1].value);
						data.append('imgKind',$(_select).val());
						data.append('titleName',_ipt[2].value);
						
						if(_moudleid == "1")
						{
							data.append('activeYN',((_ipt[3].checked)? 1:0));
							data.append('brief',"");	
						}else if(_moudleid == "2"){
							data.append('activeYN',1);
							data.append('brief',"");
						}else if(_moudleid == "3"){
							data.append('activeYN',1);
							data.append('brief',_ipt[3].value);
						}

						$.ajax({ 
							type: 'POST', 
							processData: false, // important
							contentType: false, // important
							data: data,
							cache: false,
							url: "upload.php",
							success : function(callback){
								event.target.className = "editBtn";
								var _td = $(_tr).find("td");
								$(_select).removeClass().addClass("mod");
								$(_ipt).removeClass().addClass("mod");
								
								$(_td[1]).prepend("<div class='org'>" + _ipt[0].value + "</div>");
								$(_td[2]).prepend("<div class='org'>" + (($(_select).val()==0)? "圖片":"影片") + "</div>");
								$(_td[3]).html("<div class='org sImg' style='background-image:url(" + callback + ")'></div><div class='mod sImg' style='background-image:url(" + callback + ")'><div class='rmImg' onclick='litBtnEvent(event)'></div></div><div class='sug'>長665*高310</div>");
								$(_td[4]).prepend("<div class='org'>" + _ipt[1].value + "</div>");
								$(_td[5]).prepend("<div class='org'>" + _ipt[2].value + "</div>");
								if(_moudleid == "1")
								{
									$(_td[6]).prepend("<div class='org'>" + ((_ipt[3].checked)? "上架":"下架") + "</div>");
									$(_td[7]).prepend("<div class='deleteBtn' onclick='btnEvent(event)'></div>");
									if($(_table).find("tr").length >= parseInt($(_table).attr("limit")) + 3)
									{
										$(_table).find("tr.inserBtn").addClass("hide");
									}
								}else if(_moudleid == "2"){
									
								}else if(_moudleid == "3"){
									$(_td[6]).prepend("<div class='org'>" + _ipt[3].value + "</div>");
								}
							}
						});
					}
				}catch (e){
					alert("file error");
					return;
				}
			}
			
			break;
		case "confirmBtn2" :
			//門市據點 更新
			var _form = $(event.target).parents("form")[0];
			var _actTag = $(_section).find("li.active")[0];			
			var _field = document.createElement("input");
			var _fieldId = document.createElement("input");
			$(_field).attr({
				"type": "hidden",
				"name": 'store_bond',
				"value": $(_actTag).index()+1
			});
			$(_fieldId).attr({
				"type": "hidden",
				"name": 'ID',
				"value":$(_form).attr('storeid')
			});
			$(_form).append(_field);
			$(_form).append(_fieldId);
			
			 try { 
			 	if(_form.storeBond.value == "none"){
			 		$(_form.storeBond).find("option").attr({"value":_form.oldRel.value});
			 	}
			 	
			 	if(_form.activeYN.checked){
				 	var _define = new DefinedReput(_form.storeBond.value,function(){
							_form.submit();
					});
					_define.reboot();
				}else{
					 _form.submit();
				}
				
		    } catch( err ) { 
		        _form.submit();
		    }
		break;
		case "insertItem" :
		case "insertCoupon" :
			//門市據點 新增
			var _form = $(event.target).parents("form")[0];
			for(var i = 0 ; i < _form.length ; i++){
				if(_form[i].name !== "editor1"){
					if(_form[i].value == ""){
						if(_form[i].name == "coupon_name" || _form[i].name == "store_name"){
							alert("名稱不可為空");
						}else if(_form[i].name == "fileToUpload"){
							alert("圖片不可為空");
						}
						$(_form[i]).focus();
						return;
					}
				}
			}
			
			var _fieldId = document.createElement("input");
			$(_fieldId).attr({"type": "hidden","name": 'ID',"value":$(_form).attr('newid')});
			var _fieldContent = document.createElement("input");
			$(_fieldContent).attr({"type": "hidden","name": 'couponContent',"value":CKEDITOR.instances.editor1.getData()});
			
			$(_form).append(_fieldContent).append(_fieldId);
			
			if(event.target.className=="insertCoupon" && _form.activeYN.checked)
			{
				
				if(_form.store_bond.value !== "none"){
					var _define = new DefinedReput(_form.store_bond.value,function(){
						_form.submit();
					});
					
					_define.reboot();
				};
			}else{
				_form.submit();
			}
		break;
		case "deleteBtn" :
			_tr = $(event.target).parents("tr")[0];
			var _img = $(_tr).find(".sImg").css("background-image");
			var n = _img.lastIndexOf("/") + 1;
			_img = _img.substring(n,_img.length - 2);
			jqxhr = $.get("upload.php?type=delImage&fileName=" + _img + "&moudleId=" + $(_table).attr("moudleid") ,function(callback) {
				if(callback.State == "Success"){
					$(_tr).remove();
					
					
					if($(_table).find("tr").length >= parseInt($(_table).attr("limit")) + 3)
					{
						$(_table).find("tr.inserBtn").addClass("hide");
					}else{
						$(_table).find("tr.inserBtn").removeClass("hide");
					}
					
				}
			},"json");

			break;	
		case "plus" :
			break;
		case "login" : //登入事件
			/*
			_table = $(event.target).parents(".innerFrame");
			var _account = $(_table).find("input[type=text]")[0];
			var _password = $(_table).find("input[type=password]")[0];
			
			if(_account.value !='' && _password.value != '')
			{
				var form = document.createElement("form");
				form.setAttribute("method", 'post');
				form.setAttribute("action", 'auth.php');
				
				var field = document.createElement("input");
				field.setAttribute("type", "hidden");
				field.setAttribute("name", 'account');
				field.setAttribute("value", _account.value);
				form.appendChild(field);
				
				var field = document.createElement("input");
				field.setAttribute("type", "hidden");
				field.setAttribute("name", 'password');
				field.setAttribute("value", _password.value);
				form.appendChild(field);
				
				document.body.appendChild(form);
				form.submit();
			}
			else
			{
				alert('請輸入帳密');
			}
			*/		
			break;
		case "sighOut" :
			jqxhr = $.get("jsonDataBase.php?type=logout",function(callback){
				document.location.href="login.php";
			},"json");
			break;
		case "ads" :
			$(".section.login").removeClass("hide");
			break;
		case "editBtn" :
			$(event.target).removeClass();
			$("div.confirmBtn2").removeClass().addClass("editBtn");
			$("tr.modify").removeClass().addClass("dataBase");
			if($(_section).hasClass("stdMoudle")) {
				_tr = $(event.target).parents("tr.dataBase")[0];
				$(_tr).removeClass();
				$(event.target).addClass("confirmBtn2");
			}else{
				_tr = $(event.target).parents("tr")[0];
				$(event.target).addClass("confirmBtn");
			}
			$(_tr).addClass("modify");
			break;
		default :
			break;
	}
}

//判斷資料重覆


var DefinedReput = function (_data,_fun) {
	this.reboot = function(){
		var _dataArr = _data.split("_"),jqxhr2;
		var jqxhr = $.get("jsonDataBase.php?type=definedrepost&bond=" + _dataArr[0] + "&rel=" + _dataArr[1], function(callback) {
			
			
			
			if(callback.State == "REPEAT1"){
				if(confirm("偵測到篩選門市已有折價券，若新增將會覆蓋舊的優惠券，是否新增?")){
					jqxhr2 = $.get("jsonDataBase.php?type=disableCoupon&bond=" + _dataArr[0] + "&rel=" + _dataArr[1], function(callback) {
						if(callback.State == "SUCCESS"){_fun();}
					},"json");
					return;
				}
			}else if(callback.State == "REPEAT2"){
				if(confirm("偵測到篩選門市已有折價券，若新增將會覆蓋舊的優惠券，是否新增?")){
					jqxhr2 = $.get("jsonDataBase.php?type=disableCoupon&bond=" + _dataArr[0] + "&rel=" + _dataArr[1], function(callback) {
						if(callback.State == "SUCCESS"){_fun();}
					},"json");
					return;
				}
			}else if(callback.State == "REPEAT3"){
				//有門市已有優惠券，一個門市只限一張優惠券。仍要新增，將覆蓋該門市原有的優惠券，確定覆蓋？
				if(confirm("偵測到篩選門市已有折價券，若新增將會覆蓋舊的優惠券，是否新增?")){
					jqxhr2 = $.get("jsonDataBase.php?type=disableCoupon&bond=" + _dataArr[0] + "&rel=" + _dataArr[1], function(callback) {
						if(callback.State == "SUCCESS"){_fun();}
					},"json");
					return;
				}
			}
			_fun();
		},"json");
	};
};
//標籤按鈕事件
function tagEvent(event){
	var _tag = $(event.target).parents(".tag")[0],innerFrame = $(event.target).parents(".innerFrame")[0], jqxhr, _section = $(event.target).parents(".section")[0];
	$(_tag).siblings(".tag").removeClass("active");
	$(_tag).addClass("active");
	if($(_section).hasClass("advMoudle")){
		document.location.href = "backoffice_home.php?mld=" + ($(_tag).index()+1);
		/*
		jqxhr = $.get("jsonDataBase.php?type=moudle&moudleId=" + ($(_tag).index()+1), function(callback) {
			if(callback.State == "PASS"){
				fetchImgData($(innerFrame).find("table")[$(_tag).index()], callback.ImgData, $(_tag).index()+1);
			}
		},"json");*/
		
	}else if($(_section).hasClass("stdMoudle")){
		//門市據點 撈門市資料庫 參數依點選的tag順序
		sltRange = 0;
		gettingStores($(_tag).index()+1,0);
		
	}else if($(_section).hasClass("actMoudle")){
		//門市據點 撈門市資料庫 參數依點選的tag順序
		sltRange = 0;
		gettingAccount($(_tag).index()+1,0);
	}else if($(_section).hasClass("notice")){
		$(".bogard").addClass('hide');
		$($(".bogard")[$(_tag).index()]).removeClass("hide");
		
	}else if($(_section).hasClass("bookingSec")){
		$(".bogard").addClass('hide');
		$($(".bogard")[$(_tag).index()]).removeClass("hide");
	}
}

function gettingStores(tag,rag){
//取得門市資料 店號及起始號碼
	$("tr.dataBase").remove();
	jqxhr = $.get("jsonDataBase.php?type=storeDetail&storeBond=" + tag +"&storeRange=" + rag , function(callback) {
		if(callback.State == "SUCCESS"){
			fetchImgData($("table.place")[0],callback.stdData, tag);
		}else if(callback.State == "ERROR"){

		}
	},"json");
}

function gettingAccount(tag,rag){
//取得門市帳號
	$("tr.dataBase").remove();
	jqxhr = $.get("jsonDataBase.php?type=gettingAccount&AccountBond=" + tag + "&AccountRange=" + rag , function(callback) {
		if(callback.State == "SUCCESS"){
			fetchImgData($("table.account")[0],callback.ActData, 5);
		}else if(callback.State == "ERROR"){

		}
	},"json");
}

function gettingBooking(tag,rag,unknowFun){
//取得門市資料 店號及起始號碼
	if(tag == false || tag == undefined|| tag == null){
		tag = "";
	}
	$("tr.dataBase").remove();
	var jqxhr = $.get("jsonDataBase.php?type=bookingDetail&store=" + tag +"&range=" + rag , function(callback) {
		if(callback.State == "SUCCESS"){
			$(".bogard tr.database").remove();
			$(".bogard div.pageMenu").remove();
			$(".bogard tr.thColumn").after(callback.BookingData);
			unknowFun();			
		}else if(callback.State == "ERROR"){
			alert("查無資料");
		}
	},"json");
}

function gettingSerial(storeId,gettingType,unknowFun){
//取得門市序號
	var jqxhr;
	if(gettingType == "SUM"){
		jqxhr = $.get("jsonDataBase.php?type=serialData&storeId=" + storeId , function(callback) {
			if(callback.State == "SUCCESS"){
				$(".bogard tr.database").remove();
				$(".bogard div.pageMenu").remove();
				$(".bogard tr.thColumn").after(callback.BookingData);
				unknowFun(callback.SerialNum);
			}else if(callback.State == "ERROR"){
				alert("查無資料");
			}
		},"json");
	}else if(gettingType == "NEW"){
		jqxhr = $.get("jsonDataBase.php?type=newSerial&storeId=" + storeId , function(callback) {
			if(callback.State == "SUCCESS"){
				unknowFun(callback.SerialNum);
			}else if(callback.State == "NONE"){
				alert("已無序號");
			}
		},"json");
	}else if(gettingType == "DELETE"){
		
		var _args = storeId.split(",");
		
		jqxhr = $.get("jsonDataBase.php?type=deleteSerial&storeId=" + _args[0] + "&serialNum=" + _args[1] + "&bookingId=" + _args[2]  , function(callback) {
			if(callback.State == "SUCCESS"){
				unknowFun(callback.SerialNum);
			}else{
				alert("新增失敗");
			}
		},"json");
	}
}


//小按鈕 10px 事件
function litBtnEvent(event){
	var _tag = $(event.target).parents(".tag")[0],_innerFrame,_tags = $(event.target).parents(".tags")[0],_cols;
	switch(event.target.className){
		case "plus" :
			var _input = $(_tag).find("input[type=text]")[0];
			var _section = $(_tag).parents("div.section")[0];
			if(_input.value !== "" && _input.value !== " ")
			{
				$(_tags).find(".tag").removeClass("active");
				$(_tag).addClass("active");
				$(_tag).attr("onclick","tagEvent(event)");
				var _tagtxt = $(_tag).find(".tagtxt");
				var _section = $(_tag).parents("div.section")[0]; //找尋section父層 為了辨別table種類
				
				$(_tagtxt).html(_input.value);
				event.target.className = "cross";
				jqxhr = $.get("jsonDataBase.php?type=addMoudle&moudleType=" + $(event.target).attr("moudleType") + "&moudleName=" + _input.value, function(callback) {
					_innerFrame = $(_tag).parents(".innerFrame")[0];
					var _bogard = $(_innerFrame).find("div.bogard")[0];
					if($(_bogard).find("table").length > 0)
					{
						$(_innerFrame).find("tr.dataBase").remove();
						$(innerFrame).find("tr.insert").remove();
					}else{
						
						
						if($(_section).hasClass("advMoudle"))
						{
							$(".cloneTables table.img").clone().appendTo(_bogard);
						}else if($(_section).hasClass("cusMoudle")){
							$(".cloneTables table.c").clone().appendTo(_bogard);
						}
						
						
					}
					$(_innerFrame).find('table').attr("moudleid",$(_tag).index()+1);
				},"json");
			}
			break;
		case "minerCol" :
			_innerFrame = $("tr.editColumn td")[0];
			_cols = $(_innerFrame).find("div.demoCol");
			
			if(_cols.length > 1)
			{
				$(_cols[_cols.length - 1]).remove();
			}
			
			_cols = $(_innerFrame).find("div.demoCol");
			$(_cols).removeClass();
			$(_cols).addClass("demoCol col-md-" + Math.floor(12/_cols.length));
			
			break;
		case "plusCol" :
			_innerFrame = $("tr.editColumn td")[0];
			_cols = $(_innerFrame).find("div.demoCol");
			
			if(_cols.length < 12)
			{
				$(_innerFrame).append("<div class='demoCol'><div id='summernote'></div><input type='button' value='edit' class='editHtml' onclick='litBtnEvent(event)' /><input type='button' value='finish' class='finish hide' onclick='litBtnEvent(event)' /></div>");
			}
			
			_cols = $(_innerFrame).find("div.demoCol");
			$(_cols).removeClass();
			$(_cols).addClass("demoCol col-md-" + Math.floor(12/_cols.length));
			break;
		case "confirmCol" :
			_innerFrame = $("tr.editColumn")[0];
			_cols = $(_innerFrame).find("div.demoCol");
			
			var _img = $(_innerFrame).find('img');
			var _updateNumber = _img.length;
			for(var i = 0 ; i < _img.length ; i++ )
			{
				var data = new FormData();
				
				if(_img[i].src.indexOf("/jpeg") > 0)
				{
					data.append('imgtype','jpeg');
				}else if(_img[i].src.indexOf("/png") > 0){
					data.append('imgtype','png');
				}else if(_img[i].src.indexOf("/gif") > 0){
					data.append('imgtype','gif');
				}
				data.append('colnumber',i);
				data.append('img',_img[i].src);
				data.append('type','uploadCusImg');
				data.append('idx',i);
				//圖片種類
				
				$.ajax({ 
					type: 'POST', 
					processData: false, // important
					contentType: false, // important
					data: data,
					cache: false,
					url: "upload.php",
					success : function(callback){
						var _dta = callback.split(",");
						$(_img[_dta[0]]).attr("src",_dta[1]);
						_updateNumber --;
						if(_updateNumber == 0)
						{
							setTimeout(function(){
								
								_innerFrame = $("tr.editColumn td")[0];
								$(_innerFrame).find("input").remove();
								$(_innerFrame).find("br").remove();
								$(_innerFrame).find("div.demoCol").removeClass("demoCol");
								$(_innerFrame).find("div#summernote").removeAttr("id");
								$(_innerFrame).find("img").removeAttr("style data-filename");
								$(_innerFrame).find("p").unwrap();
								var _moudleid = $(_innerFrame).parents('table').attr("moudleid");
								jqxhr = $.post("jsonDataBase.php", {type:"insertContent" ,moudleid:_moudleid, moudlecontent:$(_innerFrame).html()}, function(callback) {
										if(callback.State == "SUCCESS"){
											alert("SUCCESS");
										};
								},"json");
							},500);
						}
					}
				});
			}
			break;
		case "finish" :
			$(event.target).addClass("hide");
			$($(event.target).siblings("input")).removeClass("hide");
			_cols = $(event.target).parents("div.demoCol")[0];
			$(_cols).removeClass("editing");
			
			var markupStr = $($(_cols).find("#summernote")[0]).summernote('code');
			$($(_cols).find("#summernote")[0]).summernote('destroy');
			/*var data = new FormData();
			data.append('img',$(img).attr("src"));
			data.append('type','uploadCusImg');
			//圖片種類
			if(img.src.indexOf("jpeg;base64") > 0)
			{
				data.append('imgtype','jpeg');
			}else if(img.src.indexOf("png;base64") > 0){
				data.append('imgtype','png');
			}else if(img.src.indexOf("gif;base64") > 0){
				data.append('imgtype','gif');
			}
			$.ajax({ 
				type: 'POST', 
				processData: false, // important
				contentType: false, // important
				data: data,
				cache: false,
				url: "upload.php",
				success : function(callback){
					alert(callback);
				}
			});*/
			break;
		case "editHtml" :
			//$(event.target).parents("div.demoCol")[0];
			_cols = $(event.target).parents("div.demoCol")[0];
			$(_cols).addClass("editing");
			$($(_cols).find("#summernote")[0]).summernote();
			$(event.target).addClass("hide");
			$($(event.target).siblings("input")).removeClass("hide");
			break;
		case "rmImg" :
			var _simg = $(event.target).parents(".sImg")[0];
			$(_simg).after("<input type='file'>").addClass("hide");
			break;
		default :
			break;
	}
};

function collectIpt(_range){
	var _dataArray = {};
	var _txt = $(_range).find("input[type=text]");
	var _box = $(_range).find("input[type=checkbox]");
}

function createObject(_typ , _name) {
	var _obj = document.createElement(_typ);
	if(_name !== false) {
		_obj.className = _name;
	}
	return _obj;
};

function fetchData (_type,_value,_fun,_range){

	var jqxhr;
	switch (_type) {
		case "selectChildClass" :
			jqxhr = $.get("jsonDataBase.php?type=" + _type + "&sndBond=" + _value, function(callback) {
				if(callback.State == "SUCCESS"){
					if(_fun !== false || _fun !== null){_fun(callback.DataArray);}
				};
			},"json");
			break;
		case "selectProduct" :
			jqxhr = $.get("jsonDataBase.php?type=" + _type + "&sndBond=" + _value, function(callback) {
				if(callback.State == "SUCCESS"){
					if(_fun !== false || _fun !== null){_fun(callback.NewId);}
				};
			},"json");
			break;
		case "pickProduct" :
		jqxhr = $.get("jsonDataBase.php?type=" + _type + "&sndBond=" + _value, function(callback) {
			if(callback.State == "SUCCESS"){
				if(_fun !== false || _fun !== null){_fun(callback.DataArray);}
			};
		},"json");
		break;
		case "selectColorItem" :

		jqxhr = $.get("jsonDataBase.php?type=" + _type + "&colorKey=" + _value[0] + "&unless=" + _value[1] + "&range=" + _range, function(callback) {
			if(callback.State == "SUCCESS"){
				if(_fun !== false || _fun !== null){_fun(callback.Data);}
			};
		},"json");
		break;
		case "resetColorItem" :
		jqxhr = $.get("jsonDataBase.php?type=" + _type, function(callback) {
			if(callback.State == "SUCCESS"){
				if(_fun !== false || _fun !== null){_fun(callback.Data);}
			};
		},"json");
		break;
		default :
			break;
	};
};

function fetchImgData(_table,_rowArray,_type){
	//刪除儲存資料及資料庫的資料

	var _section = $(_table).parents("div.section")[0];
	if($(_section).hasClass("stdMoudle")){
		_type = 4;
	};
	$(_table).find("tr.modify").remove();
	$(_table).find("tr.insert").remove();
	$(_table).find("tr.dataBase").remove();
	$(_table).siblings("table").addClass("hide");
	$(_table).removeClass("hide");
	for(i = 0 ; i < _rowArray.length ; i++)
	{
		if(_type !== 5){
			var _trtemp = $(_table).find(".temp")[0];	
			var _tr = $(_trtemp).clone().removeClass("temp").addClass("dataBase");
			var _td = $(_tr).find("td");
		}
		

		if(_type == 1)
		{
			//首頁 上部
			$(_td).empty();
			$(_td[0]).text(i+1);
			$(_td[1]).append("<div class='org'>" + _rowArray[i][0] + "</div>" + "<input class='mod' type='text' value='" + _rowArray[i][0] + "' />");
			$(_td[2]).append("<div class='org'>" +((_rowArray[i][2]==0)? "圖片":"影片") + "</div>" + "<select class='mod'><option value='0' "+ ((_rowArray[i][2]==0)? "selected":"") + ">圖片</option><option value='1' "+ ((_rowArray[i][2]==1)? "selected":"") + ">影片</option></select>");
			$(_td[3]).append("<div class='org sImg' style=background-image:url('"+ _rowArray[i][4] + "')></div><input org='" + _rowArray[i][4] + "' type='file' class='mod sImg'/><div class='sug'>長1360*高633</div>");
			$(_td[4]).append("<div class='org'>" + _rowArray[i][5] + "</div>" + "<input class='mod' type='text' value='" + _rowArray[i][5] + "' />");
			$(_td[5]).append("<div class='org'>" + _rowArray[i][1] + "</div>" + "<input class='mod' type='text' value='" + _rowArray[i][1] + "' />");
			$(_td[6]).append("<div class='org'>" + ((_rowArray[i][6]==1)? "上架":"下架") + "</div>" + "<input class='mod' type='checkbox' "+ ((_rowArray[i][6]==1)? "checked":"") + " />");
			$(_td[7]).append("<div class='deleteBtn' onclick='btnEvent(event)'></div><div onclick='btnEvent(event)' class='editBtn'></div>");
			$(_trtemp).before(_tr);
			
			//首頁下部 insert鍵移除
			if($(_table).find("tr").length >= parseInt($(_table).attr("limit")) + 3) {
				$(_table).find("tr.inserBtn").addClass("hide");
			}
			
		}else if(_type == 3){
			_tr = $(_table).find("tr")[i + 1]; 
			_td = $(_tr).find("td");
			$(_td).empty();
			$(_td[0]).text(i+1);

			$(_td[1]).append("<div class='org'>" + _rowArray[i][0] + "</div>" + "<input class='mod' type='text' value='" + _rowArray[i][0] + "' />");
			$(_td[2]).append("<div class='org'>" +((_rowArray[i][2]==0)? "圖片":"影片") + "</div>" + "<select class='mod'><option value='0' "+ ((_rowArray[i][2]==0)? "selected":"") + ">圖片</option><option value='1' "+ ((_rowArray[i][2]==1)? "selected":"") + ">影片</option></select>");
			$(_td[3]).append("<div class='org sImg' style=background-image:url('"+ _rowArray[i][4] + "')></div><input org='" + _rowArray[i][4] + "' type='file' class='mod sImg'/><div class='sug'>長665*高310</div>");
			$(_td[4]).append("<div class='org'>" + _rowArray[i][5] + "</div>" + "<input class='mod' type='text' value='" + _rowArray[i][5] + "' />");
			$(_td[5]).append("<div class='org'>" + _rowArray[i][1] + "</div>" + "<input class='mod' type='text' value='" + _rowArray[i][1] + "' />");
			$(_td[6]).append("<div class='org'>" + _rowArray[i][3] + "</div>" + "<input class='mod' type='text' value='" + _rowArray[i][3] + "' />");
			$(_td[7]).append("<div onclick='btnEvent(event)' class='editBtn'></div>");
			
		}else if(_type == 2 ){
			_tr = $(_table).find("tr")[i + 1]; 
			_td = $(_tr).find("td");
			$(_td).empty();
			$(_td[0]).text(i+1);
			$(_td[1]).append("<div class='org'>" + _rowArray[i][0] + "</div>" + "<input class='mod' type='text' value='" + _rowArray[i][0] + "' />");
			$(_td[2]).append("<div class='org'>" +((_rowArray[i][2]==0)? "圖片":"影片") + "</div>" + "<select class='mod'><option value='0' "+ ((_rowArray[i][2]==0)? "selected":"") + ">圖片</option><option value='1' "+ ((_rowArray[i][2]==1)? "selected":"") + ">影片</option></select>");
			$(_td[3]).append("<div class='org sImg' style=background-image:url('"+ _rowArray[i][4] + "')></div><input org='" + _rowArray[i][4] + "' type='file' class='mod sImg'/><div class='sug'>長665*高310</div>");
			$(_td[4]).append("<div class='org'>" + _rowArray[i][5] + "</div>" + "<input class='mod' type='text' value='" + _rowArray[i][5] + "' />");
			$(_td[5]).append("<div class='org'>" + _rowArray[i][1] + "</div>" + "<input class='mod' type='text' value='" + _rowArray[i][1] + "' />");
			$(_td[6]).append("<div onclick='btnEvent(event)' class='editBtn'></div>");
		}else if(_type == 4 ){		
			var _place = $("table.place")[0];
			_tr = $("ul.tags li.active")[0];
			
			if( i < _rowArray.length - 1){
				
				$(_place).append("<tr class='dataBase'><td colspan='10'><form method='post' enctype='multipart/form-data' action='backoffice_sales.php'><table><tr><td>" + (i+1) + "</td><td><div class='org'>"+ _rowArray[i][0] +"</div><input class='mod' name='store_name' type='text' value='"+ _rowArray[i][0] +"' /></td><td><div class='org sImg' style='background-image:url("+_rowArray[i][1]+")' ></div><input class='mod' name='fileToUpload' type='file' id='fileToUpload' /></td><td><div class='org'>"+_rowArray[i][2]+"</div><input class='mod' name='store_address' type='text' value='"+_rowArray[i][2]+"' /></td><td><div class='org'>"+_rowArray[i][3]+"</div><input class='mod' name='store_inst' type='text' value='"+_rowArray[i][3]+"' /></td><td><div class='org'>"+_rowArray[i][4]+"</div><input class='mod' name='store_phone' type='text' value='"+_rowArray[i][4]+"' /></td><td><div class='org'>"+_rowArray[i][9]+"</div><input class='mod' name='store_mobile' type='text' value='"+_rowArray[i][9]+"' /></td><td><div class='org'>"+_rowArray[i][5]+"</div><input class='mod' name='store_time' type='text' value='"+_rowArray[i][5]+"' /></td><td>" + ((_rowArray[i][7] != "" && _rowArray[i][7] !=null)? "<div class='org sImg' style='background-image:url(../img/storesImg/" + _rowArray[i][7]  + ")' ></div>":"") + "<div class='mod couponBtn' onclick='buttonEvent(event)'></div></td><td><div class='org'>"+_rowArray[i][8]+"</div><input class='mod' name='store_email' type='text' value='"+_rowArray[i][8]+"' /></td><td><div onclick='buttonEvent(event)' class='editBtn'></div><div onclick='buttonEvent(event)' class='deleteBtn'></div></td></tr></table><input type='hidden' name='ID' value='" + _rowArray[i][6] + "' /><input type='hidden' name='oldImg' value='" + _rowArray[i][1] + "' /></form></td></tr>");
			}else{
				var _total = _rowArray[i][0];

				$("div.pageMenu").remove();
				var _pageMenu = createObject("div","pageMenu");
	            $(".bogard table.place").after(_pageMenu);
	              for(var j = 0 ; j < Math.ceil(_total / 5); j++){
	                  var _menuBtn = createObject("a","page");
	                  if(j == sltRange){
	                      $(_menuBtn).append(j+1).addClass("currentpage");
	                  }else{
	                       $(_menuBtn).append(j+1).attr("href","backoffice_sales.php?arg=" + ($(_tr).index()+1) + "&rag=" + (j + 1));
	                  }
	                  $(_pageMenu).append(_menuBtn);
	              }
	              
	              if(_total > 0){

		              if(sltRange !== 0){
		                  $(_pageMenu).prepend("<a href='backoffice_sales.php?arg=" + ($(_tr).index()+1) + "&rag=" + (sltRange) + "' class='prev page'>上一頁</a>");
		              }
		              if(sltRange !== Math.ceil(_total / 5) - 1){
		                  $(_pageMenu).append("<a href='backoffice_sales.php?arg=" + ($(_tr).index()+1) + "&rag=" + (sltRange + 2) + "' class='nxt page'>下一頁</a>");
		              }
	              }else{
	              	$(_pageMenu).append("【查無資料】");
	              }
			}
		}else if(_type == 5 ){
			_tr = $("ul.tags li.active")[0];
			
			$("div.pageMenu").remove();
			var _bogard = $(".bogard")[0];
			var _pageMenu = createObject("div","pageMenu");
            $(_bogard).append(_pageMenu);
			
			if( i < _rowArray.length - 1){
				$(_table).append("<tr class='" + ((_rowArray[i][1]!==null)? "DB ":"") + "dataBase' dataNum='" + _rowArray[i][0] +"'><td>" + _rowArray[i][3] + "</td><td>" + ((_rowArray[i][1]==null)? "":_rowArray[i][1]) + "</td><td>" + ((_rowArray[i][2]==null)? "":_rowArray[i][2]) + "</td><td><div onclick='buttonEvent(event)' class='editBtn'></div></td></tr>");
			}else{
				
				var _total = _rowArray[i][0];
				$("div.pageMenu").remove();
				var _pageMenu = createObject("div","pageMenu");
	            $(_table).after(_pageMenu);
	              for(var j = 0 ; j < Math.ceil(_total / 5); j++){
	                  var _menuBtn = createObject("a","page");
	                  if(j == sltRange){
	                      $(_menuBtn).append(j+1).addClass("currentpage");
	                  }else{
	                       $(_menuBtn).append(j+1).attr("href","backoffice_member.php?arg=" + ($(_tr).index()+1) + "&rag=" + (j + 1));
	                  }
	                  $(_pageMenu).append(_menuBtn);
	              }
	              
	              if(_total > 0){

		              if(sltRange !== 0){
		                  $(_pageMenu).prepend("<a href='backoffice_member.php?arg=" + ($(_tr).index()+1) + "&rag=" + (sltRange) + "' class='prev page'>上一頁</a>");
		              }
		              if(sltRange !== Math.ceil(_total / 5) - 1){
		                  $(_pageMenu).append("<a href='backoffice_member.php?arg=" + ($(_tr).index()+1) + "&rag=" + (sltRange + 2) + "' class='nxt page'>下一頁</a>");
		              }
	              }else{
	              	$(_pageMenu).append("【查無資料】");
	              }
			}
			
			
			
			
			
			
		}
	}
};

function readURL(input) {
	if (input.files && input.files[0]) {
		var reader = new FileReader();
		reader.onload = function(e) {
			$('#target').attr('src', e.target.result).removeClass("hide");
			$(".uploadSection").addClass("hide");
			$(".filterBtns").removeClass('hide');
		};
		$('#target').load(function() {
			
			//chopMode();
			$(".blackFrame.photoFrame").addClass("chosen");
			screenEvent();
			
			
		});

		reader.readAsDataURL(input.files[0]);
	}
}

function chopMode() {
	var api;
	$('#target').Jcrop();
}