<?php

namespace App\Http\Requests;

use App\Enums\ConversationType;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class ConversationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required',
            'type' => 'required|in:' . implode(',', array_map(fn($type) => $type->value, ConversationType::cases())), // Apenas permite os valores do enum
            'participant_id' => 'required|exists:users,uuid'
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'O campo nome é obrigatório.',
            'type.required' => 'O campo tipo é obrigatório.',
            'type.in' => 'O tipo selecionado é inválido.',
            'participant_id.required' => 'O identificador do Participante é obrigatório.',
            'participant_id.exists' => 'O identificador do Participante é inválido.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json($validator->errors(), 422));
    }
}
