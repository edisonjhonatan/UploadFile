@extends('layout')

@section('content')

	
	
	<div class="form-group">
		<!-- Necessidade do enctype="multipart/form-data" no form -->
		<form method="POST" enctype="multipart/form-data"
			@if (isset($file))
				action="{{ route('file.update', ['file'=>$file->id]) }}"
			@else
				action="{{ route('file.store') }}"
			@endif
			>

			@csrf
			@if (isset($file))
				@method("PUT")
			@endif
			
			<div class="form-group">
				<br>
				<label>Selecione o arquivo:</label>
				<input type="file" name="file" class="form-control-file" value="Selecionar arquivo">
				<input type="hidden" name="_token" value="{{ csrf_token() }}">
				<br>
				<button type="submit" class="btn btn-dark">Enviar</button>
			</div>
		</form>
	</div>

@endsection()