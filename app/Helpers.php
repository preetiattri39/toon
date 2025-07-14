<?php

    if (!function_exists('defaultCountryCode')) {
        function defaultCountryCode()
        {
            $country_code = config('global-constant.DEFAULT_CODE.COUNTRY_CODE');
            return $country_code;
        }
    }

    if (!function_exists('defaultLangCode')) {
        function defaultLangCode()
        {
            $lang_code = config('global-constant.DEFAULT_CODE.LANG_CODE');
            return $lang_code;
        }
    }

    if (!function_exists('defaultPaginateNumber')) {
        function defaultPaginateNumber()
        {
            $paginate_number = config('global-constant.DEFAULT_CODE.PAGINATE_NUMBER');
            return $paginate_number;
        }
    }

    

?>