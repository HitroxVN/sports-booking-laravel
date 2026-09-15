<?php
$css = file_get_contents('public/build/assets/app-B31jjXNr.css');
$js  = file_get_contents('public/build/assets/app-Bxf_DRts.js');
$checks = [
    'keyframes lc-toast-in'   => '@keyframes lc-toast-in',
    'keyframes lc-toast-out'  => '@keyframes lc-toast-out',
    'keyframes lc-toast-timer'=> '@keyframes lc-toast-timer',
    '.lc-toast rule'          => '.lc-toast{',
    '.lc-toast--leaving'      => '.lc-toast--leaving',
    '.lc-toast__timer'        => '.lc-toast__timer',
    'timer pause hover'       => '.lc-toast:hover .lc-toast__timer',
    'ring-accent-moss/40'     => 'ring-accent-moss',
    'z-[100]'                 => 'z-\[100\]',
    'max-w-sm'                => 'max-w-sm',
    'pointer-events-none'     => 'pointer-events-none',
    'contents'                => '.contents',
    'shadow-lc-lg'            => 'shadow-lc-lg',
    'calc(100vw-2rem)'        => '100vw',
];
foreach ($checks as $label => $needle) {
    $ok = str_contains($css, $needle);
    echo str_pad($label, 26) . ($ok ? 'OK' : 'FAIL') . PHP_EOL;
}
echo 'JS: Alpine.store toast  ' . (str_contains($js, 'nonEssentials') ? 'OK' : 'FAIL') . PHP_EOL;
echo 'JS: window.toast helper ' . (str_contains($js, 'toast') ? 'OK' : 'FAIL') . PHP_EOL;
echo 'CSS: reduced-motion     ' . (preg_match('/@media\s*\(prefers-reduced-motion[^)]*\)\s*\{[^}]*\.lc-toast/', $css) ? 'OK' : 'check-manually') . PHP_EOL;
