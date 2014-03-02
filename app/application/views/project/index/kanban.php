<h3>
	<?php echo __('tinyissue.kanban_board'); ?>

  <div class="points-total"> 
    <span class="points" id="points-open" style="float: none; display: inline-block;"><?php echo $open_points ?></span> todo /
    <span class="points" id="points-total" style="float: none; display: inline-block;"><?php echo $total_points ?></span> total
  </div>
</h3>

  <div class="pad" id="todo-lanes">
    <?php foreach($lanes as $index => $items):  ?>
    <div class="todo-lane blue-box" id="lane-status-<?php echo $index; ?>" data-status="<?php echo $index; ?>">
      <h4><?php echo $status[$index]; ?> <span class="points"><?php echo $points[$index]; ?></span></h4>
      <div class="inside-pad todo-lane-inner">
        <?php foreach($items as $todo):  ?>
        <div class="todo-list-item <?php if ($index > 0) { echo ' draggable'; } ?>" id="todo-id-<?php echo $todo['issue_id']; ?>" data-issue-id="<?php echo $todo['issue_id']; ?>">
          <div class="todo-list-item-inner">
            <span>#<?php echo $todo['issue_id']; ?></span>
            <a href="<?php echo $todo['issue_link']; ?>"><?php echo $todo['issue_name']; ?></a>
            <div>
              <?php if ($todo['issue_points'] > 0) : ?>
                <span class="points"><?php echo $todo['issue_points'] ?></span>
              <?php endif; ?>
              <?php echo $todo['project_name']; ?>
            </div>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
