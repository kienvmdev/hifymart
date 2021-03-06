<?php

namespace App\Http\Requests;

use Mews\Purifier\Facades\Purifier;
use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
{
  /**
   * Determine if the user is authorized to make this request.
   *
   * @return bool
   */
  public function authorize()
  {
    return true;
  }

  /**
   * Get data to be validated from the request.
   *
   * @return array
   */
  public function validationData()
  {
    $all = parent::validationData();
    if (isset($all['description'])) {
      $all['description'] = Purifier::clean($all['description']);
    }
    if (isset($all['content'])) {
      $all['content'] = Purifier::clean($all['content']);
    }
    return $all;
  }

  /**
   * Get the validation rules that apply to the request.
   *
   * @return array
   */
  public function rules()
  {
    return [
      'name' => 'required|string|max:255',
      'brand_id' => 'required|numeric|exists:brands,id',
      'category_id' => 'required|numeric|exists:categories,id',
      'tax_id' => 'required|numeric|exists:taxes,id',
      'description' => 'nullable|string',
      'content' => 'nullable|string',
      'price' => 'required|numeric|min:0',
      'quantity' => 'required|numeric|min:0',
      'featured_image' => 'image|sometimes|max:10000',
      'images' => 'nullable',
      'images.*' => 'image|max:10000',
    ];
  }

  public function messages()
  {
    return [
      'required' => ':attribute không được bỏ trống',
      'string' => ':attribute phải là chuỗi',
      'name.max' => 'Tên sản phẩm tối đa 255 ký tự',
      'featured_image.max' => 'Ảnh dung lượng tối đa 10mb',
      'price.min' => 'Giá sản phẩm không được âm',
      'quantity.min' => 'Số lượng hàng trong kho không được âm',
      'numeric' => ':attribute phải là số',
      'mimes' => ':attribute chỉ chấp nhận file ảnh',
      'exists' => ':attribute không tồn tại',
    ];
  }

  public function attributes()
  {
    return [
      'name' => 'Tên sản phẩm',
      'brand_id' => 'Nhãn hiệu',
      'category_id' => 'Danh mục',
      'tax_id' => 'Thuế',
      'description' => 'Giới thiệu',
      'price' => 'Giá',
      'quantity' => 'Số lượng hàng trong kho',
      'featured_image' => 'Ảnh đại diện',
      'images' => 'Album ảnh'
    ];
  }
}
