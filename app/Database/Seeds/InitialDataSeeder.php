<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class InitialDataSeeder extends Seeder
{
    public function run()
    {
        $now = date('Y-m-d H:i:s');

        $roles = [
            ['name' => 'system_admin', 'description' => 'Full access to all modules'],
            ['name' => 'admin', 'description' => 'Manages feedback and content'],
            ['name' => 'student', 'description' => 'Submits and tracks feedback'],
        ];

        foreach ($roles as $role) {
            $exists = $this->db->table('roles')->where('name', $role['name'])->get()->getRowArray();
            if ($exists !== null) {
                continue;
            }

            $this->db->table('roles')->insert([
                'name'       => $role['name'],
                'description'=> $role['description'],
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        $categories = [
            'Facility',
            'Teacher',
            'Service',
            'Event',
            'Academic',
            'Security',
            'Other',
        ];

        foreach ($categories as $category) {
            $exists = $this->db->table('feedback_categories')->where('name', $category)->get()->getRowArray();
            if ($exists !== null) {
                continue;
            }

            $this->db->table('feedback_categories')->insert([
                'name'       => $category,
                'is_active'  => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        $roleRows = $this->db->table('roles')->select('id, name')->get()->getResultArray();
        $roleMap = [];
        foreach ($roleRows as $row) {
            $roleMap[$row['name']] = (int) $row['id'];
        }

        $defaultUsers = [
            [
                'role'       => 'system_admin',
                'first_name' => 'System',
                'last_name'  => 'Admin',
                'email'      => 'sysadmin@campusvoice.local',
                'password'   => 'Admin@123',
                'student_no' => null,
            ],
            [
                'role'       => 'admin',
                'first_name' => 'School',
                'last_name'  => 'Admin',
                'email'      => 'admin@campusvoice.local',
                'password'   => 'Admin@123',
                'student_no' => null,
            ],
            [
                'role'       => 'student',
                'first_name' => 'Demo',
                'last_name'  => 'Student',
                'email'      => 'student@campusvoice.local',
                'password'   => 'Student@123',
                'student_no' => 'DEMO-0001',
            ],
        ];

        foreach ($defaultUsers as $user) {
            if (! isset($roleMap[$user['role']])) {
                continue;
            }

            $exists = $this->db->table('users')->where('email', $user['email'])->get()->getRowArray();
            if ($exists !== null) {
                continue;
            }

            $this->db->table('users')->insert([
                'role_id'       => $roleMap[$user['role']],
                'student_no'    => $user['student_no'],
                'first_name'    => $user['first_name'],
                'last_name'     => $user['last_name'],
                'email'         => $user['email'],
                'password_hash' => password_hash($user['password'], PASSWORD_DEFAULT),
                'is_active'     => 1,
                'created_at'    => $now,
                'updated_at'    => $now,
            ]);
        }
    }
}
