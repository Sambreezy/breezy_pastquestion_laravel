<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use App\Models\User;
use App\Helpers\Helper;
use App\Http\Requests\UserUpdateRequest;
use App\Http\Requests\UserSingleRequest;
use App\Http\Requests\UserMultipleRequest;

class UserController extends Controller
{
    protected $NO_ALLOWED_UPLOADS = 1;

    /**
     * Create a new UserController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api');
        $this->middleware("VerifyRankToken:$this->USER_LEVEL_3", [
            'only' => [
                'index',
                'blockUser',
                'unBlockUser',
                'destroy',
                'batchDestroy',
                'restore',
                'batchRestore',
            ]
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

            // Get all users with all their past questions
            $users = User::with([
                'pastQuestion',
            ])->take(500)
            ->paginate(10);

        } elseif ($request->input('deleted')){

            // Get all deleted users
            $users = User::onlyTrashed()
            ->take(500)
            ->paginate(10);

        } else {

            // Get all users with out their relations
            $users = User::orderBy('created_at', 'desc')
            ->take(500)
            ->paginate(10);
        }

        if ($users) {

            if (count($users) > 0) {
                return $this->success($users);
            } else {
               return $this->notFound('Users were not found');
            }

        } else {
            return $this->requestConflict('Currently unable to search for users');
        }
    }

    /**
     * Block / ban a user from accessing major app service
     * 
     * @param string  $id
     * @return \Illuminate\Http\Response
     */
    public function blockUser(UserSingleRequest $request)
    {
        // Check access level
        if ($this->USER_LEVEL_3 !== auth()->user()->rank) {
            return $this->authenticationFailure('Please contact management');
        }

        // Check user id
        $user = User::find($request->input('id'));
        if (!$user) {
            return $this->notFound('User was not found');
        }

        // Block user
        $user->blocked = (boolean) true;
        if (!$user->save()) {
            return $this->requestConflict('Currently unable to block / ban user');
        }

        // return success
        return $this->actionSuccess('User has been blocked / banned');
    }

