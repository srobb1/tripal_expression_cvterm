<?php
dpm($results,'results');

drupal_add_library('system', 'drupal.collapse');
$image_dir = '/pub/analysis/wish/image';
$image_count = count($results['images']);
$gene_count = count($results['genes']);
$exp_title = $results['pub']['title'];

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

function tripal_expression_cvterm_organism_getExperiment_overview($results){
	$rows = array();
	$headers = array();
	$rows[] = array(
	  array(
		'data' => 'Experiment Name',
		'header' => TRUE
	  ),
	  $results['pub']['title']
	);
	$rows[] = array(
	  array(
		'data' => 'Experimenter',
		'header' => TRUE
	  ),
	  $results['pub']['author']
	);
	$rows[] = array(
	  array(
		'data' => 'Series Title',
		'header' => TRUE
	  ),
	  $results['pub']['volumetitle']
	);
	$rows[] = array(
	  array(
		'data' => 'Volume',
		'header' => TRUE
	  ),
	  $results['pub']['volume']
	);
	$rows[] = array(
	  array(
		'data' => 'Year',
		'header' => TRUE
	  ),
	  $results['pub']['year']
	);
	$rows[] = array(
	  array(
		'data' => 'Pages',
		'header' => TRUE
	  ),
	  $results['pub']['pages']
	);
	$table = array(
	  'header' => $headers,
	  'rows' => $rows,
	  'attributes' => array(
		'id' => 'tripal_expression_cvterm_organism-table-base',
		'class' => 'tripal-data-table'
	  ),
	  'sticky' => FALSE,
	  'caption' => '',
	  'colgroups' => array(),
	  'empty' => '',
	);
	// once we have our table array structure defined, we call Drupal's
	// theme_table() function to generate the table.
	$table_content = theme_table($table);

	$content = "<H2>Something</H2>
	$table_content
	";
	return $content; 

}
function tripal_expression_cvterm_organism_getExperiment_byGene($results){
	$content =  ' <a class="waves-effect waves-light btn" onClick="expandAll();">Expand All</a> 
	  <a class="waves-effect waves-light btn" onClick="collapseAll();">Collapse All</a>
	<br /><br />';
	$content .= "
	<p><a name=\"top\"></a></p>
	<h1>All images from the Experiemnt:<strong>$exp_title</strong> are subdivided by Gene and are displayed below.</h1>
	<br><hr>
	";

	$content .= $columns_script;


	$content .= "<h2> Images($image_count) By Gene($gene_count)</h2>";
	$content .= '
	  <p>Click on the buttons to change the grid view.</p>
	  <button class="btn" onclick="one(\'gene_column\')">1</button>
	  <button class="btn active" onclick="two(\'gene_column\')">2</button>
	  <button class="btn" onclick="four(\'gene_column\')">4</button>
	<br><br>
	';


	foreach ($results['genes'] as $feature){
	  $name = $feature['name'];
	  $uniquename = $feature['uniquename'];

	  $images = array();

	 foreach ($feature['eimage_id'] as $eimage_id){
		$uri = $results['images'][$eimage_id]['image_uri'];
		$expression_id = $results['images'][$eimage_id]['expression_id'];
		$terms_id_array = $results['expressions'][$expression_id]['cvterm_id'];
		$terms_array = array();
		foreach ($terms_id_array as $term_id){
		  $terms_array[] = $results['terms'][$term_id]['name'];
		}
		$terms = "All terms tagged in this image: " . join(', ', $terms_array);
		$all_genes_array = array();
		foreach($results['expressions'][$expression_id]['feature_id'] as $other_feature){
		  $other_gene_name = $results['genes'][$other_feature]['name'];
		  $other_gene_uniquename = $results['genes'][$other_feature]['uniquename'];
		  $all_genes_array[] = "$other_gene_name ($other_gene_uniquename)";
		}
		$all_genes = "All genes tagged in this image: " . join(', ', $all_genes_array);
		$caption = "$all_genes<br>$terms";
		$caption .=  "<br>". $results['expressions'][$expression_id]['description'];
		$images[] = "<div class=\"caption_container\"><a href=\"$image_dir/$uri\"><img style=\"width:100%\" class=\"caption_image\" src=\"$image_dir/$uri\" ></a><div class=\"caption_middle\"><div class=\"caption_text\">$caption</div></div></div>";
	 }
	 $each_image_count = count($images);
	 $columns = partition($images,4);
	 $content .= '
	 <fieldset class=" collapsible collapsed">
	 <legend><span class="fieldset-legend">' . $name . " [" . $uniquename . "] ($each_image_count)" . '</span></legend>
	 <div class="fieldset-wrapper"> ' ;

	 $content .= '<div class="row"> ';
	 $content .= '  <div class="gene_column">';
     $content .= join("\n",$columns[0]);
	 $content .= '  </div>';

	 $content .= '  <div class="gene_column">';
	 $content .= join("\n",$columns[1]);
	 $content .= '  </div>';

     $content .= '  <div class="gene_column">';
     $content .= join("\n",$columns[2]);
     $content .= '  </div>';

     $content .= '  <div class="gene_column">';
	 $content .= join("\n",$columns[3]);
	 $content .= '  </div>';
	 $content .= '  </div>';
	 $content .= '</div>
	 </fieldset>';
	}

	$content .= "<p><a href=\"#top\">back to top</a></p><br>";
	return $content;
}

