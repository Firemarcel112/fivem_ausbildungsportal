<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;

class DocumentsController extends Controller
{

    /**
     * Zeigt ein Dokument an
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Document $document
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function show(Request $request, Document $document)
    {
        $document->load('documentAssign');
        $document_assign = $document->documentAssign;
        $link_type = strtolower($document->documentAssign->getLinkType());
        $is_own_file = auth()->user()->getId() == $document_assign->getLinkId();
        $has_permission = auth()->user()->hasPermissionTo('documents.show.' . $link_type);

        if ($link_type == 'ACCOUNT' && (!$is_own_file || !$has_permission)) {
            abort(403);
        } elseif (!$has_permission) {
            abort(403);
        }

        return response()->file($document->getUrl());
    }
}
