# 📚 CampusVoice — Database Documentation
### For Study / Research Purposes
> Written in simple language so anyone can understand it.
> Last updated: April 2026

---

## 🗺️ SYSTEM OVERVIEW — How Tables Connect

```
                        ┌─────────┐
                        │  roles  │
                        └────┬────┘
                             │ (every user has a role)
                        ┌────▼────┐
              ┌──────── │  users  │ ────────────────┐
              │         └────┬────┘                 │
              │              │                      │
     ┌────────▼──────┐  ┌────▼──────────┐  ┌───────▼────────┐
     │ social_posts  │  │   feedbacks   │  │ social_profiles │
     └───┬───────────┘  └────┬──────────┘  └────────────────┘
         │                   │
    ┌────┴────────────┐  ┌───▼──────────────┐
    │                 │  │ feedback_replies  │
    │  social_post_   │  │ (admin replies)  │
    │   comments      │  └──────────────────┘
    │                 │
    │  social_post_   │       ┌──────────────────────┐
    │   reactions     │       │   feedback_categories │
    │                 │       └──────────────────────┘
    │  comment_       │
    │   reactions     │
    └─────────────────┘

                   ┌─────────────┐
                   │    users    │ (admin users too)
                   └──────┬──────┘
                          │
          ┌───────────────┼───────────────┐
          │               │               │
 ┌────────▼──────┐ ┌──────▼──────┐ ┌─────▼──────────────┐
 │ announcements │ │admin_activity│ │   password_otps    │
 │               │ │    _logs    │ │ (OTP / reset links)│
 └───────────────┘ └─────────────┘ └────────────────────┘
```

---

## 🔑 KEY CONCEPTS (Simple Explanations)

---

### 📌 What is a Primary Key?
A **Primary Key** is like a student ID number — it is **unique** for every row and never repeats.

> **Example in CampusVoice:**
> In the `users` table, the column `id` is the primary key.
> - User 1 = Kaloy Mavio
> - User 2 = Ralphy Ignacio
> - User 3 = Gian Salas
> No two users can share the same `id`.

---

### 🔗 What is a Foreign Key?
A **Foreign Key** is a column in one table that **points to** the primary key of another table. Think of it as a reference or a link.

> **Example in CampusVoice:**
> In the `feedbacks` table, the column `user_id` is a foreign key.
> It points to `users.id` — telling us *which student* submitted that feedback.
>
> ```
> feedbacks.user_id = 3  →  users.id = 3  →  Gian Salas
> ```

---

### 🔒 What is Hashing?
**Hashing** converts a password into a scrambled string that cannot be reversed.

> **Example:**
> - Real password: `mypassword123`
> - Stored in database: `$2y$10$xKpL9mQ...` (looks like random characters)
>
> Even if someone steals the database, they **cannot read the passwords**.
> When a user logs in, the system hashes what they typed and compares it
> to the stored hash — never storing or comparing plain text.

---

### 🤝 What is a JOIN?
A **JOIN** combines rows from two or more tables based on a matching column.

> **Example:**
> The `feedbacks` table stores `user_id = 3` but not the student's name.
> To show the student's name alongside their feedback, we JOIN both tables:
>
> ```sql
> SELECT feedbacks.message, users.first_name, users.last_name
> FROM feedbacks
> JOIN users ON users.id = feedbacks.user_id;
> -- Result: Shows feedback message + who sent it
> ```

---
---

## 📁 TABLE DOCUMENTATION

---

## 📁 TABLE: `roles`
```
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
```

### 📝 PURPOSE
Stores the different **account types** in the system.
Every user belongs to a role, which decides what they can do.

### 📋 COLUMNS

| Column | Type | Description |
|---|---|---|
| `id` | tinyint | Unique ID for each role *(Primary Key)* |
| `name` | varchar(50) | Role name: `student`, `admin`, `system_admin` |
| `description` | varchar(255) | Short description of what this role can do |
| `created_at` | datetime | When the role was created |
| `updated_at` | datetime | Last time role info was changed |

### 🔗 RELATIONSHIPS
- One role can belong to **many users** (`roles.id → users.role_id`)

### 📊 EXAMPLE QUERIES

