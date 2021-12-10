<?php
    // Do not answer on any request except POST-Request.
    if ($_SERVER['REQUEST_METHOD'] != "POST") {
        http_response_code(405)
?>

<!DOCTYPE html>
<html>

<head>
    <title>Cevi-Logo Generator: API</title>
    <meta name="og:image" content="https://logo.cevi.ch/assets/images/logo.svg">
    <meta name="description" content="Logo Generator für dein eigenes Cevi-Logo">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
</head>

<body>
    <h1>This method is not allowed</h1>
</body>

</html>
<?php
        // quit any request except POST.
        return;
    }

    if (!isset($_POST['session_id'])) {
        http_response_code(400);
        echo '[1] data is missing.';
        return;
    }

    require_once 'ApiHelper.php';
    $apiHelper = new ApiHelper();
    $session_id = $_POST['session_id'];

    if (!$apiHelper->checkSessionId($session_id)) {
        http_response_code(403);
        echo 'Invalid Data.';
        return;
    }

    if (!isset($_POST['type'])) {
        http_response_code(400);
        echo '[2] data is missing.';
        return;
    }

    $type = $_POST['type'];

    if (!isset($_POST['image_type']) || !isset($_POST['logo_left']) || !isset($_POST['logo_right']) || !isset($_POST['logo_right_second'])) {
        http_response_code(400);
        echo '[3] data is missing.';
        return;
    }

    if ($type === 'logo') {
        $apiHelper->saveDataLogo($_POST);
    }

    else if ($type === 'claim') {
        if (!isset($_POST['claim_left']) || !isset($_POST['claim_right'])) {
            http_response_code(400);
            echo '[4] data is missing.';
            return;
        }

        $apiHelper->saveDataClaim($_POST);
    }

    http_response_code(200);
    header('Content-Type: application/json; charset=utf-8');

    $return = [
        'status' => 200,
        'type' => $type,
        'message' => 'success'
    ];

    echo json_encode($return);

?>
