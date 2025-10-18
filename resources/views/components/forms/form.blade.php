<form method="{{ $method }}" action="{{ $route_action_name ?? $action }}">
	@if($method == 'POST')@csrf @endif
	{{ $body }}
</form>
