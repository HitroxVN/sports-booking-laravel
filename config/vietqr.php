<?php

return [

    /*
    |--------------------------------------------------------------------------
    | VietQR Quick Link
    |--------------------------------------------------------------------------
    |
    | Thông tin tài khoản nhận tiền dùng để tạo QR thanh toán qua
    | Quick Link của VietQR (không cần API key):
    |   https://img.vietqr.io/image/<bank_id>-<account_no>-<template>.png
    |
    */

    // Mã ngân hàng nhận tiền (vd: mbbank, vietcombank, techcombank...)
    'bank_id' => env('VIETQR_BANK_ID', 'mbbank'),

    // Số tài khoản nhận tiền
    'account_no' => env('VIETQR_ACCOUNT_NO', '52000000001'),

    // Tên chủ tài khoản (hiển thị trên QR)
    'account_name' => env('VIETQR_ACCOUNT_NAME', 'HOANG VAN TRUONG'),

    // Template hiển thị QR (compact2, compact, print, qr_only...)
    'template' => env('VIETQR_TEMPLATE', 'compact2'),

];
