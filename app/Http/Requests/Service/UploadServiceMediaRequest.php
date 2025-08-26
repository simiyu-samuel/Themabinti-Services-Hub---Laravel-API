<?php

namespace App\Http\Requests\Service;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\Service; // Assuming Service model is available
use Illuminate\Support\Facades\Log;

class UploadServiceMediaRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Authorization for this specific media upload will be handled by the controller
        // which will use the ServicePolicy to ensure the user owns the service.
        return true;
    }

    public function rules(): array
    {
        $service = $this->route('service'); // Get the service being updated
        $user = $this->user();

        if (!$user || !$user->isSeller() || !$user->seller_package || !$service instanceof Service) {
            return []; // Should be caught by middleware/policy
        }

        $packageLimits = config('themabinti.seller_packages.' . $user->seller_package);
        $photosLimit = $packageLimits['photos_per_service'];
        $videoLimit = $packageLimits['video_per_service'];

        $rules = [
            'media_files' => ['required', 'array'],
            'media_files.*' => [
                'required',
                'file',
                Rule::when(fn($value) => in_array($value->getClientMimeType(), ['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/webp']), ['image', 'mimes:jpeg,png,jpg,gif,webp', 'max:2048']), // 2MB for images
                Rule::when(fn($value) => in_array($value->getClientMimeType(), ['video/mp4', 'video/webm']), ['mimes:mp4,webm', 'max:20480']), // 20MB for videos
            ],
        ];

        // Get current media counts
        $currentImages = 0;
        $currentVideos = 0;
        if (is_array($service->media_files)) {
            foreach ($service->media_files as $fileUrl) {
                $mime = Storage::disk('public')->mimeType(str_replace('/storage/', '', $fileUrl));
                if (str_starts_with($mime, 'image/')) {
                    $currentImages++;
                } elseif (str_starts_with($mime, 'video/')) {
                    $currentVideos++;
                }
            }
        }

        $newImages = 0;
        $newVideos = 0;
        if ($this->hasFile('media_files')) {
            foreach ($this->file('media_files') as $file) {
                if ($file->isValid()) {
                    $mime = $file->getClientMimeType();
                    if (str_starts_with($mime, 'image/')) {
                        $newImages++;
                    } elseif (str_starts_with($mime, 'video/')) {
                        $newVideos++;
                    }
                }
            }
        }

        if ($photosLimit !== null && ($currentImages + $newImages) > $photosLimit) {
            $this->validator->errors()->add('media_files', "Uploading these images would exceed your package's image limit of {$photosLimit}. Current: {$currentImages}, New: {$newImages}.");
        }
        if ($videoLimit !== null && ($currentVideos + $newVideos) > $videoLimit) {
            $this->validator->errors()->add('media_files', "Uploading these videos would exceed your package's video limit of {$videoLimit}. Current: {$currentVideos}, New: {$newVideos}.");
        }
        if (($photosLimit !== null || $videoLimit !== null) && (($currentImages + $newImages) + ($currentVideos + $newVideos)) > ($photosLimit + $videoLimit)) {
                $this->validator->errors()->add('media_files', "Total media files (images + videos) would exceed your package's total media limit.");
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'media_files.required' => 'At least one media file is required.',
            'media_files.*.file' => 'Each item in media files must be a valid file.',
            'media_files.*.image' => 'One or more files are not valid images.',
            'media_files.*.mimes' => 'One or more files have an unsupported format (allowed: jpeg, png, jpg, gif, webp for images; mp4, webm for videos).',
            'media_files.*.max' => 'One or more files are too large. Max size: 2MB for images, 20MB for videos.',
        ];
    }
}