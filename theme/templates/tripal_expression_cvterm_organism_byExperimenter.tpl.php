<?php


drupal_add_library('system', 'drupal.collapse');

$cvterm_name = $node->expression_cvterm_organism->cvterm_id->name;
$cvterm_id = $node->expression_cvterm_organism->cvterm_id->cvterm_id;
$organism_id = $node->expression_cvterm_organism->organism_id->organism_id;
$sql = "SELECT ec.expression_id ,p.title , pp1.value as author , p.uniquename as pub_uniquename, i.image_uri, f.uniquename, f.name from {expression_cvterm} ec, {feature_expression} fe, {feature} f , {expression_image} ei , {eimage} i , {expression_pub} ep, {pub} p , {pubprop} pp1, {cvterm} cvt1  where p.pub_id = pp1.pub_id and pp1.type_id = cvt1.cvterm_id and cvt1.name = 'Author' and  ec.expression_id = ep.expression_id and ep.pub_id = p.pub_id and i.eimage_id = ei.eimage_id and ei.expression_id = ec.expression_id and ec.expression_id = fe.expression_id and fe.feature_id = f.feature_id and f.organism_id = :organism_id and ec.cvterm_id = :cvterm_id and i.image_uri NOT LIKE '%Panel%' and i.image_uri NOT LIKE '%m4v' and i.image_uri NOT LIKE '%mv4'";
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

$image_dir = '/pub/analysis/wish/image/';
$count = count($all_images);

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
$author_count = count($authors);
print ' <a class="waves-effect waves-light btn" onClick="expandAll();">Expand All</a>
  <a class="waves-effect waves-light btn" onClick="collapseAll();">Collapse All</a>
<br /><br />';
print "
<p><a name=\"top\"></a></p>
<h1>All images that have been tagged with the term <strong>$cvterm_name</strong> are subdivided by Experimenter and are displayed below.</h1>
<br><hr>
";
print "<h2>Images($count) By Experimenter($author_count)</h2>";
print '
  <p>Click on the buttons to change the grid view.</p>
  <button class="btn" onclick="one(\'experimenter_column\')">1</button>
  <button class="btn active" onclick="two(\'experimenter_column\')">2</button>
  <button class="btn" onclick="four(\'experimenter_column\')">4</button>
<br><br>
';
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
   if (preg_match('/(png|jpeg|jpg)$/', $image)){
    $images[] = "<div class=\"caption_container\"><a href=\"$image_dir/$image\"><img style=\"width:100%\" class=\"caption_image\" src=\"$image_dir/$image\" ></a><div class=\"caption_middle\"><div class=\"caption_text\">$caption</div></div></div>";
  }else{
    $videos[] = "<video width=\"550\" controls><source src=\"$image_dir/$image\">Your browser doesn\'t support HTML5 video in MP4 with H.264. </video>";
  }
}
$each_image_count = count($images);
$columns = partition($images,4);
print'
<fieldset class=" collapsible collapsed">
  <legend><span class="fieldset-legend">' . "$author ( $each_image_count )" . '</span></legend>
  <div class="fieldset-wrapper">';

print '<div class="row"> ';
print '  <div class="experimenter_column">';
print join("\n",$columns[0]);
print '  </div>';

print '  <div class="experimenter_column">';
print join("\n",$columns[1]);
print '  </div>';

print '  <div class="experimenter_column">';
print join("\n",$columns[2]);
print '  </div>';

print '  <div class="experimenter_column">';
print join("\n",$columns[3]);
print '  </div>';
print '  </div>';

print '  </div>
</fieldset>';
}
print "<p><a href=\"#top\">back to top</a></p><br>";
?>
