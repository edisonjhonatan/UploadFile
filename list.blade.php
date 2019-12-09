@extends('layout')

@section('content')

	
	<br>
	<a href="{{ route('file.create') }}" class="btn btn-success">Fazer upload de um arquivo</a>
	<br>
	<br>

	@if (count($files) > 0)

	<h1>Lista de arquivos enviados</h1>

		<table class="table table-striped table-bordered">
			<thead>
				<th scope="col">#</th>
				<th scope="col">Nome</th>
				<th scope="col">Data de Upload</th>
				<th scope="col">Opções</th>
			</thead>
			<tbody>
				@foreach ($files as $f => $file)
					<tr>
						<td>{{ $file->id }}</td>
						<td>{{ $file->name }}</td>
						<td>{{ $file->created_at }}</td>
						<td>
							<a href="{{ "/download/{$file->name}" }}" class="btn btn-primary">Baixar Arquivo</a>

							<a href="{{route('file.edit', ['file'=>$file->id])}}" class="btn btn-warning">Atualizar Arquivo</a>

							<br><br>

							<form method="POST" action="{{ route('file.destroy', ['file'=>$file->id]) }}">
								@csrf
								@method("DELETE")
								<input type="submit" name="submit" value="Excluir Arquivo" class="btn btn-danger">
							</form>

						</td>
					</tr>
				@endforeach
			</tbody>
		</table>
	@else
		<h1 class="empty">Não há arquivos.</h1>
	@endif

@endsection()