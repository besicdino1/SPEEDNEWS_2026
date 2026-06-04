<?php
require_once __DIR__ . '/includes/auth.php';

$page_title = 'VIN Decoder';

// ── Key fields to surface in the summary card ─────────────────
const VIN_HIGHLIGHT = [
    'Make'               => 'Make',
    'Model'              => 'Model',
    'ModelYear'          => 'Year',
    'BodyClass'          => 'Body Style',
    'EngineCylinders'    => 'Cylinders',
    'DisplacementL'      => 'Displacement (L)',
    'FuelTypePrimary'    => 'Fuel Type',
    'DriveType'          => 'Drive Type',
    'TransmissionStyle'  => 'Transmission',
    'PlantCountry'       => 'Manufactured In',
];

// ── Server-side fetch helpers ─────────────────────────────────
function vin_fetch_json(string $vin): ?array
{
    $url = 'https://vpic.nhtsa.dot.gov/api/vehicles/decodevin/'
         . rawurlencode($vin) . '?format=json';
    $ctx = stream_context_create(['http' => ['method' => 'GET', 'timeout' => 10, 'ignore_errors' => true]]);
    $raw = @file_get_contents($url, false, $ctx); // OVDJE DOBIJEM JSON
    if ($raw === false) return null;
    $data = json_decode($raw, true);
    return (json_last_error() === JSON_ERROR_NONE) ? $data : null;
}

function vin_fetch_xml(string $vin): string
{
    $url = 'https://vpic.nhtsa.dot.gov/api/vehicles/decodevin/'
         . rawurlencode($vin) . '?format=xml';
    $ctx = stream_context_create(['http' => ['method' => 'GET', 'timeout' => 10, 'ignore_errors' => true]]);
    $raw = @file_get_contents($url, false, $ctx);
    return ($raw !== false) ? $raw : '';
}

function vin_pretty_xml(string $xml): string
{
    if ($xml === '') return '';
    $dom = new DOMDocument('1.0');
    $dom->preserveWhiteSpace = false;
    $dom->formatOutput       = true;
    if (!@$dom->loadXML($xml)) return $xml;
    return $dom->saveXML();
}

// ── Process GET request ───────────────────────────────────────
$raw_vin    = strtoupper(trim($_GET['vin'] ?? ''));
$vin        = preg_replace('/[^A-Z0-9]/', '', $raw_vin); // strip dashes/spaces
$json_data  = null;
$xml_raw    = '';
$xml_pretty = '';
$field_map  = [];
$vin_error  = '';

if ($vin !== '') {
    if (strlen($vin) !== 17) {
        $vin_error = 'A VIN must be exactly 17 alphanumeric characters. You entered ' . strlen($vin) . '.';
    } else {
        // Parallel fetch is not native in PHP — run sequentially
        $json_data  = vin_fetch_json($vin);
        $xml_raw    = vin_fetch_xml($vin);
        $xml_pretty = vin_pretty_xml($xml_raw);

        if ($json_data === null && $xml_raw === '') {
            $vin_error = 'Could not reach the NHTSA vPIC API. Check your internet connection and try again.';
        } elseif ($json_data !== null) {
            // Build variable → value map from JSON results
            foreach ($json_data['Results'] ?? [] as $r) {
                if (!empty($r['Value']) && $r['Value'] !== 'Not Applicable') {
                    $field_map[$r['Variable']] = $r['Value'];
                }
            }
        }
    }
}

require_once __DIR__ . '/includes/header.php';
?>

<!-- ── Page hero ──────────────────────────────────────────────── -->
<div class="page-hero">
    <div class="container">
        <h1>VIN Decoder</h1>
        <p>Decode any 17-character Vehicle Identification Number via the
           NHTSA vPIC API — returns both JSON and XML responses.</p>
    </div>
</div>

