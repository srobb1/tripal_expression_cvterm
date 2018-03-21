<?php
dpm($results,'results');

drupal_add_library('system', 'drupal.collapse');
$image_dir = '/pub/analysis/wish/image';
$image_count = count($results['images']);
$gene_count = count($results['genes']);
$term_count = count($results['terms']);
$exp_title = $results['pub']['title'];


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
$wordcloud_stuff = <<<EOD
<script>
//var fill =  d3.scale.category20c();
var fill = d3.scale.linear()
    .domain([0, 6])
    .range(["#def2d9", "#469834"]);

var layout = d3.layout.cloud()
    .size([500, 500])
    .words([{"text":"study","size":40},{"text":"motion","size":15},{"text":"forces","size":10},{"text":"electricity","size":15},{"text":"movement","size":10},{"text":"relation","size":5},{"text":"things","size":10},{"text":"force","size":5},{"text":"ad","size":5},{"text":"energy","size":85},{"text":"living","size":5},{"text":"nonliving","size":5},{"text":"laws","size":15},{"text":"speed","size":45},{"text":"velocity","size":30},{"text":"define","size":5},{"text":"constraints","size":5},{"text":"universe","size":10},{"text":"physics","size":120},{"text":"describing","size":5},{"text":"matter","size":90},{"text":"physics-the","size":5},{"text":"world","size":10},{"text":"works","size":10},{"text":"science","size":70},{"text":"interactions","size":30},{"text":"studies","size":5},{"text":"properties","size":45},{"text":"nature","size":40},{"text":"branch","size":30},{"text":"concerned","size":25},{"text":"source","size":40},{"text":"google","size":10},{"text":"defintions","size":5},{"text":"two","size":15},{"text":"grouped","size":15},{"text":"traditional","size":15},{"text":"fields","size":15},{"text":"acoustics","size":15},{"text":"optics","size":15},{"text":"mechanics","size":20},{"text":"thermodynamics","size":15},{"text":"electromagnetism","size":15},{"text":"modern","size":15},{"text":"extensions","size":15},{"text":"thefreedictionary","size":15},{"text":"interaction","size":15},{"text":"org","size":25},{"text":"answers","size":5},{"text":"natural","size":15},{"text":"objects","size":5},{"text":"treats","size":10},{"text":"acting","size":5},{"text":"department","size":5},{"text":"gravitation","size":5},{"text":"heat","size":10},{"text":"light","size":10},{"text":"magnetism","size":10},{"text":"modify","size":5},{"text":"general","size":10},{"text":"bodies","size":5},{"text":"philosophy","size":5},{"text":"brainyquote","size":5},{"text":"words","size":5},{"text":"ph","size":5},{"text":"html","size":5},{"text":"lrl","size":5},{"text":"zgzmeylfwuy","size":5},{"text":"subject","size":5},{"text":"distinguished","size":5},{"text":"chemistry","size":5},{"text":"biology","size":5},{"text":"includes","size":5},{"text":"radiation","size":5},{"text":"sound","size":5},{"text":"structure","size":5},{"text":"atoms","size":5},{"text":"including","size":10},{"text":"atomic","size":10},{"text":"nuclear","size":10},{"text":"cryogenics","size":10},{"text":"solid-state","size":10},{"text":"particle","size":10},{"text":"plasma","size":10},{"text":"deals","size":5},{"text":"merriam-webster","size":5},{"text":"dictionary","size":10},{"text":"analysis","size":5},{"text":"conducted","size":5},{"text":"order","size":5},{"text":"understand","size":5},{"text":"behaves","size":5},{"text":"en","size":5},{"text":"wikipedia","size":5},{"text":"wiki","size":5},{"text":"physics-","size":5},{"text":"physical","size":5},{"text":"behaviour","size":5},{"text":"collinsdictionary","size":5},{"text":"english","size":5},{"text":"time","size":35},{"text":"distance","size":35},{"text":"wheels","size":5},{"text":"revelations","size":5},{"text":"minute","size":5},{"text":"acceleration","size":20},{"text":"torque","size":5},{"text":"wheel","size":5},{"text":"rotations","size":5},{"text":"resistance","size":5},{"text":"momentum","size":5},{"text":"measure","size":10},{"text":"direction","size":10},{"text":"car","size":5},{"text":"add","size":5},{"text":"traveled","size":5},{"text":"weight","size":5},{"text":"electrical","size":5},{"text":"power","size":5}])
    .padding(5)
    .rotate(function() { return ~~(Math.random() * 2) * 90; })
    .font("Impact")
    .fontSize(function(d) { return d.size; })
    .on("end", draw);


layout.start();

