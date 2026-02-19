<?php

namespace App\Http\Controllers;

use Exception;
use App\DTO\ParticipantDTO;
use App\DTO\TrainerDTO;
use App\Enums\Gender;
use App\Facades\Alert;
use App\Models\Document;
use App\Models\DocumentLink;
use App\Models\Qualifications\Qualification;
use App\Models\User;
use App\Models\User\Account;
use App\Services\DocumentService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class DocumentsController extends Controller
{

    public DocumentService $document_service;

    public function __construct(DocumentService $document_service)
    {
        $this->document_service = $document_service;
    }

    /**
     * Zeigt die Seite an
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Contracts\View\View
     */
    public function index(Request $request)
    {
        $this->checkPermission(['documents.show.account']);

        if ($request->isMethod('POST')) {
            $request->flash();
        }
        $search = $request->input('search');
        $items_per_page = $request->input('items_per_page', 20);
        $sort_by = $request->input('sort_by', 'created_at');

        $can_edit = Auth::user()->can('documents.edit');
        $allowed_types = [];
        if (Auth::user()->can('documents.show.account')) {
            $allowed_types[] = 'ACCOUNT';
        }

        $documents = Document::with([
            'documentAssign',
            'linkedAccount'
        ])
            ->isSearch($search)
            ->joinDocumentAssign()
            ->where(function ($query) use ($can_edit, $allowed_types) {
                $query->when($can_edit, function ($query) use ($can_edit) {
                    return $query->orWhereNull(DocumentLink::getTableName() . '.id');
                })
                    ->when(!empty($allowed_types), function ($query) use ($allowed_types) {
                        return $query->orWhereIn(DocumentLink::getTableName() . '.link_type', $allowed_types);
                    });
            })
            ->when($sort_by == 'assigned', function ($query) {
                return $query->orderByAssigned();
            }, function ($query) use ($sort_by) {
                return $query->orderBy($sort_by, 'desc');
            })
            ->paginate($items_per_page);

        $genders = Gender::forSelect();

        $users = Account::get();

        $trainers = User::withIsTrainer()
            ->get();

        $qualifications = Qualification::getAllQualifications();

        return view('documents.index', [
            'data' => $documents,
            'items_per_page' => $items_per_page,
            'genders' => $genders,
            'users' => $users,
            'qualifications' => $qualifications,
            'trainers' => $trainers,
        ]);
    }

    /**
     * Speichert ein Zertifikat
     *
     * @param \Illuminate\Http\Request $request
     * @throws \Exception
     * @return \Illuminate\Http\RedirectResponse
     */

    public function store(Request $request)
    {
        $this->checkPermission('documents.create');

        if ($request->has('participant') && !empty($request->input('participant'))) {
            $user = Account::findOrFail($request->input('participant'));
            $participant_dto = ParticipantDTO::fromModel($user);
        } else {

            $salutation = Gender::tryFrom($request->input('gender'))->salutation();
            $participant_dto = new ParticipantDTO(
                $salutation,
                $request->input('first_name'),
                $request->input('last_name'),
                Carbon::create($request->input('date_of_birth')),
                $request->input('birth_location'),
            );
        }
        $trainer = Account::findOrFail($request->input('trainer'));
        $training_date = Carbon::create($request->input('training_date'))->format('d.m.Y');
        $qualification = Qualification::findOrfail($request->input('qualification'));

        $trainer_dto = TrainerDTO::fromModel($trainer);

        $document_url = $this->document_service
            ->createCertificate(
                $participant_dto,
                $trainer_dto,
                $qualification->getName(),
                $training_date,
            );
        Document::createDocument(
            'Zertifikat: ' . $qualification->getName(),
            $document_url,
            null,
            $user?->getUserId() ?? null,
            'ACCOUNT',
        );

        Alert::addAlert(__('general.erfolgreich_angelegt'), 'success');

        return redirect()->back();
    }

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
        if (!empty($document_assign)) {
            $link_type = strtolower($document->documentAssign->getLinkType());
            $is_own_file = auth()->user()->getId() == $document_assign->getLinkId();
            $has_permission = auth()->user()->hasPermissionTo('documents.show.' . $link_type);
            if ($link_type == 'ACCOUNT' && (!$is_own_file || !$has_permission)) {
                abort(403);
            }
        } else if (!auth()->user()->hasPermissionTo('documents.edit')) {
            abort(403);
        }

        if (auth()->user()->isSuperadmin() && !$request->has('download')) {
            return response()->file($document->url);
        }
        return response()->download($document->url);
    }

    /**
     * Zeigt die Bearbeiten Seite vom Dokument an
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Document $document
     * @return \Illuminate\Contracts\View\View
     */
    public function edit(Request $request, Document $document)
    {
        $this->checkPermission('documents.edit');

        $document->load('documentAssign');

        $users = Account::get();

        return view('documents.edit', [
            'document' => $document,
            'users' => $users,
        ]);
    }

    /**
     * Aktualisiert ein Dokument
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Document $document
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Document $document)
    {
        $this->checkPermission('documents.edit');

        $document->title = $request->input('title', $document->title);
        if (!empty($request->input('assign'))) {
            $assign = $request->input('assign', $document->documentAssign?->getId() ?? 0);
            if (!empty($document->documentAssign)) {
                $assign_id = $document->documentAssign->getId();
                if ($assign_id != $assign) {
                    $document->documentAssign->delete();
                }
            }

            if (!empty($assign)) {
                $model = new DocumentLink();
                $model->setLinkId($assign);
                $model->setLinkType('ACCOUNT');
                $model->setDocumentId($document->getId());
                $model->save();
            }
        }
        $document->save();
        Alert::addAlert(__('general.erfolgreich_aktualisiert'), 'success');
        return redirect()->back();
    }

    /**
     * Löscht ein Dokument
     *
     * @param \App\Models\Document $document
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Document $document)
    {
        $this->checkPermission('documents.delete');

        if (!empty($document->documentAssign)) {
            $document->documentAssign->delete();
        }
        if (file_exists($document->url)) {
            unlink($document->url);
        }
        $document->delete();
        Alert::addAlert(__('general.erfolgreich_geloescht'), 'success');

        return redirect()->back();
    }
}
