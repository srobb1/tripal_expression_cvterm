<?php

function partition( $list, $p ) {
    $listlen = count( $list );
    $partlen = floor( $listlen / $p );
    $partrem = $listlen % $p;
    $partition = array();
    $mark = 0;
    for ($px = 0; $px < $p; $px++) {
        $incr = ($px < $partrem) ? $partlen + 1 : $partlen;
        $partition[$px] = array_slice( $list, $mark, $incr );
        $mark += $incr;
    }
    return $partition;
}


drupal_add_library('system', 'drupal.collapse');

dpm($node, 'node');
$cvterm_name = $node->expression_cvterm_organism->cvterm_id->name;
$cvterm_id = $node->expression_cvterm_organism->cvterm_id->cvterm_id;
$organism_id = $node->expression_cvterm_organism->organism_id->organism_id;
$sql = "SELECT ec.expression_id ,p.title , pp1.value as author , p.uniquename as pub_uniquename, i.image_uri, f.uniquename, f.name from {expression_cvterm} ec, {feature_expression} fe, {feature} f , {expression_image} ei , {eimage} i , {expression_pub} ep, {pub} p , {pubprop} pp1, {cvterm} cvt1  where p.pub_id = pp1.pub_id and pp1.type_id = cvt1.cvterm_id and cvt1.name = 'Author' and  ec.expression_id = ep.expression_id and ep.pub_id = p.pub_id and i.eimage_id = ei.eimage_id and ei.expression_id = ec.expression_id and ec.expression_id = fe.expression_id and fe.feature_id = f.feature_id and f.organism_id = :organism_id and ec.cvterm_id = :cvterm_id";
$args = array( ':cvterm_id' => $cvterm_id , ':organism_id' => $organism_id );
$result = chado_query( $sql, $args );

$all_images = array();
$featues = array();
$authors = array();
$experiments = array();
$image_terms=array();
$image_legends=array();
$image_features=array();
$feature_images=array();
$uris=array();

foreach ($result as $r) {
  //$features[]=array($r->uniquename => $r->name); 
  $features[$r->uniquename]= $r->name; 
  //$image_features[$r->image_uri][] = array($r->uniquename => $r->name);
  $image_features[$r->image_uri][$r->uniquename][] = $r->name;
  $feature_images[$r->uniquename][] = $r->image_uri;
  $all_images[]=$r->image_uri;
  $authors[$r->author][]=$r->image_uri;
  $experiments[$r->title][]=$r->image_uri;
  $uris[$r->image_uri]['authors'][$r->title]=$r->author;
  $uris[$r->image_uri]['uniquenames'][]=$r->uniquename;
  $uris[$r->image_uri]['experiments'][$r->author]=$r->title;

  $sql_image_terms = "SELECT cvt.name ,cvt.cvterm_id from {eimage} i , {expression_image} ei , {expression_cvterm} ec , {cvterm} cvt  where ei.eimage_id = i.eimage_id and ei.expression_id = ec.expression_id and ec.cvterm_id = cvt.cvterm_id and i.image_uri = :image_uri";
  $args = array(':image_uri' => $r->image_uri);
  $result_image_terms = chado_query($sql_image_terms,$args);
  foreach ($result_image_terms as $r_i_t){
   $image_terms[$r->image_uri][] = array($r_i_t->name => $r_i_t->cvterm_id);
  }


  $sql_image_legend = "SELECT description from {expression} where expression_id = :expression_id";
  $args = array(':expression_id' => $r->expression_id);
  $results_image_legends = chado_query($sql_image_legend,$args);
  foreach ($results_image_legends as $r_i_l){
    $image_legends[$r->image_uri]=$r_i_l->description;
  }


 }
$all_images=array_unique($all_images);
// page title be genus species term
// list the images with this term
// list all genes that reference this term
// subdivide by author
// subdivide by experiment
// list of experimetns that reference this term

$image_dir = '/pub/analysis/wish/single/';
$count = count($all_images);
$gene_count = count($feature_images);
//print "<h2>$cvterm_name </h2>";
print ' <a class="waves-effect waves-light btn" onClick="expandAll();">Expand All</a> 
  <a class="waves-effect waves-light btn" onClick="collapseAll();">Collapse All</a>
<br /><br />';
print "
<p><a name=\"top\"></a></p>
<h1>All images that have been tagged with the term <strong>$cvterm_name</strong> are displayed below.</h1>
<br><hr>
<h2>&#9758; <a href=\"#all\">All Images</a></h2>
<h2>&#9758; <a href=\"#gene\">Images sorted by Gene</a></h2>
<h2>&#9758; <a href=\"#experiment\">Images sorted by Experiment</a></h2>
<h2>&#9758; <a href=\"#experimenter\">Images sorted by Experimenter</a></h2>
";



