<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Add missing columns that exist in models but were never migrated:
 * - feedbacks: rejection_reason, reviewed_by, reviewed_at, image_path (via separate migration already exists)
 */
class AddMissingFeedbackColumns extends Migration
{
    public function up()
    {
        $fields = [];

        // Check and add rejection_reason
        if (! $this->db->fieldExists('rejection_reason', 'feedbacks')) {
            $fields['rejection_reason'] = [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'admin_notes',
            ];
        }

        // Check and add reviewed_by
        if (! $this->db->fieldExists('reviewed_by', 'feedbacks')) {
            $fields['reviewed_by'] = [
                'type'       => 'BIGINT',
                'constraint' => 20,
                'unsigned'   => true,
                'null'       => true,
                'after'      => 'rejection_reason',
            ];
        }

        // Check and add reviewed_at
        if (! $this->db->fieldExists('reviewed_at', 'feedbacks')) {
            $fields['reviewed_at'] = [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'reviewed_by',
            ];
        }

        if (! empty($fields)) {
            $this->forge->addColumn('feedbacks', $fields);
        }
    }

    public function down()
    {
        $this->forge->dropColumn('feedbacks', ['rejection_reason', 'reviewed_by', 'reviewed_at']);
    }
}
