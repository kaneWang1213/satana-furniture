<html xmlns:fb="https://www.facebook.com/2008/fbml" xmlns:og="https://ogp.me/ns#" ng-app>

    <?php
        header("Content-Type:text/html;Charset:utf-8");
        $login = false;
        include 'include/common.php';
        include 'include/header.php';
        $consequest = -1; //新增資料狀態
    $activityId = 0;
    $navArray = array(array("首頁","首頁","index.php"));
    $imgArray = array(
        array("bodyBg","img/bg.png"),
        array("banner0","img/bg1.png"),
        array("banner1","img/bg2.png")
    );
    
    //新增目錄，若無的話
    $structure = '../data/';
    umask(0); //755 to 777
    if (!file_exists($structure)) {
        mkdir($structure, 0777, true);
    }

    //產生基本目錄
    if(!(file_exists( $structure . "/source.json"))) {
        $myfile = fopen($structure . "/source.json", "x+");
        $txt = '{';
        $txt .= '"source": {';
        $txt .= '"title": "",';
        $txt .= '"widthSize": "1150",';
        $txt .= '"bodyGround": "",';
        $txt .= '"bodyColor": "transparent",';
        $txt .= '"titleIcon": "",';
        $txt .= '"headerlogo": "",';
        $txt .= '"headerText": "",';
        $txt .= '"footerLogo": "",';
        $txt .= '"footerText": "",';
        $txt .= '"folderName": ""';
        $txt .= '},';
        


        $txt .= '"navlist": [';
        foreach($navArray as $key=>$org) {
            $txt .= ((($key==0)? '':',') . '{"name": "' . $org[0] . '","url": "' . $org[2] . '", "active": 1, "bondId":"", "bondName":""}');
        }
        $txt .= ']}';

        fwrite($myfile, $txt);
        fclose($myfile);
        chmod($structure . "/source.php", 0777);
    }

    //include $structure . "/source.php";

    $string = file_get_contents($structure . "/source.json");
    $objectJson = json_decode($string, true);
    $source = $objectJson["source"];

    ?>

    <script src="//cdn.ckeditor.com/4.6.2/standard/ckeditor.js"></script>
	<script>

        <?php if($consequest == -1) { //返品列表頁 ?>
            
        <?php } else if($consequest == 1){ ?>
            //alert("編輯成功");
            document.location.href = "activity_list.php";
        <?php } else if($consequest == 2){ ?>
            //alert("圖片更新成功");
            //document.location.href = "activity_list.php";
        <?php } else if($consequest == 3){ ?>
            //alert("圖片更新失敗");
            //document.location.href = "activity_list.php";
        <?php } ?>
		$(document).ready(init);
		var _userName = "";
        var activityId = "<?php echo $activityId; ?>";
		function init(){

            $(".inserBtn").click(function() {
                if($(this).hasClass("preview")) {

                } else if($(this).hasClass("addNav")) {
                    var _navForm = $(this).parents("form.navForm")[0];
                    var _confirm = $(_navForm).find("div.row.confirmFrame")[0];
                    var _dataFrame = $($(".menu .data.row.frame")[0]).clone(true);
                    var _newDataIpt = $(_dataFrame).find("input").not("input[type=button]");
                    $(_dataFrame).find(".switch input")[0].value = 0;
                    $(_newDataIpt).val("");
                    $(_confirm).before(_dataFrame);
                }
            });

            $(".confirmBtn").click(function() {

                var _form = $(this).parents("form")[0];
                if($(this).hasClass("basic")) {

                    //上傳獎項
                    $.ajax({
                        url: 'jsonDataBase.php',
                        type: 'POST',
                        cache: false,
                        data: new FormData(_form),
                        processData: false,
                        contentType: false,
                        success: function(msg) {
                            var callback = JSON.parse(msg);
                            var imgArray = ["whole", "logo", "footer", "icon"];
                            for(var i = 0 ; i < callback.Image.length ; i++) {
                                if(callback.Image[i] !== "") {
                                    var _case = $("." + imgArray[i])[0];
                                    var _preview = $(_case).find(".preview")[0];
                                    var _ipt = $(_case).find(".preview input")[0];
                                    $(_case).addClass("loaded");
                                    $(_preview).css("background-image","url(../img/" + callback.Image[i]+")");
                                    _ipt.value = callback.Image[i];

                                }
                            }
                            alert("SUCCESS");
                        }
                    });

                    
                    return;
                    $.post("jsonDataBase.php", $(_form).serialize(), function(callback){
                        alert(callback.State);
                    },"json");
                } else if($(this).hasClass("menu")) {
                    //上傳選單資料
                    $.post("jsonDataBase.php", $(_form).serialize(), function(callback){
                        alert(callback.State);
                    },"json");
                }
            });

            //取得類別資料
            $.post("jsonDataBase.php",{"classEvent": "getting"},function(callback){

                $("select.classData").each(function(){

                    var scope = angular.element(this).scope();
                    scope.$apply(function(){
                        scope.ClassItems = callback.Data;
                    });

                });

            }, "json");

            $(".menu .addBtn").click(function(){
                var _form = $(this).parents("form")[0];
                var _prt = $(this).parents("div.row")[0];
                var _slt = $(this).siblings("select")[0];
                var _opt = $(_slt).find("option:selected")
                var _bondIpt = $(_prt).find("input.classBond")[0];
                var _bondName = $(_prt).find("input.className")[0];


                if(_bondIpt.value.indexOf(_slt.value) >= 0) {
                    alert("重覆的類別");
                } else {
                    _bondIpt.value += (((_bondIpt.value=="")? "":",") + _slt.value);
                    _bondName.value += (((_bondName.value=="")? "":",") + $(_opt).text());
                    var _bdname = $(_prt).find("div.bondName")[0];

                    $(_bdname).append("<span><span class='cross' onclick='cancelBond(this)'></span>" + $(_opt).text() + "</span>");

                }


            });

            //end of initi
        }
            
        function removeImg(obj) {
            if(confirm("刪除照片?")) {
                var _ipt = $(obj).find("input[type='hidden']")[0];
                var _case = $(obj).parents("div.case")[0];
                var _form = $(obj).parents("form")[0];
                var _remove = $(_form).find("input[name='removeImg']")[0];
                _remove.value += ((_remove.value=="")? "":"," + _ipt.value);
                $(obj).removeAttr("style");
                $(_case).removeClass("loaded");
                _ipt.value = "";
            }
        }
        
        function DataCtrl($scope) {
            $scope.ClassItems = [];
        }

        function cancelBond(_obj) {
            var _span = $(_obj).parents("span")[0];
            var _prt = $(_obj).parents(".bondCase")[0];

            var _className = $(_prt).find("input.className")[0];
            var _classBond = $(_prt).find("input.classBond")[0];
            var _cln = _className.value.split(",");
            var _clb = _classBond.value.split(",");
            var _tmn = "";
            var _tmb = "";

            _className.value = "";
            _classBond.value = "";


            for(var i = 0 ; i < _clb.length ; i++) {
                if($(_span).index() != i) {
                    _className.value += (((_className.value=="")? "":",") + _cln[i]);
                    _classBond.value += (((_classBond.value=="")? "":",") + _clb[i])
                }
            }

            $(_span).remove();

        }

	</script>
