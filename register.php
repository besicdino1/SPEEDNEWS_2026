<?php
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/auth.php';

$page_title = 'Create Account';

// Redirect if already logged in
if (is_logged_in()) {
    header('Location: ' . BASE_URL . '/index.php');
    exit;
}

$errors = [];
$old    = ['firstname' => '', 'lastname' => '', 'email' => '', 'country' => ''];

$countries = [
    'Afghanistan','Albania','Algeria','Andorra','Angola','Argentina','Armenia','Australia',
    'Austria','Azerbaijan','Bahamas','Bahrain','Bangladesh','Belarus','Belgium','Belize',
    'Benin','Bolivia','Bosnia and Herzegovina','Botswana','Brazil','Brunei','Bulgaria',
    'Burkina Faso','Cambodia','Cameroon','Canada','Chile','China','Colombia','Croatia',
    'Cuba','Cyprus','Czech Republic','Denmark','Ecuador','Egypt','Estonia','Ethiopia',
    'Finland','France','Georgia','Germany','Ghana','Greece','Guatemala','Honduras',
    'Hungary','Iceland','India','Indonesia','Iran','Iraq','Ireland','Israel','Italy',
    'Jamaica','Japan','Jordan','Kazakhstan','Kenya','Kuwait','Latvia','Lebanon','Libya',
    'Lithuania','Luxembourg','Malaysia','Malta','Mexico','Moldova','Monaco','Mongolia',
    'Montenegro','Morocco','Mozambique','Myanmar','Nepal','Netherlands','New Zealand',
    'Nigeria','North Korea','North Macedonia','Norway','Oman','Pakistan','Palestine',
    'Panama','Paraguay','Peru','Philippines','Poland','Portugal','Qatar','Romania',
    'Russia','Rwanda','Saudi Arabia','Senegal','Serbia','Singapore','Slovakia','Slovenia',
    'Somalia','South Africa','South Korea','Spain','Sri Lanka','Sudan','Sweden',
    'Switzerland','Syria','Taiwan','Tanzania','Thailand','Tunisia','Turkey','Uganda',
    'Ukraine','United Arab Emirates','United Kingdom','United States','Uruguay',
    'Uzbekistan','Venezuela','Vietnam','Yemen','Zimbabwe',
];

// ── Process POST ──────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $firstname = trim($_POST['firstname'] ?? '');
    $lastname  = trim($_POST['lastname']  ?? '');
    $email     = trim($_POST['email']     ?? '');
    $country   = trim($_POST['country']   ?? '');
    $password  = $_POST['password']  ?? '';
    $confirm   = $_POST['confirm']   ?? '';

    // ── Validation ────────────────────────────────────────────
    if ($firstname === '') {
        $errors['firstname'] = 'First name is required.';
    } elseif (mb_strlen($firstname) > 100) {
        $errors['firstname'] = 'First name may not exceed 100 characters.';
    }

    if ($lastname === '') {
        $errors['lastname'] = 'Last name is required.';
    } elseif (mb_strlen($lastname) > 100) {
        $errors['lastname'] = 'Last name may not exceed 100 characters.';
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Please enter a valid email address.';
    } else {
        // Duplicate email check
        $chk = $pdo->prepare('SELECT id FROM users WHERE email = :email LIMIT 1');
        $chk->execute([':email' => $email]);
        if ($chk->fetch()) {
            $errors['email'] = 'An account with this email address already exists.';
        }
    }

    if (!in_array($country, $countries, true)) {
        $errors['country'] = 'Please select a valid country.';
    }

    if (strlen($password) < 8) {
        $errors['password'] = 'Password must be at least 8 characters.';
    } elseif (!preg_match('/[A-Z]/', $password)) {
        $errors['password'] = 'Password must contain at least one uppercase letter.';
    } elseif (!preg_match('/[0-9]/', $password)) {
        $errors['password'] = 'Password must contain at least one number.';
    }

    if (empty($errors['password']) && $password !== $confirm) {
        $errors['confirm'] = 'Passwords do not match.';
    }

    // ── Insert if valid ───────────────────────────────────────
    if (empty($errors)) {
        $hash = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $pdo->prepare(
            'INSERT INTO users (firstname, lastname, email, password, country, role)
             VALUES (:fn, :ln, :em, :pw, :co, :ro)'
        );
        $stmt->execute([
            ':fn' => $firstname,
            ':ln' => $lastname,
            ':em' => $email,
            ':pw' => $hash,
            ':co' => $country,
            ':ro' => 'user',
        ]);

        set_flash('success', 'Account created! You can now sign in.');
        header('Location: ' . BASE_URL . '/login.php');
        exit;
    }

    // Repopulate safe fields on error
    $old = [
        'firstname' => htmlspecialchars($firstname),
        'lastname'  => htmlspecialchars($lastname),
        'email'     => htmlspecialchars($email),
        'country'   => $country,
    ];
}

