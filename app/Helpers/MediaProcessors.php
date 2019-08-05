<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Storage;

/**
 * Process, store and delete media.
 */
class MediaProcessors
{
	/**
	 * Handle image file upload
	 * Expects storage folder to have been linked for laravel frameworks
	 * Expects to use Illuminate\Support\Facades\Storage;
	 * @param array $image_file
     * @param string $store_folder
	 * @return boolean false
	 * @return object $image
	 */
	public static function storeImage($image_file, $store_folder)
	{
		if($image_file)
        {
        	// Allowed Parameters
        	$allowed_ext = array('jpg','jpeg','png','gif','bmp');
        	$allowed_size = 2000000;

        	// Validate and store
            $file_name_ext = $image_file->getClientOriginalName();
            $file_name = pathinfo($file_name_ext, PATHINFO_FILENAME);
            $file_size = $image_file->getSize();
            $file_ext  = $image_file->getClientOriginalExtension();
            $file_name_to_store = mt_rand(0,999999).'_'.time().'.'.$file_ext;

            // Check if extension is allowed 
            if (!in_array($file_ext, $allowed_ext) || ($file_size > $allowed_size)) {
            	return false;
			}
			
			// Permanent file storage
			$path = $image_file->storeAS($store_folder, $file_name_to_store);

			// Create an object of the saved image properties
			if ($path) {
				$image = (object)
				[
					'file_name' => $file_name_to_store,
					'file_location' => $path,
				];

				// Return a response
				if (Storage::exists($path)){
					return $image;
				}
				else {
					return false;
				}
			} else {
				return false;
			}
        }
    }

    /**
	 * Handle image file removal
	 * Expects storage folder to have been linked for laravel frameworks
	 * Expects to use Illuminate\Support\Facades\Storage;
	 * @param  string $image_location
	 * @return boolean
	 */
	public static function unstoreImage($image_location)
	{
		if($image_location) {

            // Check if file exists
            if (Storage::exists($image_location)) {

                //  Delete file
                if (Storage::Delete($image_location)) {
                   return true;
                } else {

                    // Try unlink function if storage::delete failed
                    if (@unlink($image_location)) {
                        return true;
                    } else {
                        return false;
                    }
                }
            } else {
                return false;
            }
        }
	}
}