<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PastQuestion;
use App\Models\Image;
use App\Helpers\BatchMediaProcessors;
use App\Http\Requests\ImageStoreRequest;
use App\Http\Requests\ImageUpdateRequest;
use App\Http\Requests\ImageSingleRequest;
use App\Http\Requests\ImageMultipleRequest;

class ImageController extends Controller
{
    protected $NO_ALLOWED_UPLOADS = 10;

    /**
     * Create a new ImageController instance.
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

            // Get all images with all their past questions
            $images = Image::with([
                'pastQuestion',
            ])->orderBy('created_at', 'desc')
            ->take(500)
            ->paginate(10);

        } elseif ($request->input('deleted')){

            // Get all deleted images
            $images = Image::onlyTrashed()
            ->orderBy('created_at', 'desc')
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
            return $this->requestConflict('Currently unable to search for images');
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
         * Past questions images are being returned with both approved and unapproved past questions
         * Unapproved past questions should be separated during render
         */
        if ($request->input('properties')){
            
            // Get all past questions images with all their relations
            $past_questions_images = PastQuestion::where('uploaded_by', auth()->user()->id)
            ->with(['image'])
            ->orderBy('created_at', 'desc')
            ->take(500)
            ->paginate(10);

        } elseif ($request->input('deleted')){

            // Get all deleted past questions images with all their relations
            $past_questions_images = PastQuestion::where('uploaded_by', auth()->user()->id)
            ->onlyTrashed()
            ->with(['image'])
            ->orderBy('created_at', 'desc')
            ->take(500)
            ->paginate(10);

        } else {

            // Get all past questions images with out their relations
            $past_questions_images = Image::where('uploaded_by', auth()->user()->id)
            ->orderBy('created_at', 'desc')
            ->take(500)
            ->paginate(10);
        }