require_once __DIR__ . '/includes/header.php';
?>

<!-- ── Page hero ──────────────────────────────────────────────── -->
<div class="page-hero">
    <div class="container">
        <h1>Create an Account</h1>
        <p>Join SpeedNews and stay connected with the automotive world.</p>
    </div>
</div>

<section class="section" aria-label="Registration form">
    <div class="container" style="max-width:640px">

        <?= html_flash('error') ?>

        <!-- Password strength indicator (shown by JS) -->
        <div class="form-card" style="max-width:100%">

            <form method="POST" action="<?= BASE_URL ?>/register.php" novalidate>

                <!-- Name row -->
                <div class="form-grid-2">
                    <div class="form-group">
                        <label for="firstname">First Name <span style="color:var(--clr-accent)">*</span></label>
                        <input type="text" id="firstname" name="firstname"
                               value="<?= $old['firstname'] ?>"
                               placeholder="Alex"
                               required autocomplete="given-name"
                               maxlength="100">
                        <?php if (isset($errors['firstname'])): ?>
                            <span class="field-error"><?= htmlspecialchars($errors['firstname']) ?></span>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label for="lastname">Last Name <span style="color:var(--clr-accent)">*</span></label>
                        <input type="text" id="lastname" name="lastname"
                               value="<?= $old['lastname'] ?>"
                               placeholder="Turner"
                               required autocomplete="family-name"
                               maxlength="100">
                        <?php if (isset($errors['lastname'])): ?>
                            <span class="field-error"><?= htmlspecialchars($errors['lastname']) ?></span>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Email -->
                <div class="form-group">
                    <label for="email">Email Address <span style="color:var(--clr-accent)">*</span></label>
                    <input type="email" id="email" name="email"
                           value="<?= $old['email'] ?>"
                           placeholder="alex@example.com"
                           required autocomplete="email">
                    <?php if (isset($errors['email'])): ?>
                        <span class="field-error"><?= htmlspecialchars($errors['email']) ?></span>
                    <?php endif; ?>
                </div>

                <!-- Country -->
                <div class="form-group">
                    <label for="country">Country <span style="color:var(--clr-accent)">*</span></label>
                    <select id="country" name="country" required autocomplete="country-name">
                        <option value="" disabled <?= $old['country'] === '' ? 'selected' : '' ?>>— Select your country —</option>
                        <?php foreach ($countries as $c): ?>
                            <option value="<?= htmlspecialchars($c) ?>"
                                <?= $old['country'] === $c ? 'selected' : '' ?>>
                                <?= htmlspecialchars($c) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (isset($errors['country'])): ?>
                        <span class="field-error"><?= htmlspecialchars($errors['country']) ?></span>
                    <?php endif; ?>
                </div>

                <!-- Password -->
                <div class="form-group">
                    <label for="password">Password <span style="color:var(--clr-accent)">*</span></label>
                    <div style="position:relative">
                        <input type="password" id="password" name="password"
                               placeholder="Min. 8 characters"
                               required autocomplete="new-password"
                               minlength="8"
                               style="padding-right:3rem">
                        <button type="button" id="toggle-pw"
                                aria-label="Show password"
                                style="position:absolute;right:var(--sp-3);top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:var(--clr-text-muted);font-size:1.1rem;padding:0">
                            👁
                        </button>
                    </div>
                    <?php if (isset($errors['password'])): ?>
                        <span class="field-error"><?= htmlspecialchars($errors['password']) ?></span>
                    <?php endif; ?>

                    <!-- Strength bar -->
                    <div id="strength-bar-wrap"
                         style="margin-top:var(--sp-2);display:none">
                        <div style="height:4px;background:var(--clr-border);border-radius:2px;overflow:hidden">
                            <div id="strength-bar"
                                 style="height:100%;width:0;border-radius:2px;transition:width .3s,background .3s"></div>
                        </div>
                        <span id="strength-label"
                              style="font-size:.75rem;color:var(--clr-text-muted);margin-top:var(--sp-1);display:block"></span>
                    </div>

                    <span class="form-hint">
                        Must be at least 8 characters, include one uppercase letter and one number.
                    </span>
                </div>

                <!-- Confirm password -->
                <div class="form-group">
                    <label for="confirm">Confirm Password <span style="color:var(--clr-accent)">*</span></label>
                    <input type="password" id="confirm" name="confirm"
                           placeholder="Repeat your password"
                           required autocomplete="new-password">
                    <?php if (isset($errors['confirm'])): ?>
                        <span class="field-error"><?= htmlspecialchars($errors['confirm']) ?></span>
                    <?php endif; ?>
                    <span id="match-msg" style="font-size:.78rem;display:none"></span>
                </div>

                <!-- Submit -->
                <button type="submit" class="btn btn--primary btn--full"
                        style="margin-top:var(--sp-2)">
                    Create Account
                </button>

                <p class="form-footer">
                    Already have an account?
                    <a href="<?= BASE_URL ?>/login.php">Sign in here</a>
                </p>

            </form>
        </div>

    </div>
