<html xmlns:fb="https://www.facebook.com/2008/fbml" xmlns:og="https://ogp.me/ns#" ng-app>
    
    <?php
        header("Content-Type:text/html;Charset:utf-8");
        $login = false;
        include 'include/common.php';
        include 'include/header.php';
    ?>

    <script src="//cdn.ckeditor.com/4.6.2/standard/ckeditor.js"></script>
    
	<script>
		$(document).ready(init);
		var _userName = "";
		function init(){
            $(".bogard.class .inserBtn").click(function(){
               //新增banner類別

                var scope = angular.element($("tbody.classlist")).scope();
                scope.$apply(function(){
                    var  newid = parseInt($(".bogard.class tr.database:nth-last-child(1)").attr("bannerid")) + 1;
                    scope.banners.push(
                        {
                            "id": newid,
                            "type": 0,
                            "name": "",
                            "width": 0,
                            "height": 0
                        }
                    );

                    setTimeout(function(){
                        $(".bogard.class tr.insert input").removeAttr("readonly");
                    }, 500);

                });

            });

            $(".list select").change(function() {
                defaultTable("banners"); 
                
            });

            defaultTable("class"); 
            defaultTable("bannerList");
        }

        function cubeEvent(evt) {
            
            var _tr = $(evt).parents("tr")[0];
            var _id = $(_tr).attr("bannerid");
            var _type = "insertClass";
            var _board = $(evt).parents("div.bogard")[0];
            

            if($(evt).hasClass("confirm") && $(_board).hasClass("class")) {
                //類別確認鈕

                var _name = $(_tr).find("input[name=name]")[0].value;
                var _wdt = $(_tr).find("input[name=width]")[0].value;
                var _hgt = $(_tr).find("input[name=height]")[0].value;

                if($(_tr).hasClass("insert")) {
                     $.post("jsonDataBase.php", {jsonType: _type, id: _id, name: _name, width: _wdt, height: _hgt}, function(callback){
                        if(callback.State == "SUCCESS") {
                            alert(callback.Result);
                            defaultTable("class"); 
                        }
                    }, "json");
                } else if($(_tr).hasClass("edit")) {
                    _type = "updateClass";
                    $.post("jsonDataBase.php", {jsonType: _type, id: _id, name: _name, width: _wdt, height: _hgt}, function(callback){
                        if(callback.State == "SUCCESS") {
                            alert(callback.Result);
                            defaultTable("class"); 
                        }
                    }, "json");

                }

            } else if($(evt).hasClass("confirm") && $(_board).hasClass("banner")) {

                if($(_tr).hasClass("addBanner")) {
                    _type = "addBanner";
                    uploadBanner($(_tr).parents("form")[0]);
                    return;
                }

            } else if($(evt).hasClass("confirm") && $(_board).hasClass("list")) {
                //列表確認鈕
                if($(_tr).hasClass("edit")) {
                    var _form = document.createElement("form");
                    $(_board).append(_form);
                    $(_form).append("<input type='hidden' name='id' value='" + $(_tr).attr("bannerid") + "' />");
                    $(_form).append("<input type='hidden' name='instruction' value='" + $(_tr).find("input[name='instruction']")[0].value + "' />");
                    $(_form).append("<input type='hidden' name='className' value='" + $(_tr).find("input[name='className']")[0].value + "' />");
                    updateBanner(_form);
                } else {

                }
                
            } else  if($(evt).hasClass("remove") && $(_board).hasClass("list")) {
                 //刪除鈕 banner 項目

                 $.post("jsonDataBase.php", {jsonType:"removeBanner", id: $(_tr).attr("bannerId"), img: $(_tr).attr("bannerImg")}, function(callback){
                    if(callback.State == "SUCCESS") {

                        if(callback.Result) {
                            alert("REMOVE SUCCESSFULLY");
                            defaultTable("bannerList"); 
                        }
                        
                    }
                }, "json");
            

            } else  if($(evt).hasClass("remove") && $(_board).hasClass("class")) {
                //刪除鈕 banner 類別
                $.post("jsonDataBase.php", {jsonType:"removeItem", id: _id}, function(callback){
                    if(callback.State == "SUCCESS") {
                        if(callback.Result) {
                            alert("REMOVE SUCCESSFULLY");
                            defaultTable("class"); 
                        }
                        
                    }
                }, "json");

            } else  if($(evt).hasClass("edit")) {
                $(_tr).removeClass("database").addClass("edit");
                $(_tr).find('input[type=text]').removeAttr('readonly');
            }
        }

        function updateBanner(_form) {
            $(_form).append("<input type='hidden' name='jsonType' value='updatebanner' />");
             $.ajax({
                url: 'jsonDataBase.php',
                type: 'POST',
                cache: false,
                data: new FormData(_form),
                processData: false,
                contentType: false,
                success: function(msg) {
                    alert("SUCCESS");
                    defaultTable("bannerList");
                }
            });
        }

         function uploadBanner(_form) {
            $(_form).append("<input type='hidden' name='jsonType' value='addbanner' />");
             $.ajax({
                url: 'jsonDataBase.php',
                type: 'POST',
                cache: false,
                data: new FormData(_form),
                processData: false,
                contentType: false,
                success: function(msg) {
                    alert("SUCCESS");
                    $("tr.addBanner div.preview").removeClass("done").removeAttr("style");
                    $("tr.addBanner td.case").removeClass("loaded");
                    $("tr.addBanner input[type=file]").val("");
                    defaultTable("bannerList");
                }
            });
        }

        function bannerCtrl($scope) {
            $scope.classes = [];
            $scope.banners = [];
            $scope.lists = [];
        }

        function defaultTable(_type) {
            switch(_type) {
                case "class" :

                    //撈banner類別
                    $.get("jsonDataBase.php?jsonType=gettingBannerClass", function(callback) {
                        if(callback.State == "SUCCESS") {
                            var scope = angular.element($("tbody.classlist")).scope();
                            scope.$apply(function(){
                                scope.banners = callback.Data;
                            });

                            var scope2 = angular.element($(".banner select.classLists")).scope();
                            scope2.$apply(function(){
                                scope2.classes = callback.Data;
                            });

                            var scope3 = angular.element($(".list select.classLists")).scope();

                            scope3.$apply(function(){;
                                scope3.classes = callback.Data;
                            });
                        }
                        
                    }, "json");
                break;
                case "bannerList" :
                    //撈banner列表
                    $.get("jsonDataBase.php?jsonType=gettingBannerList", function(callback) {
                        if(callback.State == "SUCCESS") {
                            var scope = angular.element($("tbody.bannerlist")).scope();
                            scope.$apply(function(){
                                scope.lists = callback.Data;
                            });
                        }
                    }, "json");
                break;
                case "banners" :
                    //撈特定類別banner

                    $.get("jsonDataBase.php?jsonType=gettingSpecificBannerList&class=" + $(".list select").find("option:selected")[0].value , function(callback) {
                        if(callback.State == "SUCCESS") {
                            var scope = angular.element($("tbody.bannerlist")).scope();
                            scope.$apply(function(){
                                scope.lists = callback.Data;
                            });
                        }
                    }, "json");
                default :
                break;
            }
        }    
	</script>

