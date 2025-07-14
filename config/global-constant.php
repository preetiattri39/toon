<?php
return [
    'STATUS_CODE'=>[
        'SUCCESS_STATUS'=>200,
        'CREATED_STATUS'=>201,
        'NO_CONTENT_STATUS'=>204,
        'NOT_FOUND_STATUS'=>404,
        'INTERNAL_SERVER_STATUS'=>500,
        'UNPROCESSABLE_STATUS'=>422,
        'BAD_REQUEST'=>400,
        'UNAUTHORIZED'=>401
    ],
    'PAYMENT_URL'=>[
     
    ],
    'USER_ROLES'=>[
     'CUSTOMER'=>'customer'
    ],
    'STRIPE_KEYS'=>[
        'STRIPE_SECRET_KEY'=>'',
        'STRIPE_PUBLISHABLE_KEY'=>''
    ],
    'DEFAULT_CODE'=>[
        'COUNTRY_CODE'=>'us',
        'LANG_CODE'=>'en',
        'PAGINATE_NUMBER'=>10
    ]
];