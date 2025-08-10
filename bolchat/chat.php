<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Güvenlik: İstenmeyen IP'yi engelle
$ip = $_SERVER['REMOTE_ADDR'];
if ($ip == "31.14.252.4") {
    die();
}

$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';
//$kullaniciAdi = isset($_SESSION['kullaniciAdi']) ? $_SESSION['kullaniciAdi'] : '';

try {
    switch ($action) {
        case 'get_messages':
            $result = mysql_query("SELECT * FROM chat_messages ORDER BY id DESC LIMIT 50");
            if (!$result) {
                throw new Exception('Sorgu hatası: ' . mysql_error());
            }

            $messages = array();
            while ($row = mysql_fetch_assoc($result)) {
                $messages[] = array(
                    'id' => $row['id'],
                    'username' => $row['username'],
                    'message' => $row['message'],
                    'created_at' => $row['created_at']
                );
            }

            // ID'ye göre sırala (artan sıra)
            usort($messages, function($a, $b) {
                return $a['id'] - $b['id'];
            });

            header('Content-Type: application/json; charset=utf-8');
            echo json_encode($messages);
            exit;

        case 'get_online_count':
            $threshold = time() - 300;
            $result = mysql_query("SELECT COUNT(DISTINCT username) as count FROM chat_messages WHERE UNIX_TIMESTAMP(created_at) > $threshold");
            if (!$result) {
                throw new Exception('Sorgu hatası: ' . mysql_error());
            }
            $row = mysql_fetch_assoc($result);
            echo isset($row['count']) ? (int)$row['count'] : 0;
            break;

        case 'send_message':
            // Girişli kullanıcı varsa onun nicki
            if ($kullaniciAdi) {
                $nick = mysql_real_escape_string($kullaniciAdi);
            } else {
                // Anonim kullanıcı için nick kontrolü
                if (empty($_POST['nick'])) {
                    header('HTTP/1.1 400 Bad Request');
                    die('Nick boş olamaz.');
                }
                $nickInput = mysql_real_escape_string($_POST['nick']);

                // Burada "kullanici_tablosu" kısmını kendi kullanıcı tablon ile değiştir
                // Ve nick alanı senin veritabanındaki kullanıcı adı sütun adı olmalı
                $query = "SELECT COUNT(*) AS count FROM user WHERE nick = '$nickInput'";
                $res = mysql_query($query);
                if (!$res) {
                    header('HTTP/1.1 500 Internal Server Error');
                    die('Kullanıcı sorgu hatası.');
                }
                $row = mysql_fetch_assoc($res);
                if ($row['count'] > 0) {
                    header('HTTP/1.1 403 Forbidden');
                    die('Bu nick kullanılamaz.');
                }
                $nick = $nickInput;
            }

            // Rate Limiting (IP ve nick bazında)

            $ipEsc = mysql_real_escape_string($ip);

            // 1) Çok kısa aralık (en az 5 saniye)
            $lastMsgRes = mysql_query("
                SELECT created_at 
                FROM chat_messages 
                WHERE ip = '$ipEsc' OR username = '$nick' 
                ORDER BY id DESC LIMIT 1
            ");
            if ($lastMsgRes && mysql_num_rows($lastMsgRes) > 0) {
                $lastRow = mysql_fetch_assoc($lastMsgRes);
                if (strtotime($lastRow['created_at']) > time() - 5) {
                    header('HTTP/1.1 429 Too Many Requests');
                    die('Mesajlar arasında en az 5 saniye beklemelisiniz.');
                }
            }

            // 2) Saatlik limit (50 mesaj)
            $lastHour = date('Y-m-d H:i:s', time() - 3600);
            $hourCountRes = mysql_query("
                SELECT COUNT(*) AS count 
                FROM chat_messages 
                WHERE (ip = '$ipEsc' OR username = '$nick') 
                AND created_at > '$lastHour'
            ");
            $hourCountRow = mysql_fetch_assoc($hourCountRes);
            if ($hourCountRow['count'] > 50) {
                header('HTTP/1.1 429 Too Many Requests');
                die('Saatlik mesaj limitine ulaştınız. Lütfen biraz bekleyin.');
            }

            // 3) Günlük limit (500 mesaj, opsiyonel)
            $lastDay = date('Y-m-d H:i:s', time() - 86400);
            $dayCountRes = mysql_query("
                SELECT COUNT(*) AS count 
                FROM chat_messages 
                WHERE (ip = '$ipEsc' OR username = '$nick') 
                AND created_at > '$lastDay'
            ");
            $dayCountRow = mysql_fetch_assoc($dayCountRes);
            if ($dayCountRow['count'] > 500) {
                header('HTTP/1.1 429 Too Many Requests');
                die('Günlük mesaj limitine ulaştınız.');
            }

            // Mesaj ve nick doluluk kontrolü
            if (isset($_POST['message']) && trim($_POST['message']) !== '') {
                $message = mysql_real_escape_string(trim($_POST['message']));

                $insertQuery = "INSERT INTO chat_messages (username, message, ip) VALUES ('$nick', '$message', '$ipEsc')";
                if (!mysql_query($insertQuery)) {
                    throw new Exception('Mesaj eklenemedi: ' . mysql_error());
                }
                echo 'OK';
            } else {
                throw new Exception('Mesaj boş olamaz.');
            }
            break;

        default:
            throw new Exception('Geçersiz istek');
    }
} catch (Exception $e) {
    header('HTTP/1.1 500 Internal Server Error');
    die($e->getMessage());
}