</head>

<body class="manage">
    <div class="container">
        <div class="mainContent container">
            <div class="contentInner">

                
                    <div class="section">
                        <div class="innerFrame">
                            <h2>網站編輯</h2>


                            <?php if($consequest == -1) { ?>
                            <div class='selectList'><select><option value="0">請選擇活動</option></select></div>
                            <?php } ?>
                            <ul class="tags">
                                <li class="tag main active" onclick="tagEvent(event)"><div class="tagtxt">基本編輯</div></li>
                                <li class="tag menu" onclick="tagEvent(event)"><div class="tagtxt">頁首選單編輯</div></li>
                                <!-- <li class="tag product" onclick="tagEvent(event)"><div class="tagtxt">產品編輯</div></li> -->
                            </ul>


                            <div class="bogard main">
                                <form method="post" action="jsonDataBase.php" class="mainForm" enctype="multipart/form-data">
                                    <input type='hidden' name='updateType' value="basic" />
                                    <input type='hidden' name='removeImg' value="" />
                                    <h3><?php echo $ActivityInfo["NAME"];?></h3>
                                    <div class="inserBtn preview">預覽頁面</div>
                                    <div class="bogardFrame">
                                    <form class="basicForm">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <h3>標題</h3>
                                                 <input type="text" name="title" value="<?php echo $source["title"];?>" />
                                            </div>
                                            <div class="col-md-4">
                                                <h3>寬度</h3>
                                                <input type="text" name="widthSize" value="<?php echo $source["widthSize"];?>" />
                                            </div>
                                            <div class="col-md-4 whole">
                                                <h3>全頁背景</h3>
                                                <div class="case <?php echo ($source["bodyGround"] !== "")? 'loaded':''; ?>">
                                                    <input type="text" name="bgCode" value="<?php echo $source["bodyColor"];?>" />
                                                    <div onclick="removeImg(this)" class="preview" style="background-image:url(../img/<?php echo $source["bodyGround"];?>)">
                                                        <input type="hidden" name="wbg" value="<?php echo $source["bodyGround"]; ?>" />
                                                    </div>
                                                    <input type="file" name="bgGround" />
                                                </div>
                                            </div>
                                            <div class="col-md-4 logo">
                                                <h3>LOGO</h3>
                                                <div class="case <?php echo ($source["headerlogo"] !== "")? 'loaded':''; ?>">
                                                    <textarea name="headerText"><?php echo $source["headerText"];?></textarea>
                                                    <div onclick="removeImg(this)" class="preview" style="background-image:url(../img/<?php echo $source["headerlogo"];?>)">
                                                        <input type="hidden" name="limg" value="<?php echo $source["headerlogo"]; ?>" />
                                                    </div>
                                                    <input type="file" name="headerLogo" />
                                                </div>
                                            </div>
                                            <div class="col-md-4 footer">
                                                <h3>頁尾</h3>
                                                <div class="case <?php echo ($source["footerLogo"] == "")? '':'loaded'; ?>">

                                                    <textarea name="footerText"><?php echo $source["footerText"];?></textarea>
                                                    <?php if($source["footerLogo"] !== "") { ?>
                                                        <div onclick="removeImg(this)" class="preview" style="background-image:url(../img/<?php echo $source["footerLogo"];?>)">
                                                            <input type="hidden" name="fimg" value="<?php echo $source["footerLogo"]; ?>" />
                                                        </div>
                                                    <?php } ?> 
                                                    <input type="file" name="footerLogo" />

                                                </div>
                                            </div>

                                            <div class="col-md-4 footer">
                                                <h3>PAGE ICON</h3>
                                                <div class="case <?php echo (($source["titleIcon"] == "")? '':'loaded'); ?>">
                                                    <?php if($source["titleIcon"] !== "") { ?>
                                                        <div onclick="removeImg(this)" class="preview" style="background-image:url(../img/<?php echo $source["titleIcon"];?>)">
                                                            <input type="hidden" name="icon" value="<?php echo $source["titleIcon"]; ?>" />
                                                        </div>
                                                    <?php } ?> 

                                                    
                                                    <input type="file" name="icon" />

                                                </div>
                                            </div>

                                            <div class="col-md-4 folder">
                                                <h3>FolderName</h3>    
                                                <input type="text" name="folderName" value="<?= $source["folderName"]; ?>" />
                                            </div>

                                        </div>
                                        

                                        <div class="row confirmFrame">
                                            <div class="col-md-12">
                                                <div class='btnForm confirmBtn btn basic'>確認</div>
                                            </div>
                                        </div>
                                    </form>
                                    </div>
                                </form>

                                
                            </div>

                            <div class="bogard menu hide">
                                <form method="post" action="activity_edit.php" class="navForm">
                                    <input type='hidden' name='updateType' value="menu" />
                                    <h3>&nbsp;</h3>
                                    <div class="inserBtn addNav">新增</div>
                                
                                <?php foreach( $objectJson["navlist"] as $key => $list) { ?>
                                    <div class="data row frame">
                                        <div class="col-md-1"><font>顯示文字</font></div>
                                        <div class="col-md-2"><input type="text" name="navName[]" value='<?php echo $list["name"]; ?>' /></div>
                                        <div class="col-md-1"><font>連結位置</font></div>
                                        <div class="col-md-2"><input type="text" name="navUrl[]" value='<?php echo $list["url"]; ?>' /></div>
                                        <div class="col-md-1"><div onclick="doSwitch(this)" class="switch<?php echo ($list["active"]==1)? " active":""; ?>"><input type='hidden' name='active[]' value='<?php echo $list["active"]; ?>' /></div></div>
                                        <div class="col-md-2" ng-controller="DataCtrl">
                                            <select class="classData">
                                                <option ng-repeat="item in ClassItems" value="{{item.id}}">{{item.name}}</option>
                                            </select>
                                            <input class='addBtn' value="增加" type="button">
                                        </div>
                                        <div class="col-md-2 bondCase">
                                            <div class="bondName">
                                                <?php 
                                                    $bondNames = explode(",", $list["bondName"]);
                                                    foreach($bondNames as $bName) {
                                                        if($bName!="") {
                                                ?>
                                                    <span><span class="cross" onclick="cancelBond(this)"></span><?= $bName;?></span>
                                                <?php }} ?>
                                            </div>

                                            <input class="className" type="hidden" name="className[]" value='<?php echo $list["bondName"]; ?>' />
                                            <input class="classBond" type="hidden" name="classBond[]" value='<?php echo $list["bondId"]; ?>' />
                                        </div>

                                        <div class="col-md-1">
                                            <div onclick="cubeEvent(this)" class="cube remove"></div>
                                        </div>
                                    </div>
                                <?php } ?>
                                        
                            <div class="row confirmFrame">
                                <div class="col-md-12">
                                    <div class='btnForm confirmBtn btn menu'>確認</div>
                                </div>
                            </div>
                            </form>
                        </div>

                                
                            </div>

                            <div class="bogard hide menu">
                                <h3></h3>
                                <div class="bogardFrame">
                                    <form class="navForm">
                                    <input type='hidden' name='updateType' value="navlist" />
                                    <table class="activityEdit">
                                        <tbody>
                                            <tr>
                                                <td colspan="2"><div class='btnForm confirmBtn photo btn'>確認</div></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    </form>
                                </div>
                            </div>
                            
                            <div class="bogard hide product">
                                <h3></h3>
                                <div class="bogardFrame">
                                    <form class="navForm">
                                        <input type='hidden' name='updateType' value="navlist" />
                                        <table class="activityEdit">
                                            <tbody>
                                                <tr>
                                                    <td colspan="2"><div class='btnForm confirmBtn photo btn'>確認</div></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    <?php include 'include/footer.php';?>
    <div class="blackFrame itemFrame loadingFrame hide"><div class="loadingInner"><span>now loading...</span></div></div>

    <div class="blackFrame errorFrame <?php echo ($consequest > 2)? '':'hide';?> ">
        <div class="errorInner">
            
            <?php if($consequest > 1) { ?>
                發生錯誤
            <?php } ?>
            
        </div>
    </div>

</body>
</html>