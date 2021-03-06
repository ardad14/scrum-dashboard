@extends('layouts.mainLayout')

@section('content')
    <div id="app" class="">
        <documents-list
            :documents="{{ $documents }}"
            :user="{{ $userId }}"
            project="{{ $activeProject ?? ""}}"
            :create_document_notifications="{{ $create_document_notifications }}"
            :delete_document_notifications="{{ $delete_document_notifications }}"
        />
    </div>
@endsection
