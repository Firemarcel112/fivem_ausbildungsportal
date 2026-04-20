<?php

namespace App\Http\Controllers;

use App\Actions\DeleteDocumentAction;
use App\Actions\UpdateDocumentAction;
use App\DTO\DocumentViewData;
use App\DTO\ParticipantDTO;
use App\DTO\SimpleListItem;
use App\DTO\SimpleUserViewData;
use App\Enums\Gender;
use App\Facades\Alert;
use App\Http\Requests\StoreDocumentRequest;
use App\Http\Requests\UpdateDocumentRequest;
use App\Jobs\CreateCertificate;
use App\Models\Document;
use App\Models\Qualification;
use App\Models\User;
use App\Models\User\Account;
use App\Services\DocumentService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DocumentsController extends Controller
{
    use AuthorizesRequests;

    public DocumentService $document_service;

    public function __construct(DocumentService $document_service)
    {
        $this->document_service = $document_service;
    }

    /**
     * Zeigt die Seite an
     *
     * @param Request $request
     */
    public function index(Request $request)
    {
        $can_edit = $this->authorize('viewAny', Document::class);

        /** @var User $auth_user */
        $auth_user = Auth::user();

        $search = $request->input('search');
        $items_per_page = $request->input('items_per_page', 20);
        $sort_by = $request->input('sort_by', 'created_at');

        $allowed_types = [];
        if ($auth_user->can('documents.show.account')) {
            $allowed_types[] = 'ACCOUNT';
        }

        $documents = Document::query()
            ->filteredList(
                $can_edit->allowed(),
                $allowed_types,
                [
                    'search' => $search,
                    'sort_by' => $sort_by,
                ]
            )
            ->paginate($items_per_page)
            ->map(fn(Document $item) => DocumentViewData::fromModel($item, $auth_user));

        $accounts = Account::query()
            ->get()
            ->map(fn(Account $item) => new SimpleListItem(
                $item->getKey(),
                $item->salutation . ' ' . $item->full_name,
                __('general.geboren_in', [
                    'date' => $item->date_of_birth->format('d.m.Y'),
                    'location' => $item->birth_location,
                ])
            ));

        $trainers = User::getTrainers()
            ->map(fn(User $item) => new SimpleListItem(
                $item->getKey(),
                $item->account->salutation . ' ' . $item->account->full_name,
            ));

        return view('documents.index', [
            'documents' => $documents,
            'items_per_page' => $items_per_page,
            'accounts' => $accounts,
            'trainers' => $trainers,
        ]);
    }

    /**
     * Speichert ein Zertifikat
     *
     * @param  StoreDocumentRequest              $request
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Exception
     */
    public function store(StoreDocumentRequest $request)
    {
        $this->authorize('create', Document::class);

        $account_id = null;

        if ($request->filled('participant')) {
            $account = Account::findOrFail($request->validated('participant'));
            $account_id = $account->getKey();
            $participant_dto = ParticipantDTO::fromModel($account);
        } else {
            $salutation = Gender::tryFrom($request->validated('gender'))->salutation();
            $participant_dto = ParticipantDTO::fromRequest($request, $salutation);
        }

        $trainer = Account::findOrFail($request->validated('trainer'));
        $training_date = $request->date('training_date')->format('d.m.Y');
        $qualification = Qualification::findOrFail($request->validated('qualification_id'));

        $trainer_dto = SimpleUserViewData::fromModel($trainer);

        CreateCertificate::dispatch(
            $participant_dto,
            $trainer_dto,
            $qualification,
            $training_date,
            $account_id,
        );

        Alert::success(__('general.zertifikat_wird_erstellt'));

        return redirect()->action([self::class, 'index']);
    }

    /**
     * Zeigt ein Dokument an
     *
     * @param  \Illuminate\Http\Request                             $request
     * @param  \App\Models\Document                                 $document
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function show(Request $request, Document $document)
    {
        $this->authorize('view', $document);

        /** @var User $auth_user */
        $auth_user = Auth::user();

        if ($auth_user->isSuperadmin() && !$request->has('download')) {
            return response()->file($document->url);
        }

        return response()->download($document->url);
    }

    /**
     * Zeigt die Bearbeiten Seite vom Dokument an
     *
     * @param  \Illuminate\Http\Request        $request
     * @param  \App\Models\Document            $document
     * @return \Illuminate\Contracts\View\View
     */
    public function edit(Request $request, Document $document)
    {
        $this->authorize('update', $document);

        /** @var User $auth_user * */
        $auth_user = Auth::user();

        $document->load('documentAssign');

        $document = DocumentViewData::fromModel($document, $auth_user);

        $accounts = Account::get()
            ->map(fn(Account $item) => new SimpleListItem(
                $item->getKey(),
                str($item->salutation)->append(' ')->append($item->full_name)->value(),
                str("({$item->getKey()})")->value(),
                [
                    'selected' => $item->getKey() == $document->assign_to_id,
                ],
            ));

        return view('documents.edit', compact('document', 'accounts'));
    }

    /**
     * Aktualisiert ein Dokument
     *
     * @param  \Illuminate\Http\Request          $request
     * @param  \App\Models\Document              $document
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateDocumentRequest $request, Document $document, UpdateDocumentAction $update_document_action)
    {
        $this->authorize('update', $document);

        $updated = $update_document_action->execute($document, $request->toArray());

        Alert::addAlert($updated->message, $updated->success ? 'success' : 'danger');

        return redirect()->back();
    }

    /**
     * Löscht ein Dokument
     *
     * @param  \App\Models\Document              $document
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Document $document, DeleteDocumentAction $delete_document_action)
    {
        $this->authorize('delete', $document);

        $deleted = $delete_document_action->execute($document);

        Alert::addAlert($deleted->message, $deleted->success ? 'success' : 'danger');

        return redirect()->back();
    }
}
