<?php

return [

    //TIMFS
    'secret-key'                => ENV('TOKEN_KEY'),
    'get-token-url'             => ENV('URL_TOKEN'),
    'get-created-items-url'     => ENV('URL_CREATED_ITEMS'),
    'get-updated-items-url'     => ENV('URL_UPDATED_ITEMS'),

    //DAM
    'dam-secret-key'            => ENV('DAM_TOKEN_KEY'),
    'dam-get-token-url'         => ENV('DAM_URL_TOKEN'),
    'dam-get-created-items-url' => ENV('DAM_URL_CREATED_ITEMS'),
    'dam-get-updated-items-url' => ENV('DAM_URL_UPDATED_ITEMS')
];

?>