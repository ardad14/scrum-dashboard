<?php

namespace App\Services;

use App\Http\Requests\CreateDocumentRequest;
use App\Models\Document;
use App\Models\User;
use App\Notifications\CreateDocumentNotification;
use App\Notifications\DeleteDocumentNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

class DocumentService
{
    /**
     * Create new document that connect with project and notify user about it
     *
     * @param CreateDocumentRequest $request
     */
    public static function createDocument(CreateDocumentRequest $request): bool
    {
        Carbon::setLocale("rus");

        $documentId = DB::table('documents')->insertGetId([
            'name' => $request['name'],
            'description' => $request['description'],
            'url' => $request['url'],
            'author_id' => session()->get('userId')
        ]);

        DB::table("projects_documents")
            ->insert([
                "project_id" => session()->get('activeProject'),
                "document_id" => $documentId
            ]);

        $user = UserService::getUserBySession();
        $document = Document::find($documentId);

        Notification::sendNow($user, new CreateDocumentNotification($document));

        header("Location: /documents");
        return true;
    }

    /**
     * Get all documents that include at selected project
     *
     * @return string|bool
     */
    public static function getDocumentsByProject(): string|bool
    {
        return json_encode(
            DB::select('select * from documents where id in
                              (select document_id from projects_documents where project_id = ?)',
                [session()->get("activeProject")]
            )
        );
    }

    /**
     * Delete document and notify user about it
     *
     * @param $id
     */
    public static function deleteDocument(Request $request): bool
    {
        $document = Document::find($request->id);
        $user = User::find(session()->get("userId"));

        Notification::sendNow($user, new DeleteDocumentNotification($document));

        DB::table('documents')->delete($request->id);
        return true;
    }

    /**
     * Set at database data about opening document by user
     *
     * @param $id
     */
    public static function openDocument($id): bool
    {
        Carbon::setLocale("rus");

        DB::table('documents')
            ->where('id', $id)
            ->update([
                'last_user_opened' => session()->get("userId"),
                'last_time_opened' => Carbon::now()->toDateTimeString()
            ]);
        return true;
    }

    public static function editDocument(Request $request): bool
    {
        DB::table('documents')
            ->where('id', $request->id)
            ->update([
                'name' => $request->name,
                'description' => $request->description,
                'url' => $request->url
            ]);
        return true;
    }


}
