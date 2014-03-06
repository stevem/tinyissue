$(function(){ 
  // Set widths of lanes (dynamically to allow configurable N lanes).
  var laneSpacing = 10;
  var borderWidth = 4;
  var totalWidth  = parseInt($('#todo-lanes').width());
  var laneCount   = parseInt($('.todo-lane').size());
  var laneWidth   = (totalWidth - (laneSpacing * laneCount)) / laneCount;
  $('.todo-lane').each(function() {
    $(this).css( "width", laneWidth - borderWidth);
  });
  
  // Utility to recalculate point values.
  function recalculatePoints() {
    $('#points-total').html('0');
    $('#open-total').html('0');
    $('.todo-lane').each(function(){
      var lane_total = 0;
      var points_total = 0;
      var points_open = 0;
      var lane_id = $(this).attr('id');
      $('#' + lane_id + ' .todo-list-item-inner .points').each(function() {
          lane_total += parseFloat($(this).html());    
      });
      $('#' + lane_id + ' h4 span.points').html(lane_total);
      points_total = parseFloat($('#points-total').html()) + lane_total;
      if (lane_id != 'lane-status-0') points_open = parseFloat($('#points-open').html()) + lane_total;
      $('#points-total').html(points_total);
      $('#points-open').html(points_open);
    });
  }
  
  // Draggable interaction.
  $('.todo-list-item.draggable').draggable({
     snap: '.todo-lane',
     snapMode: "inner",
     revert: "invalid",
     revertDuration: 200
  });
  
  // Droppable interaction.
  $('.todo-lane').droppable({
    activeClass: "todo-state-active",
    hoverClass:  "todo-state-hover",
    drop: function( event, ui ) {
      var new_status = $(this).data('status');
      var issue_id   = $(ui.draggable).data("issue-id");
      
      // Add the dragged todo to the new lane, reset css.
      var this_id = $(this).attr('id');
      $(ui.draggable).prependTo($('#' + this_id + ' .todo-lane-inner'));
      $(ui.draggable).css('left', 0);
      $(ui.draggable).css('top', 0);
      
      // Prevent closed items from being moved again.
      if (new_status == 0) {
        $(ui.draggable).draggable( "option", "disabled", true );
      }
      
      // Recalculate.
      recalculatePoints();
      
      // POST the new status.
      $.post(
        siteurl + 'ajax/todo/update_todo', 
        { "issue_id" : issue_id, "new_status" : new_status}, 
        function( data ) {
          if (!data.success) {
            alert(data.errors);
          }
        }, "json" );
    }
  });
  
  // Remove a todo.
  $('a.todo-button.del').click(function(event) {
    event.preventDefault();
    
    var issue_id = $(this).data('issue-id');
    $.post(
      siteurl + 'ajax/todo/remove_todo', 
      { "issue_id" : issue_id }, 
      function( data ) {
        if (data.success) {
          $('#todo-id-' + issue_id).hide().remove();
        }
        else {
          alert(data.errors);
        }
      }, "json" 
    );
    
    recalculatePoints();
  });

});
