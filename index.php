<?php
// index.php  (or dashboard.php)
require_once __DIR__ . '/includes/functions.php';
$logged = !empty($_SESSION['admin_id']);
$csrf = create_csrf_token();
$admin_name = $_SESSION['admin_name'] ?? '';
$admin_email = $_SESSION['admin_email'] ?? '';
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Config Manager | Secure Login</title>
  <!-- include same CSS from your index.html head section (fonts, style) -->
  <!-- For brevity, reuse the CSS and HTML you already have in index.html (uploaded). -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <style>
    /* copy the <style> content from your uploaded index.html here exactly */
  </style>
</head>
<body>
  <!-- Copy the full body from your uploaded index.html here,
       but remove firebase initialization and GitLab private token.
       Keep the HTML markup identical so UI looks the same. -->

  <!-- Minimal injection for JS to see auth state -->
  <script>
    window.APP = {
      logged: <?= json_encode((bool)$logged) ?>,
      csrfToken: <?= json_encode($csrf) ?>,
      adminName: <?= json_encode($admin_name) ?>,
      adminEmail: <?= json_encode($admin_email) ?>
    };
  </script>

  <!-- Now include a modified script that talks to our PHP API endpoints instead of Firebase/GitLab -->
  <script>
    // IMPORTANT: Replace/merge this with your existing JS in index.html.
    // Snippets below show minimal changes to integrate with the PHP backend.

    // Login
    async function handleLogin() {
      const email = document.getElementById('email').value;
      const password = document.getElementById('password').value;
      const resp = await fetch('auth/login.php', {
        method: 'POST',
        body: new URLSearchParams({ email, password })
      });
      const json = await resp.json();
      if (json.ok) {
        // store csrf token
        window.APP.csrfToken = json.csrf;
        // reload to show dashboard (or switch UI)
        location.reload();
      } else {
        // show error...
        alert(json.message || 'Login failed');
      }
    }

    // Logout
    async function handleLogout() {
      await fetch('auth/logout.php', { method: 'POST' });
      location.reload();
    }

    // Load config (for the UI)
    async function loadCurrentConfig() {
      const res = await fetch('api/load_config.php', { credentials: 'same-origin' });
      const json = await res.json();
      if (!json.ok) {
        console.error('Failed to load config', json);
        return;
      }
      // setFormData(json.config) — same helper as in your JS
      setFormData(json.config);
      document.getElementById('configPreview').textContent = JSON.stringify(json.config, null, 2);
    }

    // Save config
    async function updateGitLabConfig(config) {
      // now posts to api/save_config.php with CSRF header
      const res = await fetch('api/save_config.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-Token': window.APP.csrfToken
        },
        body: JSON.stringify(config)
      });
      const json = await res.json();
      if (json.ok) {
        alert('Saved');
      } else {
        alert('Save failed: ' + (json.message || 'unknown'));
      }
    }

    // On DOM ready, if logged show app else show login
    document.addEventListener('DOMContentLoaded', () => {
      if (window.APP.logged) {
        // load UI as admin
        loadCurrentConfig();
        // fill display name/email if you want
        document.getElementById('appUserName').textContent = window.APP.adminName || window.APP.adminEmail.split('@')[0];
        document.getElementById('appUserEmail').textContent = window.APP.adminEmail || '';
      } else {
        // show login
      }
    });

    // Keep other UI functions from index.html (getFormData, setFormData, copy, tabs, nav)
  </script>
</body>
</html>