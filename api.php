<?php
session_start();
$file = 'data.json';

// Initialize data if file doesn't exist
if (!file_exists($file)) {
    file_put_contents($file, json_encode(['queue' => [], 'yes' => 0, 'no' => 0, 'remarks' => []]));
}

$data = json_decode(file_get_contents($file), true);

$action = $_GET['action'] ?? '';
$username = $_SESSION['username'] ?? '';

if ($action == 'status') {
    echo json_encode(['data' => $data, 'myUser' => $username]);
    exit;
}

if ($action == 'toggleQueue' && $username) {
    if (($key = array_search($username, $data['queue'])) !== false) {
        unset($data['queue'][$key]);
        $data['queue'] = array_values($data['queue']); // Re-index
    } else {
        $data['queue'][] = $username;
    }
}

if ($action == 'vote' && $username) {
    $type = $_GET['type']; // 'yes' or 'no'
    $data[$type] = ($_GET['mode'] == 'add') ? $data[$type] + 1 : max(0, $data[$type] - 1);
}

if ($action == 'remark' && $username) {
    $msg = $_POST['remark'] ?? '';
    if ($msg == "") {
        unset($data['remarks'][$username]);
    } else {
        $data['remarks'][$username] = htmlspecialchars($msg);
    }
}

if ($action == 'reset') {
    $data = ['queue' => [], 'yes' => 0, 'no' => 0, 'remarks' => []];
}

// Add this inside your action checks in api.php
if ($action == 'leave' && $username) {
    // 1. Remove from queue
    if (($key = array_search($username, $data['queue'])) !== false) {
        unset($data['queue'][$key]);
        $data['queue'] = array_values($data['queue']);
    }
    
    // 2. Remove their remark
    unset($data['remarks'][$username]);

    // 3. Save the cleaned data
    file_put_contents($file, json_encode($data));

    // 4. Kill the session and redirect
    session_destroy();
    header("Location: index.php");
    exit;
}
file_put_contents($file, json_encode($data));
echo json_encode($data);