print "<a name=\"all\"></a>";
print "<br><hr><hr><br>";
print "<h2> All Images($count)</h2>";
print '
  <p>Click on the buttons to change the grid view.</p>
  <button class="btn" onclick="one(\'all_column\')">1</button>
  <button class="btn active" onclick="two(\'all_column\')">2</button>
  <button class="btn" onclick="four(\'all_column\')">4</button>
<br><br>
';
$images= array();
foreach ($all_images as $image){
  $caption = '';
  $features_info = array();
  foreach ($uris[$image]['uniquenames'] as $unique){
    $exp_info = array();
    foreach ($uris[$image]['experiments'] as $author => $title){
      $exp_info[] = "$title by $author";
    }
    $features_info[] = "$features[$unique] ( $unique ) from " . join(", ", $exp_info);
  }
  $caption = "<em>" . join(", ",$features_info) . "</em>";
  if(array_key_exists($image,$image_legends)){
    $caption .=  "<br>$image_legends[$image]";
  }
  $collapse = theme(
      'ctools_collapsible',
    array(
      'handle' => "Figure Info",
      'content' => $caption,
      'collapsed' => TRUE
    )
  );
  if (preg_match('/(png|jpeg|jpg)$/', $image)){
    $images[] = "<div class=\"caption_container\"><a href=\"$image_dir/$image\"><img style=\"width:100%\" class=\"caption_image\" src=\"$image_dir/$image\" ></a><div class=\"caption_middle\"><div class=\"caption_text\">$caption</div></div></div>";
   //$images[] = "<a href=\"$image_dir/$image\"><img  style=\"width:100%\" src=\"$image_dir/$image\"></a>";
 //   $images[] = "<figure><a href=\"$image_dir/$image\"><img src=\"$image_dir/$image\"></a><figcaption>$collapse</figcaption></figure>";
  }else{
    $videos[] = "<video width=\"550\" controls><source src=\"$image_dir/$image\">Your browser doesn\'t support HTML5 video in MP4 with H.264. </video>";
//    $images[] = "<figure><video width=\"550\" controls><source src=\"$image_dir/$image\">Your browser doesn\'t support HTML5 video in MP4 with H.264. </video><figcaption>$collapse</figcaption></figure>";
  }

}


print '


<script>

// Get the elements with class="column"


// Declare a "loop" variable
var i;

// Full-width images
function one(type) {
    var elements = document.getElementsByClassName(type);
    for (i = 0; i < elements.length; i++) {
        elements[i].style.flex = "100%";
    }
}

// Two images side by side
function two(type) {
    var elements = document.getElementsByClassName(type);
    for (i = 0; i < elements.length; i++) {
        elements[i].style.flex = "40%";
    }
}

// Four images side by side
function four(type) {
    var elements = document.getElementsByClassName(type);
    for (i = 0; i < elements.length; i++) {
        elements[i].style.flex = "20%";
    }
}
var btns = document.getElementsByClassName("btn");
for (var i = 0; i < btns.length; i++) {
  btns[i].addEventListener("click", function() {
  var current = document.getElementsByClassName("active");
  current[0].className = current[0].className.replace(" active", "");
  this.className += " active";
});
 }
</script>
';

$columns = partition($images,4);
dpm($columns,'columns');
print '<div class="row"> ';
print '  <div class="all_column">';
print join("\n",$columns[0]);
print '  </div>';

print '  <div class="all_column">';
print join("\n",$columns[1]);
print '  </div>';

print '  <div class="all_column">';
print join("\n",$columns[2]);
print '  </div>';

print '  <div class="all_column">';
print join("\n",$columns[3]);
print '  </div>';
print '  </div>';




/*
print('
<fieldset class=" collapsible collapsed">
  <legend><span class="fieldset-legend">'. "All Images tagged with $cvterm_name ($count)" . '</span></legend>
  <div class="fieldset-wrapper">
    <h3>Content goes here</h3>
    <p><figure>' .
       join("\n",$images)
      .
    '</figure></p>
  </div>
</fieldset>');
*/

print "<p><a href=\"#top\">back to top</a></p><br>";


