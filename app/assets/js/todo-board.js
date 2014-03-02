$(function(){ 
  // Set widths of lanes (dynamically to allow configurable N lanes).
  var laneCount   = parseInt($('.todo-lane').size());
  if (laneCount) {
    var totalWidth  = parseInt($('#todo-lanes').width());
    var laneSpacing = 10;
    var borderWidth = 4;
    var laneWidth   = (totalWidth - (laneSpacing * laneCount)) / laneCount;
    var laneHeight  = 0;
    $('.todo-lane').each(function() {
      $(this).css( "width", laneWidth - borderWidth);
      if ($(this).height() > laneHeight) {
        laneHeight = $(this).height();
      }
    });
    laneHeight = laneHeight + 20;
    
    console.log(laneHeight);
    
    $('#todo-lanes').css("height", laneHeight);
  }
});
