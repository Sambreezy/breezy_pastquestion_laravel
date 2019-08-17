<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\PastQuestion;
use App\Models\Comment;
// use App\Helpers\Helper;
use App\Http\Requests\SocialVoteRequest;
use App\Http\Requests\SocialFlagRequest;

class SocialController extends Controller
{
    protected $NO_ALLOWED_UPLOADS = 0;
    protected $UPVOTE_ON_POST = 10;
    protected $UPVOTE_ON_USER = 5;
    protected $DOWNVOTE_ON_POST = 5;
    protected $DOWNVOTE_ON_USER = 3;
    protected $MAX_FLAGS = 5;
    protected $FLAG_VALUE = 1;

    /**
     * Create a new UserController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function upVote(SocialVoteRequest $request)
    {
        // Find the past question
        $past_question = PastQuestion::find($request->input('past_question_id'));
        if ($past_question) {
            $past_question->vote_up = ((integer) $past_question->vote_up) + $this->UPVOTE_ON_POST;

            // Save past question
            if ($past_question->save()) {
                $user = User::find($past_question->uploaded_by);
                if ($user) {
                    $user->votes = ((integer) $user->votes) + $this->UPVOTE_ON_USER;

                    // Save user
                    if ($user->save()) {
                        return $this->actionSuccess('Past question was voted');
                    } else {
                        return $this->actionFailure('Currently unable to up vote user');
                    }

                } else {
                    return $this->notFound('Can not find user');
                }

            } else {
                return $this->actionFailure('Currently unable to up vote past question');
            }
        } else {
            return $this->notFound('Can not find past question');
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function downVote(SocialVoteRequest $request)
    {
        // Find the past question
        $past_question = PastQuestion::find($request->input('past_question_id'));
        if ($past_question) {

            // Ensure that value does not run into negative
            $new_down_vote = ((integer) $past_question->vote_down) - $this->DOWNVOTE_ON_POST;
            if ($new_down_vote <= 0 ) {
               $new_down_vote = 0;
            }
            $past_question->vote_down = $new_down_vote;

            // Save past question
            if ($past_question->save()) {
                $user = User::find($past_question->uploaded_by);
                if ($user) {

                    // Ensure that value does not run into negative
                    $new_vote = ((integer) $user->votes) - $this->DOWNVOTE_ON_USER;
                    if ($new_vote <= 0 ) {
                        $new_vote = 0;
                    }
                    $user->votes = $new_vote;

                    // Save user
                    if ($user->save()) {
                        return $this->actionSuccess('Past question was voted');
                    } else {
                        return $this->actionFailure('Currently unable to down vote user');
                    }

                } else {
                    return $this->notFound('Can not find user');
                }

            } else {
                return $this->actionFailure('Currently unable to down vote past question');
            }
        } else {
            return $this->notFound('Can not find past question');
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function flagComment(SocialFlagRequest $request)
    {
        // Find the comment
        $comment = PastQuestion::find($request->input('comment_id'));
        if ($comment) {
            $comment->flags = ((integer) $comment->flags) + $this->FLAG_VALUE;

            // Save comment
            $comment->save();
            if ($comment) {
                
                // Check if comment has been flagged too many times
                if ($comment->flags >= $this->MAX_FLAGS) {

                    if ($comment->delete()) {
                        return $this->actionSuccess("Thank you, This comment has been flagged too many times and has now been deleted");
                    } else {
                        return $this->actionSuccess("Thank you, This comment has been flagged too many times and will be deleted");
                    }
                }

                // return a success message
                return $this->actionSuccess("Thank you, This comment has been flagged");
            } else {
                return $this->actionFailure('Currently unable to flag comment');
            }
        } else {
            return $this->notFound('Can not find comment');
        }
    }

}
