function collapseAll(){
  $(".collapsible").addClass("collapsed");
}

function expandAll(){
  $(".collapsible").removeClass(function(){
    return "collapsed";
  });
}

