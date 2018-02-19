<html xmlns:fb="https://www.facebook.com/2008/fbml" xmlns:og="https://ogp.me/ns#" ng-app>
    
    <?php
        header("Content-Type:text/html;Charset:utf-8");
        $login = false;
        include 'include/common.php';
        include 'include/header.php';
    ?>
<script>

    $(function(){

         $.get("jsonDataBase.php?jsonType=getOrderData", function (callback) {

             var _data =  $.parseJSON(callback);

             if(_data.State == "SUCCESS") {
                 
                 var scope = angular.element($("tbody.ordersList")[0]).scope();
                
                 scope.$apply(function(){
                     scope.orders = _data.Data;
                     setTimeout(function() {activeBtns()}, 1000);
                 });

             } else {
                 alert("NONE");
             }
         });

        

    });

    function activeBtns() {
        $(".func > div").click(function() {
            var _obj = this;
            var _prt = $(this).parents("tr")[0];
            var _state = "jsonDataBase.php?jsonType=toggleOrderState&orderId=" + $(_prt).attr("orderId") + "&type=";
            if($(this).hasClass("zero")) {
                _state += "1";
            } else {
                _state += "0";
            }
            $.get(_state, function (callback) {

                var _data =  $.parseJSON(callback);
                if(_data.State == "SUCCESS") {
                    var _td = $(_obj).parents("td")[0];
                    if($(_obj).hasClass("zero")) {
                        _td.className = "func state1";
                    } else {
                        _td.className = "func state0";
                    }
                    
                } else {
                    alert("ERROR");
                }
            });
        });
    }

    function orderCtrl($scope) {
        $scope.orders = [];
    }

</script>

<style>
    .func > div {
        display: none;
    }

    .state0 > div.zero {
        background-color: #3253ed;
        color: #fff;
    }

    .state0 > div.zero:hover {
        background-color: #1523ea;
        color: #fff;
    }

    .state0 > div.zero:active {
        background-color: #021199;
        color: #fff;
    }

    .state0 > div.zero, .state1 > div.one {
        display: block;
    }

</style>

<body class="manage">
    <div class="container">
        <div class="mainContent container">
            <div class="contentInner">

                
                    <div class="section">
                        <div class="innerFrame">
                            <h2>訂單</h2>
                           
                            <div class="bogard class">
                                <form method="post" action="jsonDataBase.php" class="mainForm" enctype="multipart/form-data">
                                    <input type='hidden' name='updateType' value="basic" />
                                    <input type='hidden' name='removeImg' value="" />
                                    <h3>列表</h3>
                                    <div class="bogardFrame">
                                        <table  ng-controller="orderCtrl">
                                            <thead>
                                                <tr>
                                                    <th>序號</th><th>姓名</th><th>電話</th><th>地址</th><th>產品</th><th>數量</th><th>內容</th><th>日期</th><th>功能</th>
                                                </tr>
                                            </thead>
                                            <tbody class="ordersList">
                                                <tr orderId="{{order.orderId}}" ng-repeat="order in orders">
                                                    <td ng-bind="$index + 1"></td>
                                                    <td ng-bind="order.name"></td>
                                                    <td ng-bind="order.phone"></td>
                                                    <td ng-bind="order.address"></td>
                                                    <td ng-bind="order.productName"></td>
                                                    <td ng-bind="order.productNum"></td>
                                                    <td ng-bind="order.content"></td>
                                                    <td ng-bind="order.createTime"></td>
                                                    <td class="func state{{order.state}}">
                                                        <div class="zero btn">未結案</div>
                                                        <div class="one btn">結案</div>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
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