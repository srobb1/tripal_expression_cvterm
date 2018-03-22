<?php
dpm($results,'results');

drupal_add_library('system', 'drupal.collapse');
$image_dir = '/pub/analysis/wish/image';
$image_count = count($results['images']);
$gene_count = count($results['genes']);
$term_count = count($results['terms']);
$exp_title = $results['pub']['title'];

$btn_script = <<<EOD
<script>
$("#gallery_btns .btn").on('click', function(){
    $(this).siblings().removeClass('active')
    $(this).addClass('active');
})
</script>
EOD;

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

//drupal_add_js(drupal_get_path('module', 'tripal_expression_cvterm_organism') . '/theme/js/d3.v4.min.js');
drupal_add_js(drupal_get_path('module', 'tripal_expression_cvterm_organism') . '/theme/js/d3.v3.min.js');
drupal_add_js(drupal_get_path('module', 'tripal_expression_cvterm_organism') . '/theme/js/d3.layout.cloud.js');





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

    $wordcloud_array = array();
    $wordcloud_hoover = array();
	foreach ($results['genes'] as $item){
	  $name = $item['name'];
	  $uniquename = $item['uniquename'];
      $wordcloud_hoover[] = "\"$uniquename\" : \"$uniquename: $name\"";
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
	 $wordcloud_array[]="{\"text\":\"$uniquename\",\"size\":$each_image_count}";
	 $columns = partition($images,4);
	 $content .= '
	 <fieldset class=" collapsible collapsed">
	 <legend><span class="fieldset-legend"><a name="'.$uniquename.'">' . $name . " [" . $uniquename . "] ($each_image_count)" . '</a></span></legend>
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
    $wordcloud_words = join(',',$wordcloud_array);
    dpm($wordcloud_words);
    $hoover_str = '{' . join("," , $wordcloud_hoover) . ' }';

    $wordcloud = <<<EOD
<script>
var words_array = {$hoover_str};
var word_freqs = [{$wordcloud_words}];
word_freqs.sort(function(a,b) { return parseFloat(b.freq) - parseFloat(a.freq) } );  
var fill = d3.scale.linear()
    .domain([0, 19])
    .range(["#def2d9", "#469834"]);

var layout = d3.layout.cloud()
    .size([800,500])
    .words(word_freqs)
    .padding(0)
    .rotate(function() { return ~~(Math.random() * 2) * 90; })
    .font("Impact")
    //.fontSize(24)
    .fontSize(function(d) { return d.size*5; })
    .on("end", draw);


layout.start();



function draw(words) {
var div = d3.select("#wordcloud").append("div")	
    .attr("class", "tooltip")				
    .style("opacity", 0);

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
      }).append("a").attr('xlink:href', function(d) { return "#" + d.text; })
      .text(function(d) { return d.text; })
      .on("mouseover", function(d){
         div.transition()		
                .duration(200)		
                .style("opacity", .9);
        div.html(words_array[d.text])
                .style("left", (d3.event.pageX) + "px")		
                .style("top", (d3.event.pageY - 28) + "px");	
      })
      .on("mouseout", function(d){
           div.transition()		
                .duration(500)		
                .style("opacity", 0);	
      });
       ;
      


}
</script>
EOD;
    $content .= $wordcloud;

	return  $content;
}

function tripal_expression_cvterm_organism_getExperiment_byTerm($results,$image_count,$term_count,$image_dir,$btn_script){
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
    <div id="gallery_btns">
	  <p>Click on the buttons to change the grid view.</p>
	  <button class="btn" onclick="one(\'term_column\')">1</button>
	  <button class="btn active" onclick="two(\'term_column\')">2</button>
	  <button class="btn" onclick="four(\'term_column\')">4</button>
	</div>
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
	$content .= $btn_script;
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



}elseif($id == 'experiment_byTerm'){
  $content = tripal_expression_cvterm_organism_getExperiment_byTerm($results,$image_count,$term_count,$image_dir,$btn_script);
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