print "<a name=\"gene\"></a>";
print "<h2> Images($count) By Gene($gene_count)</h2>";
ksort($feature_images);
foreach ($feature_images as $feature => $feature_image_array){
$name = $features[$feature];
$images=array();
$videos=array();
foreach ($feature_image_array as $image){
 $features_info = array();
  foreach ($uris[$image]['experiments'] as $author => $title){
      $features_info[] = "$title by $author";
  }
  $caption =  "<em>" . join(", ",$features_info) . "</em>";
  if(array_key_exists($image,$image_legends)){
    $caption .=  "<br>$image_legends[$image]";
  }
  $collapse = theme(
      'ctools_collapsible',
    array(
      'handle' => "Figure Info",
      'content' => $caption,
      'collapsed' => TRUE
    )
  );
  if (preg_match('/(png|jpeg|jpg)$/', $image)){
    $images[] = "<figure><a href=\"$image_dir/$image\"><img src=\"$image_dir/$image\"></a><figcaption>$collapse</figcaption></figure>";
  }else{
    $images[] = "<figure><video width=\"550\" controls><source src=\"$image_dir/$image\">Your browser doesn\'t support HTML5 video in MP4 with H.264. </video><figcaption>$collapse</figcaption></figure>";
  }
}
$each_image_count = count($images);
print('
<fieldset class=" collapsible collapsed">
  <legend><span class="fieldset-legend">' . $name . " [" . $feature . "] ($each_image_count)" . '</span></legend>
  <div class="fieldset-wrapper">
    <h3>Content goes here</h3>
    <p><figure>' .
       join("\n",$images)
      .
    '</figure></p>
  </div>
</fieldset>');
}
print "<p><a href=\"#top\">back to top</a></p><br>";




$experiment_count = count($experiments);
print "<a name=\"experiment\"></a>"; 
print "<h2> Images($count) By Experiment($experiment_count)</h2>";
foreach ($experiments as $exp => $image_array){
//$name = $features[$exp];
$images=array();
foreach ($image_array as $image){
  $features_info = array();
  foreach ($uris[$image]['uniquenames'] as $unique){
    $features_info[] = "$features[$unique] ( $unique )";
  }
  $caption =  "<em>" . join(", ",$features_info) . "</em>";
  if(array_key_exists($image,$image_legends)){
    $caption .=  "<br>$image_legends[$image]";
  }
  $collapse = theme(
      'ctools_collapsible',
    array(
      'handle' => "Figure Info",
      'content' => $caption,
      'collapsed' => TRUE
    )
  );
  if (preg_match('/(png|jpeg|jpg)$/', $image)){
    $images[] = "<figure><a href=\"$image_dir/$image\"><img src=\"$image_dir/$image\"></a><figcaption>$collapse</figcaption></figure>";
  }else{
    $images[] = "<figure><video width=\"550\" controls><source src=\"$image_dir/$image\">Your browser doesn\'t support HTML5 video in MP4 with H.264. </video><figcaption>$collapse</figcaption></figure>";
  }
}
$each_image_count = count($images);
print('
<fieldset class=" collapsible collapsed">
  <legend><span class="fieldset-legend">' . "$exp ( $each_image_count )" . '</span></legend>
  <div class="fieldset-wrapper">
    <h3>Content goes here</h3>
    <p><figure>' .
       join("\n",$images)
      .
    '</figure></p>
  </div>
</fieldset>');
}
print "<p><a href=\"#top\">back to top</a></p><br>";

$author_count = count($authors);
print "<a name=\"experimenter\"></a>"; 
print "<h2> Images($count) By Experimenter($author_count)</h2>";
foreach ($authors as $author => $image_array){
//$name = $features[$exp];
$images=array();
foreach ($image_array as $image){
  $features_info = array();
  foreach ($uris[$image]['uniquenames'] as $unique){
    $features_info[] = "$features[$unique] ( $unique )";
  }
  $caption =  "<em>" . join(", ",$features_info) . "</em>";
  if(array_key_exists($image,$image_legends)){
    $caption .=  "<br>$image_legends[$image]";
  }
  $collapse = theme(
      'ctools_collapsible',
    array(
      'handle' => "Figure Info", 
      'content' => $caption,
      'collapsed' => TRUE
    )
  );
  $images[] = "<figure><a href=\"$image_dir/$image\"><img src=\"$image_dir/$image\"></a><figcaption>$collapse</figcaption></figure>";
}
$each_image_count = count($images);
print('
<fieldset class=" collapsible collapsed">
  <legend><span class="fieldset-legend">' . "$author ( $each_image_count )" . '</span></legend>
  <div class="fieldset-wrapper">
    <h3>Content goes here</h3>
    <p><figure>' .
       join("\n",$images)
      .
    '</figure></p>
  </div>
</fieldset>');
}
print "<p><a href=\"#top\">back to top</a></p><br>";
?>
