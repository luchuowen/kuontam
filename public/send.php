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
$email    = $clean($_POST['email'] ?? '', 120);
$location = $clean($_POST['location'] ?? '', 120);

if (!in_array($building, $BUILDINGS, true)) out(400, false, 'Please choose a building type.');
if (mb_strlen($name) < 2) out(400, false, 'Please enter your name.');
if (!preg_match('/^\+?[0-9 ()-]{7,20}$/', $phone)) out(400, false, 'Please enter a valid phone number.');
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) out(400, false, 'Please enter a valid email address.');

date_default_timezone_set('Africa/Nairobi');
$ref    = 'KS-' . date('ymd') . '-' . strtoupper(bin2hex(random_bytes(2)));
$digits = preg_replace('/\D+/', '', $phone);
if (preg_match('/^0[17]\d{8}$/', $digits)) $digits = '254' . substr($digits, 1);   // Kenyan 07xx / 01xx -> 2547xx
$tel    = (strpos($phone, '+') === 0 || strpos($digits, '254') === 0) ? '+' . $digits : $digits;
$source = parse_url($origin, PHP_URL_HOST) ?: 'kuontamsystems.co.ke';
$when   = date('D j M Y, H:i') . ' EAT';

$subject = 'New site survey request: ' . $building . ' (' . $name . ')';
$text = "NEW SITE SURVEY REQUEST  $ref\n\n"
      . "Name:      $name\n"
      . "Phone:     $phone\n"
      . "Email:     $email\n"
      . "Location:  " . ($location ?: '-') . "\n"
      . "Building:  $building\n"
      . "Systems:   " . ($systems ? implode(', ', $systems) : '-') . "\n\n"
      . "Next step: call within one working day to confirm a survey date.\n\n"
      . "Received $when via $source\n";
require __DIR__ . '/email-template.php';
$html = kq_email_html(['name'=>$name,'phone'=>$phone,'email'=>$email,'tel'=>$tel,'wa'=>$digits,'location'=>$location,'building'=>$building,
                       'systems'=>$systems,'ref'=>$ref,'time'=>$when,'source'=>$source]);

// PHP mail() is disabled on this host, so deliver over SMTP to the local mail server.
// The recipient mailbox lives on this same server, so no SMTP login is needed.
function smtp_send($to, $from, $subject, $text, $html, $replyTo = '') {
  $fp = @fsockopen('localhost', 25, $en, $es, 10);
  if (!$fp) return false;
  stream_set_timeout($fp, 15);
  $read = function () use ($fp) { $d = ''; while (($l = fgets($fp, 515)) !== false) { $d .= $l; if (isset($l[3]) && $l[3] === ' ') break; } return $d; };
  $cmd  = function ($c, $ok) use ($fp, $read) { fwrite($fp, $c . "\r\n"); $r = $read(); return strpos($r, (string)$ok) === 0; };
  $host = 'kuontamsystems.co.ke';
  if (strpos($read(), '220') !== 0) return false;
  if (!$cmd("EHLO $host", 250)) return false;
  if (!$cmd("MAIL FROM:<$from>", 250)) return false;
  if (!$cmd("RCPT TO:<$to>", 250)) return false;
  if (!$cmd('DATA', 354)) return false;
  $b = 'kq_' . bin2hex(random_bytes(10));
  $part = fn($type, $body) => "--$b\r\nContent-Type: $type; charset=UTF-8\r\nContent-Transfer-Encoding: base64\r\n\r\n" . chunk_split(base64_encode($body)) . "\r\n";
  $msg = "From: Kuontam Website <$from>\r\nTo: <$to>\r\n" . ($replyTo ? "Reply-To: <$replyTo>\r\n" : "") . "Subject: =?UTF-8?B?" . base64_encode($subject) . "?=\r\n"
       . "Date: " . date('r') . "\r\nMessage-ID: <" . bin2hex(random_bytes(8)) . "@$host>\r\n"
       . "MIME-Version: 1.0\r\nContent-Type: multipart/alternative; boundary=\"$b\"\r\n\r\n"
       . $part('text/plain', $text) . $part('text/html', $html) . "--$b--";
  if (!$cmd($msg . "\r\n.", 250)) return false;
  $cmd('QUIT', 221); fclose($fp); return true;
}
if (!smtp_send($TO, $FROM, $subject, $text, $html, $email)) out(500, false, 'Could not send. Please call or email us.');

$hits[] = time(); @file_put_contents($rl, implode("\n", $hits));
out(200, true, 'Sent');
