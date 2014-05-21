@include(Theme::path('admin/dt-loading'))

<div id="todos-container" class="dt-wrapper">
	<table id="todos" class="table table-responsive table-striped table-hover table-bordered">
		<thead>
			<tr>
				<th></th>
				<th>{{{ Lang::get('table.title') }}}</th>
				<th>{{{ Lang::get('table.status') }}}</th>
				<th>{{{ Lang::get('table.description') }}}</th>
				<th>{{{ Lang::get('table.created_at') }}}</th>
				<th>{{{ Lang::get('core.due_at') }}}</th>
				<th>{{{ Lang::get('core.assigned_to') }}}</th>
				<th>{{{ Lang::get('table.actions') }}}</th>
			</tr>
		</thead>
		<tbody>
		</tbody>
	</table>
</div>

<script src="{{{ asset('assets/js/jquery.dataTables.min.js') }}}"></script>
<script src="{{{ asset('assets/js/datatables.js') }}}"></script>

<script type="text/javascript">
	@if(isset($type) && $type == "widget")
		dtLoad('#todos', "{{URL::to('admin/todos/data') }}", '','', 'td:eq(2), th:eq(2),td:eq(4), th:eq(4),td:eq(6), th:eq(6),td:eq(7), th:eq(7), td:eq(5), th:eq(5)', 'false', 'true',[null,null,null,null,null,null,null,null]);
	@else
		dtLoad('#todos', "{{URL::to('admin/todos/data') }}", 'td:eq(2), th:eq(2)', 'td:eq(1), th:eq(1)','', 'false', 'true',[null,null,null,null,null,null,null,null]);
	@endif
</script>