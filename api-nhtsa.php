<?php
require_once __DIR__ . '/includes/auth.php';

$page_title = 'Car Models (API Ninjas)';

// 🔑 API KEY
$apiKey = 'MonXjKz0gJg0XR51krhrsfUvrNPVOjOSa0JESZyX';

// ── API FUNCTION ─────────────────────────────────────────────
function get_cars(string $make, string $model = ''): ?array
{
    global $apiKey;

    $url = 'https://api.api-ninjas.com/v1/cars?make=' . urlencode($make);

    if ($model !== '') {
        $url .= '&model=' . urlencode($model);
    }

    $ch = curl_init($url);

    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
            'X-Api-Key: ' . $apiKey,
            'Accept: application/json'
        ],
        CURLOPT_TIMEOUT => 10,
    ]);

    $response = curl_exec($ch);
    $http = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    curl_close($ch);

    if ($response === false || $http !== 200) {
        return null;
    }

    return json_decode($response, true);
}

// ── INPUTS ───────────────────────────────────────────────────
$make  = trim($_GET['make'] ?? '');
$model = trim($_GET['model'] ?? '');

$data = null;
$error = '';

// ── FETCH ────────────────────────────────────────────────────
if ($make !== '') {
    $data = get_cars($make, $model);

    // JSON ispis samo testiranje
    // echo '<pre>';
    // echo json_encode($data, JSON_PRETTY_PRINT);
    // echo '</pre>';
    //exit;   
    
    if ($data === null) {
        $error = "API error or no data found.";
    }
}

require_once __DIR__ . '/includes/header.php';
?>

<div class="container" style="padding:40px 0">

    <h1>Car Search (API Ninjas)</h1>

    <!-- FORM -->
    <form method="GET" style="margin:20px 0">
        <input type="text" name="make" placeholder="Make (Ferrari, BMW...)"
               value="<?= htmlspecialchars($make) ?>" required>

        <input type="text" name="model" placeholder="Model (optional)"
               value="<?= htmlspecialchars($model) ?>">

        <div style="padding-bottom:1px">
                        <button type="submit" class="btn btn--primary">
                        Search
                        </button>
        </div>
    </form>

    <!-- ERROR -->
    <?php if ($error): ?>
        <p style="color:red"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <!-- RESULTS -->
    <?php if (!empty($data)): ?>

<h2 style="margin-bottom:20px;">Results</h2>

<div class="cars-grid">

    <?php foreach ($data as $car): ?>

        <div class="car-card">

            <h3>
                <?= htmlspecialchars($car['make'] ?? '') ?>
                <?= htmlspecialchars($car['model'] ?? '') ?>
            </h3>

            <div class="car-spec">
                <strong>Horsepower:</strong>
                <?= htmlspecialchars($car['horsepower'] ?? 'N/A') ?> HP
            </div>

            <div class="car-spec">
                <strong>Fuel:</strong>
                <?= htmlspecialchars($car['fuel_type'] ?? 'N/A') ?>
            </div>

            <div class="car-spec">
                <strong>Transmission:</strong>
                <?= htmlspecialchars($car['transmission'] ?? 'N/A') ?>
            </div>

            <div class="car-spec">
                <strong>Cylinders:</strong>
                <?= htmlspecialchars($car['cylinders'] ?? 'N/A') ?>
            </div>

        </div>

    <?php endforeach; ?>

</div>

<?php endif; ?>

</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