```sql
-- 1. See all available roles
SELECT * FROM roles;

-- 2. Count how many users are in each role
SELECT roles.name, COUNT(users.id) AS total
FROM roles
LEFT JOIN users ON users.role_id = roles.id
GROUP BY roles.name;

-- 3. Find which role a specific user has
SELECT users.email, roles.name AS role
FROM users
JOIN roles ON roles.id = users.role_id
WHERE users.email = 'admin@campusvoice.local';
```

---

## 📁 TABLE: `users`
```
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
```

### 📝 PURPOSE
Stores **all account holders** — both students and admins.
When anyone registers or is created in CampusVoice, a row is added here.

### 📋 COLUMNS

| Column | Type | Description |
|---|---|---|
| `id` | bigint | Unique ID for each user *(Primary Key)* |
| `role_id` | tinyint | Which role this user has *(Foreign Key → roles.id)* |
| `student_no` | varchar(50) | Student number (optional, unique per student) |
| `first_name` | varchar(100) | User's first name |
| `last_name` | varchar(100) | User's last name |
| `email` | varchar(150) | Email address — used to log in (must be unique) |
| `password_hash` | varchar(255) | Password stored as a secure hash (never plain text) |
| `phone` | varchar(30) | Phone number (optional) |
| `is_active` | tinyint(1) | `1` = active account, `0` = deactivated |
| `last_login_at` | datetime | Last time this user logged in |
| `created_at` | datetime | When the account was created |
| `updated_at` | datetime | Last time account info was updated |
| `deleted_at` | datetime | If filled in, account is soft-deleted (hidden, not erased) |

> 💡 **Soft Delete** = instead of permanently erasing a user, we just fill `deleted_at` with the date. The data stays in the database but is treated as deleted.

### 🔗 RELATIONSHIPS
- One user has ONE **role** (`users.role_id → roles.id`)
- One user can submit MANY **feedbacks** (`users.id → feedbacks.user_id`)
- One user can create MANY **social posts** (`users.id → social_posts.user_id`)
- One user has ONE **social profile** (`users.id → social_profiles.user_id`)
- One user can have MANY **API tokens** (`users.id → api_tokens.user_id`)
- One user can have MANY **OTP records** (`users.id → password_otps.user_id`)

### 📊 EXAMPLE QUERIES

```sql
-- 1. Get all active students
SELECT first_name, last_name, email
FROM users
JOIN roles ON roles.id = users.role_id
WHERE roles.name = 'student' AND users.is_active = 1;

-- 2. Find a user by email (used during login)
SELECT * FROM users WHERE email = 'student@email.com' LIMIT 1;

-- 3. Count how many students registered this month
SELECT COUNT(*) AS new_students
FROM users
JOIN roles ON roles.id = users.role_id
WHERE roles.name = 'student'
  AND MONTH(users.created_at) = MONTH(NOW())
  AND YEAR(users.created_at) = YEAR(NOW());
```

---

## 📁 TABLE: `feedbacks`
```
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
```

### 📝 PURPOSE
The **heart of CampusVoice**. Stores every feedback, complaint, suggestion,
or praise that students submit to the administration.

### 📋 COLUMNS

| Column | Type | Description |
|---|---|---|
| `id` | bigint | Unique ID for each feedback *(Primary Key)* |
| `user_id` | bigint | Who submitted it *(Foreign Key → users.id)* |
| `category_id` | int | What category it belongs to *(Foreign Key → feedback_categories.id)* |
| `type` | varchar(20) | `complaint`, `suggestion`, or `praise` |
| `subject` | varchar(150) | Short title/subject line (optional) |
| `message` | text | The full feedback message written by the student |
| `image_path` | varchar(500) | File path if the student attached a photo (optional) |
| `is_anonymous` | tinyint(1) | `1` = hide student name, `0` = show name |
| `status` | varchar(20) | Current status: `pending`, `approved`, `rejected`, `reviewed`, `resolved` |
| `submitted_at` | datetime | When the student officially submitted it |
| `resolved_at` | datetime | When admin marked it as resolved |
| `admin_notes` | text | Private notes from admin (students cannot see this) |
| `rejection_reason` | text | If rejected, the reason given to the student |
| `reviewed_by` | bigint | Which admin reviewed it *(Foreign Key → users.id)* |
| `reviewed_at` | datetime | When it was reviewed |
| `created_at` | datetime | When the record was first saved |
| `updated_at` | datetime | Last time this record was changed |
| `deleted_at` | datetime | Soft-delete timestamp (filled = deleted) |

