<?php
session_start();
$email = trim($_POST['email'] ?? '');
$email_confirm = trim($_POST['email_confirm'] ?? '');
$name = trim($_POST['name'] ?? '');
$company = trim($_POST['company'] ?? '');
$department = trim($_POST['department'] ?? '');
$password = $_POST['password'] ?? '';

$errors = [];
if (!$email || !$name || !$email_confirm) {
    $errors[] = "必須項目が入力されていません。";
}
if ($email !== $email_confirm) {
    $errors[] = "メールアドレスが一致しません。";
}
if (!empty($errors)) {
    foreach ($errors as $error) {
        echo "<p>$error</p>";
    }
    echo '<p><a href="register.html">戻る</a></p>';
    exit;
}

$csv_file = __DIR__ . '/secure/users.csv';
if (!file_exists($csv_file)) {
    file_put_contents($csv_file, "email,name,company,department,password\n");
}
$hashed_pw = password_hash($password, PASSWORD_DEFAULT);
fputcsv(fopen($csv_file, 'a'), [$email, $name, $company, $department, $hashed_pw]);

$api_url = "https://api.bme.jp/rest/1.0/contact/detail/create";
$access_token = "トークンはこちら";

$data = [
    'access_token' => $access_token,
    '__c15__' => $email,
    '__c0__' => $name,
    '__c2__' => $company,
    '__c4__' => $department
];

$options = [
    'http' => [
        'method' => 'POST',
        'header' => "Content-Type: application/x-www-form-urlencoded",
        'content' => http_build_query($data)
    ]
];

$context = stream_context_create($options);
$result = file_get_contents($api_url, false, $context);

if ($result === FALSE) {
    echo "<p>API送信に失敗しました。</p>";
} else {
    echo "<p>登録が完了しました。</p>";
}
?>