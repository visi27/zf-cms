<?php
$sent_recipe_id = $_REQUEST["r"];

$sentObj = new Table_RecipesFromWeb();
$recipe_data = $sentObj->previewRecipeData($sent_recipe_id);

if($recipe_data["provider"] == "facebook"){
    $profile_link = "https://www.facebook.com/".$recipe_data["uid"];
}elseif ($recipe_data["provider"] == "twitter"){
    $profile_link = "https://twitter.com/intent/user?user_id=".$recipe_data["uid"];
}else{
    $profile_link = "javascript:;";
}
//print_r($recipe_data);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<title>Shiko Receten</title>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
</head>
<body>

    <div class="container">
        <div class="col-sm-12 col-md-12 col-lg-12 h2">Receta: <?php echo $recipe_data["title"]?></div>
        <div class="col-sm-12 col-md-12 col-lg-12 h3">Autori: <a target="_new" href="<?php echo $profile_link?>"><?php echo $recipe_data["author"]?></a> (<?php echo $recipe_data["email"]?>)</div>
        <div class="col-sm-12 col-md-12 col-lg-12 h3">Pershkrimi: <?php echo $recipe_data["desc"]?></div>
        
        <div class="col-sm-4 col-md-4 col-lg-4 h4">Porcione: <?php echo $recipe_data["servings"]?></div>
        <div class="col-sm-4 col-md-4 col-lg-4 h4">Koha e Pregatitjes: <?php echo $recipe_data["total_time"]?></div>
        <div class="col-sm-4 col-md-4 col-lg-4 h4">Veshtiresia: <?php echo $recipe_data["difficulty"]?></div>
         
        <div class="clearfix"></div>
        <div class="col-sm-6 col-md-6 col-lg-6 h3"><?php echo $recipe_data["ingredients"]?></div>
        <div class="col-sm-6 col-md-6 col-lg-6 h3"><?php echo $recipe_data["steps"]?></div>
        
        <div class="clearfix"></div>
        <div class="col-sm-3 col-md-3 col-lg-3 h4">Kategoria: <?php echo $recipe_data["category"]?></div>
        <div class="col-sm-3 col-md-3 col-lg-3 h4">Produkti Baze: <?php echo $recipe_data["base_product"]?></div>
        <div class="col-sm-3 col-md-3 col-lg-3 h4">Lloji i Kuzhines: <?php echo $recipe_data["cuisine"]?></div>
        <div class="col-sm-3 col-md-3 col-lg-3 h4">Lloji i Recetes; <?php echo $recipe_data["receipt_type"]?></div>
        
        <div class="clearfix"></div>
        <div class="col-sm-3 col-md-3 col-lg-3 h4">Sezonaliteti: <?php echo $recipe_data["seasonality"]?></div>
        <div class="col-sm-3 col-md-3 col-lg-3 h4">Vakti: <?php echo $recipe_data["meal"]?></div>
        <div class="col-sm-3 col-md-3 col-lg-3 h4">Festiviteti: <?php echo $recipe_data["festivity"]?></div>
        <div class="clearfix"></div>
        <?php 
        $upload_dir = '/var/www/shije.al/trunk/public/web_root/uploads/';
        
        if (file_exists($upload_dir.$sent_recipe_id.'/')and $handle = opendir($upload_dir.$sent_recipe_id.'/')) {
        
            while (false !== ($entry = readdir($handle))) {
                $files[] = $entry;
            }
        
            foreach($files as $image)
            {
                if($image !== '.' && $image !== '..'){
                    echo '<div class="col-sm-3 col-md-3 col-lg-3">
                          <a target="_new" href="http://www.shije.al/uploads/'.$sent_recipe_id.'/'.$image.'">
                            <img style="max-width:100%;" src="http://www.shije.al/uploads/'.$sent_recipe_id.'/'.$image.'">
                          </a>
                          </div>';
                }
               
            }
            closedir($handle);
        }
        ?>
    </div>

</body>
</html>