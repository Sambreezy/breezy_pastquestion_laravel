<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PastQuestion;
use App\Helpers\Helper;

class GeneralController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {   
        // Get all past questions with out their relations
        $past_questions = PastQuestion::orderBy('created_at', 'desc')
        ->take(10)
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

    public function sendContactUsMessage(Request $request)
    {
        // Validate user input
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|max:100',
            'name' => 'required|string|max:50',
            'messsage' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->validationFailed('Message could not be sent',$validator->errors());
        }

        // Get and send client message to customer service
        Helper::sendSimpleMail('key',[
            'email'=>env('MAIL_SERVICE', 'bobbyaxe61@gmail.com'),
            'sender'=>$request->input('email'),
            'name'=>$request->input('name'),
            'message'=>$resquest->input('message'), 
            'topic'=>'contactus'
        ]);
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
    public function store()
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  string  $id
     * @return \Illuminate\Http\Response
     */
    public function show()
    {
        //
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
    public function update()
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  string  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy()
    {
        //
    }
}
