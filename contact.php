<?php
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/auth.php';

$page_title = 'Contact';
$errors     = [];
$success    = false;

// Full country list
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

// ── Process form ──────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $firstname  = trim($_POST['firstname']  ?? '');
    $lastname   = trim($_POST['lastname']   ?? '');
    $email      = trim($_POST['email']      ?? '');
    $country    = trim($_POST['country']    ?? '');
    $newsletter = isset($_POST['newsletter']) ? 1 : 0;
    $subject    = trim($_POST['subject']    ?? '');
    $message    = trim($_POST['message']    ?? '');

    // Validation
    if ($firstname === '')                           $errors['firstname']  = 'First name is required.';
    if ($lastname  === '')                           $errors['lastname']   = 'Last name is required.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors['email']      = 'A valid email address is required.';
    if (!in_array($country, $countries, true))      $errors['country']    = 'Please select a valid country.';
    if ($subject   === '')                           $errors['subject']    = 'Subject is required.';
    if (strlen($message) < 10)                      $errors['message']    = 'Message must be at least 10 characters.';

    if (empty($errors)) {
        $stmt = $pdo->prepare(
            'INSERT INTO contacts
             (firstname, lastname, email, country, newsletter, subject, message)
             VALUES (:fn, :ln, :em, :co, :nl, :su, :ms)'
        );
        $stmt->execute([
            ':fn' => $firstname,
            ':ln' => $lastname,
            ':em' => $email,
            ':co' => $country,
            ':nl' => $newsletter,
            ':su' => $subject,
            ':ms' => $message,
        ]);

        set_flash('success', 'Thank you, ' . htmlspecialchars($firstname) . '! Your message has been received. We\'ll be in touch shortly.');
        header('Location: ' . BASE_URL . '/contact.php');
        exit;
    }
}

// Re-populate fields after validation failure
$old = [
    'firstname'  => htmlspecialchars($_POST['firstname']  ?? ''),
    'lastname'   => htmlspecialchars($_POST['lastname']   ?? ''),
    'email'      => htmlspecialchars($_POST['email']      ?? ''),
    'country'    => $_POST['country']  ?? '',
    'newsletter' => isset($_POST['newsletter']),
    'subject'    => htmlspecialchars($_POST['subject']    ?? ''),
    'message'    => htmlspecialchars($_POST['message']    ?? ''),
];

require_once __DIR__ . '/includes/header.php';
?>

<!-- ── Page hero ──────────────────────────────────────────────── -->
<div class="page-hero">
    <div class="container">
        <h1>Contact Us</h1>
        <p>Get in touch — we read every message and reply within 48 hours.</p>
    </div>
</div>

