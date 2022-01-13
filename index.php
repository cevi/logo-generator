<?php
    $package_raw = file_get_contents('package.json');
    $package_json = json_decode($package_raw,true);
    $version = $package_json['version'];
?>
<!DOCTYPE html>
<html>

<head>
    <title>Cevi-Logo Generator</title>
    <meta name="og:image" content="https://logo.cevi.ch/assets/images/logo.svg">
    <meta name="description" content="Logo Generator für dein eigenes Cevi-Logo">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="author" content="Beat Temperli v/o Zottel">

    <link rel="icon" type="image/png" href="assets/images/favicon.png">
    <link rel="stylesheet" href="assets/css/styles.css?v<?php echo $version; ?>" />
</head>

<body>

<?php
    require_once 'ApiHelper.php';

    $show_iframe = key_exists('iframe', $_GET) && $_GET['iframe'] === 'true';

    $input_logo['left'] = 'CEVI';
    $input_logo['right'] = 'SCHWEIZ';
    $input_logo['right-second'] = '';
    $claim['left'] = 'VIELFALT GEMEINSAM';
    $claim['right'] = ' ERLEBEN.';

    $api_helper = new ApiHelper();
    $id = $api_helper->getNewSessionId();

    foreach (array_keys($input_logo) as $key) {
        if (isset($_GET['logo-' . $key])) {
            $input_logo[$key] = $_GET['logo-' . $key];
        }
    }

    foreach (array_keys($claim) as $key) {
        if (isset($_GET['claim-' . $key])) {
            $claim[$key] = $_GET['claim-' . $key];
        }
    }
?>

    <?php if (!$show_iframe): ?>
    <header class="header-bar">
        <div class="logowrapper">
            <a href="https://logo.cevi.ch/" class="logo">
                <img alt="Logo Cevi Schweiz" class="image" src="assets/images/logo.svg">
                <img alt="Kurzes Logo Cevi Schweiz" class="image -short" src="assets/images/logo.svg">
            </a>
        </div>
    </header>
    <?php endif; ?>

    <div class="logo-generator <?php if ($show_iframe): echo '-iframe'; endif; ?>">
        <h1>Cevi Logo Generator <small>[v<?php echo $version; ?>]</small></h1>

        <form name="logoGeneratorForm" class="userinputform">
            <div class="userinputs">
                <div class="form-wrapper inputwrapper">
                    <label for="input-logo-left">Logo links</label>
                    <input id="input-logo-left" class="input" name="left" value="<?php echo $input_logo['left']; ?>">
                </div>
                <div class="form-wrapper inputwrapper">
                    <label for="input-logo-right">Logo rechts (1. Zeile)</label>
                    <input id="input-logo-right" class="input" name="logo-right" value="<?php echo $input_logo['right']; ?>">
                </div>
                <div class="form-wrapper inputwrapper">
                    <label for="input-logo-right-second">Logo rechts (2. Zeile)</label>
                    <input id="input-logo-right-second" class="input" name="logo-right-second" value="<?php echo $input_logo['right-second']; ?>">
                </div>
                <div class="form-wrapper inputwrapper">
                    <label for="input-claim-left">Claim links</label>
                    <input id="input-claim-left" class="input" name="claim-left" value="<?php echo $claim['left']; ?>">
                </div>
                <div class="form-wrapper inputwrapper">
                    <label for="input-claim-right">Claim rechts</label>
                    <input id="input-claim-right" class="input" name="claim-right" value="<?php echo $claim['right']; ?>">
                </div>
            </div>
            <div class="form-wrapper">
                <legend>Farben für die Ausgabe</legend>
                <div>
                    <input type="radio" id="web"
                           name="color" value="web" checked />
                    <label class="-radio" for="web">RGB (Web / Online)</label>
                </div>

                <div>
                    <input type="radio" id="print"
                           name="color" value="print" />
                    <label class="-radio" for="print">CMYK (Druck / Druckerei)</label>
                </div>

                <div>
                    <input type="radio" id="black"
                           name="color" value="black" />
                    <label class="-radio" for="black">Schwarz</label>
                </div>
            </div>
        </form>

        <h2>Generierte Logos</h2>

        <div class="generatoroutput">
            <div class="info">SVG Logo (Vektorgrafik)</div>
            <div class="svgwrapper" id="svg-logo-output"></div>
            <div class="linkwrapper">
                <a class="link" href="#" target="_blank" id="svg-logo-link" download="logo.svg">Download SVG Logo</a>
            </div>
        </div>

        <div class="generatoroutput">
            <div class="info">
                PNG Logo
            </div>
            <img src="#" alt="Logo Ausgabe im Format PNG" id="png-logo-output"/>
            <div class="linkwrapper">
                <a class="link" href="#" target="_blank" id="png-logo-link" download="logo.png">Download PNG Logo</a>
            </div>
        </div>

        <div class="generatoroutput">
            <div class="info">
                JPG Logo
            </div>
            <img src="#" alt="Logo Ausgabe im Format JPG" id="jpg-logo-output"/>
            <div class="linkwrapper">
                <a class="link" href="#" target="_blank" id="jpg-logo-link" download="logo.jpg">Download JPG Logo</a>
            </div>
        </div>

        <div class="clear-fix"></div>

        <h2>Generierte Claims</h2>

        <div class="generatoroutput -claim">
            <div class="info">SVG Claim (Vektorgrafik)</div>
            <div class="svgwrapper" id="svg-claim-output"></div>
            <div class="linkwrapper">
                <a class="link" href="#" target="_blank" id="svg-claim-link" download="claim.svg">→</a>
            </div>
        </div>

        <div class="generatoroutput -claim">
            <div class="info">
                PNG Claim (teilweise transparent)
            </div>
            <img src="#" alt="Claim Ausgabe im Format PNG" id="png-claim-output"/>
            <div class="linkwrapper">
                <a class="link" href="#" target="_blank" id="png-claim-link" download="claim.png">→</a>
            </div>
        </div>

        <div class="clear-fix"></div>

        <div class="generatoroutput hidden">
            <div class="info">
                CANVAS LOGO
            </div>
            <canvas id="canvas-logo-output" />
        </div>

        <div class="generatoroutput hidden">
            <div class="info">
                CANVAS CLAIM
            </div>
            <canvas id="canvas-claim-output" />
        </div>

        <div class="generatoroutput hidden">
            <svg id="svg-test"></svg>
        </div>
    </div>

    <script>
        document.session_id = '<?php echo $id; ?>';
    </script>

    <script src="assets/js/scripts.min.js?v<?php echo $version; ?>"></script>
</body>

</html>
