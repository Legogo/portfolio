<?php
  
  $mediaPath = "pages/medias/";
  $pagesPath = "pages/";

  function htmlArticle($itemDate){
    //$itemDate define in index.php

    if(strlen($itemDate) > 0){
      //list($body,$item) = explode("=", $item);
      //print_r($_GET);
      
      //attention, les fichiers qui ne sont pas des articles ne doivent pas contenir de -
      if (strpos($itemDate, "-") === false) {
        $str = displayItem("", trim($itemDate));
      }else {
        $str = displayItem("pages/", trim($itemDate));

        //track only pages
        //if(!strpos($itemDate, "/")){ tracking__addCount($itemDate); }
        
      }
    }else{
      $str = "Nothing returned by ".$itemDate;
    }
    return $str;
  }
  
  function generateCatList($articles,$name){
    if(!isset($articles[$name])) return;
    $catAll = $articles[$name];
    
    $catAll = array_reverse($catAll);
    
    $str = "";
    $str .= '<div class="links" id="links-'.$name.'">'.PHP_EOL;

    foreach($catAll as $item){
      $title = $item["title"];
      $firstBlank = strpos($title, " ");
      
      $sub = substr($title, 0, $firstBlank);
      $title = substr($title, $firstBlank, strlen($title));
      
      $str .= '<div class="category-item '.$sub.'">';
        if(strlen($sub) > 0)  $str .= '<a class="cat-line-sub" href="">'.$sub.'</a>';
        $str .= '<a class="cat-line-article" href="'.$item["file"].'">'.$title.'</a>';
      $str .= '</div>'.PHP_EOL;
    }
    $str .= '</div>'.PHP_EOL;
    return $str;
  }

  function tracking__addCount($page){
    //var_dump($page);
    $file = file_get_contents("stats.txt");
    $file = explode("\n", $file);

    if(count($file) <= 0) $file = Array(); // reset to array
    $found = false;
    
    //echo count($file);

    //search for existing entry and incr value
    $i = 0;
    for($i = 0; $i < count($file); $i++){
      if($found)  continue;

      $line = $file[$i]; // temp

      if(strpos($line,"=")){
        list($cat,$count) = explode("=", $line);
        if($cat == $page){
          $count++;
          $file[$i] = $cat."=".$count;
          $found = true;
          //echo "FOUND";
        }
      }

    }

    //create new entry
    if(!$found){
      $file[] = $page."=1";
    }

    //echo "->".count($file);

    //recreate file
    $output = "";
    for ($i=0; $i < count($file); $i++) { $output .= $file[$i]."\n"; }

    //remove last \n
    $output = substr($output, 0, strlen($output) - 1);

    file_put_contents("stats.txt", $output);
  }

  //http://davidwalsh.name/facebook-meta-tags
  //https://developers.facebook.com/tools/debug/
  function header_openGraph($date){
    if(strlen($date) <= 0) return;

    $head = getItemHeader($date);
    list($cat,$type) = explode(" ", getItemHeader($date));
    $desc = substr($head, strpos($head, $type) + strlen($type));
    ?>
    <meta property="og:type" content="blog"/>
    <meta property="og:site_name" content="André Berlemont's portfolio"/>
    <meta property="og:url" content="<?php echo getCurrentUrl(); ?>"/>
    <meta property="og:title" content='<?php echo $type.$desc; ?>'/>
    <?php

      //img
      $ogimg = openGraph__getImage($date);
      //echo "og ? (".strlen($ogimg).") ".$ogimg;
      if(strlen($ogimg) > 0){ echo '<meta property="og:image" content="'.$ogimg.'"/>'; }

      //content
      echo '<meta property="og:description" content="'.$type.$desc.'"/>';
  }

  function openGraph__getImage($date){
    global $mediaPath;
    $default = "http://www.andreberlemont.com/portfolio/design/mushroom_300_meh.png";
    $path = "";
    $url = gallery__getFirstImageUrl($date);
    //echo $url;
    if(strlen($url) > 0){
      $path = "http://www.andreberlemont.com/portfolio/".$mediaPath.$date."/";
      if(strlen($path) <= 0)  $path = $default;
    }
    return $path;
  }
  
  function gallery__getFirstImageUrl($date){
    $list = getScreenshots($date);
    if(count($list) > 0){ return $list[0]; }
    return "";
  }
  
  /* get selected date from url */
  function getSelectedItemId(){
    if(isset($_GET["item"])){
      $itemId = $_GET["item"];
    }else{
      $itemId = "";
    }
    return $itemId;
  }
  
  function getCategories(){
    $all = getItemsByCategories();
    $cats = Array();
    foreach($all as $cat=>$val){

      //skip fixed pages
      if(strlen($cat) < 1) continue;

      if(!in_array($cat, $cats)) $cats[] = $cat;
    }
    //echo count($cats);
    return $cats;
  }

  function getItems(){
    if(!is_dir("pages/")) return "";

    $path = "pages/";
    $files = scandir($path);
    $all = array();
    
    for($i = 0; $i < count($files); $i++){
      $f = $files[$i];

      $info = pathinfo($f);
      //echo "<br/><br/>";print_r($info);

      if(is_dir($path.$info["filename"]))  continue;
      
      //skip #
      if(is_int(strpos($f, "#"))) continue;
      
      //print_r($info["extension"]);
      //echo strcmp($info["extension"],"txt");
      if(strcmp($info["extension"],"txt") < 0) continue;

      //echo"<br/><br/> OK";
      
      $header = getItemHeader($info["filename"]);
      $headerInfo = explode(" ", $header);
      
      //cat
      $cat = strtolower($headerInfo[0]);
      $cat = preg_replace('/[\r\n]+/', '\n', $cat);
      
      //article title
      $title = substr($header, strlen($cat)+1, strlen($header));
      $title = str_replace("\r\n", "", $title);
      $title = str_replace("\n", "", $title);
      
      //var_dump(htmlentities($cat));
      //echo "<br />".$cat.",".strcmp($cat, "ggj");
      
      //table correspondance pour old articles
      if($cat == "tools" || $cat == "rails" || $cat == "as3" || $cat == "jsx" || $cat == "php" || $cat == "unity" || $cat == "maxscript") $cat = "code";
      else if($cat == "ggj" || $cat == "jam"){
        $cat = "gamejam";
      }else if($cat == "music" || $cat == "quotes") $cat = "blog";
      
      $new = array("cat"=>$cat,"file"=>$info["filename"],"title"=>$title);
      $all[] = $new;
    }
    return $all;
  }

  /* retourne la liste des articles par categories */
  function getItemsByCategories(){
    $files = getItems();
    for($i = 0; $i < count($files); $i++){
      $file = $files[$i];
      $cat = $file["cat"];
      $new = array("cat"=>$cat,"file"=>$file["file"],"title"=>$file["title"]);
      $all[$cat][] = $new;
    }
    return $all;
  }
  
  function categoryAdd($cats, $newItem){
    $itemCat = $newItem["cat"];
    
    if(!isset($cats[$itemCat])) $cats[$itemCat] = array();
    $cats[$itemCat][] = $newItem;
    
    return $cats;
  }
  
  /* param = date, retourne l'entete titre de l'article */
  function getItemHeader($id){
    $path = "pages/".$id.".txt";
    $title = "";
    
    if(file_exists($path)){
      $h = fopen($path, "r");
      $buffer = fgets($h); // récup le titre
      
      $title = $buffer;
      
      fclose($h);
    }
    
    //ce return créer des problème avec utf-8 sur les accents
    //return htmlEntities(trim($title));
    
    return trim($title);
  }
  
  // http://forums.digitalpoint.com/showthread.php?t=182666
  function str_insert($insertstring, $intostring, $offset) {
     $part1 = substr($intostring, 0, $offset);
     $part2 = substr($intostring, $offset);
    
     $part1 = $part1 . $insertstring;
     $whole = $part1 . $part2;
     return $whole;
  }
  
  function str_remove_between($content, $beginChar, $endChar) {
    $startPos = strpos($content, $beginChar);
    $endPos = strpos($content, $endChar);
    // ...
    return $content;
  }
  
  function str_get_between($content, $beginChar, $endChar) {
    $startPos = strpos($content, $beginChar);
    $endPos = strpos($content, $endChar);
    $title = substr($content, $startPos, $endPos);
    return $title;
  }
  
  /* génère le code html d'un article */
  function displayItem($path, $fileName){
    $path = $path.$fileName.".txt";
    
    if (file_exists($path)) {
      
      //open file
      $h = fopen($path, "r");
      
      $content = "";
      
      $line = fgets($h);
      //echo strlen($line).",".$line;
      
      //remove first word
      $firstSpace = strpos($line, " ");
      $line = substr($line, $firstSpace, strlen($line) - $firstSpace);
      
      if(strlen($line) > 2){ // \n count for 1
        //create title based on first line of the file
        $content .= '<div id="content-title">';
        $content .= $line;
        $content .= "</div>";
      }
      
      $content .= '<div id="content">';
      //get all info
      while ($buffer = fgets($h)){
        $content .= $buffer;
      }
      $content .= '</div>';
      
      //close file
      fclose($h);

      $str = treatTxt($content, $fileName);
      
    }else $str = "I don't have ".$path;

    return $str;
  }
  
  /* Apply htmlEntities to content between $tagName */
  function htmlEntitiesCode($content, $tagName) {
    $tagNameClose = str_insert("/", $tagName, 1);
    
    $index = 0;
    $endIndex = 0;
    
    do {
      $index = strpos($content, $tagName, $index);
      
      if ($index !== false) {
        $index += 6;
        $endIndex = strpos($content, $tagNameClose, $index);
        
        //echo "<br />INDEX = ".$index.", END INDEX = ".$endIndex;
        $part = substr($content, $index, $endIndex - $index);
        
        //modifing
        //$part = htmlspecialchars($part);
        //var_dump($part);
        $part = str_replace("<br/>","\n",$part); // remove <br/> from code
        $part = htmlentities($part);
        //$part = "<pre><code data-language=\"javascript\">".$part."</code></pre>";
        
        $start = substr($content, 0, $index);
        $end = substr($content, $endIndex, strlen($content) - $endIndex);
        $content = $start.$part.$end;
      }
      
    }while ($index !== false);
    
    return $content;
  }
  
  function treatTxt($c, $fileName = ""){
    global $mediaPath;
    $path = $mediaPath;

    $c = str_replace("\n", "<br/>", $c);
    $c = htmlEntitiesCode($c, "[code]");
    
    $tags = array("code","history");
    
    $index = 0;
    foreach($tags as $tag) {
      $tagName = "pipe-".$tag;
      //$c = str_replace("[".$tag."]", '<div class="pipe '.$tagName.'">|'.ucFirst($tag).'</div><pre><code>', $c);
      $c = str_replace("[".$tag."]\n", '<pre><code>', $c);
      $c = str_replace("[/".$tag."]", "</code></pre>", $c);
      $index++;
    }

    //replace []() to <a>
    $reg = "/\[([^\]]+)\]\(([^\)]+)\)/";
    $replacement = '<a href="$2" target="_blank">$1</a>';
    $c = preg_replace($reg,$replacement,$c);

    //replace {{file.ext}} to <img>
    $reg = "/\{\{([^}]+)\}\}/";
    $replacement = '<img src="'.$path.'/$1" />';
    $c = preg_replace($reg, $replacement, $c);
    
    $reg = "/\{\[([^]]+)\]\}/";
    $replacement = '<video width="640" height="360" controls><source src="'.$path.'/$1" type="video/mp4">Your browser does not support the video tag.</video>';
    $c = preg_replace($reg, $replacement, $c);
    
    /*
    $idx = 0;
    $start = 0;
    $end = 0;
    for ($i=0; $i < 3; $i++) { 
      $start = $end = $idx = strpos($c,"](",$idx);
      if($idx){
        while($c[$start] != "[") $start--;
        $label = substr($c, $start, $idx - $start);
        while($c[$end] != ")") $end++;
        $url = substr($c, $idx+2, $end - $start);
        var_dump($label." ".$url);
      }
    }
    */

    //create subtitles (with  |)
    $lines = explode("<br/>",$c);
    $c = "";
    foreach($lines as $line){
      if(strpos($line,"|") === 0){
        $line = substr($line, 1, strlen($line));
        $line = '<div class="category-title"><span class="pipe">|</span> <span class="article-subtitle">'.$line.'</span></div>';
        $c .= $line;
      }else{
        $c .= $line."<br/>";
      }
    }

    return $c;
  }
  