### 🔎 STATUS LIFECYCLE
```
[Student submits]
      ↓
   pending  →  approved  →  reviewed  →  resolved
               ↓
            rejected  (admin gives a reason)
```

### 🔗 RELATIONSHIPS
- Each feedback belongs to ONE **user** (`feedbacks.user_id → users.id`)
- Each feedback belongs to ONE **category** (`feedbacks.category_id → feedback_categories.id`)
- One feedback can have MANY **admin replies** (`feedbacks.id → feedback_replies.feedback_id`)
- One feedback can be linked to ONE **social post** (`feedbacks.id → social_posts.feedback_id`)

### 📊 EXAMPLE QUERIES

```sql
-- 1. Get all pending feedback (needs admin action)
SELECT feedbacks.id, feedbacks.type, feedbacks.message, users.first_name
FROM feedbacks
JOIN users ON users.id = feedbacks.user_id
WHERE feedbacks.status = 'pending'
  AND feedbacks.deleted_at IS NULL;

-- 2. Count feedback by type
SELECT type, COUNT(*) AS total
FROM feedbacks
WHERE deleted_at IS NULL
GROUP BY type;

-- 3. Get one student's full feedback history
SELECT type, subject, status, created_at
FROM feedbacks
WHERE user_id = 3 AND deleted_at IS NULL
ORDER BY created_at DESC;
```

---

## 📁 TABLE: `feedback_categories`
```
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
```

### 📝 PURPOSE
Stores the **categories** that feedback can be filed under.
Examples: Facilities, Events, Academic, Canteen, etc.
Admins can add, edit, or disable categories.

### 📋 COLUMNS

| Column | Type | Description |
|---|---|---|
| `id` | int | Unique ID for each category *(Primary Key)* |
| `name` | varchar(100) | Category name (must be unique) |
| `description` | text | What kinds of feedback belong in this category |
| `is_active` | tinyint(1) | `1` = visible/usable, `0` = hidden from students |
| `created_at` | datetime | When it was created |
| `updated_at` | datetime | Last time it was updated |

### 🔗 RELATIONSHIPS
- One category can have MANY **feedbacks** (`feedback_categories.id → feedbacks.category_id`)

### 📊 EXAMPLE QUERIES

```sql
-- 1. Show all active categories (for the feedback submission form)
SELECT id, name, description
FROM feedback_categories
WHERE is_active = 1
ORDER BY name ASC;

-- 2. Count how many feedbacks are in each category
SELECT feedback_categories.name, COUNT(feedbacks.id) AS total
FROM feedback_categories
LEFT JOIN feedbacks ON feedbacks.category_id = feedback_categories.id
  AND feedbacks.deleted_at IS NULL
GROUP BY feedback_categories.name;

-- 3. Get all inactive categories (disabled by admin)
SELECT name FROM feedback_categories WHERE is_active = 0;
```

---

## 📁 TABLE: `feedback_replies`
```
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
```

### 📝 PURPOSE
Stores **admin replies to feedback**. When an admin responds to a student's
complaint or suggestion, the reply is saved here and shown to the student.

### 📋 COLUMNS

| Column | Type | Description |
|---|---|---|
| `id` | bigint | Unique ID for each reply *(Primary Key)* |
| `feedback_id` | bigint | Which feedback this is a reply to *(Foreign Key → feedbacks.id)* |
| `admin_user_id` | bigint | Which admin wrote this reply *(Foreign Key → users.id)* |
| `message` | text | The reply message written by the admin |
| `created_at` | datetime | When the reply was sent |
| `updated_at` | datetime | Last time the reply was edited |

### 🔗 RELATIONSHIPS
- Each reply belongs to ONE **feedback** (`feedback_replies.feedback_id → feedbacks.id`)
- Each reply is written by ONE **admin user** (`feedback_replies.admin_user_id → users.id`)

### 📊 EXAMPLE QUERIES

