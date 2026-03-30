<?php
// Test script: verify code changes via public pages + direct DB checks
// Run: php writable/tmp_test_feedback.php

echo "=== CampusVoice Test Suite ===\n\n";

$baseUrl = 'http://localhost/campusvoice';
$passed = 0;
$failed = 0;

function test($name, $condition) {
    global $passed, $failed;
    if ($condition) {
        echo "  [PASS] $name\n";
        $passed++;
    } else {
        echo "  [FAIL] $name\n";
        $failed++;
    }
}

// --- Test 1: Public pages load ---
echo "--- 1. Public Page Tests ---\n";

$ch = curl_init();
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);

// Login page
curl_setopt($ch, CURLOPT_URL, $baseUrl . '/users/login');
$resp = curl_exec($ch);
$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
test('Login page loads (200)', $code === 200);
test('Login page has email field', strpos($resp, 'email') !== false);

// Social feed page
curl_setopt($ch, CURLOPT_URL, $baseUrl . '/feed');
$resp = curl_exec($ch);
$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
test('Feed page loads (200)', $code === 200);
$feedCardCount = substr_count($resp, 'feed-card');
test('Feed page has post cards', $feedCardCount > 0);
$hasReactBar = strpos($resp, 'react-bar') !== false;
test('Feed page has emoji react-bar', $hasReactBar);
$hasReactBtn = strpos($resp, 'react-btn') !== false;
test('Feed page has react-btn buttons', $hasReactBtn);
$hasCommentForm = strpos($resp, 'comment-form') !== false;
echo "  [INFO] Comment forms on feed: " . ($hasCommentForm ? 'YES (logged in)' : 'NO (not logged in - expected)') . "\n";

// Settings page (requires auth, should redirect)
curl_setopt($ch, CURLOPT_URL, $baseUrl . '/settings');
$resp = curl_exec($ch);
$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$finalUrl = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
echo "  [INFO] Settings page: HTTP $code (redirected to: $finalUrl)\n";

curl_close($ch);

// --- Test 2: Database verification ---
echo "\n--- 2. Database Tests ---\n";

$db = new mysqli('localhost', 'root', '', 'campusvoice');
if ($db->connect_error) {
    echo "  [FAIL] DB connection: " . $db->connect_error . "\n";
    $failed++;
} else {
    test('DB connection OK', true);

    // Check tables exist
    $tables = ['users', 'feedbacks', 'social_posts', 'social_post_comments', 'social_post_reactions', 'social_post_shares', 'social_profiles', 'feedback_categories', 'announcements'];
    foreach ($tables as $t) {
        $r = $db->query("SHOW TABLES LIKE '$t'");
        test("Table '$t' exists", $r && $r->num_rows > 0);
    }

    // Check social_posts has is_anonymous column
    $r = $db->query("SHOW COLUMNS FROM social_posts LIKE 'is_anonymous'");
    test('social_posts has is_anonymous column', $r && $r->num_rows > 0);

    // Check social_post_comments has is_anonymous column
    $r = $db->query("SHOW COLUMNS FROM social_post_comments LIKE 'is_anonymous'");
    test('social_post_comments has is_anonymous column', $r && $r->num_rows > 0);

    // Check social_profiles has is_anonymous column
    $r = $db->query("SHOW COLUMNS FROM social_profiles LIKE 'is_anonymous'");
    test('social_profiles has is_anonymous column', $r && $r->num_rows > 0);

    // Check feedbacks has deleted_at (soft deletes)
    $r = $db->query("SHOW COLUMNS FROM feedbacks LIKE 'deleted_at'");
    test('feedbacks has deleted_at (soft delete)', $r && $r->num_rows > 0);

    // Data counts
    $r = $db->query("SELECT COUNT(*) as cnt FROM users WHERE is_active=1");
    $row = $r->fetch_assoc();
    echo "  [INFO] Active users: " . $row['cnt'] . "\n";

    $r = $db->query("SELECT COUNT(*) as cnt FROM social_posts WHERE deleted_at IS NULL");
    $row = $r->fetch_assoc();
    echo "  [INFO] Active social posts: " . $row['cnt'] . "\n";
    test('Social posts exist', (int)$row['cnt'] > 0);

    $r = $db->query("SELECT COUNT(*) as cnt FROM feedbacks WHERE deleted_at IS NULL");
    $row = $r->fetch_assoc();
    echo "  [INFO] Active feedbacks: " . $row['cnt'] . "\n";

    $r = $db->query("SELECT COUNT(*) as cnt FROM social_posts WHERE body LIKE CONVERT('%📋%' USING utf8)");
    if ($r) {
        $row = $r->fetch_assoc();
        echo "  [INFO] Feedback-linked social posts (📋 prefix): " . $row['cnt'] . "\n";
    } else {
        // Fallback: search without emoji
        $r = $db->query("SELECT COUNT(*) as cnt FROM social_posts WHERE body LIKE '%Feedback:%' OR body LIKE '%Suggestion:%' OR body LIKE '%Complaint:%' OR body LIKE '%Praise:%'");
        if ($r) {
            $row = $r->fetch_assoc();
            echo "  [INFO] Feedback-linked social posts (text match): " . $row['cnt'] . "\n";
        } else {
            echo "  [INFO] Could not query feedback-linked posts (collation issue)\n";
        }
    }

    $r = $db->query("SELECT COUNT(*) as cnt FROM announcements WHERE is_published=1");
    $row = $r->fetch_assoc();
    echo "  [INFO] Published announcements: " . $row['cnt'] . "\n";

    $r = $db->query("SELECT COUNT(*) as cnt FROM feedback_categories WHERE is_active=1");
    $row = $r->fetch_assoc();
    echo "  [INFO] Active feedback categories: " . $row['cnt'] . "\n";
    test('Feedback categories exist', (int)$row['cnt'] > 0);

    $db->close();
}

