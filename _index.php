<!DOCTYPE html>
<html lang="en">
  <head>
    <title>André Berlemont</title>
    <meta name="Description" content="Website/Portfolio/Blog of André Berlemont" />
    <meta charset="UTF-8" />
    <link rel="icon" type="image/png" href="design/favicon.png" />
    
    <link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css'>
    <link href='http://fonts.googleapis.com/css?family=Oswald:400,700' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" type="text/css" href="css-index.css">
    
    <script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js" type="text/javascript"></script>
    
    <script src="js-index.js" type="text/javascript"></script>
    
    <script type="text/javascript">
      //GOOGLE ANALYTICS
      var _gaq = _gaq || [];
      _gaq.push(['_setAccount', 'UA-35938177-1']);
      _gaq.push(['_trackPageview']);
      
      (function() {
        var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
        ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
        var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
      })();
    </script>
    
    <?php

      include("builder-api.php");
      
      //récup la date de l'article dans l'url
      $item = "";
      if(isset($_GET)){
        if(isset($_GET["item"]))  $item = htmlentities($_GET["item"]);
        $item = trim($item);
      }

      //var_dump($item);
      $itemDate = $item;
      if(strpos($item, "-")){ $itemDate = $item; }
      //var_dump($itemDate);
      header_openGraph($itemDate);
    ?>

  </head>
  <body>
    <div id="all">
      <?php
        //all acrticle in category[]
        $all = getItemsByCategories();
      ?>
      
      <div id="categories">
        <a href="#" class="category" id="code" rel="2">Code</a>
        <?php generateCatList($all,"code"); ?>
        <a href="#" class="category" id="olr" rel="1">One <span style="color:#e74f15;">Life</span> Remains</a>
        <?php generateCatList($all,"olr"); ?>
        <a href="#" class="category" id="gamejam" rel="3">Game jam</a>
        <?php generateCatList($all,"gamejam"); ?>
        <a href="#" class="category" id="littlethings" rel="4">Little things</a>
        <?php generateCatList($all,"littlethings"); ?>
        <a href="#" class="category" id="epicbros" rel="5">Epic bros</a>
        <?php generateCatList($all,"epicbros"); ?>
        
        <a href="#" class="category" id="notes" rel="1">Notes</a>
        <?php generateCatList($all,"notes"); ?>

        <a href="#" class="category" id="misc" rel="1">Miscellaneous</a>
        <?php generateCatList($all,"misc"); ?>
        <a href="#" class="category" id="random" rel="5">Random projects</a>
        <?php generateCatList($all,"random"); ?>
        <a href="about" class="category" id="about" rel="3">About Me</a>
      </div>
      
      
      <div id="overlay-click"></div>
      <div id="overlay-bg"></div>
      <div id="overlay" rel="<?php echo $itemDate; ?>"><?php include("article.php"); ?></div>
    </div>
  </body>
</html>