</section>

<!-- ── Inline JS: password strength + show/hide + match check ── -->
<script>
(function () {
    const pwField    = document.getElementById('password');
    const cfField    = document.getElementById('confirm');
    const toggleBtn  = document.getElementById('toggle-pw');
    const barWrap    = document.getElementById('strength-bar-wrap');
    const bar        = document.getElementById('strength-bar');
    const barLabel   = document.getElementById('strength-label');
    const matchMsg   = document.getElementById('match-msg');

    // Show / hide password
    toggleBtn.addEventListener('click', function () {
        const show = pwField.type === 'password';
        pwField.type       = show ? 'text' : 'password';
        toggleBtn.textContent = show ? '🙈' : '👁';
        toggleBtn.setAttribute('aria-label', show ? 'Hide password' : 'Show password');
    });

    // Password strength
    pwField.addEventListener('input', function () {
        const v = pwField.value;
        barWrap.style.display = v.length ? 'block' : 'none';

        let score = 0;
        if (v.length >= 8)              score++;
        if (/[A-Z]/.test(v))            score++;
        if (/[0-9]/.test(v))            score++;
        if (/[^A-Za-z0-9]/.test(v))     score++;
        if (v.length >= 12)             score++;

        const levels = [
            { pct: '20%',  bg: '#e60000', label: 'Very weak'  },
            { pct: '40%',  bg: '#f97316', label: 'Weak'        },
            { pct: '60%',  bg: '#eab308', label: 'Fair'        },
            { pct: '80%',  bg: '#22c55e', label: 'Strong'      },
            { pct: '100%', bg: '#16a34a', label: 'Very strong' },
        ];
        const lvl = levels[Math.min(score, levels.length) - 1] || levels[0];
        bar.style.width      = lvl.pct;
        bar.style.background = lvl.bg;
        barLabel.textContent = lvl.label;
        barLabel.style.color = lvl.bg;

        checkMatch();
    });

    // Confirm match
    cfField.addEventListener('input', checkMatch);

    function checkMatch() {
        if (!cfField.value) { matchMsg.style.display = 'none'; return; }
        const ok = pwField.value === cfField.value;
        matchMsg.style.display = 'block';
        matchMsg.textContent   = ok ? '✓ Passwords match' : '✗ Passwords do not match';
        matchMsg.style.color   = ok ? 'var(--clr-success)' : 'var(--clr-accent)';
    }
}());
</script>

<style>
.field-error {
    display: block;
    font-size: .8rem;
    color: var(--clr-accent);
    margin-top: var(--sp-1);
}
</style>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
