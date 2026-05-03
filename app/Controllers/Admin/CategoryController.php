<?php

namespace App\Controllers\Admin;

use App\Models\FeedbackCategoryModel;

class CategoryController extends AdminBaseController
{
    public function store()
    {
        $rules = [
            'name'        => 'required|min_length[2]|max_length[100]',
            'description' => 'permit_empty|max_length[500]',
            'color'       => 'permit_empty|regex_match[/^#[0-9a-fA-F]{6}$/]',
        ];

        if (! $this->validateData($this->request->getPost(), $rules)) {
            return redirect()->to(site_url('admin?tab=categories'))->with('error', implode(' ', $this->validator->getErrors()));
        }

        $name        = trim((string) $this->request->getPost('name'));
        $description = trim((string) ($this->request->getPost('description') ?? ''));
        $color       = trim((string) ($this->request->getPost('color') ?? ''));

        $model = new FeedbackCategoryModel();

        // Prevent exact duplicate names (case-insensitive).
        $escapedName = $model->db->escape(strtolower($name));
        $existing = $model->where('LOWER(name) = ' . $escapedName, null, false)->first();
        if ($existing !== null) {
            return redirect()->to(site_url('admin?tab=categories'))->with('error', 'A category with that name already exists.');
        }

        $model->insert([
            'name'        => $name,
            'description' => $description !== '' ? $description : null,
            'color'       => preg_match('/^#[0-9a-fA-F]{6}$/', $color) ? $color : null,
            'is_active'   => 1,
        ]);

        $this->logActivity(
            'category.created',
            'Created feedback category: ' . $name,
            ['target_type' => 'feedback_category']
        );

        return redirect()->to(site_url('admin?tab=categories'))->with('success', 'Category "' . $name . '" created.');
    }

    public function update(int $id)
    {
        $model = new FeedbackCategoryModel();
        $category = $model->find($id);

        if ($category === null) {
            return redirect()->to(site_url('admin?tab=categories'))->with('error', 'Category not found.');
        }

        $rules = [
            'name'        => 'required|min_length[2]|max_length[100]',
            'description' => 'permit_empty|max_length[500]',
            'color'       => 'permit_empty|regex_match[/^#[0-9a-fA-F]{6}$/]',
        ];

        if (! $this->validateData($this->request->getPost(), $rules)) {
            return redirect()->to(site_url('admin?tab=categories'))->with('error', implode(' ', $this->validator->getErrors()));
        }

        $name        = trim((string) $this->request->getPost('name'));
        $description = trim((string) ($this->request->getPost('description') ?? ''));
        $color       = trim((string) ($this->request->getPost('color') ?? ''));

        // Check for duplicate names (excluding this category).
        $escapedName = $model->db->escape(strtolower($name));
        $existing = $model
            ->where('LOWER(name) = ' . $escapedName, null, false)
            ->where('id !=', $id)
            ->first();

        if ($existing !== null) {
            return redirect()->to(site_url('admin?tab=categories'))->with('error', 'Another category with that name already exists.');
        }

        $model->update($id, [
            'name'        => $name,
            'description' => $description !== '' ? $description : null,
            'color'       => preg_match('/^#[0-9a-fA-F]{6}$/', $color) ? $color : null,
        ]);

        $this->logActivity(
            'category.updated',
            'Updated feedback category: ' . $name,
            ['target_type' => 'feedback_category', 'target_id' => $id]
        );

        return redirect()->to(site_url('admin?tab=categories'))->with('success', 'Category updated.');
    }

    public function delete(int $id)
    {
        $model = new FeedbackCategoryModel();
        $category = $model->find($id);

        if ($category === null) {
            return redirect()->to(site_url('admin?tab=categories'))->with('error', 'Category not found.');
        }

        $model->delete($id);

        $this->logActivity(
            'category.deleted',
            'Deleted feedback category: ' . (string) ($category['name'] ?? 'Unknown'),
            ['target_type' => 'feedback_category', 'target_id' => $id]
        );

        return redirect()->to(site_url('admin?tab=categories'))->with('success', 'Category deleted.');
    }

    public function toggleStatus(int $id)
    {
        $model = new FeedbackCategoryModel();
        $category = $model->find($id);

        if ($category === null) {
            return redirect()->to(site_url('admin?tab=categories'))->with('error', 'Category not found.');
        }

        $newStatus = (int) $category['is_active'] === 1 ? 0 : 1;
        $model->update($id, ['is_active' => $newStatus]);

        $label = $newStatus === 1 ? 'activated' : 'deactivated';
        $this->logActivity(
            'category.' . $label,
            ucfirst($label) . ' feedback category: ' . (string) ($category['name'] ?? 'Unknown'),
            ['target_type' => 'feedback_category', 'target_id' => $id]
        );

        return redirect()->to(site_url('admin?tab=categories'))->with('success', 'Category ' . $label . '.');
    }
}
