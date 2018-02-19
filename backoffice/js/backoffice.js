//標籤內容及標籤切換
function tagEvent(evt) {
    var _bogard = $(".bogard");
    var _navList = $("li.tag");
    $(_bogard).addClass("hide");
    $(_navList).removeClass("active");
    $(evt.currentTarget).addClass('active');
    $(_bogard[$(evt.currentTarget).index()]).removeClass('hide');
}

//switch 選單切換
function doSwitch(obj){
    var _ipt = $(obj).find("input")[0];
   if($(obj).hasClass("active")) {
        $(obj).removeClass("active");
        _ipt.value = 0;
    } else {
        $(obj).addClass("active");
        _ipt.value = 1;
    }
}

function cubeEvent(obj) {
    if($(obj).hasClass("remove")) {
        var _mainFrame = $(obj).parents("div.frame")[0];
        $(_mainFrame).remove();
    }
}

function animateTo(_obj) {
    var _hgt = 0;

    if (!window.matchMedia("(min-width: 768px)").matches) {
        _hgt = 0;
    }

    $('html, body').animate({
        scrollTop: $(_obj).offset().top - _hgt
    }, 500);
};

function createObj(type, name) {
    var _obj = document.createElement(type);
    if (typeof name === 'string' || name instanceof String) {
        _obj.className = name;
    }
    return _obj;
}


//產生頁碼
function pageviews(_url) {
    var _pageview = $(".pageview");

    if(_pageview.length > 0) {
        _pageview = _pageview[0];
        //取得TD、全部頁及目前頁
        var _td = $(_pageview).find("td"), _total = $(_pageview).attr("totalpos"), _current = $(_pageview).attr("currentpos"), i = 1;
        for(; i <= _total; i ++) {
            if(i == _current) {
               $(_td).append("<div class='page active'>" + i + "</div>"); 
            } else {
                $(_td).append("<div class='page'>" + i + "</div>"); 
            }
        }

        $(".page").click(function(){
            if(!$(this).hasClass("active")) {
                document.location.href = (_url + "?pagepos=" + $(this).text());
            }
        });

    }
    
}

//上傳圖片
function readURL(input) {


    if (input.files && input.files[0]) {
        var _case = $(input).parents('.case')[0];
        var _preview = $(_case).find("div.preview")[0];
        var reader = new FileReader();
        $(_case).addClass("loaded");
        $(_preview).addClass("loading").removeClass("done");
        $(_preview).unbind("click");
        reader.onload = function (e) {
            $(_preview).removeClass("loading").addClass("done");
            $(_preview).css({"background-image":"url(" + e.target.result + ")"});
            $(_preview).click(function(){
                if(confirm("確定刪除？")) {
                    $(input).val("");
                    $(_preview).unbind("click");
                    $(_preview).css("background-image","").removeClass("done");
                    alert(_case);
                    $(_case).removeClass("loaded");
                }
            });
        }
        reader.onprogress = function(data) {
            if (data.lengthComputable) {                                            
                var progress = parseInt( ((data.loaded / data.total) * 100), 10 );
                console.log(progress);
            }
        }
        //preview
        reader.readAsDataURL(input.files[0]);
    }
}

function removeImg(_obj) {


    var _ipt = $(_obj).find("input")[0];

    $.post("jsonDataBase.php", {classEvent: "removeImg", img: _ipt.value}, function (callback){
        var prt = $(_obj).parents("div")[0];
        $(prt).removeClass("loaded");
        $(_obj).removeAttr("style");
        alert(callback.State);
    }, "json");
}