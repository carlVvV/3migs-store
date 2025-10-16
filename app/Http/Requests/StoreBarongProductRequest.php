<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBarongProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->role === 'admin';
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|unique:barong_products,name',
            'description' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'sleeve_type' => 'nullable|string|in:Long Sleeve,Short Sleeve',
            'base_price' => 'required|numeric|min:0',
            'special_price' => 'nullable|numeric|min:0',
            'stock' => 'nullable|integer|min:0',
            'size_stocks' => 'nullable|array',
            'size_stocks.S' => 'nullable|integer|min:0',
            'size_stocks.M' => 'nullable|integer|min:0',
            'size_stocks.L' => 'nullable|integer|min:0',
            'size_stocks.XL' => 'nullable|integer|min:0',
            'size_stocks.XXL' => 'nullable|integer|min:0',
            'is_available' => 'boolean',
            'is_featured' => 'boolean',
            'has_variations' => 'boolean',
            'fabric' => 'nullable|array',
            'fabric.*' => 'string|max:255',
            'embroidery_style' => 'nullable|array',
            'embroidery_style.*' => 'string|max:255',
            'colors' => 'nullable|array',
            'colors.*' => 'string|max:255',
            'collar_type' => 'nullable|string|max:255',
            'design_details' => 'nullable|array',
            'design_details.*' => 'string|max:255',
            'video_url' => 'nullable|url',
            'variations' => 'nullable|array',
            'variations.*.size' => 'required_with:variations|string|max:255',
            'variations.*.color' => 'required_with:variations|string|max:255',
            'variations.*.price' => 'required_with:variations|numeric|min:0',
            'variations.*.stock' => 'required_with:variations|integer|min:0',
            'variations.*.sku' => 'nullable|string|max:255|unique:barong_products,sku',
            'images' => 'nullable|array|max:8',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'new_images' => 'nullable|array|max:8',
            'new_images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'cover_image_index' => 'nullable|integer|min:0',
        ];
    }

    /**
     * Get custom validation messages.
     */
    public function messages(): array
    {
        return [
            'name.unique' => 'A product with this name already exists. Please choose a different name.',
            'variations.*.sku.unique' => 'A product with this SKU already exists. Please choose a different SKU.',
            'name.required' => 'Product name is required.',
            'name.max' => 'Product name cannot exceed 255 characters.',
            'category_id.required' => 'Category selection is required.',
            'category_id.exists' => 'Selected category does not exist.',
            'base_price.required' => 'Base price is required.',
            'base_price.numeric' => 'Base price must be a valid number.',
            'base_price.min' => 'Base price cannot be negative.',
            'special_price.numeric' => 'Special price must be a valid number.',
            'special_price.min' => 'Special price cannot be negative.',
            'stock.integer' => 'Stock must be a whole number.',
            'stock.min' => 'Stock cannot be negative.',
            'size_stocks.S.integer' => 'Size S stock must be a whole number.',
            'size_stocks.S.min' => 'Size S stock cannot be negative.',
            'size_stocks.M.integer' => 'Size M stock must be a whole number.',
            'size_stocks.M.min' => 'Size M stock cannot be negative.',
            'size_stocks.L.integer' => 'Size L stock must be a whole number.',
            'size_stocks.L.min' => 'Size L stock cannot be negative.',
            'size_stocks.XL.integer' => 'Size XL stock must be a whole number.',
            'size_stocks.XL.min' => 'Size XL stock cannot be negative.',
            'size_stocks.XXL.integer' => 'Size XXL stock must be a whole number.',
            'size_stocks.XXL.min' => 'Size XXL stock cannot be negative.',
            'images.max' => 'You can upload a maximum of 8 images.',
            'images.*.image' => 'Uploaded files must be valid images.',
            'images.*.mimes' => 'Images must be in JPEG, PNG, JPG, or GIF format.',
            'images.*.max' => 'Each image must be smaller than 2MB.',
            'new_images.max' => 'You can upload a maximum of 8 new images.',
            'new_images.*.image' => 'Uploaded files must be valid images.',
            'new_images.*.mimes' => 'Images must be in JPEG, PNG, JPG, or GIF format.',
            'new_images.*.max' => 'Each image must be smaller than 2MB.',
            'video_url.url' => 'Please enter a valid video URL.',
        ];
    }
}
