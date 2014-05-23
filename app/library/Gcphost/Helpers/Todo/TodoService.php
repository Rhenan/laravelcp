<?php 
use Gcphost\Helpers\Todo\TodoRepository as Todos;

class TodosService {
    protected $todo;
	var $rules = array(
			'title' => 'required',
		);

    public function __construct(Todos $todo)
    {
        $this->todo = $todo;
    }

	public function index(){
		return Theme::make('admin/todos/index');
	}

	public function getCreate()
	{
        return Theme::make('admin/todos/create_edit');
	}

	public function getEdit($todo)
	{
        $due = preg_replace('/0000-00-00 00:00:00/i', '',Input::old('due_at', isset($todo) ? $todo->due_at : null));
		return Theme::make('admin/todos/create_edit', compact('due','todo'));
	}

	public function create(){
		$save=$this->todo->createOrUpdate();
		$errors = $save->errors();

		return count($errors->all()) == 0 ?
			(Api::to(array('success', Lang::get('admin/todos/messages.create.success'))) ? : Redirect::to('admin/todos/' . $this->todo->id . '/edit')->with('success', Lang::get('admin/todos/messages.create.success'))) : 
			(Api::to(array('error', Lang::get('admin/todos/messages.create.error'))) ? : Redirect::to('admin/todos/create')->withErrors($errors));
	}

	public function edit($todo){
		$save=$this->todo->createOrUpdate($todo->id);
		$errors = $save->errors();

		return count($errors->all()) == 0 ?
			(Api::to(array('success', Lang::get('admin/todos/messages.create.success'))) ? : Redirect::to('admin/todos/' . $todo->id . '/edit')->with('success', Lang::get('admin/todos/messages.create.success'))) : 
			(Api::to(array('error', Lang::get('admin/todos/messages.create.error'))) ? : Redirect::to('admin/todos/' . $todo->id . '/edit')->withErrors($errors));
	}

    public function delete($todo)
    {
		return $todo->delete() ? 
			Api::json(array('result'=>'success')) :
			Api::json(array('result'=>'error', 'error' =>Lang::get('core.delete_error')));
    }

	public function assign($todo){
        return $todo->assign() ? Api::json(array('result'=>'success')) : Api::json(array('result'=>'error', 'error' =>Lang::get('core.delete_error')));
	}

	public function page($limit=10){
		return $this->todo->paginate($limit);
	}

    public function get()
    {
		if(Api::Enabled()){
			return Api::make($this->todo->all()->get()->toArray());
		} else return Datatables::of($this->todo->all())
			->edit_column('title', '<a href="{{{ URL::to(\'admin/todos/\' . $id . \'/edit\' ) }}}" class="modalfy">{{{$title}}}</a>')
			->edit_column('description', '<a href="{{{ URL::to(\'admin/todos/\' . $id . \'/edit\' ) }}}" class="modalfy">{{{$description}}}</a>')

			 ->edit_column('status','<a href="{{{ URL::to(\'admin/todos/\' . $id . \'/edit\' ) }}}" class="modalfy">{{{ Lang::get(\'admin/todos/todos.status_\'.$status) }}}</a>')
			 ->edit_column('due_at','{{{ Carbon::parse($due_at)->diffForHumans() }}}')
			 ->edit_column('created_at','{{{ Carbon::parse($created_at)->diffForHumans() }}}')
			 ->edit_column('displayname','{{{ $displayname ? : "Nobody" }}}')
	        ->add_column('actions', '<div class="btn-group" style="width: 200px">
		<a href="{{{ URL::to(\'admin/todos/\' . $id . \'/edit\' ) }}}" class="modalfy btn btn-sm btn-primary">{{{ Lang::get(\'button.edit\') }}}</a> 
		<a href="{{{ URL::to(\'admin/todos/\' . $id . \'/assign\' ) }}}" data-row="{{{  $id }}}" data-table="todos" class="confirm-ajax-update btn btn-sm btn-default">{{{ Lang::get(\'button.assign_to_me\') }}}</a>
			<a data-row="{{{  $id }}}" data-table="todos" data-method="delete" href="{{{ URL::to(\'admin/todos/\' . $id . \'\' ) }}}" class="confirm-ajax-update btn btn-sm btn-danger">{{{ Lang::get(\'button.delete\') }}}</a>
		</div>
            ')
			->make();
	}
}