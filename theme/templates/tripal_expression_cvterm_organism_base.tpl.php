<?php
dpm($node,'node-base');
dpm($results,'result-base');
$cvterm_name = $node->expression_cvterm_organism->cvterm_id->name;
$cvterm_def = $node->expression_cvterm_organism->cvterm_id->definition;
$cv_name = ucwords(str_replace("_", " ", $node->expression_cvterm_organism->cv_id->name));
$organism_species = $node->expression_cvterm_organism->organism_id->species;
$organism_genus = $node->expression_cvterm_organism->organism_id->genus;
print "
<h2>All images that have been tagged with the term <u>$cvterm_name</u> are displayed on these pages. The images can be viewed unsorted or organized by gene, experiment, or exprimenter. Use the links in the left side bar to view the images accordingly.</h2>";

$headers = array();
$rows = array();
// Type row
$rows[] = array(
  array(
    'data' => 'Term Name',
    'header' => TRUE
  ),
  $cvterm_name
);
$rows[] = array(
array(
    'data' => 'Definition',
    'header' => TRUE
  ),
  $cvterm_def
);
$rows[] = array(
  array(
    'data' => 'Organism',
    'header' => TRUE
  ),
  "$organism_genus $organism_species"
);

$rows[] = array(
  array(
    'data' => 'Library Name',
    'header' => TRUE
  ),
  $cv_name
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
print theme_table($table);