?>







<?php

  function getSizeString($url, $limitX = 200, $limitY = 150){
    $dim = getimagesize($url);
    $width = $dim[0];
    $height = $dim[1];
    
    $ratioX = ($limitX / $width);
    $widthX = $width * $ratioX;
    $heightX = $height * $ratioX;
    
    if($widthX <= $limitX && $heightX <= $limitY){
      return "width=".intval($widthX)." height=".intval($heightX);
    }
    
    $ratioY = ($limitY / $height);
    $widthY = $width * $ratioY;
    $heightY = $height * $ratioY;
    
    if($widthY <= $limitX && $heightY <= $limitY){
      return "width=".intval($widthY)." height=".intval($heightY);
    }
    
    return "";
  }
  
  
  function generateList($path){
    $ext = array("png", "jpg");
    $list = array();
    
    //stop si c'est pas un dossier
    if (!is_dir($path)) return array();
    
    $files = scandir($path);
    
    foreach($files as $file) {
      if(is_dir($file)) continue;
      $info = pathinfo($file);
      if(!isset($info["extension"]))  continue;
      
      //take only pics
      $filepath = strtolower($file);
      
      //png / jpg ?
      if(in_array($info["extension"], $ext)){
        $list[] = $filepath;
        //echo $file."<br />";
      }
    }
    
    return $list;
  }
  
  function getScreenshots($id) {
    //create thumbs
    global $mediaPath;
    $path = $mediaPath.$id."/";
    $thumbs = $mediaPath.$id."/thumbs/";

    //echo $path." , ".$thumbs;
    createThumbs($path,$thumbs, 200);
    
    $list = generateList($mediaPath.$id);
    
    $realList = $list;
    
    return $realList;
  }
  
  function createThumbs( $pathToImages, $pathToThumbs, $thumbWidth ) 
  {
    //si pas de dossier avec des images ...
    if (!is_dir($pathToImages)) return;
    
    //créer le thumbs/
    if(!is_dir($pathToThumbs))  mkdir($pathToThumbs);
    
    $files = scandir($pathToImages);
    foreach($files as $file){
      if(is_dir($file)) continue;
      
      // parse path for the extension
      $info = pathinfo($file);
      //$fname = $info["filename"].".".$info["extension"];
      $fname = $info["basename"];
      $filePath = $pathToImages.$fname;
      
      //dossier ? pas d'extension ? ... zap
      if(!isset($info["extension"]))  continue;
      
      $exts = array("jpg", "png");
      foreach($exts as $ext) {
        
        if ( strtolower($info['extension']) == $ext ) 
        {
          //print_r($info);

          $min = $pathToThumbs."m_".$fname;
          //echo "<br/>\nmin ? ".$min;
          if(file_exists($min)) continue;

          // load image and get image size
          if(strpos($ext, "png") !== false){

            try{
              $img = imagecreatefrompng($filePath);
            }catch(Exception $e){
              die("PNG Image creation error");
            }
            
          }else{

            try{
              $img = imagecreatefromjpeg($filePath);
            }catch(Exception $e){
              die("JPG Image creation error");
            }
            
          }
          
          $width = imagesx( $img );
          $height = imagesy( $img );
          
          // calculate thumbnail size
          $new_width = $thumbWidth;
          $new_height = floor( $height * ( $thumbWidth / $width ) );
          
          // create a new temporary image
          $tmp_img = imagecreatetruecolor( $new_width, $new_height );
          
          // copy and resize old image into new image 
          imagecopyresized( $tmp_img, $img, 0, 0, 0, 0, $new_width, $new_height, $width, $height );

          // save thumbnail into a file
          imagejpeg( $tmp_img, "{$pathToThumbs}m_{$fname}");
          imagedestroy($tmp_img);
        }
        
      }
      
    }
  }