<body class="manage">
    <div class="container">
        <div class="mainContent container">
            <div class="contentInner">

                
                    <div class="section">
                        <div class="innerFrame">
                            <h2>頁面編輯</h2>
                           
                            <ul class="tags">
                                <li class="tag class active" onclick="tagEvent(event)"><div class="tagtxt">類別編輯</div></li>
                                <li class="tag banner" onclick="tagEvent(event)"><div class="tagtxt">banner列表</div></li>
                                <li class="tag banner" onclick="tagEvent(event)"><div class="tagtxt">banner圖編輯</div></li>
                            </ul>


                            <div class="bogard class">
                                <form method="post" action="jsonDataBase.php" class="mainForm" enctype="multipart/form-data">
                                    <input type='hidden' name='updateType' value="basic" />
                                    <input type='hidden' name='removeImg' value="" />
                                    <h3>banner類別列表</h3>
                                    <div class="inserBtn addNav">新增</div>
                                    <div class="bogardFrame">
                                        <form class="listForm">
                                            <table>
                                                <thead>
                                                    <tr>
                                                        <th>banner名稱</th><th>尺寸</th><th>修改/移除</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="classlist" ng-controller="bannerCtrl">
                                                    <tr ng-repeat="b in banners" bannerid="{{b.id}}" class="{{b.type == 0? 'insert':'database'}}">
                                                        <td><input type='text' name='name' value='{{b.name}}' readonly /></td>
                                                        <td><input type='text' class="half" name='width' value='{{b.width}}' readonly /> / <input type='text' class="half" name='height' value='{{b.height}}' readonly /></td>
                                                        <td>
                                                            <div class='cube confirm' onclick="cubeEvent(this)"></div>
                                                            <div class='cube edit'  onclick="cubeEvent(this)"></div>
                                                            <div class='cube remove'  onclick="cubeEvent(this)"></div>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>

                                        </form>
                                    </div>
                                </form>
                            </div>

                            <div class="bogard list hide">
                                <h3>banner列表</h3>

                                <select class="classLists" name='className' ng-controller="bannerCtrl">
                                    <option id="{{c.id}}" value="{{c.id}}" ng-repeat="c in classes">{{c.name}}</option>
                                </select>

                                <table>
                                    <thead>
                                        <tr>
                                            <th>banner_desktop</th><th>類別名稱</th><th>說明</th><th>修改/移除</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bannerlist" ng-controller="bannerCtrl">
                                        <tr class="database" ng-repeat="l in lists" bannerid="{{l.id}}" bannerImg="{{l.image}}">
                                            <td class="case loaded">
                                                <div class="preview done" style="background-image:url(../img/bannerImg/{{l.image}})"></div>
                                            </td>
                                            <td><input type="text" name="className" value="{{l.class}}" readonly /></td>
                                            <td><input type="text" name="instruction" value="{{l.instruction}}" readonly /></td>
                                            <td>
                                                <div class='cube confirm' onclick="cubeEvent(this)"></div>
                                                <div class='cube edit'  onclick="cubeEvent(this)"></div>
                                                <div class='cube remove'  onclick="cubeEvent(this)"></div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <div class="bogard banner hide">
                                <h3>banner新增</h3>
                                <form action="jsonDataBase.php" class="bannerForm" enctype="multipart/form-data">
                                
                                    <table>
                                        <thead>
                                            <tr>
                                                <th>banner_desktop</th><th>類別名稱</th><th>說明</th><th>修改/移除</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr class='addBanner'>
                                                <td class="case">
                                                    <div class='preview'><input type='hidden' name='imgSrc' /></div><input onchange='readURL(this)' type='file' name='bannerImg' />
                                                </td>
                                                <td>
                                                    <select class="classLists" name='className' ng-controller="bannerCtrl">
                                                        <option id="{{c.id}}" value="{{c.id}}" ng-repeat="c in classes">{{c.name}}</option>
                                                    </select>
                                                </td>
                                                <td>
                                                    <input type='text' name='instruction' />
                                                </td>
                                                <td>
                                                    <div class='cube confirm' onclick="cubeEvent(this)"></div>
                                                    <div class='cube edit'  onclick="cubeEvent(this)"></div>
                                                    <div class='cube remove'  onclick="cubeEvent(this)"></div>
                                                </td>
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