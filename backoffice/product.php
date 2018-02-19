<html xmlns:fb="https://www.facebook.com/2008/fbml" xmlns:og="https://ogp.me/ns#" ng-app>

    <?php
        header("Content-Type:text/html;Charset:utf-8");
        $login = false;
        include 'include/common.php';
        include 'include/header.php';
    ?>
    
    <script src="https://cloud.tinymce.com/stable/tinymce.min.js"></script>

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
		function init() {

            $(".inserBtn").click(function() {

                if($(this).hasClass("preview")) {

                } else if($(this).hasClass("addNav")) {
                    var _navForm = $(this).parents("form.navForm")[0];
                    var _confirm = $(_navForm).find("div.row.confirmFrame")[0];
                    $(_confirm).before("<div class='data row'><div class='col-md-1'><font>顯示文字</font></div><div class='col-md-3'><input type='text' name='navName[]' /></div><div class='col-md-1'><font>連結位置</font></div><div class='col-md-3'><input type='text' name='navUrl[]' /></div><div class='col-md-3'><div class='switch active'><input type='hidden' name='active[]' value='1' /></div></div></div>");
                } else if($(this).hasClass("newClass")) {
                    //新增類別
                    var _row = $(this).siblings("div")[0];
                    var _cf = $(this).siblings("div.confirmFrame")[0];
                    $(this).addClass('hide');
                    $(_row).append("<div class='col-md-3 insert dataframe'><form><div class='cubes'><div onclick='cubeEvent(this)' class='cube edit'></div><div onclick='cubeEvent(this)' class='cube remove'></div><div onclick='cubeEvent(this)' class='cube confirm'></div></div><input type='hidden' name='classEvent' value='insert' /><input type='hidden' name='classId' value'' /><h3><input type='text' name='className' placeholder='輸入類別名稱' /></h3><input type='text' name='alt' placeholder='輸入類別代號' /><div class='case'><div onclick='removeImg(this)' class='preview'><input type='hidden' name='wbg' value='' /></div><input type='file' name='img' onchange='readURL(this)' /></div></form></div>");
                } else if ($(this).hasClass("newParam")) {
                    paramlist("insert");
                }
            });

            $(".confirmBtn").click(function() {
                var _bogard = $(this).parents("div.bogard")[0];
                if($(this).hasClass("listBtn")) {
                    var _slt = $(this).siblings("select")[0];
                    if(_slt.value > 0) {
                        productsList(0, _slt.value);
                    }
                } else {
                    var _form = $(this).parents("form")[0];
                    
                     $(_form).attr({
                        "action": "jsonDataBase.php",
                        "method": "post",
                        "enctype":"multipart/form-data"
                    }); 
                    
                    if($($(this).parents("form")[0]).hasClass("productModifiedForm")) {
                        _form.content.value = tinyMCE.get('modifiy').getContent();              //修改
                    } else {
                        _form.content.value = tinyMCE.get('new').getContent();              //新增

                    }

                    if($(this).hasClass("productConfirm")) {
                        
                        //$(_form).submit();
                        //return; 
                        //alert(_form.productEvent.value);
                        $.ajax({
                            url: 'jsonDataBase.php',
                            type: 'POST',
                            cache: false,
                            data: new FormData(_form),
                            processData: false,
                            contentType: false,
                            success: function(msg) {
                                var callback = JSON.parse(msg);
                                if(callback.State == "SUCCESS") {
                                    alert("upload successifully!");
                                    location.reload();
                                }
                            }
                        });
                    }
                }

                
            });

            $(".plusImage").click(function() {
                //新增產品圖片
                var _div = $(this).parents("div.case")[0];
                $(_div).before("<div class='col-md-2 case'><div class='preview'><input type='hidden' name='imgSrc' /></div><input onchange='readURL(this)' type='file' name='productImg[]' /></div>");
            });

            //選擇類別
            $("select.pickClass.modify").change(function () {
                var _bogard = $(this).parents("div.bogard")[0];
                if($(_bogard).hasClass("modify")) {
                    //選擇所有產品
                    if(this.value < 0) { return;};

                    $.get("jsonDataBase.php?selectProducts=" + this.value, function (callback) {

                        var scope = angular.element($("select.pickProduct")).scope();
                        scope.$apply(function(){
                            if(callback.State == "SUCCESS") {
                                scope.products = callback.Data;
                            } else {
                                scope.products = [];
                            }
                        });

                    }, "json");
                }                
            });

            //選擇產品
            $("select.pickProduct").change(function () {
                if(this.value !== 0 && this.value !== -1) {
                    putDataToEditTable(this.value);
                }  
            });

            classList();
            paramlist("db");
            productsList(0, 0);

            $("td#parameter input[type=button]").click(function() {
                var scope = angular.element($("td#chosen")).scope();
                var _slt = $(this).siblings("select")[0];

                if(scope.chosenPara.length > 0) {
                    
                    for(var i = 0 ; i < scope.chosenPara.length ; i++) {
                        if(scope.chosenPara[i].name == $($(_slt).find("option:selected")).text()) {
                            alert("重覆的屬性");
                            return;
                        }
                    }
                    
                }

                scope.$apply(function(){
                    scope.chosenPara.push(
                        {
                            id: _slt.value,
                            name:  $($(_slt).find("option:selected")).text()
                        }
                    );
                });
            });

            tinymce.init({ selector:'textarea[name=content]'});
            

        }; //end of initi

        function paramlist(_type) {
            if(_type == "db") {
                $.get("jsonDataBase.php?Param=getting", function (callback) {
                    $(".pickparameter").each(function(){
                            var scope = angular.element(this).scope();
                            scope.$apply(function(){
                                scope.parameters = callback.Data;
                            });
                    });

                }, "json");
            } else if(_type == "classList") {

            } else if(_type == "insert") {
                $(".param .pickparameter").each(function() {
                    var scope = angular.element(this).scope();
                    scope.$apply(function(){
                        scope.parameters.push({name:'', meth:'insert'});
                        setTimeout(function(){
                            $($(".param .insert input[type=text]")[1]).removeAttr("readonly");
                        }, 500);
                    });


                });
            }
            
        }

        function classList() {
            $.get("jsonDataBase.php?jsonType=gettingClass" , function (callback) {

                if(callback.State == "SUCCESS") {

                    var _slt = $(".pickClass");

                    _slt.each(function(){
                        var scope = angular.element(this).scope();
                        scope.$apply(function(){
                            scope.items = callback.Data;
                        });
                    });

                    

                }
            }, "json");
        }

        function productsList(_page, _class) {
            //get data
            $.get("jsonDataBase.php?selectProductList=" + _page + "&selectClass=" + _class, function (callback) {
                if(callback.State == "SUCCESS") {
                    
                    var scope = angular.element($(".productlist")[0]).scope();


                    scope.$apply(function(){
                        scope.products = [];

                        scope.products = callback.Data;
                    });

                    var scope2 = angular.element($(".pageMap")).scope();

                    var _mapSource = [];
                    var _target = callback.Target + 1;
                    var _total = Math.floor(callback.Total / 10) + 1;

                   
                    for(var i = 1 ; i <= _total ; i++) {
                        _mapSource.push({
                            "number": i,
                            "active": (_target == i)? true:false,
                            "class": callback.Class
                        });
                    }

                    

                    scope2.$apply(function(){
                        scope2.pages = _mapSource;
                    });


                } else { 

                }


                
            }, "json");
        }

        function cubeEvent(obj) {
            var _divframe = $(obj).parents("div.dataframe")[0];
            var _form = $(obj).parents("form")[0];
            switch(obj.className) {
                case "cube edit" :
                    var _tr = $(obj).parents("tr")[0], _td;
                    if(_form !== undefined) {
                        if($(_form).hasClass("listForm")) {
                            //產品列表編輯
                            _td = $(_tr).find("td")[2];
                            listToEdit($(_td).text(), $(_tr).attr("prdId"));
                        // productsList($(_tr).attr("prdId"), $(_td).text())
                        } else {
                            //產品類別圖片編輯
                            _form.classEvent.value = "update";
                            $(_divframe).removeClass("database").addClass("update");
                        }
                    } else {
                        //產品屬性
                        _td = $(_tr).find("td");
                        $(_tr).find("input[type=text]").each(function(idx){
                            if(idx == 1) {
                                $(this).removeAttr('readonly');
                            }
                        });
                        $(_tr).removeClass("database").addClass("edit");
                    }

                break;
                case "cube remove" :
                if(confirm("Are you sure?")) {

                    if($(_form).hasClass("listForm")) {             //產品



                        $(_form).append("<input type='hidden' name='productEvent' value='remove' /><input type='hidden' name='productId' value='" + $(obj).parents("tr").attr("prdid") + "' />");

                        $.post("jsonDataBase.php", $(_form).serialize(), function (callback) {
                            if(callback.State == "SUCCESS") {
                                alert(callback.REALSTATE);
                                alert("remove successifully");
                            }
                        },"json");

                    } else {
                        if(_form.classId.value == "") {
                            $(_divframe).remove();        
                        } else {
                            _form.classEvent.value = "remove";
                            $.post("jsonDataBase.php", $(_form).serialize(), function (callback) {
                                if(callback.State == "SUCCESS" && callback.RemoveState) {
                                    $(_divframe).remove(); 
                                    alert("remove successifully");
                                }
                            },"json");
                        }
                    }

                    
                }
                break;
                case "cube confirm" :
                    if(_form !== undefined) {
                        if(_form.classEvent.value == "insert") {
                            $.ajax({
                                url: 'jsonDataBase.php',
                                type: 'POST',
                                cache: false,
                                data: new FormData(_form),
                                processData: false,
                                contentType: false,
                                success: function(msg) {
                                    var callback = JSON.parse(msg);
                                    var _case = $(_divframe).find(".case")[0];
                                    if(callback.Img !== "NONE") {
                                        var _preview = $(_case).find(".preview")[0];
                                        $(_case).addClass("loaded");
                                        $(_preview).css("background-image","url(../data/img/" + callback.Img + ")");
                                        _form.wbg.value = "../data/img/" + callback.Img;
                                    }
                                    
                                    _form.classEvent.value = "";
                                    $(_divframe).removeClass("insert").addClass("database");
                                    alert("upload successifully");
                                    $(".bogard.class .inserBtn").removeClass("hide");

                                }
                            });
                        } else {

                             $.ajax({
                                url: 'jsonDataBase.php',
                                type: 'POST',
                                cache: false,
                                data: new FormData(_form),
                                processData: false,
                                contentType: false,
                                success: function(callback) {
                                    var _data = JSON.parse(callback);
                                    
                                    if(_data.State == "SUCCESS") {
                                        _form.classEvent.value = "";
                                        $(_divframe).removeClass("update").addClass("database");
                                        alert("update successifully");
                                    }
                                    
                                }
                             });

                            
                        }
                    } else {
                        var _tr = $(obj).parents("tr")[0];

                        if($(_tr).hasClass("edit")) {
                            
                            $.post("jsonDataBase.php", {Param: "update", id: $(_tr).find("input[type=text]")[0].value, name: $(_tr).find("input[type=text]")[1].value}, function (callback) {
                                if(callback.State == "SUCCESS") {
                                    alert("UPDATE SUCCESSFULLY");
                                    paramlist("db");
                                }
                            }, "json");
                        } else if($(_tr).hasClass("insert")) {
                            $.post("jsonDataBase.php", {Param: "insert", name: $(_tr).find("input[type=text]")[1].value}, function (callback) {
                                if(callback.State == "SUCCESS") {
                                    alert("UPDATE SUCCESSFULLY");
                                    paramlist("db");
                                }
                            }, "json");
                        }
                    }

                    
                break;
                default :
                break;
            }
        }
            
        function handleEvt(obj) {
            if($(obj).hasClass("insert")) {
                var _form = $(obj).parents("form")[0];

                $.post("jsonDataBase.php", $(_form).serialize(), function (callback){
                    alert(callback.State);
                }, "json");
            }
        }

        function productCtrl($scope) {
            $scope.products = [{}];
            $scope.pages = [];
        }

        //跳到地圖頁面
        function moveTo(_obj, _class) {
            if($(_obj).hasClass("active"))
                return;
                
            productsList(parseInt($(_obj).text()) - 1, _class);
        }

        //編輯列表
        function listToEdit(_cls, _id) {
            $(".bogard").addClass("hide");
            $(".tag").removeClass("active");
            $(".bogard.modify").removeClass("hide");
            $(".tag.modify").addClass("active").removeClass("hide");
            $(".modify select.pickClass").val(_cls);
            
            $.get("jsonDataBase.php?selectProducts=" +_cls, function (callback) {
                if(callback.State == "SUCCESS") {
                    var scope = angular.element($("select.pickProduct")).scope();
                    scope.$apply(function(){
                        if(callback.State == "SUCCESS") {
                            scope.products = callback.Data;
                            setTimeout(function(){
                                $(".modify select.pickProduct").val(_id);
                                putDataToEditTable(_id);
                            },200);
                            
                        } else {
                            scope.products = [];
                        }
                    });

                }
                 
            }, "json");
           
        }

        function putDataToEditTable(_value) {
            
            var scope = angular.element($("select.pickProduct")).scope();
            for(var i = 0 ; i < scope.products.length ; i++) {
                if(scope.products[i]["id"] == _value) {

                    var _form = $(".bogard.modify form")[0];
                    _form.productName.value = scope.products[i]["name"];
                    _form.altName.value = scope.products[i]["alt"];
                    _form.price.value =  scope.products[i]["price"];
                    _form.brief.value = scope.products[i]["brief"];
                    _form.content.value = scope.products[i]["description"];
                    _form.id.value = scope.products[i]["id"];
                    _form.className.value = scope.products[i]["bond"];

                    tinyMCE.get('modifiy').setContent(scope.products[i]["description"]);

                    var _paramScope = angular.element($("td#chosen")).scope();
                    
                    _paramScope.$apply(function(){
                         _paramScope.currentPara = [];
                         if(scope.products[i]["param"] != null) {

                             var _param = scope.products[i]["param"].split(",");
                             for(var k = 0 ; k < _param.length ; k++) {
                                 var _val = _param[k].split("|");
                                 _paramScope.currentPara.push({id: _val[0], name: _val[1]});
                             }
                         }

                        
                    });

                    var _imgScope = angular.element($("#imgpreview")).scope();

                    if(scope.products[i]["image"] !== null) {
                        var _imgs = scope.products[i]["image"].split(",");
                        _imgScope.$apply(function(){
                            _imgScope.images = [];

                            $(_imgs).each(function(){
                                _imgScope.images.push({
                                    "link": "../data/img/product/" + this,
                                    "img": this
                                });
                            });

                            setTimeout(function(){
                                $("#imgpreview .preview").click(function(){
                                    var _obj = $(this).parents(".case")[0];
                                    var _str = $(this).attr("source").replace(/"/g, '');
                                    $.post("jsonDataBase.php?jsonType", {jsonType: "removeProductImage", IMG: _str}, function (callback) {
                                        if(callback.State == "SUCCESS") {
                                            $(_obj).remove();
                                        }
                                    }, "json");


                                });
                            });
                        });
                    } else {
                        _imgScope.$apply(function(){
                            _imgScope.images = [];
                        });
                    }
                }
            }
        }
	</script>
    <style>
        .mce-notification-inner {
            display: none;
        }
    </style>

<body class="manage">
    <div class="container">
        <div class="mainContent container">
            <div class="contentInner">
                    <div class="section">
                        <div class="innerFrame">
                            <h2>產品編輯</h2>
                            <?php if($consequest == -1) { ?>
                            <div class='selectList'><select><option value="0">請選擇活動</option></select></div>
                            <?php } ?>
                            <ul class="tags">
                                
                                <li class="tag main active" onclick="tagEvent(event)"><div class="tagtxt">產品列表</div></li>
                                <li class="tag class" onclick="tagEvent(event)"><div class="tagtxt">產品類別</div></li>
                                <li class="tag param" onclick="tagEvent(event)"><div class="tagtxt">產品屬性</div></li>
                                <li class="tag add" onclick="tagEvent(event)"><div class="tagtxt">新增產品</div></li>
                                <li class="tag modify" onclick="tagEvent(event)"><div class="tagtxt">修改產品</div></li>

                                
                                <!-- <li class="tag product" onclick="tagEvent(event)"><div class="tagtxt">產品編輯</div></li> -->
                            </ul>

                            <div class="bogard list">
                                <input type='hidden' name='updateType' value="basic" />
                                <input type='hidden' name='removeImg' value="" />
                                <h3>&nbsp;</h3>

                                
                                <div class="sltClass" ng-controller="MainCtrl">
                                    
                                    <select name="className" class="pickClass">
                                        <option value="{{item.id}}" ng-repeat="item in items">{{item.name}}</option>
                                    </select>
                                    
                                    <div class="btnForm confirmBtn listBtn">確定</div>
                                </div>
                                <div class="inserBtn preview">預覽頁面</div>
                                <div class="bogardFrame">
                                    <form class="listForm">
                                        <table>
                                            <thead>
                                                <tr>
                                                    <th>商品名稱</th><th>商品編號</th><th>商品分類</th><th>商品售價</th><th>修改/移除</th>
                                                </tr>
                                            </thead>
                                            <tbody class="productlist" ng-controller="productCtrl">
                                                <tr ng-repeat="p in products" prdId="{{p.prdid}}">
                                                    <td>{{p.name}}</td>
                                                    <td>{{p.no}}</td>
                                                    <td>{{p.class}}</td>
                                                    <td>{{p.price}}</td>
                                                    <td><div class='cube edit' onclick="cubeEvent(this)"></div><div class='cube remove' onclick="cubeEvent(this)"></div></td>
                                                </tr>
                                            </tbody>
                                        </table>

                                        <div class="pageMap" ng-controller="productCtrl">
                                            <div class="page {{page.active? 'active':''}}" onclick="moveTo(this,'{{page.class}}')" ng-repeat="page in pages">{{page.number}}</div>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            <div class="bogard class hide">
                                <h3>&nbsp;</h3>
                                <div class="inserBtn newClass">新增類別</div>
                                <div class="bogardInner">
                                    <?php
                                        $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_DATABASE);
                                        $mysqli->set_charset("utf8"); //設定UTF8
                                        $sqlStr = "SELECT id, name, alt, img FROM class_data;";
                                        $result = $mysqli -> query($sqlStr);
                                        $classArray = array(array("select below" , -1), array("select all" , 0));
                                        if (mysqli_num_rows($result) > 0) {
                                            while($row = mysqli_fetch_assoc($result)) {
                                                array_push($classArray, array($row["name"], $row["id"]));
                                        
                                    ?>


                                    <div class="col-md-3 database dataframe">
                                        <form>
                                            <input name="classEvent" value="" type="hidden">
                                            <input name="classId" value="<?= $row["id"] ?>" type="hidden">
                                            <div class="cubes">
                                                <div onclick='cubeEvent(this)' class="cube edit"></div><div onclick='cubeEvent(this)' class="cube remove"></div><div onclick='cubeEvent(this)' class="cube confirm"></div>
                                            </div>
                                            <h3><input name="className" placeholder="輸入類別名稱" type="text" value="<?= $row["name"] ?>"></h3>
                                            <input name="alt" placeholder="輸入類別代號" type="text" value="<?= $row["alt"] ?>">

                                            <?php if($row["img"] !== "" && $row["img"] !== null) { ?>

                                                <div class="case loaded">
                                                    <div onclick="removeImg(this)" class="preview" style="background-image:url(../data/img/<?= $row["img"] ?>)">
                                                        <input name="wbg" value="../data/img/<?= $row["img"] ?>" type="hidden">
                                                    </div>
                                                    <input name="img" type="file" onchange="readURL(this)">
                                                </div>

                                            <?php } else { ?>

                                                <div class="case">
                                                    <div onclick="removeImg(this)" class="preview">
                                                        <input name="wbg" value="" type="hidden">
                                                    </div>
                                                    <input name="img" type="file" onchange="readURL(this)">
                                                </div>

                                            <?php } ?>
                                        </form>
                                    </div>


                                    <?php }} ?>

                                    <script>
                                            function MainCtrl($scope) {
                                                $scope.items = [];
                                            }

                                            function SecCtrl($scope) {
                                                $scope.parameters = [];
                                                $scope.chosenPara = [];
                                                $scope.currentPara = [];
                                            }

                                            function ImgCtrl($scope) {
                                                 $scope.images = [];
                                            }
                                    </script>

                            
                                    
                                    
                                </div>

                                <div class="col-md-12 confirmFrame hide">
                                    <div class='btnForm confirmBtn btn'>確認</div>
                                </div>
                                <div class="clear"></div>
                            </div>

                            <div class="bogard param hide">
                                <h3>&nbsp;</h3>
                                <div class="inserBtn newParam">新增屬性</div>
                                <div class="bogardFrame">
                                   <div class="bogardInner">
                                        <table id="parameter">
                                            <thead><tr><th>產品序號</th><th>產品屬性</th><th>修改</th></tr></thead>
                                            <tbody class="pickparameter" ng-controller="SecCtrl">
                                                <tr class='{{param.meth}}' ng-repeat="param in parameters">
                                                    <td><input type='text' name='id' value='{{param.id}}' readonly /></td>
                                                    <td><input type='text' name='id' value='{{param.name}}' readonly /></td>
                                                    <td>
                                                        <div class='cube edit' onclick="cubeEvent(this)"></div>
                                                        <div class='cube confirm' onclick="cubeEvent(this)"></div>
                                                    </td>
                                                </tr>
                                            </tbody>

                                        </table>
                                   </div>
                                </div>
                            </div>

                            <div class="bogard products hide">
                                <div class="bogardFrame">
                                   <div class="bogardInner">
                                        <form class="productForm">
                                            <input type='hidden' name='productEvent' value="insert" />
                                            <table>
                                                <tr>
                                                    <th>產品名稱</th><th>產品代號</th><th>產品特價</th>
                                                </tr>
                                                <tr>
                                                    <td><input type="text" name="productName" /></td>
                                                    <td><input type="text" name="altName" value="<?= uniqid(); ?>" /></td>
                                                    <td><input type="text" name="price" /></td>
                                                </tr>
                                                <tr>
                                                    <th>所屬類別</th><th colspan="2">簡單說明</th>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <div ng-controller="MainCtrl">
                                                            <select name="className" class="pickClass">
                                                                <option value="{{item.id}}" ng-repeat="item in items">{{item.name}}</option>
                                                            </select>
                                                        </div>
                                                    </td>
                                                    <td colspan="2"><textarea name="brief"></textarea></td>
                                                </tr>
                                                <tr>
                                                    <th colspan="3">產品內容</th>
                                                </tr>
                                                <tr>
                                                    <td colspan="3"><textarea id="new" name="content"></textarea></td>
                                                </tr>
                                                <tr>
                                                    <th colspan="3">產品圖片</th>
                                                </tr>
                                                <tr>
                                                    <td colspan="3">
                                                        <div class="col-md-2 case">
                                                            <div class="preview"><input type='hidden' name="imgSrc" /></div>
                                                            <input onchange="readURL(this)" type="file" name="productImg[]" />
                                                        </div>
                                                        <div class="col-md-2 case">
                                                            <div class="plusImage btn">新增圖片</div>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td colspan="2"><div class='btnForm confirmBtn productConfirm btn'>確認</div></td>
                                                </tr>
                                            </table>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <div class="bogard hide modify">
                                <h3></h3>
                                <div class="bogardFrame">
                                    <div ng-controller="MainCtrl">
                                        <select class="pickClass modify">
                                            <option value="{{item.id}}" ng-repeat="item in items">{{item.name}}</option>
                                        </select>
                                    </div>
                                    <div ng-controller="SecCtrl">
                                        <select class="pickProduct">
                                            <option value="{{product.id}}" ng-repeat="product in products">{{product.name}}</option>
                                        </select>
                                    </div>
                                    <form class="productModifiedForm">
                                            <input type='hidden' name='productEvent' value="update" />
                                            <input type='hidden' name='id' value="" />
                                            <table>
                                                <tr>
                                                    <th>產品名稱</th><th>產品代號</th><th>產品特價</th>
                                                </tr>
                                                <tr>
                                                    <td><input type="text" name="productName" /></td>
                                                    <td><input type="text" name="altName" /></td>
                                                    <td><input type="text" name="price" /></td>
                                                </tr>
                                                <tr>
                                                    <th>所屬類別</th>
                                                    <th colspan="2">簡單說明</th>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <div ng-controller="MainCtrl">
                                                            <select name="className" class="pickClass">
                                                                <option value="{{item.id}}" ng-repeat="item in items">{{item.name}}</option>
                                                            </select>
                                                        </div>
                                                    </td>
                                                    <td colspan="2">
                                                        <textarea name="brief"></textarea>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th colspan="3">產品內容<th>
                                                </tr>
                                                <tr>
                                                    <td colspan="3"><textarea id="modifiy" class="product-content" name="content"></textarea></td>
                                                </tr>
                                                
                                                <tr>
                                                    <th>所屬屬性</th><th colspan="2">屬性</th>
                                                </tr>

                                                <tr>
                                                    <td ng-controller="SecCtrl" id="parameter">
                                                        <select class="pickparameter">
                                                            <option value="{{p.id}}" ng-repeat="p in parameters">{{p.name}}</option>
                                                        </select>
                                                        <input type='button' value="增加" />
                                                    </td>
                                                    <td ng-controller="SecCtrl"  id="chosen">
                                                        <span ng-repeat="cc in currentPara">
                                                            {{cc.name}}
                                                            <input type='hidden' name="delparam[]" paraId=""  value="{{cc.id}}"/>
                                                        </span>

                                                        <span ng-repeat="cp in chosenPara">
                                                            {{cp.name}}
                                                            <input type='hidden' name="parameter[]" paraId=""  value="{{cp.id}}"/>
                                                        </span>
                                                        
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <th colspan="3">產品圖片</th>
                                                </tr>
                                                <tr>
                                                    <td colspan="3" ng-controller="ImgCtrl" id="imgpreview">
                                                        <div class="col-md-2 case loaded" ng-repeat="img in images">
                                                            <div class="preview done" source="{{img.img}}" style="background-image:url({{img.link}})"><input type='hidden' name="imgSrc" /></div>
                                                            <input onchange="readURL(this)" type="file" name="productImg[]" />
                                                        </div>
                                                        <div class="col-md-2 case">
                                                            <div class="plusImage btn">新增圖片</div>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td colspan="2"><div class='btnForm confirmBtn productConfirm btn'>確認</div></td>
                                                </tr>
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