        if ($past_questions_images) {
            
            if (count($past_questions_images) > 0) {
                return $this->success($past_questions_images);
            } else {
               return $this->notFound('Images were not found');
            }

        } else {
            return $this->requestConflict('Currently unable to search for images');
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
    public function store(ImageStoreRequest $request)
    {
        // Validate past question id
        $past_question = PastQuestion::find($request->past_question_id);
        if (!$past_question) {
            return $this->notFound('Image related past question was not found');
        }

        // Validate past question owner
        if ($past_question->uploaded_by !== auth()->user()->id) {
            return $this->authenticationFailure('This image related past question was not uploaded by you');
        }

        // Check if photos were submitted 
        if (!is_null($request->file('photos')) && is_array($request->file('photos'))) {

            // Load pervious uploaded images and count them
            if ($past_question_images = $past_question->load('image')) {
                $number_of_previous_images = count($past_question_images->image->toArray());

                // Calculate new number of allowed image uploads
                if ($number_of_previous_images < $this->NO_ALLOWED_UPLOADS) {
                    $NEW_NO_ALLOWED_UPLOADS = $this->NO_ALLOWED_UPLOADS - $number_of_previous_images;

                    // Add additional parameters to be returned with every successful saved image
                    $additional = [
                        'past_question_id' => $past_question->id,
                        'uploaded_by' => auth()->user()->id,
                    ];

                    // Store new images to server or cloud
                    $processed_images = BatchMediaProcessors::batchStoreImages(
                        $request->file('photos'), 
                        'public/images', 
                        $additional,
                        $NEW_NO_ALLOWED_UPLOADS
                    );

                    // Save past question images
                    if (!$processed_images || !Image::insert($processed_images)) {
                        return $this->requestConflict('Currently unable to save images');
                    }
                } else {
                    return $this->forbiddenAccess('Only a maximum of '.$this->NO_ALLOWED_UPLOADS.' images are allowed');
                }

            } else {
                return $this->requestConflict('Currently unable to process image past question records');
            }
        } else {
           return $this->badRequest('Current request can not be processed');
        }

        // return success
        return $this->actionSuccess('Image was saved');
    }

    /**
     * Display the specified resource.
     *
     * @param  string  $id
     * @return \Illuminate\Http\Response
     */
    public function show(ImageSingleRequest $request)
    {
        $image = Image::with(['pastQuestion'])
        ->find($request->input('id'));

        if ($image) {
            return $this->success($image);
        } else {
            return $this->notFound('Image was not found');
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
    public function update(ImageUpdateRequest $request)
    {
        $image = Image::find($request->input('id'));
        if ($image) {

            // Check if photo was submitted 
            if (!is_null($request->file('photos')) && is_array($request->file('photos'))) {

                // Validate image owner
                if ($image->uploaded_by !== auth()->user()->id) {
                    return $this->authenticationFailure('The original image was not uploaded by you');
                }

                // Add additional parameters to be returned with every successful saved image
                $additional = [
                    'past_question_id' => $image->past_question_id,
                    'uploaded_by' => auth()->user()->id,
                ];

                // Store new images to server or cloud
                $processed_images = BatchMediaProcessors::batchStoreImages(
                    $request->file('photos'), 
                    'public/images', 
                    $additional,
                    $this->NO_ALLOWED_UPLOADS
                );

                // Check if images were processed
                if (!$processed_images) {
                    return $this->requestConflict('Currently unable to update image');
                }

                // Remove previously stored image from server or cloud
                if (config('ovsettings.image_permanent_delete')) {
                    $removed_images = BatchMediaProcessors::batchUnStoreImages([$image]);
                    if (!$removed_images) {
                        return $this->requestConflict('Currently unable to delete old image');
                    }
                }

                // Fill in replacement image details
                $image->image_name = $processed_images[0]['image_name'];
                $image->image_url = $processed_images[0]['image_url'];

                // Save image
                if (!$image->save()) {
                    return $this->requestConflict('Currently unable to update image');
                }
            } else {
                return $this->badRequest('Current request can not be processed');
            }

            // return success
            return $this->actionSuccess('Image was updated');

        } else {
            return $this->notFound('Image was not found');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  string  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(ImageSingleRequest $request)
    {
        $image = Image::find($request->input('id'));

        if ($image) {  

            if ($image->uploaded_by !== auth()->user()->id || $this->USER_LEVEL_3 !== auth()->user()->rank) {
                return $this->authenticationFailure('This image was not uploaded by you');
            }

            if ($image->delete()) {
                return $this->actionSuccess('Image was deleted');
            } else {
                return $this->requestConflict('Currently unable to delete image');
            }

        } else {
            return $this->notFound('Image was not found');
        }
    }

    /**
     * Remove the specified resources from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function batchDestroy(ImageMultipleRequest $request)
    {
        // Gets all the images in the array by id
        $images = Image::whereIn('id', $request->input('images'))->get();
        if ($images) {

            // Deletes all found images
            $filtered = $images->filter(function ($value, $key) {
                if ($value->uploaded_by === auth()->user()->id || $this->USER_LEVEL_3 === auth()->user()->rank) {
                    if ($value->delete()) {
                        return $value;
                    }
                }
            });

            // Check's if any images were deleted
            if (($deleted = count($filtered)) > 0) {
                return $this->actionSuccess("$deleted Image(s) deleted");
            } else {
                return $this->requestConflict('Currently unable to delete image(s)');
            }

        } else {
            return $this->notFound('Image(s) not found');
        }
    }

    /**
     * Return the specified resource from trash.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function restore(ImageSingleRequest $request)
    {
        $image = Image::onlyTrashed()->find($request->input('id'));
        
        if ($image) {  
            
            if ($image->uploaded_by !== auth()->user()->id || $this->USER_LEVEL_3 !== auth()->user()->rank) {
                return $this->authenticationFailure('This image was not uploaded by you');
            }

            if ($image->restore()) {
                return $this->actionSuccess('Image was restored');
            } else {
                return $this->requestConflict('Currently unable to restore image');
            }

        } else {
            return $this->notFound('Image was not found');
        }
    }

    /**
     * Return the specified resources from trash.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function batchRestore(ImageMultipleRequest $request)
    {
        // Gets all the images in the array by id
        $images = Image::onlyTrashed()
        ->whereIn('id', $request->input('images'))
        ->get();

        if ($images) {

            // Restores all found deleted images
            $filtered = $images->filter(function ($value, $key) {
                if ($value->uploaded_by === auth()->user()->id || $this->USER_LEVEL_3 === auth()->user()->rank) {
                    if ($value->restore()) {
                        return $value;
                    }
                }
            });

            // Check's if any images were restored
            if (($restored = count($filtered)) > 0) {
                return $this->actionSuccess("$restored Image(s) restored");
            } else {
                return $this->requestConflict('Currently unable to restore Image(s)');
            }

        } else {
            return $this->notFound('Image(s) not found');
        }
    }

    /**
     * Permanently remove the specified resource from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function permanentDestroy(ImageSingleRequest $request)
    {
        // Check access level
        if ($this->USER_LEVEL_3 !== auth()->user()->rank) {
            return $this->authenticationFailure('Please contact management');
        }

        // Find the image
        $image = Image::find($request->input('id'));
        if ($image) {

            if ($image->forceDelete()) {

                // Remove previously stored images from server or cloud
                if (config('ovsettings.image_permanent_delete')) {
                    $removed_image = BatchMediaProcessors::batchUnStoreImages([$image]);
                    $removed_image = (!$removed_image)? 0 : 1;
                } else { $removed_image = 0; }

                return $this->actionSuccess("Image record was deleted with $removed_image image file(s) removed");
            } else {
                return $this->requestConflict('Currently unable to delete image');
            }

        } else {
            return $this->notFound('Image was not found');
        }
    }

    /**
     * permanently remove the specified resources from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function batchPermanentDestroy(ImageMultipleRequest $request)
    {
        // Check access level
        if ($this->USER_LEVEL_3 !== auth()->user()->rank) {
            return $this->authenticationFailure('Please contact management');
        }

        // Gets all the image in the array by id
        $image = Image::whereIn('id', $request->input('images'))->get();
        if ($image) {

            // Deletes all found image
            $filtered = $image->filter(function ($value, $key) {
                if ($value->forceDelete()) {
                    return $value;
                }
            });

            // Check's if any image were deleted
            if (($deleted = count($filtered)) > 0) {

                // Remove previously stored images from server or cloud
                if (config('ovsettings.image_permanent_delete')) {
                    $removed_images = $filtered->filter(function ($value, $key){
                        if (BatchMediaProcessors::batchUnStoreImages([$value])){
                            return true;
                        }
                    });
                    $removed_images = count($removed_images);
                } else { $removed_images = 0; }

                return $this->actionSuccess("$deleted Image record(s) deleted with $removed_images image file(s) removed");
            } else {
                return $this->requestConflict('Currently unable to delete image(s)');
            }

        } else {
            return $this->notFound('Image(s) not found');
        }
    }
}