```sql
-- 1. Get all replies for a specific feedback
SELECT feedback_replies.message, users.first_name AS admin_name, feedback_replies.created_at
FROM feedback_replies
JOIN users ON users.id = feedback_replies.admin_user_id
WHERE feedback_replies.feedback_id = 5
ORDER BY feedback_replies.created_at ASC;

-- 2. Count how many feedbacks have been replied to
SELECT COUNT(DISTINCT feedback_id) AS replied_count FROM feedback_replies;

-- 3. Get the most recent reply across all feedbacks
SELECT * FROM feedback_replies ORDER BY created_at DESC LIMIT 1;
```

---

## 📁 TABLE: `announcements`
```
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
```

### 📝 PURPOSE
Stores **official announcements** created by admins.
Students can read these announcements on their portal home page.

### 📋 COLUMNS

| Column | Type | Description |
|---|---|---|
| `id` | bigint | Unique ID for each announcement *(Primary Key)* |
| `title` | varchar(180) | Announcement headline |
| `body` | text | Full announcement content |
| `posted_by` | bigint | Which admin created it *(Foreign Key → users.id)* |
| `audience` | varchar(20) | Who can see it (default: `all`) |
| `publish_at` | datetime | Scheduled publish time (empty = publish immediately) |
| `expires_at` | datetime | When it should stop being shown (empty = never expires) |
| `is_published` | tinyint(1) | `1` = visible to students, `0` = draft |
| `pinned` | tinyint(1) | `1` = shown at the top, `0` = normal order |
| `created_at` | datetime | When the record was created |
| `updated_at` | datetime | Last time it was edited |

### 🔗 RELATIONSHIPS
- Each announcement is created by ONE **admin** (`announcements.posted_by → users.id`)

### 📊 EXAMPLE QUERIES

```sql
-- 1. Get all published announcements for students to read
SELECT title, body, created_at
FROM announcements
WHERE is_published = 1
  AND (expires_at IS NULL OR expires_at > NOW())
  AND (publish_at IS NULL OR publish_at <= NOW())
ORDER BY pinned DESC, created_at DESC;

-- 2. Get only pinned announcements
SELECT title, body FROM announcements
WHERE is_published = 1 AND pinned = 1;

-- 3. Count total announcements created by an admin
SELECT COUNT(*) AS total
FROM announcements
WHERE posted_by = 1;
```

---

## 📁 TABLE: `social_posts`
```
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
```

### 📝 PURPOSE
Stores **social feed posts** — the public version of approved feedback
that shows on the campus social wall. Students can react and comment on these.

> 💡 When feedback is approved, it becomes a social post so other students
> can see it (without personal details if anonymous).

### 📋 COLUMNS

| Column | Type | Description |
|---|---|---|
| `id` | bigint | Unique ID for each post *(Primary Key)* |
| `user_id` | bigint | Who created this post *(Foreign Key → users.id)* |
| `feedback_id` | bigint | The original feedback it came from *(Foreign Key → feedbacks.id)* |
| `body` | text | The post content shown on the feed |
| `is_public` | tinyint(1) | `1` = visible on feed, `0` = hidden |
| `is_anonymous` | tinyint(1) | `1` = hide the author's name |
| `created_at` | datetime | When it was posted |
| `updated_at` | datetime | Last edit time |
| `deleted_at` | datetime | Soft-delete timestamp |

### 🔗 RELATIONSHIPS
- Each post belongs to ONE **user** (`social_posts.user_id → users.id`)
- Each post may link to ONE **feedback** (`social_posts.feedback_id → feedbacks.id`)
- One post can have MANY **comments** (`social_posts.id → social_post_comments.post_id`)
- One post can have MANY **reactions** (`social_posts.id → social_post_reactions.post_id`)

### 📊 EXAMPLE QUERIES

```sql
-- 1. Fetch the public feed (newest first)
SELECT social_posts.id, social_posts.body, users.first_name,
       social_posts.is_anonymous, social_posts.created_at
FROM social_posts
JOIN users ON users.id = social_posts.user_id
WHERE social_posts.is_public = 1
  AND social_posts.deleted_at IS NULL
ORDER BY social_posts.created_at DESC
LIMIT 20;

-- 2. Get one student's own posts
SELECT body, created_at FROM social_posts
WHERE user_id = 3 AND deleted_at IS NULL
ORDER BY created_at DESC;

-- 3. Count total public posts
SELECT COUNT(*) FROM social_posts
WHERE is_public = 1 AND deleted_at IS NULL;
```

