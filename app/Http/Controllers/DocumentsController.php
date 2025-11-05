<?php

namespace App\Http\Controllers;

use Storage;
use Exception;
use App\Facades\Alert;
use App\Models\Document;
use App\Models\DocumentLink;
use App\Models\Qualifications\Qualification;
use App\Models\User;
use App\Models\User\Account;
use App\Services\PdfService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Spatie\LaravelPdf\Facades\Pdf;
use Illuminate\Support\Facades\Log;

class DocumentsController extends Controller
{

    /**
     * Zeigt die Seite an
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Contracts\View\View
     */
    public function index(Request $request)
    {
        $link_types = [
            'edit_permission' => auth()->user()->can('documents.edit'),
            'ACCOUNT' => auth()->user()->can('documents.show.account'),
        ];
        if (!auth()->user()->canAny([
            'documents.show.account',
        ])) {
            abort(403);
        }
        if ($request->isMethod('POST')) {
            $request->flash();
        }
        $search = $request->input('search');
        $items_per_page = $request->input('items_per_page', 20);
        $sort_by = $request->input('sort_by', 'created_at');

        $documents = Document::with('documentAssign')
            ->when(!empty($search), function ($query) use ($search) {
                $query->whereLike('title', '%' . $search . '%');
            })
            ->get()
            ->filter(function ($document) use ($link_types) {
                if (empty($document->documentAssign) && !$link_types['edit_permission']) {
                    return null;
                }
                if (empty($document->documentAssign) && $link_types['edit_permission']) {
                    return $document;
                }
                $doc_link = $document->documentAssign->getLinkType();
                if (empty($link_types[$doc_link] ?? false)) {
                    return null;
                }
                return $document;
            })
            ->each(function ($document) {
                if (Str::contains($document->getTitle(), 'Zertifikat')) {
                    $document->type = 'ZERTIFIKAT';
                }
                if ($document?->documentAssign) {
                    $assign = $document->documentAssign;
                    if ($assign->getLinkType() == 'ACCOUNT') {
                        $assign_model = Account::find($assign->getLinkId());
                        $document->assign = [
                            'name' => $assign_model->getFullName(),
                            'url' => route('profile.show', $assign_model->getId()),
                        ];
                    }
                } else {
                    $document->assign = [
                        'name' => __('general.nicht_zugeordnet'),
                        'url' => null,
                    ];
                }
                return $document;
            });

        switch ($sort_by) {
            case 'created_at':
                $documents = $documents->sortByDesc('created_at');
                break;
            case 'assigned':
                $documents = $documents->sortBy(function ($document) {
                    return $document->assign['url'];
                });
        }

        $genders = [
            [
                'name' => __('general.maennlich') . ' (' . __('general.anrede') . ': ' . __('general.herr') . ')',
                'value' => 'M',
            ],
            [
                'name' => __('general.weiblich') . ' (' . __('general.anrede') . ': ' . __('general.frau') . ')',
                'value' => 'W',
            ],
            [
                'name' => __('general.divers') . ' (' . __('general.anrede') . ': ' . __('general.ohne') . ')',
                'value' => 'D',
            ],
        ];

        $users = Account::get();

        $trainers = User::with(['permissions', 'account'])
            ->withIsTrainer()
            ->get();

        $data = new LengthAwarePaginator(
            $documents->forPage($request->input('page', 1), $items_per_page),
            $documents->count(),
            $items_per_page,
            $request->input('page', 1),
            ['path' => $request->url(), 'query' => $request->query()]
        );

        $qualifications = Cache::rememberForever('qualifications_all', function () {
            return Qualification::isOrderByDefault()
                ->get();
        });

        return view('documents.index', [
            'data' => $data,
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
            $first_name = $user->getFirstName();
            $last_name = $user->getLastName();
            $salutation = $user->getSalutation();
            $birth_date = $user->getDateOfBirth()->format('d.m.Y');
            $birth_location = $user->getBirthLocation();
            $full_name = $user->getFullName();
        } else {
            $genders = [
                [
                    'name' => __('general.maennlich') . ' (' . __('general.anrede') . ': ' . __('general.herr') . ')',
                    'value' => 'M',
                    'salutation' => __('general.herr'),
                ],
                [
                    'name' => __('general.weiblich') . ' (' . __('general.anrede') . ': ' . __('general.frau') . ')',
                    'value' => 'W',
                    'salutation' => __('general.frau'),
                ],
                [
                    'name' => __('general.divers') . ' (' . __('general.anrede') . ': ' . __('general.ohne') . ')',
                    'value' => 'D',
                    'salutation' => '',
                ],
            ];
            $genders = collect($genders);
            $first_name = $request->input('first_name');
            $last_name = $request->input('last_name');
            $salutation = $genders->firstWhere('value', $request->input('gender'))['salutation'];
            $birth_date = Carbon::create($request->input('date_of_birth'))->format('d.m.Y');
            $birth_location = $request->input('birth_location');
            $full_name = $first_name . ' ' . $last_name;
        }
        $trainer = Account::findOrFail($request->input('trainer'));
        $training_date = Carbon::create($request->input('training_date'))->format('d.m.Y');
        $qualification = Qualification::findOrfail($request->input('qualification'));

        $certificate_name = 'Zertifikat_' . str_replace(' ', '_', $full_name);
        $certificate_name = $certificate_name . '_' . $qualification->getName();
        $certificate_name = $certificate_name . '_' . now()->format('Y_m_d_His');

        $certificate_path = Storage::disk('certificates')->path($certificate_name . '.pdf');

        try {
            Pdf::view('certificate.index', [
                'trainer_name' => $trainer->getFullName(),
                'training_date' => $training_date,
                'name' => $full_name,
                'birth_date' => $birth_date,
                'birth_location' => $birth_location,
                'qualification' => $qualification->getName(),
                'salutation' => $salutation,
                'salutation_trainer' => $trainer->getSalutation(),
            ])
                ->format('A4')
                ->paperSize(210, 297)
                ->save($certificate_path);

            $pdf_service = new PdfService();
            $pdf_service->sign($certificate_path);

            $url = Storage::disk('certificates')->path($certificate_name . '.pdf');

            $document_model = new Document();
            $document_model->setTitle('Zertifikat: ' . $qualification->getName());
            $document_model->setUrl($url);
            $document_model->save();

            if (!empty($user)) {
                $document_link = new DocumentLink();
                $document_link->setDocumentId($document_model->getId());
                $document_link->setLinkId($user->getId());
                $document_link->setLinkType('ACCOUNT');
                $document_link->save();
            }
        } catch (Exception $e) {
            if (!empty($document_model)) {
                if ($document_model->exists) {
                    $document_model->delete();
                }
            }
            if (!empty($certificate_path)) {
                if (file_exists($certificate_path)) {
                    unlink($certificate_path);
                }
            }
            Log::error($e);
            throw new Exception($e->getMessage());
        }
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
            } elseif (!$has_permission) {
                abort(403);
            }
        } else {
            if (!auth()->user()->isSuperadmin() || !auth()->user()->hasPermissionTo('documents.edit')) {
                abort(403);
            }
        }

        if (auth()->user()->isSuperadmin() && !$request->has('download')) {
            return response()->file($document->getUrl());
        }
        return response()->download($document->getUrl());
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

        $document->setTitle($request->input('title', $document->getTitle()));
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
        if (file_exists($document->getUrl())) {
            unlink($document->getUrl());
        }
        $document->delete();
        Alert::addAlert(__('general.erfolgreich_geloescht'), 'success');

        return redirect()->back();
    }
}
