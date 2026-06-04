<?php
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/auth.php';

$page_title = 'Sign In';

// Redirect if already logged in
if (is_logged_in()) {
    header('Location: ' . BASE_URL . '/index.php');
    exit;
}

$errors  = [];
$old_email = '';

// ── Process POST ──────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email    = trim($_POST['email']    ?? '');
    $password = $_POST['password'] ?? '';

    // Basic input presence check before hitting the DB
    if ($email === '') {
        $errors['email'] = 'Email address is required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Please enter a valid email address.';
    }

    if ($password === '') {
        $errors['password'] = 'Password is required.';
    }

    if (empty($errors)) {
        // Fetch user by email — use a generic error message on failure
        // so we do not confirm whether an email is registered.
        $stmt = $pdo->prepare(
            'SELECT id, firstname, lastname, email, password, role
             FROM   users
             WHERE  email = :email
             LIMIT  1'
        );
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {

            // Regenerate session ID to prevent session fixation
            session_regenerate_id(true);

            $_SESSION['user_id']   = $user['id'];
            $_SESSION['firstname'] = $user['firstname'];
            $_SESSION['lastname']  = $user['lastname'];
            $_SESSION['email']     = $user['email'];
            $_SESSION['role']      = $user['role'];

            // Redirect to the page the user originally tried to visit,
            // or to the admin dashboard / homepage based on role.
            if (isset($_SESSION['redirect_after_login'])) {
                $redirect = $_SESSION['redirect_after_login'];
                unset($_SESSION['redirect_after_login']);
            } elseif ($user['role'] === 'admin') {
                $redirect = BASE_URL . '/admin/index.php';
            } else {
                $redirect = BASE_URL . '/index.php';
            }

            header('Location: ' . $redirect);
            exit;

        } else {
            // Intentionally vague — do not confirm email existence
            $errors['general'] = 'Incorrect email address or password. Please try again.';
        }
    }

    $old_email = htmlspecialchars($email);
}

require_once __DIR__ . '/includes/header.php';
?>

<!-- ── Page hero ──────────────────────────────────────────────── -->
<div class="page-hero">
    <div class="container">
        <h1>Sign In</h1>
        <p>Welcome back — enter your credentials to access your account.</p>
    </div>
</div>

<section class="section" aria-label="Login form">
    <div class="container">

        <div class="form-card">

            <!-- Logo mark -->
            <div style="text-align:center;margin-bottom:var(--sp-6)">
                <a href="<?= BASE_URL ?>/index.php" class="logo" style="font-size:2rem">
                    <span class="logo-speed">SPEED</span><span class="logo-news">NEWS</span>
                </a>
                <p style="color:var(--clr-text-muted);font-size:.9rem;margin-top:var(--sp-3);margin-bottom:0">
                    Sign in to your account
                </p>
            </div>

            <?= html_flash('success') ?>
            <?= html_flash('error') ?>

            <!-- General error (wrong credentials) -->
            <?php if (isset($errors['general'])): ?>
                <div class="alert alert--error">
                    <?= htmlspecialchars($errors['general']) ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="<?= BASE_URL ?>/login.php" novalidate>

                <!-- Email -->
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email"
                           value="<?= $old_email ?>"
                           placeholder="you@example.com"
                           required
                           autocomplete="email"
                           autofocus>
                    <?php if (isset($errors['email'])): ?>
                        <span style="display:block;font-size:.8rem;color:var(--clr-accent);margin-top:var(--sp-1)">
                            <?= htmlspecialchars($errors['email']) ?>
                        </span>
                    <?php endif; ?>
                </div>

                <!-- Password -->
                <div class="form-group">
                    <div style="display:flex;justify-content:space-between;align-items:baseline;margin-bottom:var(--sp-2)">
                        <label for="password" style="margin-bottom:0">Password</label>
                    </div>
                    <div style="position:relative">
                        <input type="password" id="password" name="password"
                               placeholder="Your password"
                               required
                               autocomplete="current-password"
                               style="padding-right:3rem">
                        <button type="button" id="toggle-pw"
                                aria-label="Show password"
                                style="position:absolute;right:var(--sp-3);top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:var(--clr-text-muted);font-size:1.1rem;padding:0">
                            👁
                        </button>
                    </div>
                    <?php if (isset($errors['password'])): ?>
                        <span style="display:block;font-size:.8rem;color:var(--clr-accent);margin-top:var(--sp-1)">
                            <?= htmlspecialchars($errors['password']) ?>
                        </span>
                    <?php endif; ?>
                </div>

                <!-- Submit -->
                <button type="submit" class="btn btn--primary btn--full"
                        style="margin-top:var(--sp-4)">
                    Sign In &rarr;
                </button>

                <p class="form-footer" style="margin-top:var(--sp-6)">
                    Don't have an account?
                    <a href="<?= BASE_URL ?>/register.php">Create one for free</a>
                </p>

            </form>
        </div>

        <!-- Demo credentials hint (remove before production) -->
        <div style="max-width:400px;margin:var(--sp-6) auto 0;background:var(--clr-surface);border:1px dashed var(--clr-border);border-radius:var(--radius-md);padding:var(--sp-5)">
            <p style="font-size:.8rem;color:var(--clr-text-muted);margin-bottom:var(--sp-3);font-weight:600;text-transform:uppercase;letter-spacing:1px">
                Demo Credentials
            </p>
            <div style="font-family:var(--font-mono);font-size:.82rem;color:var(--clr-text)">
                <div style="margin-bottom:var(--sp-2)">
                    <span style="color:var(--clr-text-muted)">Email:</span>
                    admin@speednews.com
                </div>
                <div>
                    <span style="color:var(--clr-text-muted)">Password:</span>
                    password
                </div>
            </div>
            <p style="font-size:.75rem;color:var(--clr-text-faint);margin-top:var(--sp-3);margin-bottom:0">
                Remove this box before submitting or deploying.
            </p>
        </div>

    </div>
</section>

<script>
(function () {
    const pw  = document.getElementById('password');
    const btn = document.getElementById('toggle-pw');
    if (!pw || !btn) return;
    btn.addEventListener('click', function () {
        const show = pw.type === 'password';
        pw.type            = show ? 'text' : 'password';
        btn.textContent    = show ? '🙈' : '👁';
        btn.setAttribute('aria-label', show ? 'Hide password' : 'Show password');
    });
}());
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