// --- Test 3: Code verification ---
echo "\n--- 3. Code Structure Tests ---\n";

// Check PortalController has the feedback-to-social-post code
$portalCode = file_get_contents(__DIR__ . '/../app/Controllers/Student/PortalController.php');
test('PortalController has SocialPostModel import', strpos($portalCode, 'use App\\Models\\SocialPostModel') !== false);
test('PortalController has feedback-to-feed insert', strpos($portalCode, "new SocialPostModel())->insert") !== false);
test('PortalController has 📋 prefix in feed body', strpos($portalCode, '📋') !== false);
test('PortalController has deleteFeedback method', strpos($portalCode, 'function deleteFeedback') !== false);
test('PortalController has buildCommunityPosts method', strpos($portalCode, 'function buildCommunityPosts') !== false);
test('PortalController has anonymousAlias method', strpos($portalCode, 'function anonymousAlias') !== false);

// Check settings view has anon toggle at top
$settingsCode = file_get_contents(__DIR__ . '/../app/Views/social/settings.php');
$formPos = strpos($settingsCode, '<form');
$anonPos = strpos($settingsCode, 'anon-toggle-row');
$firstNamePos = strpos($settingsCode, 'first_name');
test('Settings: anon toggle exists', $anonPos !== false);
test('Settings: anon toggle before first_name field', $anonPos !== false && $firstNamePos !== false && $anonPos < $firstNamePos);

// Check _post_card has react-bar
$postCardCode = file_get_contents(__DIR__ . '/../app/Views/social/_post_card.php');
test('Post card has react-bar', strpos($postCardCode, 'react-bar') !== false);
test('Post card has emoji buttons (👍)', strpos($postCardCode, '👍') !== false);
test('Post card has comment-form textarea', strpos($postCardCode, 'comment-form') !== false);

// Check my_feedback has delete modal
$myFeedbackCode = file_get_contents(__DIR__ . '/../app/Views/student/portal/my_feedback.php');
test('My Feedback has delete modal', strpos($myFeedbackCode, 'deleteModal') !== false);
test('My Feedback has btn-delete-sm', strpos($myFeedbackCode, 'btn-delete-sm') !== false);

// Check home page has sections
$homeCode = file_get_contents(__DIR__ . '/../app/Views/student/portal/home.php');
test('Home has My Recent Feedback section', strpos($homeCode, 'My Recent Feedback') !== false);
test('Home has Latest Announcements section', strpos($homeCode, 'Latest Announcements') !== false);

// Check CSS files
$socialCss = file_get_contents(__DIR__ . '/../public/assets/student/social.css');
test('Social CSS has react-bar styles', strpos($socialCss, '.react-bar') !== false);
test('Social CSS has react-btn styles', strpos($socialCss, '.react-btn') !== false);
test('Social CSS has anon-toggle styles', strpos($socialCss, '.anon-toggle-row') !== false);

$portalCss = file_get_contents(__DIR__ . '/../public/assets/student/portal.css');
test('Portal CSS has btn-delete-sm styles', strpos($portalCss, '.btn-delete-sm') !== false);
test('Portal CSS has modal styles', strpos($portalCss, '.modal-overlay') !== false);

// Check routes
$routesCode = file_get_contents(__DIR__ . '/../app/Config/Routes.php');
test('Routes has feedback delete route', strpos($routesCode, "feedback/(:num)/delete") !== false);
test('Routes has feedback submit route', strpos($routesCode, "feedback/submit") !== false);

echo "\n=== RESULTS: $passed passed, $failed failed ===\n";
