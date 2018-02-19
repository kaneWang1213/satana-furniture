<nav class="navbar navbar-default navbar-fixed-top" id="navData" ng-controller="MainCtrl">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>

            <a alt="<?= TITLE; ?>" href="index.php" class="navbar-brand" >
                <img alt="<?= TITLE; ?>" ng-src="/satana/img/{{basic.headerlogo}}" />
            </a>

            
        </div>
        <div id="navbar" class="navbar-collapse collapse">
            <ul class="nav navbar-nav">
                <li ng-repeat="list in navlists" class="dropdown">
                    <a href="{{list.url}}" ng-if="list.bondId!=''&&list.active!=''" data-toggle="dropdown">{{list.name}}
                        <span class="caret"></span>
                    </a>

                    <a href="{{list.url}}" ng-if="list.bondId==''&&list.active!=''">{{list.name}}</a>

                    <ul ng-if="list.bondId!=''" class="dropdown-menu" ng-init="bids=list.bondId.split(','); bnames=list.bondName.split(',')">
                        <li ng-repeat="bid in bids">
                            <a target="_self" href="/site/class.php?id={{bid}}">{{bnames[$index]}}</a>
                        </li>
                    </ul>
                </li>
            </ul>

            <ul class="nav navbar-nav navbar-right">
                <li class="login"><a href="#"><span class="glyphicon glyphicon-log-in"></span> LogIn</a></li>
                <li class="logout"><a href="#"><span class="glyphicon glyphicon-log-out"></span> LogOut</a></li>
                <li><a target="_blank" href="https://www.facebook.com/%E7%B4%B3%E6%B3%B0%E8%98%AD%E7%B1%90%E6%9F%9A%E6%9C%A8%E5%AE%B6%E4%BF%B1-1739653726278222/">FB Sign In</a></li>
            </ul>

        </div>
    </div>
</nav>
