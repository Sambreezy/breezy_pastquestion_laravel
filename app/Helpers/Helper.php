<?php

namespace App\Helpers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Helpers\MediaProcessors;

/**
 * Get page loading dependencies
 *
 */
class Helper
{
	/**
     * Get a list of all columns with data types for specific table
     *
     *  @return array
     */
    public static function attributesList()
    {
        // $table_doctrine = DB::connection()->getDoctrineSchemaManager()->listTableColumns('listings_attributes');
        // if ($table_doctrine) {
        //     foreach ($table_doctrine as $key) {
        //         $name = $key->getName();
        //         $type = $key->getType()->getName();

        //         if ($type == 'integer' || $type == 'boolean') {
        //             $attributes_list[$name] = $type;
        //         }
        //     }
        // }

        // return $attributes_list;
    }

    /**
     * Get general page options from options model
     *
     *  @return array
     */
    public static function optionsList()
    {
        // $options = Option::all();
        // if ($options->isNotEmpty()) {
            
        //     foreach ($options as $option) {
        //         $options_list[$option->option_value] = $option->option_name;
        //     }
        // }

        // return $options_list;
    }

    /**
     * Store multiple images
     * 
     * @param array $user_file
     * @param string $image_dir
     * @param string $identification
     * @param integer $no_to_store
     * @return array $stored_images
     */
    public static function batchStoreImages($user_file, $image_dir, $identification, $no_to_store = 8)
    {
        $no_to_store = is_integer($no_to_store) ? $no_to_store : 8 ;

        if (is_uploaded_file($user_file[0])) {
            // Ensure that only eight(8) or $no_to_store images are saved
            for ($i=0; $i < $no_to_store; $i++) {
                if (isset($user_file[$i])) {
                    
                    // Save image to storage
                    $processed_media = MediaProcessors::storeImage($user_file[$i], $image_dir);
                    if ($processed_media) {
                        $stored_images[] = [
                            'image_name' => $processed_media->file_name,
                            'image_url'  => $processed_media->file_location,
                            'listing_id' => $identification,
                        ];
                    } else {
                        $stored_images[] = [
                            'image_name' => 'no_image.jpg',
                            'image_url'  => 'public/listings_images/no_image.jpg',
                            'listing_id' => $identification,
                        ];
                    }
                } 
            }
        } else {
            $stored_images[] = [
                'image_name' => 'no_image.jpg',
                'image_url'  => 'public/listings_images/no_image.jpg',
                'listing_id' => $identification,
            ];
        }
        return $stored_images;
    }

    /**
     * Unstore multiple images
     * 
     * @param array $user_file
     * @param string $identification
     * @param string $user_id
     * @return array $unstored_images
     * @return boolean false
     */
    public static function batchUnstoreImages($user_file, $identification, $user_id)
    {
        if ($user_file) {
            foreach ($user_file as $key) {

                // Unsave image from storage
                $processed_media = MediaProcessors::unstoreImage($key->image_url);
                if ($processed_media) {
                    $unstored_images[] = true;
                }
            }

        } else {
           return false;
        }
        return $unstored_images;
    }

    /**
     * Check the number of allowed user file upload
     * @param array $all_user_uploaded_images
     * @param integer $no_to_store
     * @return integer
     * @return boolean false
     */
    public static function numberOfAllowedUploads($all_user_uploaded_images, $no_to_store=8)
    {
        if(!isset($all_user_uploaded_images) || is_null($all_user_uploaded_images)){
            return false;
        }

        if ($all_user_uploaded_images){
            $no_user_uploaded_images = count($all_user_uploaded_images);

            if ($no_user_uploaded_images < $no_to_store) {
               return $no_to_store - $no_user_uploaded_images;
            }
        }

        return false;
    }

    /**
     * Calculate resource rating
     * 
     * @param integer $total_voted_users
     * @param integer $total_votes
     * @param integer $max_stars
     * @return integer $rating
     * @return boolean false
     */
    public static function averageRating($total_voted_users, $total_votes, $max_stars = 5)
    {
        if (is_integer($total_voted_users) && is_integer($total_votes) && is_integer($max_stars)) {

            $capacity = $total_voted_users * $max_stars;
            $percentage = $total_votes / $capacity * 100;
            $rating = $percentage * $max_stars / 100;

            return ceil($rating);
        }

        return false;
    }

    /**
     * Escape special characters for a LIKE query.
     *
     * @param string $value
     * @param string $char
     *
     * @return string
     */
    public static function escapeLikeForQuery(string $value, string $char = '\\'): string
    {
        return str_replace(
            [$char, '%', '_'],
            [$char.$char, $char.'%', $char.'_'],
            $value
        );
    }
}