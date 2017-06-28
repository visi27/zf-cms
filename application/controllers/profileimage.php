<?php

// $receiptsObj = new Table_Receipts();
// $receipts = $receiptsObj->selectData();
// $root_path = "files/receipts/";
// foreach($receipts as $receipt){
// $search_dir = $root_path.$receipt->id;
// $images = glob("$search_dir/*.jpg");
// sort($images);

// // Image selection and display:
// //display first image
// if (count($images) > 0) { // make sure at least one image exists
// $img = $images[0]; // first image
// echo "<img src='$img' height='150' width='150' /><br> ";
// $receiptsObj->assignProfileImage($receipt->id, basename($img));
// } else {
// $search_dir = $root_path.$receipt->id;
// $images = glob("$search_dir/*.png");
// sort($images);
// if (count($images) > 0) { // make sure at least one image exists
// $img = $images[0]; // first image
// echo "<img src='$img' height='150' width='150' /><br> ";
// $receiptsObj->assignProfileImage($receipt->id, basename($img));
// } else {
// // possibly display a placeholder image?
// echo "NO IMAGE<br>";
// }
// }
// print_r($search_dir.'<br>');
// }
$articlesObj = new Table_BlogArticles();
$articles = $articlesObj->selectData();
$root_path = "files/articles/";
foreach ($articles as $article) {
    if (empty($article->profile_image)) {
        $search_dir = $root_path . $article->id;
        $images = glob("$search_dir/*.jpg");
        sort($images);
        
        // Image selection and display:
        // display first image
        if (count($images) > 0) { // make sure at least one image exists
            $img = $images[0]; // first image
            echo "<img src='$img' height='150' width='150' /><br> ";
            $articlesObj->assignProfileImage($article->id, basename($img));
        } else {
            $search_dir = $root_path . $article->id;
            $images = glob("$search_dir/*.png");
            sort($images);
            if (count($images) > 0) { // make sure at least one image exists
                $img = $images[0]; // first image
                echo "<img src='$img' height='150' width='150' /><br> ";
                $articlesObj->assignProfileImage($article->id, basename($img));
            } else {
                // possibly display a placeholder image?
                echo "NO IMAGE<br>";
            }
        }
    } else {
        echo "Profile Image Already Set";
    }
    print_r($search_dir . '<br>');
}

?>