<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\PastQuestion;
use App\Models\Image;

class ImageController extends Controller
{
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
     * @param  boolean  $properties
     * @param  boolean  $deleted
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if ($request->input('properties')){

            // Get all images with all their past questions
            $images = Image::with([
                'pastQuestion',
            ])->take(500)
            ->paginate(10);

        } elseif ($request->input('deleted')){

            // Get all deleted images
            $images = Image::onlyTrashed()
            ->take(500)
            ->paginate(10);

        } else {

            // Get all images with out their relations
            $images = Image::orderBy('created_at', 'desc')
            ->take(500)
            ->paginate(10);
        }

        if ($images) {

            if (count($images) > 0) {
                return $this->success($images);
            } else {
               return $this->notFound('Images were not found');
            }

        } else {
            return $this->actionFailure('Currently unable to search for images');
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
