<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use App\Models\PastQuestion;
use App\Models\Comment;
// use App\Helpers\Helper;
use App\Http\Requests\CommentStoreRequest;
use App\Http\Requests\CommentUpdateRequest;
use App\Http\Requests\CommentSingleRequest;
use App\Http\Requests\CommentMultipleRequest;

class CommentController extends Controller
{
    protected $NO_ALLOWED_UPLOADS = 0;

    /**
     * Create a new CommentController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api');
        $this->middleware("VerifyRankToken:$this->USER_LEVEL_3", [
            'only' => ['flaggedCommentIndex','permanentDestroy','batchpermanentDestroy']
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

            // Get all comments with all their past questions
            $comments = Comment::with(['pastQuestion'])
            ->orderBy('created_at', 'desc')
            ->take(500)
            ->paginate(10);

        } elseif ($request->input('deleted')){

            // Get all deleted comments
            $comments = Comment::onlyTrashed()
            ->orderBy('created_at', 'desc')
            ->take(500)
            ->paginate(10);

        } else {

            // Get all comments with out their relations
            $comments = Comment::orderBy('created_at', 'desc')
            ->take(500)
            ->paginate(10);
        }

        if ($comments) {

            if (count($comments) > 0) {
                return $this->success($comments);
            } else {
               return $this->notFound('Comments were not found');
            }

        } else {
            return $this->actionFailure('Currently unable to search for comments');
        }
    }

    /**
     * Display a specific listing of the resource.
     *
     * @param  boolean  $properties
     * @param  boolean  $deleted
     * @return \Illuminate\Http\Response
     */
    public function personalIndex(Request $request)
    {
        /**
         * Past questions comments are being returned with both approved and unapproved past questions
         * Unapproved past questions should be separated during render
         */
        if ($request->input('properties')){
            
            // Get all past questions comments with all their relations
            $past_questions_comments = PastQuestion::whereHas('comment', function(Builder $query){
                $query->where('user_id', '=', auth()->user()->id)
                ->orderBy('created_at', 'desc');
            })->orderBy('created_at', 'desc')
            ->take(500)
            ->paginate(10);

        } elseif ($request->input('deleted')){

            // Get all deleted past questions comments with all their relations
            $past_questions_comments = PastQuestion::onlyTrashed()
            ->whereHas('comment', function(Builder $query){
                $query->where('user_id', '=', auth()->user()->id)
                ->orderBy('created_at', 'desc');
            })->orderBy('created_at', 'desc')
            ->take(500)
            ->paginate(10);

        } else {

            // Get all past questions comments with out their relations
            $past_questions_comments = Comment::where('user_id', auth()->user()->id)
            ->orderBy('created_at', 'desc')
            ->take(500)
            ->paginate(10);
        }

        if ($past_questions_comments) {
            
            if (count($past_questions_comments) > 0) {
                return $this->success($past_questions_comments);
            } else {
               return $this->notFound('Comments were not found');
            }

        } else {
            return $this->actionFailure('Currently unable to search for comments');
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @param  boolean  $properties
     * @param  boolean  $deleted
     * @param  void
     * @return \Illuminate\Http\Response
     */
    public function flaggedCommentIndex(Request $request)
    {
        if ($request->input('properties')){

            // Get all flagged comments with relationship
            $comments = PastQuestion::whereHas('comment', function(Builder $query){
                $query->where('flags', '>', 0)->orderBy('flags', 'desc');
            })->take(500)
            ->paginate(10);

        } elseif ($request->input('deleted')){

            // Get all deleted flagged comments
            $comments = Comment::onlyTrashed()
            ->where('flags','>', 0)
            ->orderBy('flags', 'desc')
            ->take(500)
            ->paginate(10);

        } else {

            // Get all flagged comments
            $comments = Comment::where('flags','>', 0)
            ->orderBy('flags', 'desc')
            ->take(500)
            ->paginate(10);
        }

        if ($comments) {

            if (count($comments) > 0) {
                return $this->success($comments);
            } else {
               return $this->notFound('Comments were not found');
            }

        } else {
            return $this->actionFailure('Currently unable to search for comments');
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
    public function store(CommentStoreRequest $request)
    {
        // check if requirements are met
        if (empty($request->input('comment')) && empty($request->input('reply'))) {
            return $this->failure('A comment or reply is required');
        }

        if (!empty($request->input('comment')) && !empty($request->input('reply'))) {
            return $this->failure('Can not process both comment and reply');
        }

        // Validate past question id
        $past_question = PastQuestion::find($request->past_question_id);
        if (!$past_question) {
            return $this->notFound('Comment related past question was not found');
        }

        // Merge additional required values
        if (!empty($request->input('comment'))) {
            
            $request->only([
                'comment',
                'past_question_id'
            ]);
            
            $request->merge([
                'user_id' => auth()->user()->id,
                'user_name' => auth()->user()->name,
                'user_picture' => auth()->user()->picture
            ]);
        }

        if (!empty($request->input('reply'))) {
            
            $request->only([
                'reply',
                'past_question_id',
                'parent_comment_id',
            ]);

            $request->merge([
                'user_id' => auth()->user()->id,
                'user_name' => auth()->user()->name,
                'user_picture' => auth()->user()->picture
            ]);
        }

        // Create new comment
        $comment = new Comment;
        $comment->fill($request->toArray());

        // Save comment
        if ($comment->save()) {
            return $this->actionSuccess('Comment was added');
        } else {
            return $this->actionFailure('Currently unable to add comment');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  string  $id
     * @return \Illuminate\Http\Response
     */
    public function show(CommentSingleRequest $request)
    {
        $comment = Comment::with(['pastQuestion'])
        ->find($request->input('id'));

        if ($comment) {
            return $this->success($comment);
        } else {
            return $this->notFound('Comment was not found');
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
    public function update(CommentUpdateRequest $request)
    {
        // check if requirements are met
        if (empty($request->input('comment')) && empty($request->input('reply'))) {
            return $this->failure('A comment or reply is required');
        }

        if (!empty($request->input('comment')) && !empty($request->input('reply'))) {
            return $this->failure('Can not process both comment and reply');
        }

        $comment = Comment::find($request->input('id'));
        if ($comment) {

            // Check if original comment was a comment 
            // If so then it can only be edited by a comment
            if ($comment->comment && empty($request->input('comment'))) {
               return $this->failure('Only a comment input can be accepted for this id');
            }

            // Check if original comment was a reply
            // If so then it can only be edited by a reply
            if ($comment->reply && empty($request->input('reply'))) {
               return $this->failure('Only a reply input can be accepted for this id');
            }

            // Validate comment owner
            if ($comment->user_id !== auth()->user()->id) {
                return $this->unauthorized('The comment was not made by you');
            }

            // Merge additional required values
            if (!empty($request->input('comment'))) {
                
                $request->only([
                    'comment',
                ]);
                
                $request->merge([
                    'user_picture' => auth()->user()->picture,
                    'user_name' => auth()->user()->name,
                ]);
            }

            if (!empty($request->input('reply'))) {
                
                $request->only([
                    'reply',
                ]);
                
                $request->merge([
                    'user_picture' => auth()->user()->picture,
                    'user_name' => auth()->user()->name,
                ]);
            }

            // Save comment
            $comment->fill($request->toArray());
            if ($comment->save()) {
                return $this->actionSuccess('Comment was updated');
            } else {
                return $this->actionFailure('Currently unable to update comment');
            }

        } else {
            return $this->notFound('Comment was not found');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  string  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(CommentSingleRequest $request)
    {
        $comment = Comment::find($request->input('id'));

        if ($comment) {  

            if ($comment->user_id !== auth()->user()->id || $this->USER_LEVEL_3 !== auth()->user()->rank) {
                return $this->unauthorized('This comment was not uploaded by you');
            }

            if ($comment->delete()) {
                return $this->actionSuccess('Comment was deleted');
            } else {
                return $this->actionFailure('Currently unable to delete comment');
            }

        } else {
            return $this->notFound('Comment was not found');
        }
    }

    /**
     * Remove the specified resources from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function batchDestroy(CommentMultipleRequest $request)
    {
        // Gets all the comments in the array by id
        $comments = Comment::whereIn('id', $request->input('comments'))->get();
        if ($comments) {

            // Deletes all found comments
            $filtered = $comments->filter(function ($value, $key) {
                if ($value->user_id === auth()->user()->id || $this->USER_LEVEL_3 === auth()->user()->rank) {
                    if ($value->delete()) {
                        return $value;
                    }
                }
            });

            // Checkes if any comments were deleted
            if (($deleted = count($filtered)) > 0) {
                return $this->actionSuccess("$deleted Comment(s) deleted");
            } else {
                return $this->actionFailure('Currently unable to delete comment(s)');
            }

        } else {
            return $this->notfound('Comment(s) not found');
        }
    }

    /**
     * Return the specified resource from trash.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function restore(CommentSingleRequest $request)
    {
        $comment = Comment::onlyTrashed()->find($request->input('id'));
        
        if ($comment) {  
            
            if ($comment->user_id !== auth()->user()->id || $this->USER_LEVEL_3 !== auth()->user()->rank) {
                return $this->unauthorized('This comment was not uploaded by you');
            }

            if ($comment->restore()) {
                return $this->actionSuccess('Comment was restored');
            } else {
                return $this->actionFailure('Currently unable to restore comment');
            }

        } else {
            return $this->notFound('Comment was not found');
        }
    }

    /**
     * Return the specified resources from trash.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function batchRestore(CommentMultipleRequest $request)
    {
        // Gets all the comments in the array by id
        $comments = Comment::onlyTrashed()
        ->whereIn('id', $request->input('comments'))
        ->get();

        if ($comments) {

            // Restores all found deleted comments
            $filtered = $comments->filter(function ($value, $key) {
                if ($value->user_id === auth()->user()->id || $this->USER_LEVEL_3 === auth()->user()->rank) {
                    if ($value->restore()) {
                        return $value;
                    }
                }
            });

            // Checkes if any comments were restored
            if (($restored = count($filtered)) > 0) {
                return $this->actionSuccess("$restored Comment(s) restored");
            } else {
                return $this->actionFailure('Currently unable to restore Comment(s)');
            }

        } else {
            return $this->notfound('Comment(s) not found');
        }
    }

    /**
     * Permanently remove the specified resource from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function permanentDestroy(CommentSingleRequest $request)
    {
        // Check access level
        if ($this->USER_LEVEL_3 !== auth()->user()->rank) {
            return $this->unauthorized('Please contact management');
        }

        // Find the comment
        $comment = Comment::find($request->input('id'));
        if ($comment) {

            if ($comment->forceDelete()) {
                return $this->actionSuccess('Comment was deleted');
            } else {
                return $this->actionFailure('Currently unable to delete comment');
            }

        } else {
            return $this->notFound('Comment was not found');
        }
    }

    /**
     * permanently remove the specified resources from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function batchpermanentDestroy(CommentMultipleRequest $request)
    {
        // Check access level
        if ($this->USER_LEVEL_3 !== auth()->user()->rank) {
            return $this->unauthorized('Please contact management');
        }

        // Gets all the comment in the array by id
        $comment = Comment::whereIn('id', $request->input('comments'))->get();
        if ($comment) {

            // Deletes all found comment
            $filtered = $comment->filter(function ($value, $key) {
                if ($value->forceDelete()) {
                    return $value;
                }
            });

            // Checkes if any comment were deleted
            if (($deleted = count($filtered)) > 0) {
                return $this->actionSuccess("$deleted Comment(s) deleted");
            } else {
                return $this->actionFailure('Currently unable to delete comment(s)');
            }

        } else {
            return $this->notfound('Comment(s) not found');
        }
    }
}
