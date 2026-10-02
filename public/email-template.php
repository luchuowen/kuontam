<?php
// Enquiry email ("Spec sheet" direction). Email-safe: tables, inline styles, system fonts, 600px.
// Included by send.php; requesting this file directly outputs nothing.
if (!function_exists('kq_email_html')) {
function kq_email_html(array $d): string {
  $e = fn($s) => htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');
  $S = "Arial,Helvetica,sans-serif"; $M = "'Courier New',Courier,monospace";
  $INK = '#231F20'; $RED = '#BF1E2E'; $PAPER = '#F4F2EE'; $MUTE = '#76706B'; $LINE = '#E6E1DA';
  $logo = $d['logo'] ?? 'https://kuontamsystems.co.ke/img/logo_red_w.png';

  $chips = '';
  foreach ($d['systems'] as $s) $chips .= '<span style="display:inline-block;background:#FBEBEC;color:'.$RED.';font:bold 13px/1 '.$S.';padding:8px 11px;margin:0 6px 6px 0;border-radius:2px">'.$e($s).'</span>';
  if ($chips === '') $chips = '<span style="color:'.$MUTE.'">Not specified</span>';

  $row = function ($k, $v, $last = false) use ($M, $S, $INK, $MUTE, $LINE) {
    $bd = $last ? '' : "border-bottom:1px solid $LINE;";
    return '<tr><td class="lbl" style="'.$bd.'padding:16px 0;width:128px;vertical-align:top;font:bold 10px/1.6 '.$M.';letter-spacing:.16em;text-transform:uppercase;color:'.$MUTE.'">'.$k.'</td>'
         . '<td style="'.$bd.'padding:14px 0 10px;vertical-align:top;font:15px/1.5 '.$S.';color:'.$INK.'">'.$v.'</td></tr>';
  };
  $btn = fn($label, $href, $bg, $fg, $bd) => '<a href="'.$href.'" style="display:inline-block;background:'.$bg.';color:'.$fg.';border:1px solid '.$bd.';font:bold 12px/1 '.$S.';letter-spacing:.14em;text-transform:uppercase;text-decoration:none;padding:16px 24px">'.$label.'</a>';

  $loc = $d['location'] !== '' ? $e($d['location']) : 'Location not given';
  $tel = 'tel:'.$e($d['tel']);
  $wa  = 'https://wa.me/'.$e($d['wa']);
  $pre = $e($d['name'].' · '.$d['building'].' · '.($d['systems'] ? implode(', ', $d['systems']) : 'systems not specified').' · call '.$d['phone']);

  return '<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">'
  . '<meta name="color-scheme" content="light"><meta name="supported-color-schemes" content="light"><title>New site survey request</title>'
  . '<style>body{margin:0;padding:0}a{text-decoration:none}'
  . '@media (max-width:620px){.wrap{width:100%!important}.px{padding-left:24px!important;padding-right:24px!important}.stack{display:block!important;width:100%!important}.gap{padding:0 0 10px!important}.h1{font-size:28px!important}.lbl{width:96px!important}.hide-m{display:none!important}}</style></head>'
  . '<body style="margin:0;background:#ECE8E2">'
  . '<div style="display:none;max-height:0;overflow:hidden;opacity:0">'.$pre.'</div>'
  . '<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#ECE8E2"><tr><td align="center" style="padding:32px 12px">'
  . '<table role="presentation" class="wrap" width="600" cellpadding="0" cellspacing="0" style="width:600px;max-width:600px;background:#ffffff">'

  // header
  . '<tr><td style="height:3px;background:'.$RED.';font-size:0;line-height:0">&nbsp;</td></tr>'
  . '<tr><td class="px" style="padding:28px 44px 24px;border-bottom:1px solid '.$LINE.'">'
  .   '<table role="presentation" width="100%" cellpadding="0" cellspacing="0"><tr>'
  .   '<td style="vertical-align:middle"><img src="'.$e($logo).'" width="54" height="52" alt="Kuontam Systems" style="display:block;border:0"></td>'
  .   '<td align="right" style="vertical-align:middle;font:bold 10px/1.7 '.$M.';letter-spacing:.16em;text-transform:uppercase;color:'.$MUTE.'">Website enquiry<br><span style="color:'.$INK.';white-space:nowrap">'.$e($d['ref']).'</span></td>'
  .   '</tr></table></td></tr>'

  // title
  . '<tr><td class="px" style="padding:40px 44px 0">'
  .   '<div style="font:bold 10px '.$M.';letter-spacing:.2em;text-transform:uppercase;color:'.$RED.'">New site survey request</div>'
  .   '<div class="h1" style="font:bold 34px/1.1 '.$S.';color:'.$INK.';letter-spacing:-1px;margin:14px 0 10px">'.$e($d['name']).'</div>'
  .   '<div style="font:16px/1.5 '.$S.';color:'.$MUTE.'">'.$e($d['building']).' &nbsp;·&nbsp; '.$loc.'</div>'
  . '</td></tr>'

  // call-back panel
  . '<tr><td class="px" style="padding:30px 44px 0">'
  .   '<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:'.$PAPER.'"><tr><td style="padding:24px 26px">'
  .     '<div style="font:bold 10px '.$M.';letter-spacing:.18em;text-transform:uppercase;color:'.$MUTE.'">Call back on</div>'
  .     '<a href="'.$tel.'" style="display:block;font:bold 26px/1.2 '.$S.';color:'.$INK.';letter-spacing:-.3px;margin:8px 0 18px;text-decoration:none">'.$e($d['phone']).'</a>'
  .     '<table role="presentation" cellpadding="0" cellspacing="0"><tr>'
  .       '<td class="stack gap" style="padding-right:10px">'.$btn('Call now', $tel, $RED, '#ffffff', $RED).'</td>'
  .       '<td class="stack">'.$btn('WhatsApp', $wa, '#ffffff', $INK, $INK).'</td>'
  .     '</tr></table>'
  .   '</td></tr></table>'
  . '</td></tr>'

  // details
  . '<tr><td class="px" style="padding:30px 44px 0">'
  .   '<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border-top:1px solid '.$INK.'">'
  .     $row('Name', $e($d['name']))
  .     $row('Phone', '<a href="'.$tel.'" style="color:'.$INK.';font-weight:bold;text-decoration:none">'.$e($d['phone']).'</a>')
  .     $row('Location', $loc)
  .     $row('Building', $e($d['building']))
  .     $row('Systems', $chips, true)
  .   '</table>'
  . '</td></tr>'

  // next step
  . '<tr><td class="px" style="padding:22px 44px 40px">'
  .   '<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border:1px solid '.$LINE.'"><tr>'
  .   '<td style="padding:16px 18px;font:14px/1.55 '.$S.';color:'.$INK.'"><span style="font:bold 10px '.$M.';letter-spacing:.18em;text-transform:uppercase;color:'.$RED.'">Next step</span><br>'
  .   'Call within one working day to confirm a survey date.</td></tr></table>'
  . '</td></tr>'

  // footer
  . '<tr><td class="px" style="background:'.$INK.';padding:26px 44px">'
  .   '<table role="presentation" width="100%" cellpadding="0" cellspacing="0"><tr>'
  .   '<td style="vertical-align:top;font:bold 12px '.$S.';letter-spacing:.2em;text-transform:uppercase;color:#ffffff">Kuontam Systems'
  .     '<div style="font:10px/1.8 '.$M.';letter-spacing:.14em;color:#A8A19C;margin-top:4px">Power · Protection · Intelligence</div></td>'
  .   '<td class="hide-m" align="right" style="vertical-align:top;font:12px '.$S.'"><a href="https://kuontamsystems.co.ke" style="color:#ffffff;text-decoration:none">kuontamsystems.co.ke</a></td>'
  .   '</tr></table>'
  .   '<div style="border-top:1px solid #3A3536;margin-top:18px;padding-top:14px;font:11px/1.6 '.$M.';color:#8F8984">Received '.$e($d['time']).' &nbsp;·&nbsp; via '.$e($d['source']).'<br>Sent automatically by the survey form on the Kuontam Systems website.</div>'
  . '</td></tr>'

  . '</table></td></tr></table></body></html>';
}
}
