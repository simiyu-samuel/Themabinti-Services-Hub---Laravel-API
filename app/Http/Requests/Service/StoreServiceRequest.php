<?php

namespace App\Http\Requests\Service;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;

class StoreServiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Authorization handled by ServicePolicy 'create' method
        return true;
    }

    public function rules(): array
    {
        $user = $this->user();
        // if (!$user || !$user->isSeller() || !$user->seller_package) {
        //     // This scenario should be caught by the middleware/policy, but as a fallback
        //     return [];
        // }

        $packageLimits = config('themabinti.seller_packages.' . $user->seller_package);
        
        if($user->isAdmin()){
            $photosLimit = null;
            $videoLimit = null;
        } else{
            $photosLimit = $packageLimits['photos_per_service'];
            $videoLimit = $packageLimits['video_per_service'];
        }

        $rules = [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:5000'],
            'category' => ['required', 'string', 'max:255'],
            'subcategory' => ['nullable', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0', 'max:1000000'],
            'duration' => ['required', 'integer', 'min:5', 'max:1440'], // 5 minutes to 24 hours
            'location' => ['required', 'string', 'max:255'],
            'is_mobile' => ['boolean'],
            'media_files' => ['array'],
        ];

        if ($photosLimit !== null) {
            $rules['media_files.*'] = [
                'required', // Each file in array is required
                'file',
                Rule::when(fn($value) => in_array($value->getClientMimeType(), ['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/webp']), ['image', 'mimes:jpeg,png,jpg,gif,webp', 'max:2048']), // 2MB for images
                Rule::when(fn($value) => in_array($value->getClientMimeType(), ['video/mp4', 'video/webm']), ['mimes:mp4,webm', 'max:20480']), // 20MB for videos
            ];
            $rules['media_files'][] = 'max:' . ($photosLimit + $videoLimit); // Total files limit
        } else {
            if($user->isAdmin()){
                
            } else{
                // Unlimited, but still apply file type and size limits
                $rules['media_files.*'] = [
                    'required',
                    'file',
                    Rule::when(fn($value) => in_array($value->getClientMimeType(), ['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/webp']), ['image', 'mimes:jpeg,png,jpg,gif,webp', 'max:5120']), // 5MB for images (unlimited package)
                    Rule::when(fn($value) => in_array($value->getClientMimeType(), ['video/mp4', 'video/webm']), ['mimes:mp4,webm', 'max:51200']), // 50MB for videos (unlimited package)
                ];   
            }

        }

        // Custom validation for media limits based on type (image/video)
        $this->addMediaCountValidation($rules, $photosLimit, $videoLimit);

        return $rules;
    }

    protected function addMediaCountValidation(array &$rules, ?int $photosLimit, ?int $videoLimit): void
    {
        if ($this->hasFile('media_files')) {
            $imageCount = 0;
            $videoCount = 0;

            foreach ($this->file('media_files') as $file) {
                if ($file->isValid()) {
                    $mime = $file->getClientMimeType();
                    if (str_starts_with($mime, 'image/')) {
                        $imageCount++;
                    } elseif (str_starts_with($mime, 'video/')) {
                        $videoCount++;
                    }
                }
            }

            if ($photosLimit !== null && $imageCount > $photosLimit) {
                $this->validator->errors()->add('media_files', "You can only upload a maximum of {$photosLimit} images for your current package.");
            }
            if ($videoLimit !== null && $videoCount > $videoLimit) {
                $this->validator->errors()->add('media_files', "You can only upload a maximum of {$videoLimit} video(s) for your current package.");
            }
            if (($photosLimit !== null || $videoLimit !== null) && ($imageCount + $videoCount) > ($photosLimit + $videoLimit)) {
                $this->validator->errors()->add('media_files', "Total media files exceed your package limit.");
            }
        }
    }
}