---

## 📁 TABLE: `social_post_comments`
```
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
```

### 📝 PURPOSE
Stores **comments on social posts**. Students can comment on feed posts,
and other students can reply to comments (nested/threaded replies).

### 📋 COLUMNS

| Column | Type | Description |
|---|---|---|
| `id` | bigint | Unique ID for each comment *(Primary Key)* |
| `post_id` | bigint | Which post this comment is on *(Foreign Key → social_posts.id)* |
| `parent_id` | bigint | If this is a reply to another comment, this points to it. Empty = top-level comment |
| `user_id` | bigint | Who wrote the comment *(Foreign Key → users.id)* |
| `body` | text | The comment text |
| `image_path` | varchar(500) | Optional image attached to comment |
| `is_anonymous` | tinyint(1) | `1` = hide commenter's name |
| `created_at` | datetime | When it was posted |
| `updated_at` | datetime | Last edit |
| `deleted_at` | datetime | Soft-delete timestamp |

### 🔗 RELATIONSHIPS
- Each comment belongs to ONE **post** (`social_post_comments.post_id → social_posts.id`)
- Each comment is written by ONE **user** (`social_post_comments.user_id → users.id`)
- A comment can be a reply to another **comment** (`social_post_comments.parent_id → social_post_comments.id`)
- One comment can have MANY **reactions** (`social_post_comments.id → comment_reactions.comment_id`)

### 📊 EXAMPLE QUERIES

```sql
-- 1. Get all top-level comments for a post
SELECT social_post_comments.body, users.first_name, social_post_comments.created_at
FROM social_post_comments
JOIN users ON users.id = social_post_comments.user_id
WHERE social_post_comments.post_id = 10
  AND social_post_comments.parent_id IS NULL
  AND social_post_comments.deleted_at IS NULL
ORDER BY social_post_comments.created_at ASC;

-- 2. Get all replies to a specific comment
SELECT body, created_at FROM social_post_comments
WHERE parent_id = 5 AND deleted_at IS NULL;

-- 3. Count total comments on a post
SELECT COUNT(*) FROM social_post_comments
WHERE post_id = 10 AND deleted_at IS NULL;
```

---

## 📁 TABLE: `social_post_reactions`
```
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
```

### 📝 PURPOSE
Stores **emoji reactions on social posts** (like 👍 ❤️ 😂 etc.).
Each row = one student reacted with one emoji on one post.

### 📋 COLUMNS

| Column | Type | Description |
|---|---|---|
| `id` | bigint | Unique ID *(Primary Key)* |
| `post_id` | bigint | Which post was reacted to *(Foreign Key → social_posts.id)* |
| `user_id` | bigint | Who reacted *(Foreign Key → users.id)* |
| `reaction_type` | varchar(20) | The emoji/reaction type (e.g., `like`, `heart`, `laugh`) |
| `created_at` | datetime | When the reaction was added |
| `updated_at` | datetime | If they changed their reaction |

### 🔗 RELATIONSHIPS
- Each reaction belongs to ONE **post** (`social_post_reactions.post_id → social_posts.id`)
- Each reaction belongs to ONE **user** (`social_post_reactions.user_id → users.id`)

### 📊 EXAMPLE QUERIES

```sql
-- 1. Count all reactions on a post grouped by type
SELECT reaction_type, COUNT(*) AS count
FROM social_post_reactions
WHERE post_id = 10
GROUP BY reaction_type;

-- 2. Check if a specific user already reacted to a post
SELECT * FROM social_post_reactions
WHERE post_id = 10 AND user_id = 3;

-- 3. Get the most reacted post
SELECT post_id, COUNT(*) AS total_reactions
FROM social_post_reactions
GROUP BY post_id
ORDER BY total_reactions DESC
LIMIT 1;
```

---

## 📁 TABLE: `comment_reactions`
```
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
```

### 📝 PURPOSE
Same idea as `social_post_reactions` but for **reactions on comments**
instead of on posts.

