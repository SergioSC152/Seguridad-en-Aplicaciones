<?php
namespace App\Services;
use App\Models\Client;
use App\Models\ClientDocument;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Throwable;
class ClientDocumentService
{
    public function create(Client $client,array $data): ClientDocument
    {
        $file=$data['file']; unset($data['file']);
        $path=$file->store('client-documents/'.$client->user_id,'local');
        if (!$path) throw ValidationException::withMessages(['file'=>'No se pudo guardar el documento.']);
        try { return $client->documents()->create(['user_id'=>$client->user_id,'path'=>$path,'mime_type'=>$file->getMimeType()]+$data); }
        catch(Throwable $e) { Storage::disk('local')->delete($path); throw $e; }
    }
    public function delete(ClientDocument $document): void { $path=$document->path; $document->delete(); Storage::disk('local')->delete($path); }
}
