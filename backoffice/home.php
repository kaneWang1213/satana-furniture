<html xmlns:fb="https://www.facebook.com/2008/fbml" xmlns:og="https://ogp.me/ns#" ng-app>

    <?php
        header("Content-Type:text/html;Charset:utf-8");
        $login = false;
        include 'include/common.php';
        include 'include/header.php';
    ?>

    <style>
        div.section.welcome {
            width: 100%;
            height: 40vh;
            background-color: rgba(255, 255, 255, .5);
            text-align: center;
            margin: 5vh 0;
            position: relative;
        }

        div.section.welcome div{
            transform: translate(-50%, -50%);
            position: absolute;
            left: 50%;
            top: 50%;
            font-size: 42px;
            font-weight: bold;
            color: #555;
        }
    </style>
    
<body>
    <div class="container">
        <div class="mainContent">
            <div class="contentInner">
                    <div class="section welcome">
                        <div>
                            WELCOME
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