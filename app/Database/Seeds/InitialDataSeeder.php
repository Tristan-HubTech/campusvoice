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
<<<<<<< HEAD

        if (! $this->db->tableExists('social_profiles') || ! $this->db->tableExists('social_posts')) {
            return;
        }

        $userRows = $this->db->table('users')->select('id, email, first_name, last_name')->get()->getResultArray();
        $userMap = [];
        foreach ($userRows as $row) {
            $userMap[$row['email']] = $row;
        }

        $profileColors = [
            'sysadmin@campusvoice.local' => 'violet',
            'admin@campusvoice.local' => 'amber',
            'student@campusvoice.local' => 'blue',
        ];

        $profileBios = [
            'sysadmin@campusvoice.local' => 'Keeping the campus conversation organized and visible.',
            'admin@campusvoice.local' => 'Tracking updates and making sure community issues are seen.',
            'student@campusvoice.local' => 'Sharing ideas, reporting issues, and following campus updates.',
        ];

        foreach ($userMap as $email => $row) {
            $exists = $this->db->table('social_profiles')->where('user_id', $row['id'])->get()->getRowArray();
            if ($exists !== null) {
                continue;
            }

            $this->db->table('social_profiles')->insert([
                'user_id'      => $row['id'],
                'bio'          => $profileBios[$email] ?? 'CampusVoice community member.',
                'avatar_color' => $profileColors[$email] ?? 'blue',
                'created_at'   => $now,
                'updated_at'   => $now,
            ]);
        }

        $demoPosts = [
            [
                'email' => 'student@campusvoice.local',
                'body'  => 'The new main website feed is now live. Everyone can post updates, react, comment, and share campus news from one place.',
            ],
            [
                'email' => 'admin@campusvoice.local',
                'body'  => 'If you spot issues around campus, post them here so the whole community can follow the conversation and progress.',
            ],
            [
                'email' => 'sysadmin@campusvoice.local',
                'body'  => 'CampusVoice is now running as a public community feed. Keep posts respectful, useful, and easy for others to respond to.',
            ],
        ];

        $postIds = [];
        foreach ($demoPosts as $post) {
            $user = $userMap[$post['email']] ?? null;
            if ($user === null) {
                continue;
            }

            $exists = $this->db->table('social_posts')
                ->where('user_id', $user['id'])
                ->where('body', $post['body'])
                ->get()
                ->getRowArray();

            if ($exists !== null) {
                $postIds[] = (int) $exists['id'];
                continue;
            }

            $this->db->table('social_posts')->insert([
                'user_id'    => $user['id'],
                'body'       => $post['body'],
                'is_public'  => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            $postIds[] = (int) $this->db->insertID();
        }

        if (count($postIds) >= 3) {
            $reactionSeed = [
                ['post_id' => $postIds[0], 'email' => 'admin@campusvoice.local', 'reaction_type' => 'love'],
                ['post_id' => $postIds[0], 'email' => 'sysadmin@campusvoice.local', 'reaction_type' => 'support'],
                ['post_id' => $postIds[1], 'email' => 'student@campusvoice.local', 'reaction_type' => 'like'],
            ];

            foreach ($reactionSeed as $row) {
                $user = $userMap[$row['email']] ?? null;
                if ($user === null) {
                    continue;
                }

                $exists = $this->db->table('social_post_reactions')
                    ->where('post_id', $row['post_id'])
                    ->where('user_id', $user['id'])
                    ->get()
                    ->getRowArray();

                if ($exists !== null) {
                    continue;
                }

                $this->db->table('social_post_reactions')->insert([
                    'post_id'       => $row['post_id'],
                    'user_id'       => $user['id'],
                    'reaction_type' => $row['reaction_type'],
                    'created_at'    => $now,
                    'updated_at'    => $now,
                ]);
            }

            $commentSeed = [
                ['post_id' => $postIds[0], 'email' => 'admin@campusvoice.local', 'body' => 'This is the kind of main website space the users needed.'],
                ['post_id' => $postIds[0], 'email' => 'sysadmin@campusvoice.local', 'body' => 'Now the feed can work as the public community dashboard.'],
                ['post_id' => $postIds[1], 'email' => 'student@campusvoice.local', 'body' => 'Seeing everything in one feed is much easier than separate pages.'],
            ];

            foreach ($commentSeed as $row) {
                $user = $userMap[$row['email']] ?? null;
                if ($user === null) {
                    continue;
                }

                $exists = $this->db->table('social_post_comments')
                    ->where('post_id', $row['post_id'])
                    ->where('user_id', $user['id'])
                    ->where('body', $row['body'])
                    ->get()
                    ->getRowArray();

                if ($exists !== null) {
                    continue;
                }

                $this->db->table('social_post_comments')->insert([
                    'post_id'    => $row['post_id'],
                    'user_id'    => $user['id'],
                    'body'       => $row['body'],
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }

            $shareSeed = [
                ['post_id' => $postIds[0], 'email' => 'student@campusvoice.local'],
                ['post_id' => $postIds[1], 'email' => 'admin@campusvoice.local'],
            ];

            foreach ($shareSeed as $row) {
                $user = $userMap[$row['email']] ?? null;
                if ($user === null) {
                    continue;
                }

                $exists = $this->db->table('social_post_shares')
                    ->where('post_id', $row['post_id'])
                    ->where('user_id', $user['id'])
                    ->get()
                    ->getRowArray();

                if ($exists !== null) {
                    continue;
                }

                $this->db->table('social_post_shares')->insert([
                    'post_id'    => $row['post_id'],
                    'user_id'    => $user['id'],
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }
=======
>>>>>>> 8f683a475b049c70f2e46bdc1a59b56eb5b110f1
    }
}
