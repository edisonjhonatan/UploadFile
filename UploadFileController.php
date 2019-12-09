<?php

namespace App\Http\Controllers;

use App\UploadFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UploadFileController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $files = UploadFile::all();
        return view("UploadFile.list")->with("files", $files);
    }

    /**
    * Realiza o download do arquivo selecionado.
    *
    * @param App\UploadFile   $file
    * @return Arquivo enviado pelo usuário 
    */
    public function download($file)
    {
        return Storage::download("/upload/{$file}");
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view("UploadFile.form");
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {   
        
        $file = $request->file('file');

        /* Verifica se algum arquivo foi enviado, para que a função de excluir o arquivo existente não seja executada sem que haja um novo arquivo que deveria ser salvo. */ 
        if (empty($file)){
            abort(400, 'Nenhum arquivo foi enviado');
        }

        /* Busca o número de arquivos no banco de dados e utiliza este valor como atribuição ao nome do arquivo, para que este se torne único na pasta em que será salvo, evitando que tenham arquivos salvos com exatamente o mesmo nome. */
        $files = UploadFile::all();
        $fileName = $file->getClientOriginalName() . count($files);

        /* Guardar o arquivo na pasta Upload, em storage/app/upload */
        $file->storeAs('upload', $fileName);

        $UploadFile = new UploadFile;
        $UploadFile->name = $fileName;
        $UploadFile->url = asset('upload/'. $fileName);

        $UploadFile->save();

        return redirect()->route('file.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\UploadFile  $uploadFile
     * @return \Illuminate\Http\Response
     */
    public function edit(UploadFile $file)
    {
        return view("UploadFile.form")->with('file', $file);
    }

    /**
     * Update the specified resource in storage.
     * Atualiza o arquivo através do envio de um novo arquivo.
     * O arquivo antigo é substituído na pasta de Upload do projeto.
     * As informações no banco de dados são atualizadas (name, url, date).
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\UploadFile  $uploadFile
     * @param  \App\UploadFile  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, UploadFile $uploadFile, $id)
    {
        /* Guarda o novo arquivo que será salvo na pasta Upload. */
        $fileNew = $request->file('file');

        if (empty($fileNew)){
            abort(400, 'Nenhum arquivo foi enviado');
        } else {            
            /* Apaga o arquivo que está salvo na pasta Upload e o usuário pretende atualizar. */
            $file = UploadFile::find($id);
            Storage::delete("/upload/{$file->name}");

            /* Salva o novo arquivo enviado pelo usuário na pasta upload. */
            $files = UploadFile::all();
            $fileName = $fileNew->getClientOriginalName() . count($files);
            $fileNew->storeAs('upload', $fileName);

            /* Atualiza as informações do novo arquivo no banco de dados (name, url e data) */
            $newFileInfo = UploadFile::find($id);
            $newFileInfo->name = $fileName;
            $newFileInfo->url = asset('upload/'. $fileName);
            $newFileInfo->save();

            return redirect()->route('file.index');
        }
    }

    /**
     * Remove the specified resource from storage.
     * Exclui o arquivo salvo na pasta Upload e
     * posteriormente apaga as informações relativas ao
     * arquivo contantes no banco de dados.
     *
     * @param  \App\UploadFile $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {   
        $file = UploadFile::find($id);

        if (Storage::delete("/upload/{$file->name}")) {
            UploadFile::destroy($id);
            return redirect()->route('file.index');
        } else {
            echo "O arquivo não foi excluído.";
        }    
    }
}
