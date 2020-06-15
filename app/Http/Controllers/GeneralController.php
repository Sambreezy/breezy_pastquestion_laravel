<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cache;
use App\Models\PastQuestion;
use App\Helpers\Helper;

class GeneralController extends Controller
{
    /**
     * Create a new DocumentController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api',['only' => ['destroyUniversities']]);
        $this->middleware("VerifyRankToken:$this->USER_LEVEL_3", [
            'only' => ['destroyUniversities']
        ]);
    }

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
            return $this->requestConflict('Currently unable to search for past questions');
        }
    }

    public function sendContactUsMessage(Request $request)
    {
        // Validate user input
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|max:100',
            'name' => 'required|string|max:50',
            'message' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->formProcessingFailure('Message could not be sent',$validator->errors());
        }

            // Get and send client message to customer service
            if (!Helper::sendSimpleMail('key',[
                    'email'=>env('CUSTOMER_SERVICE_MAIL','bobbyaxe61@gmail.com'),
                    'sender'=>$request->input('email'),
                    'name'=>$request->input('name'),
                    'message'=>$request->input('message'), 
                    'topic'=>'contactus'
                ])) {
                return $this->requestConflict('Message could not be sent');
            }

        // Return success
        return $this->actionSuccess('Message has been sent');
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

    /**
     * Display the specified resource.
     *
     * @param void
     * @return \Illuminate\Http\Response
     */
    public function showUniversities()
    {
        $universities_list = Cache::remember('universities', 43200, function () {
            
            // Retrieve universities from json file
            $contents = file_get_contents('../dependencies/universities.json');
            return collect(json_decode($contents, true));
        });

        // Return success
        return $this->success($universities_list);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param void
     * @return \Illuminate\Http\Response
     */
    public function destroyUniversities()
    {
        if (Cache::forget('universities')) {

            // Return success
            return $this->actionSuccess('Cleared universities from cache');
        } else {
            // Return success
            return $this->requestConflict('Unable to clear universities from cache');
        }
    }
}
