<?php
// Site-survey form handler for kuontamsystems.co.ke (cPanel/PHP hosting).
// Sends each request to info@kuontamsystems.co.ke. No database, no third-party service.
$TO   = 'info@kuontamsystems.co.ke';
$FROM = 'website@kuontamsystems.co.ke';
$ALLOWED_ORIGINS = ['https://kuontamsystems.co.ke','https://www.kuontamsystems.co.ke','https://kuontam.navac.co.ke','https://kuontam-website.web.app'];

$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
if (in_array($origin, $ALLOWED_ORIGINS, true)) {
  header('Access-Control-Allow-Origin: ' . $origin);
  header('Vary: Origin');
  header('Access-Control-Allow-Methods: POST, OPTIONS');
  header('Access-Control-Allow-Headers: Content-Type');
}
header('Content-Type: application/json; charset=utf-8');
header('X-Content-Type-Options: nosniff');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(204); exit; }
function out($code, $ok, $msg) { http_response_code($code); echo json_encode(['ok'=>$ok,'message'=>$msg]); exit; }
if ($_SERVER['REQUEST_METHOD'] !== 'POST') out(405, false, 'Method not allowed');

// Spam guards: honeypot, minimum fill time, per-IP rate limit (5 per hour).
if (!empty($_POST['company_site'])) out(200, true, 'Thanks');            // bots fill hidden field; pretend success
if ((int)($_POST['elapsed'] ?? 0) < 3000) out(400, false, 'Please try again.');
$ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
$rl = sys_get_temp_dir() . '/kuontam_form_' . md5($ip);
$hits = array_filter(array_map('intval', @file($rl, FILE_IGNORE_NEW_LINES) ?: []), fn($t) => $t > time() - 3600);
if (count($hits) >= 5) out(429, false, 'Too many requests. Please call or email us.');

$clean = fn($v, $max) => trim(mb_substr(preg_replace('/[\r\n\t]+/', ' ', strip_tags((string)$v)), 0, $max));
$BUILDINGS = ['Home','Office or retail','School or clinic','Industrial','New development'];
$SYSTEMS   = ['Solar & power','Automation','Access & CCTV','Fire','Cabling'];
$building = $clean($_POST['building'] ?? '', 40);
$systems  = array_values(array_intersect($SYSTEMS, array_map(fn($s) => $clean($s, 40), (array)($_POST['systems'] ?? []))));
$name     = $clean($_POST['name'] ?? '', 80);
$phone    = $clean($_POST['phone'] ?? '', 30);
$location = $clean($_POST['location'] ?? '', 120);

if (!in_array($building, $BUILDINGS, true)) out(400, false, 'Please choose a building type.');
if (mb_strlen($name) < 2) out(400, false, 'Please enter your name.');
if (!preg_match('/^\+?[0-9 ()-]{7,20}$/', $phone)) out(400, false, 'Please enter a valid phone number.');

$subject = 'Site survey request: ' . $building . ' (' . $name . ')';
$body = "New site survey request from the website\n\n"
      . "Name:      $name\n"
      . "Phone:     $phone\n"
      . "Location:  " . ($location ?: '-') . "\n"
      . "Building:  $building\n"
      . "Systems:   " . ($systems ? implode(', ', $systems) : '-') . "\n\n"
      . "Sent from: " . ($origin ?: 'unknown') . "\n"
      . "Time:      " . date('D j M Y, H:i') . " (server time)\n";
$headers = "From: Kuontam Website <$FROM>\r\n"
         . "Content-Type: text/plain; charset=UTF-8\r\n"
         . "X-Mailer: kuontam-site\r\n";
if (!@mail($TO, '=?UTF-8?B?' . base64_encode($subject) . '?=', $body, $headers, '-f' . $FROM)) out(500, false, 'Could not send. Please call or email us.');

$hits[] = time(); @file_put_contents($rl, implode("\n", $hits));
out(200, true, 'Sent');