$columns_script = <<<EOD
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
EOD;


$script = <<<EOD
  <script type="text/javascript">
    // We do not use Drupal Behaviors because we do not want this
    // code to be executed on AJAX callbacks. This code only needs to 
    // be executed once the page is ready.
    jQuery(document).ready(function($){

      // Hide all but the first data pane 
      $(".tripal-data-pane").hide().filter(":first-child").show();
  
      // When a title in the table of contents is clicked, then 
      // show the corresponding item in the details box 
      $(".tripal_toc_list_item_link").click(function(){
        var id = $(this).attr('id') + "-tripal-data-pane";
        $(".tripal-data-pane").hide().filter("#"+ id).fadeIn('fast');
        return false;
      });
  
      // If a ?pane= is specified in the URL then we want to show the
      // requested content pane. For previous version of Tripal,
      // ?block=, was used.  We support it here for backwards
      // compatibility
      var pane;
      pane = window.location.href.match(/[\?|\&]pane=(.+?)[\&|\#]/)
      if (pane == null) {
        pane = window.location.href.match(/[\?|\&]pane=(.+)/)
      }
      // if we don't have a pane then try the old style ?block=
      if (pane == null) {
        pane = window.location.href.match(/[\?|\&]block=(.+?)[\&|\#]/)
        if (pane == null) {
          pane = window.location.href.match(/[\?|\&]block=(.+)/)
        }
      }
      if(pane != null){
        $(".tripal-data-pane").hide().filter("#" + pane[1] + "-tripal-data-pane").show();
      }
      // Remove the 'active' class from the links section, as it doesn't
      // make sense for this layout
      $("a.active").removeClass('active');
    });
  </script>
EOD;

print $script;

print '
<div id="tripal_chado_expression_cvterm_organism_contents" class="tripal-contents">
    <table id="tripal-chado_expression_cvterm_organism-contents-table" class="tripal-contents-table">
      <tbody><tr class="tripal-contents-table-tr">
        <td nowrap="" class="tripal-contents-table-td tripal-contents-table-td-toc" align="left"><div id="chado_expression_cvterm_organism-tripal-toc-pane" class="tripal-toc-pane">
';

foreach ($results['toc'] as $id => $header){
  print '<div class="tripal_toc_list_item"><a id="' . $id . '" class="tripal_toc_list_item_link" href="?pane='.$id.'">'.$header.'</a></div>';
}

print '
</div>
</td>
<td class="tripal-contents-table-td-data" align="left" width="100%"> 
';

foreach ($results['toc'] as $id => $header){
print" 
        <div id=\"$id-tripal-data-pane\" class=\"tripal-data-pane\" style=\"display: block;\">
          <div class=\"$id-tripal-data-pane-title tripal-data-pane-title\">$header</div>
";

if ($id == 'experiment_overview'){
  $content = tripal_expression_cvterm_organism_getExperiment_overview($results);
  print $content;           
}elseif($id == 'experiment_byGene'){
  $content = tripal_expression_cvterm_organism_getExperiment_byGene($results);
  print $content;


}elseif($id == 'experiment_byTerm'){
  print "hi";
}elseif($id == 'experiment_all')
{
  print "hi";
}


print "        </div>
";
}

print '
  </td>
';


print '
  </tr></tbody></table></div>
';

?>