### 📋 COLUMNS

| Column | Type | Description |
|---|---|---|
| `id` | bigint | Unique ID *(Primary Key)* |
| `comment_id` | bigint | Which comment was reacted to *(Foreign Key → social_post_comments.id)* |
| `user_id` | bigint | Who reacted *(Foreign Key → users.id)* |
| `reaction_type` | varchar(20) | The emoji/reaction type |
| `created_at` | datetime | When it happened |
| `updated_at` | datetime | If they changed their reaction |

### 🔗 RELATIONSHIPS
- Each reaction belongs to ONE **comment** (`comment_reactions.comment_id → social_post_comments.id`)
- Each reaction belongs to ONE **user** (`comment_reactions.user_id → users.id`)

### 📊 EXAMPLE QUERIES

```sql
-- 1. Count reactions on a comment
SELECT reaction_type, COUNT(*) AS count
FROM comment_reactions
WHERE comment_id = 7
GROUP BY reaction_type;

-- 2. Find all comments a user has reacted to
SELECT comment_id, reaction_type
FROM comment_reactions
WHERE user_id = 3;
```

---

## 📁 TABLE: `social_profiles`
```
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
```

### 📝 PURPOSE
Stores each student's **social profile settings** — their bio, avatar color,
and whether they prefer to stay anonymous on the feed by default.

### 📋 COLUMNS

| Column | Type | Description |
|---|---|---|
| `id` | bigint | Unique ID *(Primary Key)* |
| `user_id` | bigint | Which user this profile belongs to *(Foreign Key → users.id, unique)* |
| `bio` | text | Short bio the student wrote about themselves |
| `avatar_color` | varchar(30) | Chosen avatar background color (default: `blue`) |
| `is_anonymous` | tinyint(1) | `1` = student prefers to post anonymously by default |
| `created_at` | datetime | When profile was created |
| `updated_at` | datetime | Last time profile was updated |

> 💡 Each user has exactly **one** social profile (one-to-one relationship).

### 🔗 RELATIONSHIPS
- Each profile belongs to exactly ONE **user** (`social_profiles.user_id → users.id`)

### 📊 EXAMPLE QUERIES

```sql
-- 1. Get a student's profile
SELECT users.first_name, social_profiles.bio, social_profiles.avatar_color
FROM social_profiles
JOIN users ON users.id = social_profiles.user_id
WHERE social_profiles.user_id = 3;

-- 2. Count students who prefer anonymous posting
SELECT COUNT(*) FROM social_profiles WHERE is_anonymous = 1;
```

---

## 📁 TABLE: `admin_activity_logs`
```
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
```

### 📝 PURPOSE
Stores a **history of every action performed by admins**.
Like a diary — every time an admin approves feedback, replies, creates
an announcement, etc., a record is saved here for accountability.

### 📋 COLUMNS

| Column | Type | Description |
|---|---|---|
| `id` | bigint | Unique ID *(Primary Key)* |
| `admin_user_id` | bigint | Which admin did the action *(Foreign Key → users.id)* |
| `action` | varchar(100) | Action code (e.g., `feedback.approved`, `user.deactivated`) |
| `target_type` | varchar(60) | What kind of item was affected (e.g., `feedback`, `user`) |
| `target_id` | bigint | The ID of the item that was affected |
| `description` | varchar(255) | Human-readable description of what happened |
| `metadata` | text | Extra details stored as JSON (flexible extra info) |
| `ip_address` | varchar(45) | The admin's IP address when they did the action |
| `user_agent` | varchar(255) | The admin's browser/device info |
| `created_at` | datetime | Exactly when the action happened |

### 🔗 RELATIONSHIPS
- Each log entry belongs to ONE **admin user** (`admin_activity_logs.admin_user_id → users.id`)

### 📊 EXAMPLE QUERIES

```sql
-- 1. See everything a specific admin has done
SELECT action, description, created_at
FROM admin_activity_logs
WHERE admin_user_id = 1
ORDER BY created_at DESC;

-- 2. See all feedback approval actions today
SELECT * FROM admin_activity_logs
WHERE action = 'feedback.approved'
  AND DATE(created_at) = CURDATE();

-- 3. Count admin actions per day this week
SELECT DATE(created_at) AS day, COUNT(*) AS actions
FROM admin_activity_logs
WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
GROUP BY DATE(created_at)
ORDER BY day DESC;
```

