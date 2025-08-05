<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

abstract class JsonApiRequest extends FormRequest
{
    /**
     * Get the resource type for the request.
     */
    abstract protected function getResourceType(): string;

    /**
     * Get the validation rules for query parameters.
     */
    protected function getQueryRules(): array
    {
        $rules = [];

        if (!empty($this->getAllowedSorts())) {
            $rules['sort'] = [
                'sometimes',
                'string',
                function ($attribute, $value, $fail) {
                    $sortFields = explode(',', $value);
                    foreach ($sortFields as $field) {
                        $fieldName = ltrim($field, '-');
                        if (!in_array($fieldName, $this->getAllowedSorts()))
                            $fail("Sort field $fieldName is not allowed.");
                    }
                },
            ];
        }

        if (!empty($this->getAllowedIncludes())) {
            $rules['include'] = [
                'sometimes',
                'string',
                function ($attribute, $value, $fail) {
                    $includes = explode(',', $value);
                    foreach ($includes as $include) {
                        if (!in_array($include, $this->getAllowedIncludes()))
                            $fail("Include $include is not allowed.");
                    }
                },
            ];
        }

        if (!empty($this->getAllowedFields())) {
            $rules['fields'] = 'sometimes|array';
            $rules['fields.*'] = [
                'string',
                function ($attribute, $value, $fail) {
                    $fields = explode(',', $value);
                    $resourceType = str_replace('fields.', '', $attribute);

                    if (!isset($this->getAllowedFields()[$resourceType])) {
                        $fail("Resource type $resourceType is not supported for sparse fieldsets.");
                        return;
                    }

                    foreach ($fields as $field) {
                        if (!in_array($field, $this->getAllowedFields()[$resourceType]))
                            $fail("Field $field is not allowed for resource type $resourceType.");
                    }
                },
            ];
        }

        if (!empty($this->getAllowedFilters())) {
            $rules['filter'] = 'sometimes|array';
            foreach ($this->getAllowedFilters() as $filter => $rule) {
                $rules["filter.$filter"] = $rule;
            }
        }

        $rules['page'] = 'sometimes|array';
        $rules['page.number'] = 'sometimes|integer|min:1';
        $rules['page.size'] = 'sometimes|integer|min:1|max:100';

        return $rules;
    }

    /**
     * Get the allowed sorts for the resource.
     */
    protected function getAllowedSorts(): array
    {
        return [];
    }

    /**
     * Get the allowed fields for sparse fieldsets.
     */
    protected function getAllowedFields(): array
    {
        return [];
    }

    /**
     * Get the allowed includes for the resource.
     */
    protected function getAllowedIncludes(): array
    {
        return [];
    }

    /**
     * Get the allowed filters for the resource.
     */
    protected function getAllowedFilters(): array
    {
        return [];
    }

    /**
     * Get the allowed attributes for the resource.
     */
    protected function getAllowedAttributes(): array
    {
        return [];
    }

    /**
     * Get the allowed relationships for the resource.
     */
    protected function getAllowedRelationships(): array
    {
        return [];
    }

    /**
     * Get the validation rules for the request body.
     */
    protected function getBodyRules(): array
    {
        if (!$this->filled('data') && !($this->isCreating() || $this->isUpdating()))
            return [];

        $rules = [];
        $resourceType = $this->getResourceType();

        $rules['data'] = 'required|array';
        $rules['data.type'] = "required|string|in:$resourceType";

        if ($this->isUpdating()) $rules['data.id'] = 'required|string';

        $attributeRules = $this->getAttributeRules();
        if (!empty($attributeRules)) {
            $rules['data.attributes'] = $this->isCreating() ? 'required|array' : 'sometimes|array';
            foreach ($attributeRules as $attribute => $rule) {
                $rules["data.attributes.$attribute"] = $rule;
            }
        }

        $relationshipRules = $this->getRelationshipRules();
        if (!empty($relationshipRules)) {
            $rules['data.relationships'] = 'sometimes|array';
            $rules = array_merge($rules, $relationshipRules);
        }

        return $rules;
    }

    /**
     * Determine if the request is for creating a resource.
     */
    protected function isCreating(): bool
    {
        return $this->isMethod('POST');
    }

    /**
     * Determine if the request is for updating a resource.
     */
    protected function isUpdating(): bool
    {
        return $this->isMethod('PATCH') || $this->isMethod('PUT');
    }

    /**
     * Get the validation rules for attributes.
     */
    protected function getAttributeRules(): array
    {
        $allowedAttributes = $this->getAllowedAttributes();
        $rules = [];

        foreach ($allowedAttributes as $attribute => $rule) {
            $rules[$attribute] = $rule;
        }

        return $rules;
    }

    /**
     * Get the validation rules for relationships.
     */
    protected function getRelationshipRules(): array
    {
        $allowedRelationships = $this->getAllowedRelationships();
        $rules = [];

        foreach ($allowedRelationships as $relationship => $config) {
            $relationshipType = $config['type'];
            $isRequired = $config['required'] ?? false;
            $isToMany = $config['to_many'] ?? false;
            $existsRule = $config['exists_rule'] ?? "exists:$relationshipType,id";

            $baseKey = "data.relationships.$relationship";
            $relationshipRequirement = $isRequired ? 'required' : 'sometimes';

            $rules[$baseKey] = "$relationshipRequirement|array";

            $dataKey = "$baseKey.data";
            $rules[$dataKey] = "required_with:$baseKey|array";

            if ($isToMany) $dataKey .= '.*';

            $rules["$dataKey.type"] = "required_with:$baseKey|in:$relationshipType";
            $rules["$dataKey.id"] = "required_with:$baseKey|$existsRule";
        }

        return $rules;
    }

    /**
     * Get validated sort parameters
     */
    public function getSort(): array
    {
        if (!$this->filled('sort')) return [];

        $sorts = [];
        $sortFields = explode(',', $this->input('sort'));

        foreach ($sortFields as $field) {
            if (str_starts_with($field, '-')) {
                $sorts[substr($field, 1)] = 'desc';
            } else {
                $sorts[$field] = 'asc';
            }
        }

        return $sorts;
    }

    /**
     * Get validated include parameters
     */
    public function getIncludes(): array
    {
        return $this->filled('include') ? explode(',', $this->input('include')) : [];
    }

    /**
     * Get validated fields parameters
     */
    public function getFields(): array
    {
        return $this->input('fields', []);
    }

    /**
     * Get validated filter parameters
     */
    public function getFilters(): array
    {
        return $this->input('filter', []);
    }

    /**
     * Get pagination parameters
     */
    public function getPagination(): array
    {
        return [
            'number' => $this->input('page.number', 1),
            'size' => $this->input('page.size', 15),
        ];
    }

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
        return array_merge(
            $this->getQueryRules(),
            $this->getBodyRules(),
        );
    }
}