    /**
     * Unblock / un-ban a user to access major app service
     * 
     * @param string  $id
     * @return \Illuminate\Http\Response
     */
    public function unBlockUser(UserSingleRequest $request)
    {
        // Check access level
        if ($this->USER_LEVEL_3 !== auth()->user()->rank) {
            return $this->authenticationFailure('Please contact management');
        }

        // Check user id
        $user = User::find($request->input('id'));
        if (!$user) {
            return $this->notFound('User was not found');
        }

        // Unblock user
        $user->blocked = (boolean) false;
        if (!$user->save()) {
            return $this->requestConflict('Currently unable to unblock / un-ban user');
        }

        // return success
        return $this->actionSuccess('User has been unblocked / un-banned');
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
    public function show(UserSingleRequest $request)
    {
        $user = User::with(['pastQuestion'])
        ->find($request->input('id'));

        if ($user) {
            return $this->success($user);
        } else {
            return $this->notFound('User was not found');
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
    public function update(UserUpdateRequest $request)
    {
        $user = User::find($request->input('id'));
        if ($user) {

            // Validate user owner
            if ($user->id !== auth()->user()->id) {
                return $this->authenticationFailure('The profile does not belong to you');
            }

            // Check if photo was submitted 
            if (!is_null($request->file('photos'))) {

                // Store new images to server or cloud
                $processed_images = Helper::batchStoreImages($request->file('photos'), 'public/profile', $user->id, $this->NO_ALLOWED_UPLOADS);
                if (!$processed_images) {
                    return $this->requestConflict('Currently unable to update image');
                }

                // Remove previously stored image from server or cloud
                // if ($user->picture) {
                //     // create a fake collection 
                //     // in order to use the batchUnstoreImages function
                //     $image = collect([['image_url' => $user->picture,'image_name'=> $user->id,]]);

                //     // remove previous user image
                //     $removed_images = Helper::batchUnstoreImages($image);
                //     if (!$removed_images) {
                //         return $this->requestConflict('Currently unable to update image');
                //     }
                // }

                $user->picture = $processed_images[0]['image_url'];
            }

            // clean request
            $request->only(['name','phone','description']);

            // Save user
            $user->fill($request->toArray());
            if ($user->save()) {
                return $this->actionSuccess('Profile was updated');
            } else {
                return $this->requestConflict('Currently unable to update profile');
            }

        } else {
            return $this->notFound('Profile was not found');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  string  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(UserSingleRequest $request)
    {
        // Check access level
        if ($this->USER_LEVEL_3 !== auth()->user()->rank) {
            return $this->authenticationFailure('Please contact management');
        }

        $user = User::find($request->input('id'));
        if ($user) {
            if ($user->delete()) {
                return $this->actionSuccess('User was deleted');
            } else {
                return $this->requestConflict('Currently unable to delete user');
            }

        } else {
            return $this->notFound('User was not found');
        }
    }

    /**
     * Remove the specified resources from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function batchDestroy(UserMultipleRequest $request)
    {
        // Check access level
        if ($this->USER_LEVEL_3 !== auth()->user()->rank) {
            return $this->authenticationFailure('Please contact management');
        }

        // Gets all the users in the array by id
        $users = User::whereIn('id', $request->input('users'))->get();
        if ($users) {

            // Deletes all found users
            $filtered = $users->filter(function ($value, $key) {
                if ($value->delete()) {
                    return $value;
                }
            });

            // Check's if any users were deleted
            if (($deleted = count($filtered)) > 0) {
                return $this->actionSuccess("$deleted User(s) deleted");
            } else {
                return $this->requestConflict('Currently unable to delete user(s)');
            }

        } else {
            return $this->notFound('User(s) not found');
        }
    }

    /**
     * Return the specified resource from trash.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function restore(UserSingleRequest $request)
    {
        // Check access level
        if ($this->USER_LEVEL_3 !== auth()->user()->rank) {
            return $this->authenticationFailure('Please contact management');
        }

        $user = User::onlyTrashed()->find($request->input('id'));
        if ($user) {

            if ($user->restore()) {
                return $this->actionSuccess('User was restored');
            } else {
                return $this->requestConflict('Currently unable to restore user');
            }

        } else {
            return $this->notFound('User was not found');
        }
    }

    /**
     * Return the specified resources from trash.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function batchRestore(UserMultipleRequest $request)
    {
        // Check access level
        if ($this->USER_LEVEL_3 !== auth()->user()->rank) {
            return $this->authenticationFailure('Please contact management');
        }

        // Gets all the users in the array by id
        $users = User::onlyTrashed()
        ->whereIn('id', $request->input('users'))
        ->get();

        if ($users) {

            // Restores all found deleted users
            $filtered = $users->filter(function ($value, $key) {
                if ($value->restore()) {
                    return $value;
                }
            });

            // Check's if any users were restored
            if (($restored = count($filtered)) > 0) {
                return $this->actionSuccess("$restored User(s) restored");
            } else {
                return $this->requestConflict('Currently unable to restore User(s)');
            }

        } else {
            return $this->notFound('User(s) not found');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function permanentDestroy(UserSingleRequest $request)
    {
        // Check access level
        if ($this->USER_LEVEL_3 !== auth()->user()->rank) {
            return $this->authenticationFailure('Please contact management');
        }

        // Find the user
        $user = User::find($request->input('id'));
        if ($user) {

            if ($user->forceDelete()) {
                return $this->actionSuccess('User was deleted');
            } else {
                return $this->requestConflict('Currently unable to delete user');
            }

        } else {
            return $this->notFound('User was not found');
        }
    }

    /**
     * Remove the specified resources from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function batchPermanentDestroy(UserMultipleRequest $request)
    {
        // Check access level
        if ($this->USER_LEVEL_3 !== auth()->user()->rank) {
            return $this->authenticationFailure('Please contact management');
        }

        // Gets all the users in the array by id
        $users = User::whereIn('id', $request->input('users'))->get();
        if ($users) {

            // Deletes all found users
            $filtered = $users->filter(function ($value, $key) {
                if ($value->forceDelete()) {
                    return $value;
                }
            });

            // Check's if any users were deleted
            if (($deleted = count($filtered)) > 0) {
                return $this->actionSuccess("$deleted User(s) deleted");
            } else {
                return $this->requestConflict('Currently unable to delete user(s)');
            }

        } else {
            return $this->notFound('User(s) not found');
        }
    }
}
