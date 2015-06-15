<?php
  include("builder-api.php");

  $configData = file_get_contents("config.txt");
  $config = json_decode($configData);
  //print_r($config);
  //echo "<hr/>";
  //print_r($config->{"header"});

  $outputFile = "index.html";
  $output = "";




  // PAGE
  $output = '<!DOCTYPE html><html lang="en">'.PHP_EOL;

  //HEADER
  $configHeader = $config->{"header"};
  $output .= '<head>'.PHP_EOL;
  $output .= '<title>'.$configHeader->{"title"}.'</title>'.PHP_EOL;
  
  $output .= PHP_EOL;
  $output .= '<meta charset="'.$configHeader->{"charset"}.'" />'.PHP_EOL;
  $output .= '<meta name="Description" content="'.$configHeader->{"description"}.'" />'.PHP_EOL;

    //CSS
  $css = $configHeader->{"css"};
  $output .= PHP_EOL;
  foreach($css as $line){ $output .= '<link rel="stylesheet" type="text/css" href="'.$line.'">'.PHP_EOL; }
    
    //JS
  $js = $configHeader->{"js"};
  $output .= PHP_EOL;
  foreach($js as $line){ $output .= '<script type="text/javascript" src="'.$line.'"></script>'.PHP_EOL; }

    //GOOGLE
  $output .= html_googleAnalytics($configHeader->{"googleAnalytics"}).PHP_EOL;

  $output .= '</head>'.PHP_EOL;





  //BODY
  $output .= PHP_EOL;
  $output .= '<body>'.PHP_EOL;

  $output .= PHP_EOL.PHP_EOL;

  $output .= '<div id="all">';
    //$configCats = $config->{"categories"}; 
    $categories = getCategories();
    //print_r($categories);

    $articles = getItemsByCategories();
    //echo count($articles);

      //CATEGORIES
    $output .= '<div id="categories">'.PHP_EOL.PHP_EOL;
    //echo count($categories);

    foreach($categories as $cat){
      $color = floor(rand(1,5));
      $output .= '<a href="#" class="category" id="'.$cat.'" rel="'.$color.'">'.ucfirst($cat).'</a>'.PHP_EOL;
      $output .= generateCatList($articles, $cat).PHP_EOL;
    }

      //FIXED PAGE
    $fixed = $config->{"fixed"};
    foreach($fixed as $page){
      $color = floor(rand(1,5));
      $output .= '<a href="page-'.$page.'" class="category" id="'.$page.'" rel="'.$color.'">'.ucfirst($page).'</a>'.PHP_EOL;
    }

    $output .= '</div>';

  $output .= '</div>';


    //OVERLAY
  $output .= PHP_EOL.PHP_EOL;
  $output .= '<div id="overlay-click"></div>';
  $output .= '<div id="overlay-bg"></div>';
  $output .= '<div id="overlay" rel=""></div>';

  $output .= PHP_EOL.PHP_EOL;
  $output .= '</body>'.PHP_EOL;

  $output .= '</html>';


  //WRITE OUPUT
  $file = fopen($outputFile, "w+");

  fwrite($file, $output);
  fclose($file);

  function html_googleAnalytics($id){
    $str = "";
    $str .= '<script type="text/javascript">';
    $str .= 'var _gaq = _gaq || []; _gaq.push(["_setAccount", "'.$id.'"]); _gaq.push(["_trackPageview"]);';
    $str .= '(function() {'.PHP_EOL;
    $str .= 'var ga = document.createElement("script"); ga.type = "text/javascript"; ga.async = true;'.PHP_EOL;
    $str .= 'ga.src = ("https:" == document.location.protocol ? "https://ssl" : "http://www") + ".google-analytics.com/ga.js";'.PHP_EOL;
    $str .= 'var s = document.getElementsByTagName("script")[0]; s.parentNode.insertBefore(ga, s);'.PHP_EOL;
    $str .= '})();';
    $str .= '</script>';
    return $str;
  }

  $path = "pages/";

  $items = getItems();
  foreach($items as $article){
    $output = "";
    //print_r($article);
    $date = $article["file"];
    $output .= htmlArticle($date);

    echo $output;

    $file = fopen($path.$date.".html", "w+");
    fwrite($file,$output);
    //echo $output;

    fclose($file);
  }

?>