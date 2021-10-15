<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\DocumentService;
use App\Http\Requests\CreateDocumentRequest;

class DocumentController extends Controller
{
    public function createDocument(CreateDocumentRequest $request): void
    {
        DocumentService::createDocument($request);
    }
}