<!-- ── Contact layout ─────────────────────────────────────────── -->
<section class="section" aria-label="Contact information and form">
    <div class="container">

        <?= html_flash('success') ?>
        <?= html_flash('error') ?>

        <div class="contact-layout">

            <!-- ── Left: map + info ───────────────────────────── -->
            <div>
                <!-- Google Maps embed -->
                <div class="contact-map">
                   	<iframe
						src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2785.830336855123!2d16.071352176926776!3d45.71444041670653!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x47667e543ebb2c65%3A0xe159703d90972cf3!2sVeleu%C4%8Dili%C5%A1te%20Velika%20Gorica!5e0!3m2!1shr!2shr!4v1780562589045!5m2!1shr!2shr"
						allowfullscreen=""
                        loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade"
                        title="SpeedNews HQ location map — Velik Gorica, Croatia"
					></iframe>
                </div>

                <!-- Contact details -->
                <div class="contact-info">

                    <div class="contact-info-item">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24">
                            <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                            <circle cx="12" cy="10" r="3"/>
                        </svg>
                        <address style="font-style:normal">
                            Zagrebačka Ul. 5,<br>
                            10410, Velika Gorica<br>
                            Croatia
                        </address>
                    </div>

                    <div class="contact-info-item">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24">
                            <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                            <polyline points="22,6 12,13 2,6"/>
                        </svg>
                        <a href="mailto:hello@speednews.com">hello@speednews.com</a>
                    </div>

                    <div class="contact-info-item">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24">
                            <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 12a19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 3.6 1.27h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L7.91 8.97a16 16 0 0 0 6 6l.92-.92a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 21.73 16.92z"/>
                        </svg>
                        <span>+385 91 1235 678</span>
                    </div>

                    <div class="contact-info-item">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24">
                            <circle cx="12" cy="12" r="10"/>
                            <polyline points="12 6 12 12 16 14"/>
                        </svg>
                        <span>Mon – Fri &nbsp;|&nbsp; 09:00 – 18:00 GMT</span>
                    </div>

                </div><!-- /.contact-info -->
            </div><!-- /.left -->

            <!-- ── Right: form ────────────────────────────────── -->
            <div>
                <div style="background:var(--clr-surface);border:1px solid var(--clr-border);border-radius:var(--radius-lg);padding:var(--sp-8)">
                    <h2 style="font-size:1.4rem;margin-bottom:var(--sp-6)">Send a Message</h2>

                    <form method="POST" action="<?= BASE_URL ?>/contact.php" novalidate>

                        <!-- Name row -->
                        <div class="form-grid-2">
                            <div class="form-group">
                                <label for="firstname">First Name <span style="color:var(--clr-accent)">*</span></label>
                                <input type="text" id="firstname" name="firstname"
                                       value="<?= $old['firstname'] ?>"
                                       placeholder="Alex"
                                       required autocomplete="given-name">
                                <?php if (isset($errors['firstname'])): ?>
                                    <span style="color:var(--clr-accent);font-size:.8rem"><?= htmlspecialchars($errors['firstname']) ?></span>
                                <?php endif; ?>
                            </div>

                            <div class="form-group">
                                <label for="lastname">Last Name <span style="color:var(--clr-accent)">*</span></label>
                                <input type="text" id="lastname" name="lastname"
                                       value="<?= $old['lastname'] ?>"
                                       placeholder="Turner"
                                       required autocomplete="family-name">
                                <?php if (isset($errors['lastname'])): ?>
                                    <span style="color:var(--clr-accent);font-size:.8rem"><?= htmlspecialchars($errors['lastname']) ?></span>
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
                                <span style="color:var(--clr-accent);font-size:.8rem"><?= htmlspecialchars($errors['email']) ?></span>
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
                                <span style="color:var(--clr-accent);font-size:.8rem"><?= htmlspecialchars($errors['country']) ?></span>
                            <?php endif; ?>
                        </div>

                        <!-- Subject -->
                        <div class="form-group">
                            <label for="subject">Subject <span style="color:var(--clr-accent)">*</span></label>
                            <input type="text" id="subject" name="subject"
                                   value="<?= $old['subject'] ?>"
                                   placeholder="Press enquiry / General question / …"
                                   required>
                            <?php if (isset($errors['subject'])): ?>
                                <span style="color:var(--clr-accent);font-size:.8rem"><?= htmlspecialchars($errors['subject']) ?></span>
                            <?php endif; ?>
                        </div>

                        <!-- Message -->
                        <div class="form-group">
                            <label for="message">Message <span style="color:var(--clr-accent)">*</span></label>
                            <textarea id="message" name="message"
                                      rows="6"
                                      placeholder="Tell us what's on your mind…"
                                      required><?= $old['message'] ?></textarea>
                            <?php if (isset($errors['message'])): ?>
                                <span style="color:var(--clr-accent);font-size:.8rem"><?= htmlspecialchars($errors['message']) ?></span>
                            <?php endif; ?>
                        </div>

                        <!-- Newsletter checkbox -->
                        <div class="form-check">
                            <input type="checkbox" id="newsletter" name="newsletter"
                                   <?= $old['newsletter'] ? 'checked' : '' ?>>
                            <label for="newsletter">
                                Subscribe to the SpeedNews weekly newsletter — no spam, unsubscribe any time.
                            </label>
                        </div>

                        <!-- Submit -->
                        <button type="submit" class="btn btn--primary btn--full"
                                style="margin-top:var(--sp-4)">
                            Send Message &rarr;
                        </button>

                        <p style="font-size:.78rem;color:var(--clr-text-faint);margin-top:var(--sp-4);text-align:center">
                            Fields marked <span style="color:var(--clr-accent)">*</span> are required.
                            Your data is never shared with third parties.
                        </p>

                    </form>
                </div>
            </div><!-- /.right -->

        </div><!-- /.contact-layout -->
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
