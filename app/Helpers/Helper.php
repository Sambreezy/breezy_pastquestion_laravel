<?php

namespace App\Helpers;

use App\Helpers\MediaProcessors;
use Ramsey\Uuid\Uuid;

/**
 * Get dependencies
 *
 */
class Helper extends MediaProcessors
{
    /**
     * Store multiple images
     * 
     * @param array $user_file
     * @param string $image_dir
     * @param string $identification
     * @param integer $number_to_store
     * @return array $stored_images
     */
    public static function batchStoreImages($user_file, $image_dir, $identification, $user_id, $number_to_store = 8)
    {
        $number_to_store = is_integer($number_to_store) ? $number_to_store : 8 ;

        // If no image is uploaded, try to return a single no image entry
        if (!is_uploaded_file($user_file[0])) {

            try {
                $time = new \DateTime();

                $stored_images[] = [
                    'id' => Uuid::uuid4()->toString(),
                    'image_name' => 'no_image.jpg',
                    'image_url'  => $image_dir.'/no_image.jpg',
                    'past_question_id' => $identification,
                    'uploaded_by' => $user_id,
                    'created_at' => $time->format('Y-m-d H:i:s'),
                    'updated_at' => $time->format('Y-m-d H:i:s'),
                ];
            } catch (\Throwable $th) {
                return false;
            }

            return $stored_images;
        }

        
        // Save image to storage
        try {
            // Ensure that only eight(8) or $number_to_store images are saved
            for ($i=0; $i < $number_to_store; $i++) {

                if (!isset($user_file[$i])) {
                    throw new Exception("No file available");
                }

                $processed_media = self::storeImage($user_file[$i], $image_dir);
                $time = new \DateTime();

                if ($processed_media) {
                    $stored_images[] = [
                        'id' => Uuid::uuid4()->toString(),
                        'image_name' => $processed_media->file_name,
                        'image_url'  => $processed_media->file_location,
                        'past_question_id' => $identification,
                        'created_at' => $time->format('Y-m-d H:i:s'),
                        'updated_at' => $time->format('Y-m-d H:i:s'),
                    ];
                }
            }
        } catch (\Throwable $th) {

            if (empty($stored_images)) {
                try {
                    $time = new \DateTime();

                    $stored_images[] = [
                        'id' => Uuid::uuid4()->toString(),
                        'image_name' => 'no_image.jpg',
                        'image_url'  => $image_dir.'/no_image-1.jpg',
                        'past_question_id' => $identification,
                        'created_at' => $time->format('Y-m-d H:i:s'),
                        'updated_at' => $time->format('Y-m-d H:i:s'),
                    ];
                } catch (\Throwable $th) {
                    return false;
                }
            }
        }

        return empty($stored_images)? false : $stored_images;
    }

    /**
     * Unstore multiple images
     * Expects collection's objects to contain keys named image_url and image_name
     * 
     * @param collection $user_file
     * @return array $unstored_images
     * @return boolean false
     */
    public static function batchUnstoreImages($user_file)
    {
        try {
            foreach ($user_file as $key) {

                // Unsave image from storage
                $unsaved_image = self::unstoreImage($key->image_url);
                if ($unsaved_image) {
                    $unstored_images[] = $key->image_name;
                }
            }
        } catch (\Throwable $th) {

            if (empty($unstored_images)) {
                return false;
            }

            return $unstored_images;
        }

        return $unstored_images;
    }

    /**
     * Store multiple files
     * 
     * @param array $user_file
     * @param string $doc_dir
     * @param string $identification
     * @param integer $number_to_store
     * @return array $stored_files
     */
    public static function batchStoreFiles($user_file, $doc_dir, $identification, $user_id, $number_to_store = 8)
    {
        $number_to_store = is_integer($number_to_store) ? $number_to_store : 8 ;

        // If no doc is uploaded, try to return a single no doc entry
        if (!is_uploaded_file($user_file[0])) {
            return false;
        }
        
        // Save doc to storage
        try {
            // Ensure that only eight(8) or $number_to_store files are saved
            for ($i=0; $i < $number_to_store; $i++) {

                if (!isset($user_file[$i])) {
                    throw new Exception("No file available");
                }

                $saved_file = self::storeFile($user_file[$i], $doc_dir);
                $time = new \DateTime();

                if ($saved_file) {
                    $stored_files[] = [
                        'id' => Uuid::uuid4()->toString(),
                        'doc_name' => $saved_file->file_name,
                        'doc_url'  => $saved_file->file_location,
                        'past_question_id' => $identification,
                        'uploaded_by' => $user_id,
                        'created_at' => $time->format('Y-m-d H:i:s'),
                        'updated_at' => $time->format('Y-m-d H:i:s'),
                    ];
                }
            }
        } catch (\Throwable $th) {

            if (empty($stored_files)) {
                return false;
            }
        }

        return empty($stored_files)? false : $stored_files;
    }

    /**
     * Unstore multiple files
     * Expects collection's objects to contain keys named doc_url and doc_name
     * 
     * @param collection $user_file
     * @return array $unstored_files
     * @return boolean false
     */
    public static function batchUnstoreFiles($user_file)
    {
        try {
            foreach ($user_file as $key) {

                // Unsave file from storage
                $unsaved_file = self::unstoreFile($key->doc_url);
                if ($unsaved_file) {
                    $unstored_files[] = $key->doc_name;
                }
            }
        } catch (\Throwable $th) {

            if (empty($unstored_files)) {
                return false;
            }

            return $unstored_files;
        }

        return $unstored_files;
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