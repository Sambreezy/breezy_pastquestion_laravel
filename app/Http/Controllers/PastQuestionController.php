<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PastQuestion;
use App\Models\Image;
use App\Models\Document;
use App\Helpers\Helper;
use App\Http\Requests\PastQuestionStoreRequest;
use App\Http\Requests\PastQuestionUpdateRequest;
use App\Http\Requests\PastQuestionSingleRequest;
use App\Http\Requests\PastQuestionMultipleRequest;


class PastQuestionController extends Controller
{
    protected $NO_ALLOWED_UPLOADS = 10;

    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api');
        $this->middleware("VerifyRankToken:$this->USER_LEVEL_3", [
            'only' => ['permanentDestroy','batchpermanentDestroy']
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @param  boolean  $status
     * @param  boolean  $properties
     * @param  boolean  $deleted
     * @param  void
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        /**
         * Past questions are being returned with both approved and unapproved past questions
         * Unapproved past questions should be separated during render
         */
        if ($request->input('status')){
            
            // Get all past questions that are active/approved or inactive/unapproved
            $past_questions = PastQuestion::where('approved', (boolean)$request->input('status'))
            ->with([
                'image',
                'document',
            ])->take(500)
            ->paginate(10);

        } elseif ($request->input('properties')){
            
            // Get all past questions with all their relations
            $past_questions = PastQuestion::with([
                'image',
                'document',
            ])->take(500)
            ->paginate(10);

        } elseif ($request->input('deleted')){

            // Get all deleted past questions with all their relations
            $past_questions = PastQuestion::onlyTrashed()->with([
                'image',
                'document',
            ])->take(500)
            ->paginate(10);

        } else {

            // Get all past questions with out their relations
            $past_questions = PastQuestion::orderBy('created_at', 'desc')
            ->take(500)
            ->paginate(10);
        }

        if ($past_questions) {
            
            if (count($past_questions) > 0) {
                return $this->success($past_questions);
            } else {
               return $this->notFound('Past questions were not found');
            }

        } else {
            return $this->actionFailure('Currently unable to search for past questions');
        }
    }

    /**
     * Display a specific listing of the resource.
     *
     * @param  boolean  $status
     * @param  boolean  $properties
     * @param  boolean  $deleted
     * @return \Illuminate\Http\Response
     */
    public function personalIndex(Request $request)
    {
        /**
         * Past questions are being returned with both approved and unapproved past questions
         * Unapproved past questions should be separated during render
         */
        if ($request->input('status')){
            
            // Get all past questions that are active/approved or inactive/unapproved
            $past_questions = PastQuestion::where('approved', (boolean)$request->input('status'))
            ->where('uploaded_by', auth()->user()->id)
            ->with(['image','document',])->take(500)
            ->paginate(10);

        } elseif ($request->input('properties')){
            
            // Get all past questions with all their relations
            $past_questions = PastQuestion::where('uploaded_by', auth()->user()->id)
            ->with(['image','document',])->take(500)
            ->paginate(10);

        } elseif ($request->input('deleted')){

            // Get all deleted past questions with all their relations
            $past_questions = PastQuestion::where('uploaded_by', auth()->user()->id)
            ->onlyTrashed()
            ->with(['image','document',])->take(500)
            ->paginate(10);

        } else {

            // Get all past questions with out their relations
            $past_questions = PastQuestion::where('uploaded_by', auth()->user()->id)
            ->orderBy('created_at', 'desc')
            ->get();
        }

        if ($past_questions) {
            
            if (count($past_questions) > 0) {
                return $this->success($past_questions);
            } else {
               return $this->notFound('Past questions were not found');
            }

        } else {
            return $this->actionFailure('Currently unable to search for past questions');
        }
    }

    /**
     * Search for specific listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function multiSearchIndex(Request $request)
    {
        $department = is_null($request->input('department'))?$request->input('department'):Helper::escapeLikeForQuery($request->input('department'));
        $course_name = is_null($request->input('course_name'))?$request->input('course_name'):Helper::escapeLikeForQuery($request->input('course_name'));
        $course_code = is_null($request->input('course_code'))?$request->input('course_code'):Helper::escapeLikeForQuery($request->input('course_code'));
        $semester = is_null($request->input('semester'))?$request->input('semester'):Helper::escapeLikeForQuery($request->input('semester'));
        $year = is_null($request->input('year'))?$request->input('year'):Helper::escapeLikeForQuery($request->input('year'));

        $past_questions = PastQuestion::where('department', 'like', '%'.$department.'%')
        ->where('course_name', 'like', '%'.$course_name.'%')
        ->where('course_code', 'like', '%'.$course_code.'%')
        ->where('semester', 'like', '%'.$semester.'%')
        ->where('year', 'like', '%'.$year.'%')
        ->orderBy('created_at', 'desc')
        ->take(500)
        ->get();

        if ($past_questions) {
            
            if (count($past_questions) > 0) {
                return $this->success($past_questions);
            } else {
               return $this->notFound('Past questions were not found');
            }

        } else {
            return $this->actionFailure('Currently unable to search for past questions');
        }
    }

    /**
     * Search for specific listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function singleSearchIndex(Request $request)
    {
        $search = is_null($request->input('search'))?$request->input('search'):Helper::escapeLikeForQuery($request->input('search'));

        $past_questions = PastQuestion::where('department', 'like', '%'.$search.'%')
        ->orWhere('course_name', 'like', '%'.$search.'%')
        ->orWhere('course_code', 'like', '%'.$search.'%')
        ->orWhere('semester', 'like', '%'.$search.'%')
        ->orWhere('year', 'like', '%'.$search.'%')
        ->orderBy('created_at', 'desc')
        ->take(500)
        ->get();

        if ($past_questions) {
            
            if (count($past_questions) > 0) {
                return $this->success($past_questions);
            } else {
               return $this->notFound('Past questions were not found');
            }

        } else {
            return $this->actionFailure('Currently unable to search for past questions');
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
    public function store(PastQuestionStoreRequest $request)
    {
        $request->merge([
            'user_id' => auth()->user()->id, 
            'uploaded_by' => auth()->user()->id
        ]);

        $past_question = new PastQuestion;
        $past_question->fill($request->toArray());

        // Save past question details
        if (!$past_question->save()) {
            return $this->actionFailure('Currently unable to save past question');
        }

        // Check if photos were submitted 
        if (!is_null($request->file('photos')) && is_array($request->file('photos'))) {
            $processed_images = Helper::batchStoreImages($request->file('photos'), 'public/images', $past_question->id, auth()->user()->id, $this->NO_ALLOWED_UPLOADS);

            // Save past question images
            if (!$processed_images || !Image::insert($processed_images)) {
                return $this->actionFailure('Currently unable to save images');
            }
        }

        // Check if documents were submitted 
        if (!is_null($request->file('docs')) && is_array($request->file('docs'))) {
            $processed_docs = Helper::batchStoreFiles($request->file('docs'), 'public/documents', $past_question->id, auth()->user()->id, $this->NO_ALLOWED_UPLOADS);

            // Save past question documents
            if (!$processed_docs || !Document::insert($processed_docs)) {
                return $this->actionFailure('Currently unable to save documents');
            }
        }

        // return success
        return $this->actionSuccess('Past question was saved');
    }

    /**
     * Display the specified resource.
     *
     * @param  string  $id
     * @return \Illuminate\Http\Response
     */
    public function show(PastQuestionSingleRequest $request)
    {
        $past_question = PastQuestion::with([
            'image',
            'document',
        ])->find($request->input('id'));

        if ($past_question) {  
            return $this->success($past_question);
        } else {
            return $this->notFound('Past question was not found');
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
     * @return \Illuminate\Http\Response
     */
    public function update(PastQuestionUpdateRequest $request)
    {
        $past_question = PastQuestion::find($request->input('id'));

        if ($past_question) {
            
            // Validate past question owner
            if ($past_question->uploaded_by !== auth()->user()->id) {
                return $this->unauthorized('This past question was not uploaded by you');
            }

            $new_request = $request->except(['id','user_id', 'uploaded_by']);
            $past_question->fill($new_request);

            // Save past question details
            if (!$past_question->save()) {
                return $this->actionFailure('Currently unable to update past question');
            }

            // Check if photos were submitted 
            if (!is_null($request->file('photos')) && is_array($request->file('photos'))) {

                // Load pervious uploaded images and count them
                if ($past_question_images = $past_question->load('image')) {
                    $number_of_previous_images = count($past_question_images->image->toArray());

                    // Calculate new number of allowed image uploads
                    if ($number_of_previous_images < $this->NO_ALLOWED_UPLOADS) {
                        $NEW_NO_ALLOWED_UPLOADS = $this->NO_ALLOWED_UPLOADS - $number_of_previous_images;

                        // Store new images to server or cloud
                        $processed_images = Helper::batchStoreImages($request->file('photos'), 'public/images', $past_question->id, auth()->user()->id, $NEW_NO_ALLOWED_UPLOADS);

                        // Save past question images
                        if (!$processed_images || !Image::insert($processed_images)) {
                            return $this->actionFailure('Currently unable to save images');
                        }
                    } else {
                        return $this->forbidden('Only a maximum of '.$this->NO_ALLOWED_UPLOADS.' images are allowed');
                    }

                } else {
                    return $this->actionFailure('Currently unable to process image records');
                }
            }

            // Check if documents were submitted 
            if (!is_null($request->file('docs')) && is_array($request->file('docs'))) {

                // Load pervious uploaded documents and count them
                if ($past_question_docs = $past_question->load('document') ) {
                    $number_of_previous_docs = count($past_question_docs->document->toArray());

                    // Calculate new number of allowed document uploads
                    if ($number_of_previous_docs < $this->NO_ALLOWED_UPLOADS) {
                        $NEW_NO_ALLOWED_UPLOADS = $this->NO_ALLOWED_UPLOADS - $number_of_previous_docs;

                        // Store new documents to server or cloud
                        $processed_docs = Helper::batchStoreFiles($request->file('docs'), 'public/documents', $past_question->id, auth()->user()->id, $NEW_NO_ALLOWED_UPLOADS);

                        // Save past question documents
                        if (!$processed_docs || !Document::insert($processed_docs)) {
                            return $this->actionFailure('Currently unable to save documents');
                        }
                    } else {
                        return $this->forbidden('Only a maximum of '.$this->NO_ALLOWED_UPLOADS.' documents are allowed');
                    }
                } else {
                    return $this->actionFailure('Currently unable to process document records');
                }
            }

            // return success
            return $this->actionSuccess('Past question was updated');

        } else {
            return $this->notFound('Past question was not found');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function destroy(PastQuestionSingleRequest $request)
    {
        $past_question = PastQuestion::find($request->input('id'));

        if ($past_question) {  

            if ($past_question->uploaded_by !== auth()->user()->id || $this->USER_LEVEL_3 !== auth()->user()->rank) {
                return $this->unauthorized('This past question was not uploaded by you');
            }

            if ($past_question->delete()) {
                return $this->actionSuccess('Past question was deleted');
            } else {
                return $this->actionFailure('Currently unable to delete past question');
            }

        } else {
            return $this->notFound('Past question was not found');
        }
    }

    /**
     * Remove the specified resources from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function batchDestroy(PastQuestionMultipleRequest $request)
    {
        // Gets all the past questions in the array by id
        $past_questions = PastQuestion::whereIn('id', $request->input('past_questions'))->get();
        if ($past_questions) {

            // Deletes all found past questions
            $filtered = $past_questions->filter(function ($value, $key) {
                if ($value->uploaded_by === auth()->user()->id || $this->USER_LEVEL_3 === auth()->user()->rank) {
                    if ($value->delete()) {
                        return $value;
                    }
                }
            });

            // Checkes if any past questions were deleted
            if (($deleted = count($filtered)) > 0) {
                return $this->actionSuccess("$deleted Past question(s) deleted");
            } else {
                return $this->actionFailure('Currently unable to delete past question(s)');
            }

        } else {
            return $this->notfound('Past question(s) not found');
        }
    }

    /**
     * Return the specified resource from trash.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function restore(PastQuestionSingleRequest $request)
    {
        $past_question = PastQuestion::onlyTrashed()->find($request->input('id'));
        
        if ($past_question) {  
            
            if ($past_question->uploaded_by !== auth()->user()->id || $this->USER_LEVEL_3 !== auth()->user()->rank) {
                return $this->unauthorized('This past question was not uploaded by you');
            }

            if ($past_question->restore()) {
                return $this->actionSuccess('Past question was restored');
            } else {
                return $this->actionFailure('Currently unable to restore past question');
            }

        } else {
            return $this->notFound('Past question was not found');
        }
    }

    /**
     * Return the specified resources from trash.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function batchRestore(PastQuestionMultipleRequest $request)
    {
        // Gets all the past questions in the array by id
        $past_questions = PastQuestion::onlyTrashed()
        ->whereIn('id', $request->input('past_questions'))
        ->get();

        if ($past_questions) {

            // Restores all found deleted past questions
            $filtered = $past_questions->filter(function ($value, $key) {
                if ($value->uploaded_by === auth()->user()->id || $this->USER_LEVEL_3 === auth()->user()->rank) {
                    if ($value->restore()) {
                        return $value;
                    }
                }
            });

            // Checkes if any past questions were restored
            if (($restored = count($filtered)) > 0) {
                return $this->actionSuccess("$restored Past question(s) restored");
            } else {
                return $this->actionFailure('Currently unable to restore past question(s)');
            }

        } else {
            return $this->notfound('Past question(s) not found');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function permanentDestroy(PastQuestionSingleRequest $request)
    {
        // Check access level
        if ($this->USER_LEVEL_3 !== auth()->user()->rank) {
            return $this->unauthorized('Please contact management');
        }

        // Find the past question
        $past_question = PastQuestion::find($request->input('id'));
        if ($past_question) {

            if ($past_question->forceDelete()) {
                return $this->actionSuccess('Past question was deleted');
            } else {
                return $this->actionFailure('Currently unable to delete past question');
            }

        } else {
            return $this->notFound('Past question was not found');
        }
    }

    /**
     * Remove the specified resources from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function batchpermanentDestroy(PastQuestionMultipleRequest $request)
    {
        // Check access level
        if ($this->USER_LEVEL_3 !== auth()->user()->rank) {
            return $this->unauthorized('Please contact management');
        }

        // Gets all the past questions in the array by id
        $past_questions = PastQuestion::whereIn('id', $request->input('past_questions'))->get();
        if ($past_questions) {

            // Deletes all found past questions
            $filtered = $past_questions->filter(function ($value, $key) {
                if ($value->forceDelete()) {
                    return $value;
                }
            });

            // Checkes if any past questions were deleted
            if (($deleted = count($filtered)) > 0) {
                return $this->actionSuccess("$deleted Past question(s) deleted");
            } else {
                return $this->actionFailure('Currently unable to delete past question(s)');
            }

        } else {
            return $this->notfound('Past question(s) not found');
        }
    }

}
