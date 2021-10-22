<?php

namespace App\Services;

use App\Http\Requests\CreateDocumentRequest;
use App\Models\Document;
use App\Models\User;
use App\Notifications\CreateDocumentNotification;
use App\Notifications\DeleteDocumentNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

class DocumentService
{
    public static function createDocument(CreateDocumentRequest $request): void
    {
        $documentId = DB::table('documents')->insertGetId([
            'name' => $request['name'],
            'description' => $request['description'],
            'url' => $request['url'],
            'author_id' => session()->get('userId')
        ]);

        DB::table("projects_documents")
            ->insert([
                "project_id" => session()->get('project'),
                "document_id" => $documentId
            ]);

        $user = UserService::getUserBySession();
        $document = Document::find($documentId);

        Notification::sendNow($user, new CreateDocumentNotification($document));

        header("Location: /documents");
    }

    public static function getDocumentsByProject(): string|bool
    {
        return json_encode(
            DB::select('select * from documents where id in
                              (select document_id from projects_documents where project_id = ?)',
                [session()->get("project")]
            )
        );
    }

    public static function deleteDocument($id): void
    {
        $document = Document::find($id);
        $user = User::find(session()->get("userId"));

        Notification::sendNow($user, new DeleteDocumentNotification($document));

        DB::table('documents')->delete($id);

        header("Location: /documents");
    }


}