<section class="section" aria-label="VIN decoder tool">
    <div class="container">

        <!-- ── Search form ─────────────────────────────────────── -->
        <div class="api-search-box">
            <h2 style="font-size:1.2rem;margin-bottom:var(--sp-2)">Enter a VIN Number</h2>
            <p style="color:var(--clr-text-muted);font-size:.9rem;margin-bottom:var(--sp-6)">
                A VIN (Vehicle Identification Number) is the unique 17-character code
                assigned to every motor vehicle. Find it on the dashboard, door jamb,
                or vehicle documents.
            </p>

            <form id="vin-form" method="GET" action="<?= BASE_URL ?>/api-vin.php" novalidate>
                <div style="display:grid;grid-template-columns:1fr auto;gap:var(--sp-4);align-items:flex-end">

                    <div class="form-group" style="margin-bottom:0">
                        <label for="vin-input">
                            VIN <span style="color:var(--clr-accent)">*</span>
                            <span style="font-weight:400;text-transform:none;letter-spacing:0;color:var(--clr-text-faint);font-size:.8rem">
                                — 17 characters, letters and digits only
                            </span>
                        </label>
                        <input type="text" id="vin-input" name="vin"
                               value="<?= htmlspecialchars($raw_vin) ?>"
                               placeholder="e.g. 1HGBH41JXMN109186"
                               maxlength="17"
                               pattern="[A-Za-z0-9]{17}"
                               autocomplete="off"
                               style="font-family:var(--font-mono);letter-spacing:2px;font-size:1.05rem"
                               required>
                        <span class="form-hint">
                            Sample VIN for testing:
                            <a href="?vin=1HGBH41JXMN109186" style="font-family:var(--font-mono)">1HGBH41JXMN109186</a>
                            (2021 Honda Civic)<br>
							<i>*[ -- Just click on Honda VIN -- ]</i>
                        </span>
                    </div>

                    <div style="padding-bottom:1px">
                        <button type="submit" class="btn btn--primary">
                            Decode VIN
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- ── Error ───────────────────────────────────────────── -->
        <?php if ($vin_error !== ''): ?>
            <div class="alert alert--error"><?= htmlspecialchars($vin_error) ?></div>
        <?php endif; ?>

        <!-- ── JS-driven results land here (main.js §8) ─────────── -->
        <div id="vin-results">

        <?php if ($json_data !== null && $vin_error === ''): ?>

            <!-- ── Summary card ────────────────────────────────── -->
            <div class="vin-card">
                <div class="vin-card-header">
                    <span style="font-size:1.8rem">🚗</span>
                    <div>
                        <h2 style="font-size:1.2rem;margin:0">
                            <?= htmlspecialchars(
                                trim(
                                    ($field_map['ModelYear'] ?? '')  . ' ' .
                                    ($field_map['Make']      ?? '')  . ' ' .
                                    ($field_map['Model']     ?? '')
                                ) ?: 'Vehicle Details'
                            ) ?>
                        </h2>
                        <span style="font-size:.85rem;color:var(--clr-text-muted);font-family:var(--font-mono)">
                            VIN: <?= htmlspecialchars($vin) ?>
                        </span>
                    </div>
                </div>

                <!-- Highlighted fields grid -->
                <dl class="vin-fields">
                    <?php foreach (VIN_HIGHLIGHT as $key => $label): ?>
                        <?php if (isset($field_map[$key])): ?>
                        <div class="vin-field">
                            <dt><?= htmlspecialchars($label) ?></dt>
                            <dd><?= htmlspecialchars($field_map[$key]) ?></dd>
                        </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </dl>
            </div>

            <!-- ── Full response tabs ──────────────────────────── -->
            <div class="tab-container" style="margin-top:var(--sp-8)">
                <h3 style="font-size:1rem;color:var(--clr-text-muted);margin-bottom:var(--sp-4)">
                    Raw API Responses
                </h3>

                <div class="tab-bar" role="tablist" aria-label="API response format">
                    <button class="tab-btn active" type="button"
                            role="tab" aria-selected="true"
                            aria-controls="tab-json" id="btn-json">
                        JSON Response
                    </button>
                    <button class="tab-btn" type="button"
                            role="tab" aria-selected="false"
                            aria-controls="tab-xml" id="btn-xml">
                        XML Response
                    </button>
                </div>

                <!-- JSON tab -->
                <div class="tab-panel active" id="tab-json" role="tabpanel" aria-labelledby="btn-json">
                    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:var(--sp-3)">
                        <span style="font-size:.8rem;color:var(--clr-text-muted)">
                            <?= count($json_data['Results'] ?? []) ?> fields returned
                        </span>
                        <span class="tag">JSON</span>
                    </div>
                    <pre class="code-block"><?= htmlspecialchars(  // OVDJE JE ZAPRAVO JSON
                        json_encode($json_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
                    ) ?></pre>
                </div>

                <!-- XML tab -->
                <div class="tab-panel" id="tab-xml" role="tabpanel" aria-labelledby="btn-xml">
                    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:var(--sp-3)">
                        <span style="font-size:.8rem;color:var(--clr-text-muted)">
                            Pretty-printed via PHP <code>DOMDocument</code>
                        </span>
                        <span class="tag">XML</span>
                    </div>
                    <pre class="code-block"><?= htmlspecialchars($xml_pretty ?: $xml_raw) ?></pre>  // OVDJE JE ZAPRAVO XML
                </div>

            </div><!-- /.tab-container -->

            <!-- All decoded fields table -->
            <?php
                $all_fields = array_filter(
                    $json_data['Results'] ?? [],
                    fn($r) => !empty($r['Value']) && $r['Value'] !== 'Not Applicable'
                );
            ?>
            <?php if (!empty($all_fields)): ?>
            <div style="margin-top:var(--sp-10)">
                <h3 style="font-size:1rem;margin-bottom:var(--sp-4)">
                    All Decoded Fields
                    <small style="font-weight:400;color:var(--clr-text-muted)">
                        (<?= count($all_fields) ?> non-empty values)
                    </small>
                </h3>
                <div class="table-wrap">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Variable ID</th>
                                <th>Field Name</th>
                                <th>Value</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($all_fields as $r): ?>
                            <tr>
                                <td style="color:var(--clr-text-faint);font-family:var(--font-mono);font-size:.82rem">
                                    <?= htmlspecialchars((string)($r['VariableId'] ?? '')) ?>
                                </td>
                                <td style="font-weight:500">
                                    <?= htmlspecialchars($r['Variable'] ?? '') ?>
                                </td>
                                <td><?= htmlspecialchars($r['Value'] ?? '') ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php endif; ?>

        <?php elseif ($vin === ''): ?>
            <!-- Initial empty state -->
            <div style="text-align:center;padding:var(--sp-16);color:var(--clr-text-muted)">
                <div style="font-size:3.5rem;margin-bottom:var(--sp-4)">🔎</div>
                <h3 style="color:var(--clr-text-muted);font-weight:400">
                    Enter a VIN above to decode it
                </h3>
                <p style="font-size:.9rem;margin-top:var(--sp-3)">
                    No account or API key needed — powered by the free NHTSA vPIC database.
                </p>
            </div>
        <?php endif; ?>

        </div><!-- /#vin-results -->

        <!-- ── What is a VIN? ─────────────────────────────────── -->
        <div style="margin-top:var(--sp-16);background:var(--clr-surface);border:1px solid var(--clr-border);border-radius:var(--radius-md);padding:var(--sp-8)">
            <h3 style="font-size:1.1rem;margin-bottom:var(--sp-5)">How to Read a VIN</h3>
            <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:var(--sp-5)">
                <?php
                $vin_parts = [
                    ['1–3',   'World Manufacturer Identifier (WMI)', 'Country, manufacturer, vehicle type.'],
                    ['4–8',   'Vehicle Descriptor Section (VDS)',    'Model, body style, engine, restraint systems.'],
                    ['9',     'Check Digit',                         'Mathematical validation digit to detect transcription errors.'],
                    ['10',    'Model Year',                          'Encoded as a letter or digit (e.g. M = 1991, K = 2019).'],
                    ['11',    'Plant Code',                          'The assembly plant where the vehicle was built.'],
                    ['12–17', 'Production Sequence Number',          'Unique serial number for this specific vehicle.'],
                ];
                foreach ($vin_parts as [$pos, $name, $desc]): ?>
                <div style="border-left:3px solid var(--clr-accent);padding-left:var(--sp-4)">
                    <div style="font-family:var(--font-mono);font-size:.75rem;color:var(--clr-accent);margin-bottom:var(--sp-1)">
                        Position <?= $pos ?>
                    </div>
                    <div style="font-weight:600;font-size:.9rem;margin-bottom:var(--sp-1);color:var(--clr-white)">
                        <?= htmlspecialchars($name) ?>
                    </div>
                    <div style="font-size:.82rem;color:var(--clr-text-muted)">
                        <?= htmlspecialchars($desc) ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <div style="margin-top:var(--sp-6);padding-top:var(--sp-5);border-top:1px solid var(--clr-border)">
                <p style="font-size:.85rem;color:var(--clr-text-muted);margin:0">
                    <strong style="color:var(--clr-text)">API endpoints used:</strong><br>
                    <code style="font-size:.78rem;color:#a5d6a7">
                        GET https://vpic.nhtsa.dot.gov/api/vehicles/decodevin/{vin}?format=json<br>
                        GET https://vpic.nhtsa.dot.gov/api/vehicles/decodevin/{vin}?format=xml
                    </code>
                </p>
            </div>
        </div>

    </div><!-- /.container -->
</section>

<!-- Wire up PHP-rendered tabs (JS from main.js handles JS-rendered ones) -->
<script>
(function () {
    document.querySelectorAll('.tab-bar[role="tablist"]').forEach(function (bar) {
        const btns   = bar.querySelectorAll('.tab-btn');
        const cont   = bar.closest('.tab-container');
        const panels = cont ? cont.querySelectorAll('.tab-panel') : [];
        btns.forEach(function (btn, idx) {
            btn.addEventListener('click', function () {
                btns.forEach(function (b) {
                    b.classList.remove('active');
                    b.setAttribute('aria-selected', 'false');
                });
                panels.forEach(function (p) { p.classList.remove('active'); });
                btn.classList.add('active');
                btn.setAttribute('aria-selected', 'true');
                if (panels[idx]) panels[idx].classList.add('active');
            });
        });
    });
}());
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
