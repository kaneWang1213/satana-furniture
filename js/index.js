//$(document).ready(initi);

var $ofwidth = innerWidth;

function fetchData(_str) {
    
    switch(_str) {
        case "new" :
            $.get("/satana/fetchdata.php?gettingData=new", function(callback){

                if(callback.State == "SUCCESS") {
                    var scope = angular.element($("section.new .lists")[0]).scope();

                    scope.$apply(function(){
                        scope.newItems = callback.Data;
                    });
                }

            }, "json");
        break;
        case "fore" :
            $.get("/satana/fetchdata.php?gettingData=fore", function(callback){

                if(callback.State == "SUCCESS") {
                    var scope = angular.element($("section.fore .bannerFrame")[0]).scope();

                    var _rate =  callback.Data[0]["height"] / callback.Data[0]["width"] + 0.1;

                    if($ofwidth > 767) {
                        $("section.fore .bannerFrame").css({
                            "height": $("section.fore .bannerFrame").width() * _rate
                        });
                    }
                    scope.$apply(function(){
                        scope.foreBanner = callback.Data;
                    });
                    
                }

            }, "json");
            break;
        case "sale" :
            $.get("/satana/fetchdata.php?gettingData=sale", function(callback){

                if(callback.State == "SUCCESS") {
                    
                    var scope = angular.element($("section.sale .saleFrame")[0]).scope();

                    scope.$apply(function(){
   
                        scope.saleItems = callback.Data;
                    });
                    
                }

            }, "json");
        break;
        default :
        break;
    }
}



