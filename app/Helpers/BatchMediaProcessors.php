<?php

namespace App\Helpers;

use App\Helpers\MediaProcessors;
use Ramsey\Uuid\Uuid;

/**
 * Get dependencies
 *
 */
class BatchMediaProcessors extends MediaProcessors
{
    /**
     * Store multiple images
     * $additional must be an array of key value pairs or null
     * 
     * @param collection $user_files
     * @param string $location
     * @param array $additional
     * @param integer $number_to_store
     * @return array $stored_images
     */
    public static function batchStoreImages($user_files, $location, $additional = null, $number_to_store = 8)
    {
        // Check that the inputs meet requirements
        if (!is_integer($number_to_store) || $number_to_store < 1 || $number_to_store > 25) {
            $number_to_store = 8;
        }

        if (is_null($additional)) {
            $additional= array();
        }

        // Save image to storage
        try {
            // Ensure that only eight(8) or $number_to_store images are saved
            for ($i=0; $i < $number_to_store; $i++) {

                if (is_uploaded_file($user_files[$i]) && isset($user_files[$i])) {

                    $processed_media = self::storeImage($user_files[$i], $location);
                    $time = new \DateTime();

                    if ($processed_media) {
                        $result = [
                            'id' => Uuid::uuid4()->toString(),
                            'image_name' => $processed_media->file_name,
                            'image_url'  => $processed_media->file_location,
                            'created_at' => $time->format('Y-m-d H:i:s'),
                            'updated_at' => $time->format('Y-m-d H:i:s'),
                        ];

                        $stored_images[] = array_merge($result, $additional);
                    }
                }
            }
        } catch (\Throwable $th) {

            if (empty($stored_images)) {
                try {
                    $time = new \DateTime();

                    $result = [
                        'id' => Uuid::uuid4()->toString(),
                        'image_name' => 'no_image.jpg',
                        'image_url'  => $location.'/no_image.jpg',
                        'created_at' => $time->format('Y-m-d H:i:s'),
                        'updated_at' => $time->format('Y-m-d H:i:s'),
                    ];

                    $stored_images[] = array_merge($result, $additional);

                } catch (\Throwable $th) {
                    return false;
                }
            }
        }

        return empty($stored_images)? false : $stored_images;
    }

    /**
     * Un-store multiple images
     * Expects collection's objects to contain keys named image_url and image_name
     * 
     * @param collection $user_files
     * @return array $unstored_images
     * @return boolean false
     */
    public static function batchUnStoreImages($user_files)
    {
        try {
            foreach ($user_files as $key) {

                // Remove image from storage
                $processed_media = self::unStoreImage($key->image_url);
                if ($processed_media) {
                    $unstored_images[] = $key->image_name;
                }
            }
        } catch (\Throwable $th) {

            if (empty($unstored_images)) {
                return false;
            }

            return $unstored_images;
        }

        return empty($unstored_images)? false : $unstored_images;
    }

    /**
     * Store multiple files
     * $additional must be an array of key value pairs or null
     * 
     * @param collection $user_files
     * @param string $location
     * @param array $additional
     * @param integer $number_to_store
     * @return array $stored_files
     */
    public static function batchStoreFiles($user_files, $location, $additional = null, $number_to_store = 8)
    {
        // Check that the inputs meet requirements
        if (!is_integer($number_to_store) || $number_to_store < 1 || $number_to_store > 25) {
            $number_to_store = 8;
        }

        if (is_null($additional)) {
            $additional= array();
        }

        // Save files to storage
        try {
            // Ensure that only eight(8) or $number_to_store files are saved
            for ($i=0; $i < $number_to_store; $i++) {

                if (is_uploaded_file($user_files[$i]) && isset($user_files[$i])) {

                    $processed_media = self::storeFile($user_files[$i], $location);
                    $time = new \DateTime();

                    if ($processed_media) {
                        $result = [
                            'id' => Uuid::uuid4()->toString(),
                            'doc_name' => $processed_media->file_name,
                            'doc_url'  => $processed_media->file_location,
                            'created_at' => $time->format('Y-m-d H:i:s'),
                            'updated_at' => $time->format('Y-m-d H:i:s'),
                        ];

                        $stored_files[] = array_merge($result, $additional);

                    }
                }
            }
        } catch (\Throwable $th) {

            if (empty($stored_files)) {
                try {
                    $time = new \DateTime();

                    $result = [
                        'id' => Uuid::uuid4()->toString(),
                        'doc_name' => 'no_image.jpg',
                        'doc_url'  => $location.'/no_image.jpg',
                        'created_at' => $time->format('Y-m-d H:i:s'),
                        'updated_at' => $time->format('Y-m-d H:i:s'),
                    ];

                    $stored_files[] = array_merge($result, $additional);

                } catch (\Throwable $th) {
                    return false;
                }
            }
        }

        return empty($stored_files)? false : $stored_files;
    }

    /**
     * Un-store multiple images
     * Expects collection's objects to contain keys named doc_url and doc_name
     * 
     * @param collection $user_files
     * @return array $unstored_images
     * @return boolean false
     */
    public static function batchUnStoreFiles($user_files)
    {
        try {
            foreach ($user_files as $key) {

                // Remove docs from storage
                $processed_media = self::unStoreFile($key->doc_url);
                if ($processed_media) {
                    $unstored_files[] = $key->doc_name;
                }
            }
        } catch (\Throwable $th) {

            if (empty($unstored_files)) {
                return false;
            }

            return $unstored_files;
        }

        return empty($unstored_files)? false : $unstored_files;
    }
}