---

## 📁 TABLE: `admin_credentials`
```
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
```

### 📝 PURPOSE
Stores a **system-level master password** used for special system admin operations.
This table only ever has **one row** — it's a single system-wide secret.

### 📋 COLUMNS

| Column | Type | Description |
|---|---|---|
| `id` | bigint | Unique ID *(Primary Key)* |
| `master_password_hash` | varchar(255) | The system master password (hashed, never plain text) |
| `last_password_changed_at` | datetime | When the master password was last changed |
| `created_at` | datetime | When this record was created |
| `updated_at` | datetime | Last update |

> ⚠️ This table has no foreign keys — it is standalone system config.

---

## 📁 TABLE: `password_otps`
```
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
```

### 📝 PURPOSE
Stores **one-time passwords (OTPs)** and **secure reset tokens** used for:
- Email verification when registering
- Forgot password flow (student requests OTP)
- Admin-initiated password reset (sends a link to student's email)

### 📋 COLUMNS

| Column | Type | Description |
|---|---|---|
| `id` | bigint | Unique ID *(Primary Key)* |
| `user_id` | bigint | Which user this OTP is for *(Foreign Key → users.id)* |
| `email` | varchar(150) | The email address the OTP was sent to |
| `purpose` | varchar(30) | What is this OTP for: `register`, `reset`, or `admin_reset` |
| `otp_hash` | varchar(255) | The OTP or token (hashed — never stored as plain text) |
| `attempts` | tinyint | How many times a wrong code was tried |
| `max_attempts` | tinyint | Maximum allowed tries before it is blocked (default: 5) |
| `expires_at` | datetime | When this OTP stops being valid |
| `used_at` | datetime | When it was successfully used (empty = not used yet) |
| `created_at` | datetime | When it was generated |
| `updated_at` | datetime | Last update |

### 🔗 RELATIONSHIPS
- Each OTP belongs to ONE **user** (`password_otps.user_id → users.id`)

### 📊 EXAMPLE QUERIES

```sql
-- 1. Find a valid unused OTP for an email
SELECT * FROM password_otps
WHERE email = 'student@email.com'
  AND purpose = 'reset'
  AND used_at IS NULL
  AND expires_at > NOW();

-- 2. Check how many failed attempts an OTP has
SELECT attempts, max_attempts FROM password_otps WHERE id = 12;

-- 3. Clean up all expired OTPs
DELETE FROM password_otps WHERE expires_at < NOW();
```

---

## 📁 TABLE: `api_tokens`
```
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
```

### 📝 PURPOSE
Stores **API authentication tokens** for the CampusVoice mobile/API access.
When a student logs in through the API, a token is created and given to them.
They send this token with every API request to prove who they are.

> 💡 Think of it like a temporary keycard — log in once, get a card, use the card until it expires.

### 📋 COLUMNS

| Column | Type | Description |
|---|---|---|
| `id` | bigint | Unique ID *(Primary Key)* |
| `user_id` | bigint | Which user owns this token *(Foreign Key → users.id)* |
| `name` | varchar(80) | Label for this token (e.g., device name) |
| `token_hash` | char(64) | The token stored as a secure hash |
| `expires_at` | datetime | When this token expires (empty = never) |
| `last_used_at` | datetime | Last time this token was used to make a request |
| `created_at` | datetime | When it was created |
| `updated_at` | datetime | Last update |

### 🔗 RELATIONSHIPS
- Each token belongs to ONE **user** (`api_tokens.user_id → users.id`)

### 📊 EXAMPLE QUERIES

```sql
-- 1. Find an active token (used to authenticate API requests)
SELECT user_id FROM api_tokens
WHERE token_hash = 'abc123...'
  AND (expires_at IS NULL OR expires_at > NOW());

-- 2. See all tokens for a user
SELECT name, last_used_at, expires_at
FROM api_tokens WHERE user_id = 3;

-- 3. Delete expired tokens
DELETE FROM api_tokens WHERE expires_at < NOW();
```

---
---

## 🔍 COMMON SYSTEM QUERIES

These are the most important queries CampusVoice runs day-to-day:

---

### 1. 🔐 Login — Check email and password
```sql
-- Step 1: Find the user by email
SELECT id, email, password_hash, is_active, role_id
FROM users
WHERE email = 'student@email.com'
LIMIT 1;

-- Step 2: In PHP, verify password:
-- password_verify($inputPassword, $row['password_hash'])
-- If TRUE → login succeeds. If FALSE → wrong password.
```

---

### 2. 🏠 Home Feed — Load social posts with reactions
```sql
SELECT
  social_posts.id,
  social_posts.body,
  social_posts.is_anonymous,
  social_posts.created_at,
  users.first_name,
  users.last_name,
  COUNT(social_post_reactions.id) AS reaction_count
FROM social_posts
JOIN users ON users.id = social_posts.user_id
LEFT JOIN social_post_reactions ON social_post_reactions.post_id = social_posts.id
WHERE social_posts.is_public = 1
  AND social_posts.deleted_at IS NULL
GROUP BY social_posts.id
ORDER BY social_posts.created_at DESC
LIMIT 20;
```

---

### 3. 👤 My Feedback — Student views their own submissions
```sql
SELECT
  feedbacks.id,
  feedbacks.type,
  feedbacks.subject,
  feedbacks.status,
  feedbacks.created_at,
  feedback_categories.name AS category
FROM feedbacks
JOIN feedback_categories ON feedback_categories.id = feedbacks.category_id
WHERE feedbacks.user_id = 3        -- replace 3 with the logged-in student's ID
  AND feedbacks.deleted_at IS NULL
ORDER BY feedbacks.created_at DESC;
```

---

### 4. 😀 Reaction Counts — How many of each reaction on a post
```sql
SELECT reaction_type, COUNT(*) AS total
FROM social_post_reactions
WHERE post_id = 10       -- replace 10 with the post ID
GROUP BY reaction_type;
```

---

### 5. 🔔 Admin — Fetch pending feedback (needs approval)
```sql
SELECT
  feedbacks.id,
  feedbacks.type,
  feedbacks.subject,
  feedbacks.message,
  feedbacks.created_at,
  feedback_categories.name AS category,
  users.first_name,
  users.last_name,
  feedbacks.is_anonymous
FROM feedbacks
JOIN feedback_categories ON feedback_categories.id = feedbacks.category_id
LEFT JOIN users ON users.id = feedbacks.user_id
WHERE feedbacks.status = 'pending'
  AND feedbacks.deleted_at IS NULL
ORDER BY feedbacks.created_at ASC;  -- oldest first so nothing is missed
```

---

### 6. 📢 Fetch Active Announcements (Student portal)
```sql
SELECT title, body, created_at, pinned
FROM announcements
WHERE is_published = 1
  AND (publish_at IS NULL OR publish_at <= NOW())
  AND (expires_at IS NULL OR expires_at > NOW())
ORDER BY pinned DESC, created_at DESC;
-- pinned=1 posts come first, then newest first
```

---

## 📊 TABLE SUMMARY

| Table | Rows Represent | Key Relationships |
|---|---|---|
| `roles` | Account types (student, admin) | → users |
| `users` | All user accounts | → roles, feedbacks, posts |
| `feedbacks` | Student feedback submissions | → users, categories, replies |
| `feedback_categories` | Feedback topic labels | → feedbacks |
| `feedback_replies` | Admin responses to feedback | → feedbacks, users |
| `announcements` | Admin announcements | → users (posted_by) |
| `social_posts` | Public feed posts | → users, feedbacks |
| `social_post_comments` | Comments on feed posts | → social_posts, users |
| `social_post_reactions` | Emoji reactions on posts | → social_posts, users |
| `comment_reactions` | Emoji reactions on comments | → social_post_comments, users |
| `social_profiles` | Student profile settings | → users (1-to-1) |
| `admin_activity_logs` | Admin action history | → users |
| `admin_credentials` | System master password | (standalone) |
| `password_otps` | OTP codes and reset tokens | → users |
| `api_tokens` | Mobile/API login tokens | → users |

---

*📄 End of CampusVoice Database Documentation*
*Generated for study/research purposes — CampusVoice System, 2026*
