<?php

class Project_Controller extends Base_Controller {

	public $layout = 'layouts.project';

	public function __construct()
	{
		parent::__construct();

		$this->filter('before', 'project');
		$this->filter('before', 'permission:project-modify')->only('edit');
	}

	/**
	 * Display activity for a project
	 * /project/(:num)
	 *
	 * @return View
	 */
	public function get_index()
	{
		return $this->layout->nest('content', 'project.index', array(
			'page' => View::make('project/index/activity', array(
				'project' => Project::current(),
				'activity' => Project::current()->activity(10)
			)),
			'active' => 'activity',
			'open_count' => Project::current()->issues()
				 ->where('status', '=', 1)
				 ->count(),
			'closed_count' => Project::current()->issues()
				 ->where('status', '=', 0)
				 ->count(),
			'assigned_count' => Project::current()->count_assigned_issues()
		));
	}

	/**
	 * Display issues for a project
	 * /project/(:num)
	 *
	 * @return View
	 */
	public function get_issues()
	{
		$status = Input::get('status', 1);

		return $this->layout->nest('content', 'project.index', array(
			'page' => View::make('project/index/issues', array(
				'issues' => Project::current()->issues()
				->where('status', '=', $status)
				->order_by('updated_at', 'DESC')
				->get(),
			)),
			'active' => $status == 1 ? 'open' : 'closed',
			'open_count' => Project::current()->issues()
				->where('status', '=', 1)
				->count(),
			'closed_count' => Project::current()->issues()
				->where('status', '=', 0)
				->count(),
			'assigned_count' => Project::current()->count_assigned_issues()
		));
	}

	/**
	 * Display issues assigned to current user for a project
	 * /project/(:num)
	 *
	 * @return View
	 */
	public function get_assigned()
	{
		$status = Input::get('status', 1);

		return $this->layout->nest('content', 'project.index', array(
			'page' => View::make('project/index/issues', array(
				'issues' => Project::current()->issues()
					->where('status', '=', $status)
					->where('assigned_to', '=', Auth::user()->id)
					->order_by('updated_at', 'DESC')
					->get(),
			)),
			'active' => 'assigned',
			'open_count' => Project::current()->issues()
				->where('status', '=', 1)
				->count(),
			'closed_count' => Project::current()->issues()
				->where('status', '=', 0)
				->count(),
			'assigned_count' => Project::current()->count_assigned_issues()
		));
	}
  
  /**
   * Display kanban-style TODO board
   */
	public function get_kanban()
	{
		// @TODO Make configurable. Global or per-user?
		$status_codes = array(
			0 => __('tinyissue.todo_status_0'),
			1 => __('tinyissue.todo_status_1'),
			2 => __('tinyissue.todo_status_2'),
			3 => __('tinyissue.todo_status_3'),
		);
		
		// Ensure we have an entry for each lane. 
		$lanes  = array();
    $points = array();
		foreach ($status_codes as $index => $name) {
			$lanes[$index]  = array();
      $points[$index] = 0;
		}
    
		// Load todos into lanes according to status.
    $total_points = 0;
    $open_points  = 0;
		$todos = Todo::load_project_todos(Project::current()->id);
		foreach ($todos as $todo) {
			$lanes[$todo['status']][] = $todo;
      $points[$todo['status']] += $todo['issue_points'];
      $total_points += $todo['issue_points'];
      if ($todo['status'] > 0) $open_points += $todo['issue_points'];
		}
    
		return $this->layout->nest('content', 'project.index', array(
			'page' => View::make('project/index/kanban', array(
        'lanes'   => $lanes,
        'points'  => $points,
        'total_points' => $total_points,
        'open_points'  => $open_points,
        'status'  => $status_codes,
        'columns' => count($status_codes),
			)),
			'active' => 'kanban',
			'open_count' => Project::current()->issues()
				->where('status', '=', 1)
				->count(),
			'closed_count' => Project::current()->issues()
				->where('status', '=', 0)
				->count(),
			'assigned_count' => Project::current()->count_assigned_issues()
		));
	}
  

	/**
	 * Edit the project
	 * /project/(:num)/edit
	 *
	 * @return View
	 */
	public function get_edit()
	{
		return $this->layout->nest('content', 'project.edit', array(
			'project' => Project::current()
		));
	}

	public function post_edit()
	{
		/* Delete the project */
		if(Input::get('delete'))
		{
			Project::delete_project(Project::current());

			return Redirect::to('projects')
				->with('notice', __('tinyissue.project_has_been_deleted'));
		}

		/* Update the project */
		$update = Project::update_project(Input::all(), Project::current());

		if($update['success'])
		{
			return Redirect::to(Project::current()->to('edit'))
				->with('notice', __('tinyissue.project_has_been_updated'));
		}

		return Redirect::to(Project::current()->to('edit'))
			->with_errors($update['errors'])
			->with('notice-error', __('tinyissue.we_have_some_errors'));
	}
}
