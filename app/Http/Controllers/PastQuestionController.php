<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\PastQuestion;
use App\Models\Image;
use App\Models\Document;
use App\Models\Comment;
use App\Helpers\Helper;

class PastQuestionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $past_questions = PastQuestion::orderBy('created_at', 'desc')
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
     * Display a specific listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function personalIndex(Request $request)
    {
        $past_questions = PastQuestion::where('uploaded_by', auth()->user()->id)
        ->orderBy('created_at', 'desc')
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
     * @return \Illuminate\Http\Response
     */
    public function multiSearchIndex(Request $request)
    {
        $department = Helper::escapeLikeForQuery($request->input('department'));
        $course_name = Helper::escapeLikeForQuery($request->input('course_name'));
        $course_code = Helper::escapeLikeForQuery($request->input('course_code')); 
        $semester = Helper::escapeLikeForQuery($request->input('semester'));
        $year = Helper::escapeLikeForQuery($request->input('year'));

        $past_questions = PastQuestion::where('department', 'like', $department)
        ->where('course_name', 'like', $course_name)
        ->where('course_code', 'like', $course_code)
        ->where('semester', 'like', $semester)
        ->where('year', 'like', $year)
        ->orderBy('created_at', 'desc')
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
     * @return \Illuminate\Http\Response
     */
    public function singleSearchIndex(Request $request)
    {
        $search = Helper::escapeLikeForQuery($request->input('search'));

        $past_questions = PastQuestion::where('department', 'like', $search)
        ->orWhere('course_name', 'like', $search)
        ->orWhere('course_code', 'like', $search)
        ->orWhere('semester', 'like', $search)
        ->orWhere('year', 'like', $search)
        ->orderBy('created_at', 'desc')
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
    public function store(Request $request)
    {
        $request->merge([
            'user_id' => auth()->user()->id, 
            'uploaded_by' => auth()->user()->id
        ]);

        $past_question = new PastQuestion;
        $past_question->fill($request->toArray());

        if ($past_question->save()) {
            return $this->actionSuccess('Past question was saved');
        } else {
            return $this->actionFailure('Currently unable to save past question');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  string  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        $past_question = PastQuestion::with([
            'image',
            'document',
            'comment',
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
    public function edit(Request $request)
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
    public function update(Request $request, $id)
    {
        $past_question = PastQuestion::find($request->input('id'));

        if ($past_question) {  
            
            if ($past_question->uploaded_by !== auth()->user()->id) {
                return $this->unauthorized('This past question was not uploaded by you');
            }

            $past_question->fill($request->toarray());
            if ($past_question->save()) {
                return $this->actionSuccess('Past question was updated');
            } else {
                return $this->actionFailure('Currently unable to update past question');
            }

        } else {
            return $this->notFound('Past question was not found');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  string  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $past_question = PastQuestion::find($request->input('id'));
        
        if ($past_question) {  
            
            if ($past_question->uploaded_by !== auth()->user()->id) {
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
     * Retrun the specified resource from trash.
     *
     * @param  string  $id
     * @return \Illuminate\Http\Response
     */
    public function restore(Request $request)
    {
        $past_question = PastQuestion::onlyTrashed()->find($request->input('id'));
        
        if ($past_question) {  
            
            if ($past_question->uploaded_by !== auth()->user()->id) {
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
}