function draw(words) {
  d3.select("#wordcloud").append("svg")
      .attr("width", layout.size()[0])
      .attr("height", layout.size()[1])
    .append("g")
      .attr("transform", "translate(" + layout.size()[0] / 2 + "," + layout.size()[1] / 2 + ")")
    .selectAll("text")
      .data(words)
    .enter().append("text")
      .style("font-size", function(d) { return d.size + "px"; })
      .style("font-family", "Impact")
      .style("fill", function(d, i) { return fill(i); })
      .attr("text-anchor", "middle")
      .attr("transform", function(d) {
        return "translate(" + [d.x, d.y] + ")rotate(" + d.rotate + ")";
      })
      .text(function(d) { return d.text; });
}
</script>
EOD;
//drupal_add_js(drupal_get_path('module', 'tripal_expression_cvterm_organism') . '/theme/js/d3.v4.min.js');
drupal_add_js(drupal_get_path('module', 'tripal_expression_cvterm_organism') . '/theme/js/d3.v3.min.js');
//drupal_add_js(drupal_get_path('module', 'tripal_expression_cvterm_organism') . '/theme/js/d3-cloud/build/d3.layout.cloud.js');
drupal_add_js(drupal_get_path('module', 'tripal_expression_cvterm_organism') . '/theme/js/d3.layout.cloud.js');
//drupal_add_js(drupal_get_path('module', 'tripal_expression_cvterm_organism') . '/theme/js/d3-cloud/examples/browserify.js');



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
//        $content .= $wordcloud_stuff;

	return $content; 

}
function tripal_expression_cvterm_organism_getExperiment_byGene($results,$image_count,$gene_count,$image_dir){
	$content =  ' <a class="waves-effect waves-light btn" onClick="expandAll();">Expand All</a> 
	  <a class="waves-effect waves-light btn" onClick="collapseAll();">Collapse All</a>
	<br /><br />';
	$content .= "
	<p><a name=\"top\"></a></p>
	<h1>All images from the Experiemnt:<strong>$exp_title</strong> are subdivided by Gene and are displayed below.</h1>
	<br><hr>
	";

	//$content .= $columns_script;


	$content .= "<h2> Images($image_count) By Gene($gene_count)</h2>";
        $content .= '<div id="wordcloud"></div>';
	$content .= '
	  <p>Click on the buttons to change the grid view.</p>
	  <button class="btn" onclick="one(\'gene_column\')">1</button>
	  <button class="btn active" onclick="two(\'gene_column\')">2</button>
	  <button class="btn" onclick="four(\'gene_column\')">4</button>
	<br><br>
	';


	foreach ($results['genes'] as $item){
	  $name = $item['name'];
	  $uniquename = $item['uniquename'];

	  $images = array();

	 foreach ($item['eimage_id'] as $eimage_id){
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

function tripal_expression_cvterm_organism_getExperiment_byTerm($results,$image_count,$term_count,$image_dir){
	$content =  ' <a class="waves-effect waves-light btn" onClick="expandAll();">Expand All</a> 
	  <a class="waves-effect waves-light btn" onClick="collapseAll();">Collapse All</a>
	<br /><br />';
	$content .= "
	<p><a name=\"top\"></a></p>
	<h1>All images from the Experiemnt: <strong>$exp_title</strong> are subdivided by Term and are displayed below.</h1>
	<br><hr>
	";

	//$content .= $columns_script;


	$content .= "<h2> Images($image_count) By Term($term_count)</h2>";
	$content .= '
	  <p>Click on the buttons to change the grid view.</p>
	  <button class="btn" onclick="one(\'term_column\')">1</button>
	  <button class="btn active" onclick="two(\'term_column\')">2</button>
	  <button class="btn" onclick="four(\'term_column\')">4</button>
	<br><br>
	';


	foreach ($results['terms'] as $item){
	  $name = $item['name'];
	  $def = $item['definition'];

	  $images = array();

	 foreach ($item['eimage_id'] as $eimage_id){
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
	 <legend><span class="fieldset-legend">' . $name . ": " . $definition . " ($each_image_count)" . '</span></legend>
	 <div class="fieldset-wrapper"> ' ;

	 $content .= '<div class="row"> ';
	 $content .= '  <div class="term_column">';
     $content .= join("\n",$columns[0]);
	 $content .= '  </div>';

	 $content .= '  <div class="term_column">';
	 $content .= join("\n",$columns[1]);
	 $content .= '  </div>';

     $content .= '  <div class="term_column">';
     $content .= join("\n",$columns[2]);
     $content .= '  </div>';

     $content .= '  <div class="term_column">';
	 $content .= join("\n",$columns[3]);
	 $content .= '  </div>';
	 $content .= '  </div>';
	 $content .= '</div>
	 </fieldset>';
	}

	$content .= "<p><a href=\"#top\">back to top</a></p><br>";
	return $content;
}

function tripal_expression_cvterm_organism_getExperiment_all($results,$image_count,$exp_title,$image_dir){
	$content =  ' <a class="waves-effect waves-light btn" onClick="expandAll();">Expand All</a> 
	  <a class="waves-effect waves-light btn" onClick="collapseAll();">Collapse All</a>
	<br /><br />';
	$content .= "
	<p><a name=\"top\"></a></p>
	<h1>All images from the Experiemnt:<strong>$exp_title</strong> are displayed below.</h1>
	<br><hr>
	";

	//$content .= $columns_script;


	$content .= "<h2> Images($image_count)</h2>";
	$content .= '
	  <p>Click on the buttons to change the grid view.</p>
	  <button class="btn" onclick="one(\'gene_column\')">1</button>
	  <button class="btn active" onclick="two(\'gene_column\')">2</button>
	  <button class="btn" onclick="four(\'gene_column\')">4</button>
	<br><br>
	';


	


	$images = array();

	 foreach ($results['images'] as $eimage){
		$uri = $eimage['image_uri'];
		$expression_id = $eimage['expression_id'];
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
	 <legend><span class="fieldset-legend"> All Images' .  "($each_image_count)" . '</span></legend>
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
	

	$content .= "<p><a href=\"#top\">back to top</a></p><br>";
	return $content;
}



print $script;
print $columns_script;



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
  $content = tripal_expression_cvterm_organism_getExperiment_byGene($results,$image_count,$gene_count,$image_dir);
  print $content;
print $wordcloud_stuff;


}elseif($id == 'experiment_byTerm'){
  $content = tripal_expression_cvterm_organism_getExperiment_byTerm($results,$image_count,$term_count,$image_dir);
  print $content;
}elseif($id == 'experiment_all'){
  $content = tripal_expression_cvterm_organism_getExperiment_all($results,$image_count,$exp_title,$image_dir);
  print $content;
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
