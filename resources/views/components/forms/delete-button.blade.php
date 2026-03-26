@props(['formName', 'routeName', 'id', 'name'])
<button form="{{ $formName }}-{{ $id }}" class="btn btn-outline-danger px-3" >{{$name}}</button>
                             
<form id="{{ $formName }}-{{ $id }}" action="{{ route($routeName, $id) }}" method="post" onsubmit="return confirm('Are you sure you want to delete this ?');">
    @csrf
    @method('DELETE')
</form>