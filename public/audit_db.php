<?php
define('FCPATH', __DIR__ . DIRECTORY_SEPARATOR);
require FCPATH . '../app/Config/Paths.php';
$paths = new Config\Paths();
require rtrim($paths->systemDirectory, '\\/ ') . '/bootstrap.php';
require_once SYSTEMPATH . 'Config/DotEnv.php';
(new CodeIgniter\Config\DotEnv(ROOTPATH))->load();
define('ENVIRONMENT', $_SERVER['CI_ENVIRONMENT'] ?? 'development');
$db = \Config\Database::connect();

$tables = $db->listTables();
echo "Tables found: " . implode(', ', $tables) . "\n\n";

foreach ($tables as $table) {
    echo "TABLE: {$table}\n";
    $fields = $db->getFieldData($table);
    foreach ($fields as $field) {
        $pk = $field->primary_key ? ' PK' : '';
        echo "  - {$field->name} ({$field->type}({$field->max_length})){$pk}\n";
    }
    
    $indexes = $db->getIndexData($table);
    if (!empty($indexes)) {
        echo "  Indexes:\n";
        foreach ($indexes as $index) {
            $fields = implode(', ', $index->fields);
            $type = $index->type;
            echo "    - {$index->name} ({$fields}) [{$type}]\n";
        }
    }
    
    $fks = $db->getForeignKeyData($table);
    if (!empty($fks)) {
        echo "  Foreign Keys:\n";
        foreach ($fks as $fk) {
            $cols = implode(', ', $fk->column_name);
            $foreignCols = implode(', ', $fk->foreign_column_name);
            echo "    - {$fk->constraint_name}: ({$cols}) -> {$fk->foreign_table_name}({$foreignCols})\n";
        }
    }
    echo "\n";
}
