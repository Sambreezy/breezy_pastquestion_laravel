<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PastQuestion;
use App\Models\Document;
use App\Helpers\BatchMediaProcessors;
use App\Http\Requests\DocumentStoreRequest;
use App\Http\Requests\DocumentUpdateRequest;
use App\Http\Requests\DocumentSingleRequest;
use App\Http\Requests\DocumentMultipleRequest;

class DocumentController extends Controller
{
    protected $NO_ALLOWED_UPLOADS = 10;

    /**
     * Create a new DocumentController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api');
        $this->middleware("VerifyRankToken:$this->USER_LEVEL_3", [
            'only' => ['permanentDestroy','batchPermanentDestroy']
        ]);
    }

    /**
     * Display a listing of the resource.
     * 
     * @param  boolean  $properties
     * @param  boolean  $deleted
     * @param  void
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->input('properties')){

            // Get all documents with all their past questions
            $documents = Document::with([
                'pastQuestion',
            ])->orderBy('created_at', 'desc')
            ->take(500)
            ->paginate(10);

        } elseif ($request->input('deleted')){

            // Get all deleted documents
            $documents = Document::onlyTrashed()
            ->orderBy('created_at', 'desc')
            ->take(500)
            ->paginate(10);

        } else {

            // Get all documents with out their relations
            $documents = Document::orderBy('created_at', 'desc')
            ->take(500)
            ->paginate(10);
        }

        if ($documents) {

            if (count($documents) > 0) {
                return $this->success($documents);
            } else {
               return $this->notFound('Documents were not found');
            }

        } else {
            return $this->actionFailure('Currently unable to search for documents');
        }
    }

    /** Display a specific listing of the resource.
     *
     * @param  boolean  $properties
     * @param  boolean  $deleted
     * @return \Illuminate\Http\Response
     */
    public function personalIndex(Request $request)
    {
        /**
         * Past questions documents are being returned with both approved and unapproved past questions
         * Unapproved past questions should be separated during render
         */
        if ($request->input('properties')){
            
            // Get all past questions documents with all their relations
            $past_questions_documents = PastQuestion::where('uploaded_by', auth()->user()->id)
            ->with(['document'])
            ->orderBy('created_at', 'desc')
            ->take(500)
            ->paginate(10);

        } elseif ($request->input('deleted')){

            // Get all deleted past questions documents with all their relations
            $past_questions_documents = PastQuestion::where('uploaded_by', auth()->user()->id)
            ->onlyTrashed()
            ->with(['document'])
            ->orderBy('created_at', 'desc')
            ->take(500)
            ->paginate(10);

        } else {

            // Get all past questions documents with out their relations
            $past_questions_documents = Document::where('uploaded_by', auth()->user()->id)
            ->orderBy('created_at', 'desc')
            ->take(500)
            ->paginate(10);
        }

        if ($past_questions_documents) {
            
            if (count($past_questions_documents) > 0) {
                return $this->success($past_questions_documents);
            } else {
               return $this->notFound('Documents were not found');
            }

        } else {
            return $this->actionFailure('Currently unable to search for documents');
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(DocumentStoreRequest $request)
    {
        // Validate past question id
        $past_question = PastQuestion::find($request->past_question_id);
        if (!$past_question) {
            return $this->notFound('Document related past question was not found');
        }

        // Validate past question owner
        if ($past_question->uploaded_by !== auth()->user()->id) {
            return $this->unauthorized('This document related past question was not uploaded by you');
        }

        // Check if photos were submitted 
        if (!is_null($request->file('docs')) && is_array($request->file('docs'))) {

            // Load pervious uploaded documents and count them
            if ($past_question_documents = $past_question->load('document')) {
                $number_of_previous_documents = count($past_question_documents->document->toArray());

                // Calculate new number of allowed document uploads
                if ($number_of_previous_documents < $this->NO_ALLOWED_UPLOADS) {
                    $NEW_NO_ALLOWED_UPLOADS = $this->NO_ALLOWED_UPLOADS - $number_of_previous_documents;

                    // Add additional parameters to be returned with every successful saved document
                    $additional = [
                        'past_question_id' => $past_question->id,
                        'uploaded_by' => auth()->user()->id,
                    ];

                    // Store new documents to server or cloud
                    $processed_documents = BatchMediaProcessors::batchStoreFiles(
                        $request->file('docs'), 
                        'public/documents',
                        $additional,
                        $NEW_NO_ALLOWED_UPLOADS
                    );

                    // Save past question documents
                    if (!$processed_documents || !Document::insert($processed_documents)) {
                        return $this->actionFailure('Currently unable to save documents');
                    }
                } else {
                    return $this->forbidden('Only a maximum of '.$this->NO_ALLOWED_UPLOADS.' documents are allowed');
                }

            } else {
                return $this->actionFailure('Currently unable to process document past question records');
            }
        } else {
            return $this->failure('Current request can not be processed');
        }

        // return success
        return $this->actionSuccess('Document was saved');
    }

    /**
     * Display the specified resource.
     *
     * @param  string  $id
     * @return \Illuminate\Http\Response
     */
    public function show(DocumentSingleRequest $request)
    {
        $document = Document::with(['pastQuestion'])
        ->find($request->input('id'));

        if ($document) {
            return $this->success($document);
        } else {
            return $this->notFound('Document was not found');
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  string  $id
     * @return \Illuminate\Http\Response
     */
    public function edit()
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $id
     * @return \Illuminate\Http\Response
     */
    public function update(DocumentUpdateRequest $request)
    {
        $document = Document::find($request->input('id'));
        if ($document) {

            // Check if photo was submitted 
            if (!is_null($request->file('docs')) && is_array($request->file('docs'))) {

                // Validate document owner
                if ($document->uploaded_by !== auth()->user()->id) {
                    return $this->unauthorized('The original document was not uploaded by you');
                }

                // Add additional parameters to be returned with every successful saved document
                $additional = [
                    'past_question_id' => $document->past_question_id,
                    'uploaded_by' => auth()->user()->id,
                ];

                $processed_documents = BatchMediaProcessors::batchStoreFiles(
                    $request->file('docs'), 
                    'public/documents',
                    $additional,
                    $this->NO_ALLOWED_UPLOADS
                );

                // Check if documents were processed
                if (!$processed_documents) {
                    return $this->actionFailure('Currently unable to update document');
                }

                // Remove previously stored document from server or cloud
                if (config('ovsettings.document_permanent_delete')) {
                    $removed_documents = BatchMediaProcessors::batchUnStoreFiles([$document]);
                    if (!$removed_documents) {
                        return $this->actionFailure('Currently unable to delete old document');
                    }
                }

                // Fill in replacement document details
                $document->doc_name = $processed_documents[0]['doc_name'];
                $document->doc_url = $processed_documents[0]['doc_url'];

                // Save document
                if (!$document->save()) {
                    return $this->actionFailure('Currently unable to update document');
                }
            } else {
                return $this->failure('Current request can not be processed');
            }

            // return success
            return $this->actionSuccess('Document was updated');

        } else {
            return $this->notFound('Document was not found');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  string  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(DocumentSingleRequest $request)
    {
        $document = Document::find($request->input('id'));

        if ($document) {  

            if ($document->uploaded_by !== auth()->user()->id || $this->USER_LEVEL_3 !== auth()->user()->rank) {
                return $this->unauthorized('This document was not uploaded by you');
            }

            if ($document->delete()) {
                return $this->actionSuccess('Document was deleted');
            } else {
                return $this->actionFailure('Currently unable to delete document');
            }

        } else {
            return $this->notFound('Document was not found');
        }
    }

    /**
     * Remove the specified resources from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function batchDestroy(DocumentMultipleRequest $request)
    {
        // Gets all the documents in the array by id
        $documents = Document::whereIn('id', $request->input('documents'))->get();
        if ($documents) {

            // Deletes all found documents
            $filtered = $documents->filter(function ($value, $key) {
                if ($value->uploaded_by === auth()->user()->id || $this->USER_LEVEL_3 === auth()->user()->rank) {
                    if ($value->delete()) {
                        return $value;
                    }
                }
            });

            // Check's if any documents were deleted
            if (($deleted = count($filtered)) > 0) {
                return $this->actionSuccess("$deleted Documents(s) deleted");
            } else {
                return $this->actionFailure('Currently unable to delete document(s)');
            }

        } else {
            return $this->notFound('Documents(s) not found');
        }
    }

    /**
     * Return the specified resource from trash.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function restore(DocumentSingleRequest $request)
    {
        $document = Document::onlyTrashed()->find($request->input('id'));
        
        if ($document) {  
            
            if ($document->uploaded_by !== auth()->user()->id || $this->USER_LEVEL_3 !== auth()->user()->rank) {
                return $this->unauthorized('This document was not uploaded by you');
            }

            if ($document->restore()) {
                return $this->actionSuccess('Document was restored');
            } else {
                return $this->actionFailure('Currently unable to restore document');
            }

        } else {
            return $this->notFound('Document was not found');
        }
    }

    /**
     * Return the specified resources from trash.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function batchRestore(DocumentMultipleRequest $request)
    {
        // Gets all the documents in the array by id
        $documents = Document::onlyTrashed()
        ->whereIn('id', $request->input('documents'))
        ->get();

        if ($documents) {

            // Restores all found deleted documents
            $filtered = $documents->filter(function ($value, $key) {
                if ($value->uploaded_by === auth()->user()->id || $this->USER_LEVEL_3 === auth()->user()->rank) {
                    if ($value->restore()) {
                        return $value;
                    }
                }
            });

            // Check's if any documents were restored
            if (($restored = count($filtered)) > 0) {
                return $this->actionSuccess("$restored Documents(s) restored");
            } else {
                return $this->actionFailure('Currently unable to restore Documents(s)');
            }

        } else {
            return $this->notFound('Documents(s) not found');
        }
    }

    /**
     * Permanently remove the specified resource from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function permanentDestroy(DocumentSingleRequest $request)
    {
        // Check access level
        if ($this->USER_LEVEL_3 !== auth()->user()->rank) {
            return $this->unauthorized('Please contact management');
        }

        // Find the document
        $document = Document::find($request->input('id'));
        if ($document) {
            
            if ($document->forceDelete()) {
                
                // Remove previously stored document from server or cloud
                if (config('ovsettings.document_permanent_delete')) {
                    $removed_document = BatchMediaProcessors::batchUnStoreFiles([$document]);
                    $removed_document = (!$removed_document)? 0 : 1;
                } else { $removed_document = 0; }

                return $this->actionSuccess("Document record was deleted with $removed_document document file(s) removed");
            } else {
                return $this->actionFailure('Currently unable to delete document');
            }

        } else {
            return $this->notFound('Document was not found');
        }
    }

    /**
     * permanently remove the specified resources from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function batchPermanentDestroy(DocumentMultipleRequest $request)
    {
        // Check access level
        if ($this->USER_LEVEL_3 !== auth()->user()->rank) {
            return $this->unauthorized('Please contact management');
        }

        // Gets all the document in the array by id
        $document = Document::whereIn('id', $request->input('documents'))->get();
        if ($document) {

            // Deletes all found document
            $filtered = $document->filter(function ($value, $key) {
                if ($value->forceDelete()) {
                    return $value;
                }
            });

            // Check's if any document were deleted
            if (($deleted = count($filtered)) > 0) {

                // Remove previously stored documents from server or cloud
                if (config('ovsettings.document_permanent_delete')) {
                    $removed_documents = $filtered->filter(function ($value, $key){
                        if (BatchMediaProcessors::batchUnStoreFiles([$value])){
                            return true;
                        }
                    });
                    $removed_documents = count($removed_documents);
                } else { $removed_documents = 0; }

                return $this->actionSuccess("$deleted Document record(s) deleted with $removed_documents document file(s) removed");
            } else {
                return $this->actionFailure('Currently unable to delete documents(s)');
            }

        } else {
            return $this->notFound('Documents(s) not found');
        }
    }
}
