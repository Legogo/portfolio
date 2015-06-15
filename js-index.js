//website palette colors
var colors = [
  ["fff", "000", "555", "AAA", "555", "777", "111"]
];

/*
  ["040a19", "1b543d", "7d8c35", "c7a734", "fab95b", "de7f50"],
  ["0f141f", "46594B", "A6977C", "D9B384", "734F30", "532e25"]
*/

$(function(){

  //set website color (random)
  setPalette();
  
  //hide categories articles
  $(".links").hide();
  
  //remove article from screen
  hideOverlay(0);
  
  setup__interactivity();

  //articles related
	//$(".content-toggle").not(".code").hide(); // hide toggle content that is not code
  
  //if(article) open menu at this article
  openParentCategory(); // will open article

  //console.log(getFileDateFromUrl());

  var path = "pages/"+getFileDateFromUrl()+".html";
  console.log(path);
  $.get(path,function(data){
    $("#overlay").html(data);
    openOverlay();
  });

});

function getFileDateFromUrl(){
  var url = document.URL;
  if(url[url.length] == "/") url = url.substring(0,url.length-2);
  var data = url.split("/");
  return data[data.length-1];
}

function setup__interactivity(){

  //remove article overlay
  $("#overlay-click").click(function(e){
    hideOverlay(100);
  });
  
  //click category
  $(".category").click(function(e){
    
    //inactive links
    if($(this).attr("href") != "#") return;
    
    e.preventDefault();
    var id = $(this).attr("id");
    var elmt = $("#links-"+id);
    
    $(".links").hide(200);

    if(!elmt.is(":visible")){
      //reset lines behaviour
      elmt.children(".category-item").stop().css("margin-left", "0px");
      elmt.children(".category-item").show();
      elmt.show(300);
    }
  });
  

  //hover on article or category make it go right a littel
  $(".category, .category-item").hover(function(){
    $this = $(this);
    $this.stop().animate({marginLeft:"10px"}, 150);
  }, function(){
    $this = $(this);
    $this.stop().animate({marginLeft:'0px'}, 100);
  });

  //filter by subcategory
  $(".cat-line-sub").click(function(e){
    e.preventDefault();
    $this = $(this);
    var filter = $this.html();
    
    //hide all
    var cat = $this.parent().parent(); // line wrapper > links wrapper
    cat.children('.category-item').hide();

    console.log("filtering "+filter+" for cat : "+cat.attr("id"));
    //show only what we want
    cat.children('.'+filter).show();
  });

}

function trim (myString)
{
  return myString.replace(/^\s+/g,'').replace(/\s+$/g,'')
}
 
function openParentCategory(){
  var date = $("#overlay").attr("rel");
  date = trim(date);
  if(date.length <= 0)  return;
  
  //ouvrir le menu de l'article selectionné
  var articles = $(".cat-line-article"); // gather all article LINKS
  var current = null;
  for(i = 0; i < articles.length; i++){
    var jQueryObject = $(articles[i]);
    //console.log(jQueryObject.attr("href")+" == "+date);
    if(jQueryObject.attr("href") == date){
      current = jQueryObject;
    }
  }
  
  //si c'est un article, on ouvre son parent
  if(current != null){
    current.parent().parent().show(); //category > line wrapper > link wrapper
    current.addClass("category-item-selected");
  }
  
  //charger l'article de l'url
  updateOverlay(); // resize to fit article

  openOverlay();
  Shadowbox.init();
}

var updateId = -1;
function openOverlay(){
  updateOverlay();
  $("#overlay-bg").slideToggle(500);
  $("#overlay").slideToggle(700, function(){
    if(updateId < 0){
      //console.log("added");
      updateId = setInterval(function(){
        updateOverlay();
      }, 200);
    }
  });
  $("#overlay-click").slideToggle(100);
  //$("#overlay").show('slide', {direction: 'right'}, 200);

}

/* setup black overlay based on the size of the screen */
function updateOverlay(){
  var clickLayer = $("#overlay-click"); // zone transparente pour fermer l'article
  var bg = $("#overlay-bg"); // zone sombre derrière
  var overlay = $("#overlay"); // zone de texte
  
  //background layer to receive click to remove overlay
  var winWidth = parseInt($(window).width());
  //var winHeight = parseInt($(window).height());
  var winHeight = parseInt($(document).height());
  
  if(getInternetExplorerVersion() >= 0){
    winWidth = screen.width;
    winHeight = screen.height;
  }
  
  //alert(winWidth+", "+winHeight);
  clickLayer.css("width", winWidth);
  
  var h = Math.max(parseInt(overlay.css("height")) + 75, winHeight);
  clickLayer.css("height",h);
  
  //setup black background behind text
  bg.css("width",parseInt(overlay.css("width")) + 50);
  bg.css("height",h);
  bg.css("left", Math.max(0, (($(window).width() - $(this).outerWidth()) * 0.5) + $(window).scrollLeft()) + "px");
  
  //setup text zone
  overlay.css("left", Math.max(0, (($(window).width() - $(this).outerWidth()) * 0.5) + $(window).scrollLeft()) + "px");
}

function hideOverlay(delay){
  var other = (delay <= 0) ? 0 : delay + 100; // 100 more ?
  $("#overlay-click").hide(other);
  $("#overlay-bg").hide(other);
  $("#overlay").hide(delay);
  
  //remove article selection
  $(".category-item").removeClass("category-item-selected");

  if(updateId > -1){
    //console.log("cleared");
    clearInterval(updateId);
    updateId = -1;
  }
}

function setPalette(){
  var rand = Math.floor(Math.random() * colors.length);
  var palette = colors[rand];
  //console.log(rand, colors, palette);
  $(document.body).css("background-color","#"+palette[0]);
  $(".category").each(function(index){
    var paletteId = $(this).attr("rel");
    var color = "#"+palette[paletteId];
    $(this).css("color",color);
    
    //set same color to all link children
    var id = "."+$(this).attr("id");
    $(id).css("color",color);
  });
}




function getInternetExplorerVersion()
// Returns the version of Internet Explorer or a -1
// (indicating the use of another browser).
{
  var rv = -1; // Return value assumes failure.
  if (navigator.appName == 'Microsoft Internet Explorer')
  {
    var ua = navigator.userAgent;
    var re  = new RegExp("MSIE ([0-9]{1,}[\.0-9]{0,})");
    if (re.exec(ua) != null)
      rv = parseFloat( RegExp.$1 );
  }
  return rv;
}
function checkVersion()
{
  var msg = "You're not using Internet Explorer.";
  var ver = getInternetExplorerVersion();

  if ( ver > -1 )
  {
    if ( ver >= 8.0 ) 
      msg = "You're using a recent copy of Internet Explorer."
    else
      msg = "You should upgrade your copy of Internet Explorer.";
  }
  alert( msg );
}