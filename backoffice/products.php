<html xmlns:fb="https://www.facebook.com/2008/fbml" xmlns:og="https://ogp.me/ns#" ng-app>

    <?php
        header("Content-Type:text/html;Charset:utf-8");
        include 'include/common.php';
        include 'include/header.php';
    ?>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="/node_modules/core-js/client/shim.min.js"></script>
    <script src="/node_modules/zone.js/dist/zone.js"></script>
    <script src="/node_modules/systemjs/dist/system.src.js"></script>
    <script src="js/systemjs.config.js"></script>
    <script>
        System.import("/app/main.js").catch(function (err) { console.error(err); });
    </script>

	</script>

<body class="manage">
    <div class="container">
        <!-- <div class="mainContent container">
            <div class="contentInner">
                    <div class="section">
                        <div class="innerFrame">
                            <h2>產品編輯</h2>
                            
                            <ul class="tags">
                                <li class="tag main active" onclick="tagEvent(event)"><div class="tagtxt">產品列表</div></li>
                            </ul>

                            <div class="bogard list">
                                <input type='hidden' name='updateType' value="basic" />
                                <input type='hidden' name='removeImg' value="" />
                                <h3>&nbsp;</h3>

                                <div class="inserBtn preview">預覽頁面</div>
                                <div class="bogardFrame">
                                    <form class="listForm">
                                        <table>
                                            <thead>
                                                <tr>
                                                    <th>商品名稱</th><th>商品編號</th><th>商品分類</th><th>商品售價</th><th>修改/移除</th>
                                                </tr>
                                            </thead>
                                            <tbody class="productlist">
                                                <tr ng-repeat="p in products" prdId="{{p.prdid}}">
                                                    <td>{{p.name}}</td>
                                                    <td>{{p.no}}</td>
                                                    <td>{{p.class}}</td>
                                                    <td>{{p.price}}</td>
                                                    <td><div class='cube edit' onclick="cubeEvent(this)"></div><div class='cube remove'></div></td>
                                                </tr> 
                                            </tbody>
                                        </table>

                                        <div class="pageMap" ng-controller="productCtrl">
                                            <div class="page {{page.active? 'active':''}}" onclick="moveTo(this,'{{page.class}}')" ng-repeat="page in pages">{{page.number}}</div>
                                        </div> 
                                    </form>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div> -->
            <my-app>Loading...</my-app>


            <footer>
                <!--<div>12345{{ AppModule.myVariable }}</div>
                 {{ footer }} -->
                <!--example 6: multislot transclusion -->
                <!--<ng-content select="card-footer"></ng-content>-->
            </footer>


        </div>


    
    <div class="blackFrame itemFrame loadingFrame hide"><div class="loadingInner"><span>now loading...</span></div></div>
</body>
</html>