?>

<?php
  /*
    André BERLEMONT
    2013-12-21
  */
  
  function getCurrentRewriteUrl(){
    $data = explode("/", $_SERVER['PHP_SELF']);
    $url = "";
    for($i=0;$i<count($data)-1;$i++){
      if($i==0) $url = $data[$i];
      else  $url .= "/".$data[$i];
    }
    return "http://".$_SERVER['HTTP_HOST'].$url;
  }
  
  function getCurrentUrl() {
    $pageURL = 'http';
    
    //if ($_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
    $pageURL .= "://";
    
    if ($_SERVER["SERVER_PORT"] != "80") {
      $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
    } else {
      $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
    }
    return $pageURL;
  }
  
  function encryptPwd($str){
    return md5($str);
  }

  function redirect($url, $time = 500){
    //header("location:".$url);
    
    echo "<script type='text/javascript'>";
      echo "window.setTimeout(\"window.location='".$url."'\",".$time.");";
    echo "</script>";
    
  }
  
  function manageData($data){
    $out = htmlspecialchars($data);
    return $out;
  }
  


  // ======= DATE


  //GMT +2
  function getDbDate($h = 0){
    return Date("Y-m-d H:i:s", mktime(Date("H") + $h, Date("i"), Date("s"), Date("m"), Date("d"), Date("Y"))); 
  }
  
  function getOnlyDate($datetime, $format = true){
    $str = explode(" ", $datetime);
    if($format){
      $str = explode("-",$str[0]);
      $str = $str[2]."/".$str[1]."/".$str[0];
    }else{
      $str = $str[0];
    }
    return $str;
  }
  
  function formatToDate($dbDate){
    $temp = explode(" ", $dbDate);
    $date = $temp[0];
    list($y,$m,$d) = explode("-", $date);
    return $d."/".$m."/".$y;
  }

  function formatToDateTime($dbDate){
    list($date,$time) = explode(" ", $dbDate);
    list($y,$m,$d) = explode("-", $date);
    list($h,$min,$s) = explode(":", $time);
    return $d."/".$m."/".$y." à ".$h."h".$min;
  }

?>