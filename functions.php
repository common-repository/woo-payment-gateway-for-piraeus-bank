<?php

function piraeusbank_message()
{
    $order_id = absint(get_query_var('order-received'));
    $order = new WC_Order($order_id);
    if (method_exists($order, 'get_payment_method')) {
        $payment_method = $order->get_payment_method();
    } else {
        $payment_method = $order->payment_method;
    }

    if (is_order_received_page() && ('piraeusbank_gateway' == $payment_method)) {

        $piraeusbank_message = '';
        if (method_exists($order, 'get_meta')) {
            $piraeusbank_message = $order->get_meta('_piraeusbank_message', true);
        } else {
            $piraeusbank_message = get_post_meta($order_id, '_piraeusbank_message', true);
        }
        if (!empty($piraeusbank_message)) {
            $message = $piraeusbank_message['message'];
            $message_type = $piraeusbank_message['message_type'];
            if (method_exists($order, 'delete_meta_data')) {
                $order->delete_meta_data('_piraeusbank_message');
                $order->save_meta_data();
            } else {
                delete_post_meta($order_id, '_piraeusbank_message');
            }

            wc_add_notice($message, $message_type);
        }
    }
}

function woocommerce_add_piraeusbank_gateway($methods)
{
    $methods[] = 'WC_Piraeusbank_Gateway';
    return $methods;
}

function pb_getCardholderName($orderId, $name, $enabled)
{
	//check if has the field
    if ($enabled == 'yes') {
        $cardholder_field = get_post_meta($orderId, 'cardholder_name', true);
        if (!empty($cardholder_field)) {
            return pb_convertNonLatinToLatin($cardholder_field);
        }
    }
    return pb_convertNonLatinToLatin($name);
}

function pb_getCountryNumericCode($country)
{
    $countries = array(
        "AF" => "004",
        "AL" => "008",
        "DZ" => "012",
        "AS" => "016",
        "AD" => "020",
        "AO" => "024",
        "AI" => "660",
        "AQ" => "010",
        "AG" => "028",
        "AR" => "032",
        "AM" => "051",
        "AW" => "533",
        "AU" => "036",
        "AT" => "040",
        "AZ" => "031",
        "BS" => "044",
        "BH" => "048",
        "BD" => "050",
        "BB" => "052",
        "BY" => "112",
        "BE" => "056",
        "BZ" => "084",
        "BJ" => "204",
        "BM" => "060",
        "BT" => "064",
        "BO" => "068",
        "BQ" => "535",
        "BA" => "070",
        "BW" => "072",
        "BV" => "074",
        "BR" => "076",
        "IO" => "086",
        "BN" => "096",
        "BG" => "100",
        "BF" => "854",
        "BI" => "108",
        "CV" => "132",
        "KH" => "116",
        "CM" => "120",
        "CA" => "124",
        "KY" => "136",
        "CF" => "140",
        "TD" => "148",
        "CL" => "152",
        "CN" => "156",
        "CX" => "162",
        "CC" => "166",
        "CO" => "170",
        "KM" => "174",
        "CD" => "180",
        "CG" => "178",
        "CK" => "184",
        "CR" => "188",
        "HR" => "191",
        "CU" => "192",
        "CW" => "531",
        "CY" => "196",
        "CZ" => "203",
        "CI" => "384",
        "DK" => "208",
        "DJ" => "262",
        "DM" => "212",
        "DO" => "214",
        "EC" => "218",
        "EG" => "818",
        "SV" => "222",
        "GQ" => "226",
        "ER" => "232",
        "EE" => "233",
        "SZ" => "748",
        "ET" => "231",
        "FK" => "238",
        "FO" => "234",
        "FJ" => "242",
        "FI" => "246",
        "FR" => "250",
        "GF" => "254",
        "PF" => "258",
        "TF" => "260",
        "GA" => "266",
        "GM" => "270",
        "GE" => "268",
        "DE" => "276",
        "GH" => "288",
        "GI" => "292",
        "GR" => "300",
        "GL" => "304",
        "GD" => "308",
        "GP" => "312",
        "GU" => "316",
        "GT" => "320",
        "GG" => "831",
        "GN" => "324",
        "GW" => "624",
        "GY" => "328",
        "HT" => "332",
        "HM" => "334",
        "VA" => "336",
        "HN" => "340",
        "HK" => "344",
        "HU" => "348",
        "IS" => "352",
        "IN" => "356",
        "ID" => "360",
        "IR" => "364",
        "IQ" => "368",
        "IE" => "372",
        "IM" => "833",
        "IL" => "376",
        "IT" => "380",
        "JM" => "388",
        "JP" => "392",
        "JE" => "832",
        "JO" => "400",
        "KZ" => "398",
        "KE" => "404",
        "KI" => "296",
        "KP" => "408",
        "KR" => "410",
        "KW" => "414",
        "KG" => "417",
        "LA" => "418",
        "LV" => "428",
        "LB" => "422",
        "LS" => "426",
        "LR" => "430",
        "LY" => "434",
        "LI" => "438",
        "LT" => "440",
        "LU" => "442",
        "MO" => "446",
        "MG" => "450",
        "MW" => "454",
        "MY" => "458",
        "MV" => "462",
        "ML" => "466",
        "MT" => "470",
        "MH" => "584",
        "MQ" => "474",
        "MR" => "478",
        "MU" => "480",
        "YT" => "175",
        "MX" => "484",
        "FM" => "583",
        "MD" => "498",
        "MC" => "492",
        "MN" => "496",
        "ME" => "499",
        "MS" => "500",
        "MA" => "504",
        "MZ" => "508",
        "MM" => "104",
        "NA" => "516",
        "NR" => "520",
        "NP" => "524",
        "NL" => "528",
        "NC" => "540",
        "NZ" => "554",
        "NI" => "558",
        "NE" => "562",
        "NG" => "566",
        "NU" => "570",
        "NF" => "574",
        "MP" => "580",
        "NO" => "578",
        "OM" => "512",
        "PK" => "586",
        "PW" => "585",
        "PS" => "275",
        "PA" => "591",
        "PG" => "598",
        "PY" => "600",
        "PE" => "604",
        "PH" => "608",
        "PN" => "612",
        "PL" => "616",
        "PT" => "620",
        "PR" => "630",
        "QA" => "634",
        "MK" => "807",
        "RO" => "642",
        "RU" => "643",
        "RW" => "646",
        "RE" => "638",
        "BL" => "652",
        "SH" => "654",
        "KN" => "659",
        "LC" => "662",
        "MF" => "663",
        "PM" => "666",
        "VC" => "670",
        "WS" => "882",
        "SM" => "674",
        "ST" => "678",
        "SA" => "682",
        "SN" => "686",
        "RS" => "688",
        "SC" => "690",
        "SL" => "694",
        "SG" => "702",
        "SX" => "534",
        "SK" => "703",
        "SI" => "705",
        "SB" => "090",
        "SO" => "706",
        "ZA" => "710",
        "GS" => "239",
        "SS" => "728",
        "ES" => "724",
        "LK" => "144",
        "SD" => "729",
        "SR" => "740",
        "SJ" => "744",
        "SE" => "752",
        "CH" => "756",
        "SY" => "760",
        "TW" => "158",
        "TJ" => "762",
        "TZ" => "834",
        "TH" => "764",
        "TL" => "626",
        "TG" => "768",
        "TK" => "772",
        "TO" => "776",
        "TT" => "780",
        "TN" => "788",
        "TR" => "792",
        "TM" => "795",
        "TC" => "796",
        "TV" => "798",
        "UG" => "800",
        "UA" => "804",
        "AE" => "784",
        "GB" => "826",
        "UM" => "581",
        "US" => "840",
        "UY" => "858",
        "UZ" => "860",
        "VU" => "548",
        "VE" => "862",
        "VN" => "704",
        "VG" => "092",
        "VI" => "850",
        "WF" => "876",
        "EH" => "732",
        "YE" => "887",
        "ZM" => "894",
        "ZW" => "716",
        "AX" => "248"
    );

    if (isset($countries[$country])) {
        return $countries[$country];
    }
    // if nothing found - return Greece
    return '300';
}

function pb_getCountryPhoneCode($country)
{

    if (empty($country)) {
        $default_location = wc_get_customer_default_location();
        $country = $default_location['country'];
    }

    $countries_phone_codes = array(
        "AF" => "93",
        "AL" => "355",
        "DZ" => "213",
        "AS" => "1684",
        "AD" => "376",
        "AO" => "244",
        "AI" => "1264",
        "AQ" => "672",
        "AG" => "1268",
        "AR" => "54",
        "AM" => "374",
        "AW" => "297",
        "AU" => "61",
        "AT" => "43",
        "AZ" => "994",
        "BS" => "1242",
        "BH" => "973",
        "BD" => "880",
        "BB" => "1246",
        "BY" => "375",
        "BE" => "32",
        "BZ" => "501",
        "BJ" => "229",
        "BM" => "1441",
        "BT" => "975",
        "BO" => "591",
        "BA" => "387",
        "BW" => "267",
        "BV" => "74",
        "BR" => "55",
        "BL" => "590",
        "BQ" => "599",
        "CW" => "599",
        "GG" => "44",
        "IO" => "246",
        "BN" => "673",
        "BG" => "359",
        "GR" => "30",
        "AX" => "358",
        "GB" => "44",
        "IM" => "44",
        "JE" => "44",
        "ME" => "382",
        "MF" => "1599",
        "PS" => "970",
        "RS" => "381",
        "SX" => "1721",
        "TL" => "670",
        "IR" => "98",
        "BF" => "226",
        "BI" => "257",
        "KH" => "855",
        "CM" => "237",
        "CA" => "1",
        "CV" => "238",
        "KY" => "1345",
        "CF" => "236",
        "TD" => "235",
        "CL" => "56",
        "CN" => "86",
        "CX" => "61",
        "CC" => "61",
        "CO" => "57",
        "KM" => "269",
        "CG" => "242",
        "CD" => "243",
        "CK" => "682",
        "CR" => "506",
        "CI" => "225",
        "HR" => "385",
        "CY" => "357",
        "CZ" => "420",
        "DK" => "45",
        "DJ" => "253",
        "DM" => "1767",
        "DO" => "1809",
        "EC" => "593",
        "EG" => "20",
        "SV" => "503",
        "GQ" => "240",
        "ER" => "291",
        "EE" => "372",
        "ET" => "251",
        "FK" => "500",
        "FO" => "298",
        "FJ" => "679",
        "FI" => "358",
        "FR" => "33",
        "GF" => "594",
        "PF" => "689",
        "TF" => "",
        "GA" => "241",
        "GM" => "220",
        "GE" => "995",
        "DE" => "49",
        "GH" => "233",
        "GI" => "350",
        "GL" => "299",
        "GD" => "1473",
        "GP" => "590",
        "GU" => "1671",
        "GT" => "502",
        "GN" => "224",
        "GW" => "245",
        "GY" => "592",
        "HT" => "509",
        "HM" => "",
        "VA" => "39",
        "HN" => "504",
        "HK" => "852",
        "HU" => "36",
        "IS" => "354",
        "IN" => "91",
        "ID" => "62",
        "IQ" => "964",
        "IE" => "353",
        "IL" => "972",
        "IT" => "39",
        "JM" => "1876",
        "JP" => "81",
        "JO" => "962",
        "KZ" => "7",
        "KE" => "254",
        "KI" => "686",
        "KR" => "82",
        "KW" => "965",
        "KG" => "996",
        "LA" => "856",
        "LV" => "371",
        "LB" => "961",
        "LS" => "266",
        "LR" => "231",
        "LI" => "423",
        "LT" => "370",
        "LU" => "352",
        "MO" => "853",
        "MK" => "389",
        "MG" => "261",
        "MW" => "265",
        "MY" => "60",
        "MV" => "960",
        "ML" => "223",
        "MT" => "356",
        "MH" => "692",
        "MQ" => "596",
        "MR" => "222",
        "MU" => "230",
        "YT" => "262",
        "MX" => "52",
        "FM" => "691",
        "MD" => "373",
        "MC" => "377",
        "MN" => "976",
        "MS" => "1664",
        "MA" => "212",
        "MZ" => "258",
        "NA" => "264",
        "NR" => "674",
        "NP" => "977",
        "NL" => "31",
        "NC" => "687",
        "NZ" => "64",
        "NI" => "505",
        "NE" => "227",
        "NG" => "234",
        "NU" => "683",
        "NF" => "672",
        "MP" => "1670",
        "NO" => "47",
        "OM" => "968",
        "PK" => "92",
        "PW" => "680",
        "PA" => "507",
        "PG" => "675",
        "PY" => "595",
        "PE" => "51",
        "PH" => "63",
        "PN" => "870",
        "PL" => "48",
        "PT" => "351",
        "PR" => "1",
        "QA" => "974",
        "RE" => "262",
        "RO" => "40",
        "RU" => "7",
        "RW" => "250",
        "SH" => "290",
        "KN" => "1869",
        "LC" => "1758",
        "PM" => "508",
        "VC" => "1784",
        "WS" => "685",
        "SM" => "378",
        "ST" => "239",
        "SA" => "966",
        "SN" => "221",
        "SC" => "248",
        "SL" => "232",
        "SG" => "65",
        "SK" => "421",
        "SI" => "386",
        "SB" => "677",
        "SO" => "252",
        "ZA" => "27",
        "GS" => "500",
        "ES" => "34",
        "LK" => "94",
        "SR" => "597",
        "SJ" => "47",
        "SZ" => "268",
        "SE" => "46",
        "CH" => "41",
        "TW" => "886",
        "TJ" => "992",
        "TZ" => "255",
        "TH" => "66",
        "TG" => "228",
        "TK" => "690",
        "TO" => "676",
        "TT" => "1868",
        "TN" => "216",
        "TR" => "90",
        "TM" => "993",
        "TC" => "1649",
        "TV" => "688",
        "UG" => "256",
        "UA" => "380",
        "AE" => "971",
        "UM" => "",
        "US" => "1",
        "UY" => "598",
        "UZ" => "998",
        "VU" => "678",
        "VE" => "58",
        "VN" => "84",
        "VG" => "1284",
        "VI" => "1340",
        "WF" => "681",
        "EH" => "",
        "YE" => "967",
        "ZM" => "260",
        "IC" => "34"
    );
    if (isset($countries_phone_codes[$country])) {
        return $countries_phone_codes[$country];
    }
    // if nothing found - return Greece
    return '30';
}

function pb_validatePhoneNumberAllCountries($phone, $country)
{
    $countries_phone_codes = array(
        "AF" => "93",
        "AL" => "355",
        "DZ" => "213",
        "AS" => "1684",
        "AD" => "376",
        "AO" => "244",
        "AI" => "1264",
        "AQ" => "672",
        "AG" => "1268",
        "AR" => "54",
        "AM" => "374",
        "AW" => "297",
        "AU" => "61",
        "AT" => "43",
        "AZ" => "994",
        "BS" => "1242",
        "BH" => "973",
        "BD" => "880",
        "BB" => "1246",
        "BY" => "375",
        "BE" => "32",
        "BZ" => "501",
        "BJ" => "229",
        "BM" => "1441",
        "BT" => "975",
        "BO" => "591",
        "BA" => "387",
        "BW" => "267",
        "BV" => "74",
        "BR" => "55",
        "BL" => "590",
        "BQ" => "599",
        "CW" => "599",
        "GG" => "44",
        "IO" => "246",
        "BN" => "673",
        "BG" => "359",
        "GR" => "30",
        "AX" => "358",
        "GB" => "44",
        "IM" => "44",
        "JE" => "44",
        "ME" => "382",
        "MF" => "1599",
        "PS" => "970",
        "RS" => "381",
        "SX" => "1721",
        "TL" => "670",
        "IR" => "98",
        "BF" => "226",
        "BI" => "257",
        "KH" => "855",
        "CM" => "237",
        "CA" => "1",
        "CV" => "238",
        "KY" => "1345",
        "CF" => "236",
        "TD" => "235",
        "CL" => "56",
        "CN" => "86",
        "CX" => "61",
        "CC" => "61",
        "CO" => "57",
        "KM" => "269",
        "CG" => "242",
        "CD" => "243",
        "CK" => "682",
        "CR" => "506",
        "CI" => "225",
        "HR" => "385",
        "CY" => "357",
        "CZ" => "420",
        "DK" => "45",
        "DJ" => "253",
        "DM" => "1767",
        "DO" => "1809",
        "EC" => "593",
        "EG" => "20",
        "SV" => "503",
        "GQ" => "240",
        "ER" => "291",
        "EE" => "372",
        "ET" => "251",
        "FK" => "500",
        "FO" => "298",
        "FJ" => "679",
        "FI" => "358",
        "FR" => "33",
        "GF" => "594",
        "PF" => "689",
        "GA" => "241",
        "GM" => "220",
        "GE" => "995",
        "DE" => "49",
        "GH" => "233",
        "GI" => "350",
        "GL" => "299",
        "GD" => "1473",
        "GP" => "590",
        "GU" => "1671",
        "GT" => "502",
        "GN" => "224",
        "GW" => "245",
        "GY" => "592",
        "HT" => "509",
        "VA" => "39",
        "HN" => "504",
        "HK" => "852",
        "HU" => "36",
        "IS" => "354",
        "IN" => "91",
        "ID" => "62",
        "IQ" => "964",
        "IE" => "353",
        "IL" => "972",
        "IT" => "39",
        "JM" => "1876",
        "JP" => "81",
        "JO" => "962",
        "KZ" => "7",
        "KE" => "254",
        "KI" => "686",
        "KR" => "82",
        "KW" => "965",
        "KG" => "996",
        "LA" => "856",
        "LV" => "371",
        "LB" => "961",
        "LS" => "266",
        "LR" => "231",
        "LI" => "423",
        "LT" => "370",
        "LU" => "352",
        "MO" => "853",
        "MK" => "389",
        "MG" => "261",
        "MW" => "265",
        "MY" => "60",
        "MV" => "960",
        "ML" => "223",
        "MT" => "356",
        "MH" => "692",
        "MQ" => "596",
        "MR" => "222",
        "MU" => "230",
        "YT" => "262",
        "MX" => "52",
        "FM" => "691",
        "MD" => "373",
        "MC" => "377",
        "MN" => "976",
        "MS" => "1664",
        "MA" => "212",
        "MZ" => "258",
        "NA" => "264",
        "NR" => "674",
        "NP" => "977",
        "NL" => "31",
        "NC" => "687",
        "NZ" => "64",
        "NI" => "505",
        "NE" => "227",
        "NG" => "234",
        "NU" => "683",
        "NF" => "672",
        "MP" => "1670",
        "NO" => "47",
        "OM" => "968",
        "PK" => "92",
        "PW" => "680",
        "PA" => "507",
        "PG" => "675",
        "PY" => "595",
        "PE" => "51",
        "PH" => "63",
        "PN" => "870",
        "PL" => "48",
        "PT" => "351",
        "PR" => "1",
        "QA" => "974",
        "RE" => "262",
        "RO" => "40",
        "RU" => "7",
        "RW" => "250",
        "SH" => "290",
        "KN" => "1869",
        "LC" => "1758",
        "PM" => "508",
        "VC" => "1784",
        "WS" => "685",
        "SM" => "378",
        "ST" => "239",
        "SA" => "966",
        "SN" => "221",
        "SC" => "248",
        "SL" => "232",
        "SG" => "65",
        "SK" => "421",
        "SI" => "386",
        "SB" => "677",
        "SO" => "252",
        "ZA" => "27",
        "GS" => "500",
        "ES" => "34",
        "LK" => "94",
        "SR" => "597",
        "SJ" => "47",
        "SZ" => "268",
        "SE" => "46",
        "CH" => "41",
        "TW" => "886",
        "TJ" => "992",
        "TZ" => "255",
        "TH" => "66",
        "TG" => "228",
        "TK" => "690",
        "TO" => "676",
        "TT" => "1868",
        "TN" => "216",
        "TR" => "90",
        "TM" => "993",
        "TC" => "1649",
        "TV" => "688",
        "UG" => "256",
        "UA" => "380",
        "AE" => "971",
        "US" => "1",
        "UY" => "598",
        "UZ" => "998",
        "VU" => "678",
        "VE" => "58",
        "VN" => "84",
        "VG" => "1284",
        "VI" => "1340",
        "WF" => "681",
        "YE" => "967",
        "ZM" => "260",
        "IC" => "34"
    );
    $found = false;
    foreach ($countries_phone_codes as $key => $country_prefix) {
        $final_phone = preg_replace('/[^0-9]/', '', $phone);
        $pattern = '/^(?:\+|0{0,2}?)((' . $country_prefix . '))( |\.|-)?([\d \-\(\)]*)/';

        preg_match($pattern, $final_phone, $matches);

        if (!empty($matches) && !$found) {
            if (!empty($matches[4])) {
                $found = true;
                $int_phone = $country_prefix . '-' . $matches[4];
            }
        }
    }

    if (!$found) {
        $country_prefix = pb_getCountryPhoneCode($country);
        $int_phone = $country_prefix . '-' . preg_replace('/[^0-9]/', '', $phone);
    }
    return substr($int_phone, 0, 19);
}

function pb_validateStateCode($state, $country)
{
    $country_prefix = pb_getCountryPhoneCode($country);
    $pattern = '/(' . $country . '-?)(.*)/';
    preg_match($pattern, $state, $matches);
    $stateCode = $state;

    if (!empty($matches)) {
        if (!empty($matches[2])) {
            $stateCode = $matches[2];
        }
    }
    if (empty($stateCode)) {
        //if nothing found for state, assume that is for Attiki
        $stateCode = 'I';
    }
    return $stateCode;
}

function pb_nonLatinChars()
{
    return array(
        'À', 'à', 'Á', 'á', 'Â', 'â', 'Ã', 'ã', 'Ä', 'ä', 'Å', 'å', 'Ā', 'ā', 'Ă', 'ă', 'Ą', 'ą', 'Ǟ', 'ǟ', 'Ǻ', 'ǻ', 'Α', 'α', 'ά', 'Ά',
        'Ḃ', 'ḃ', 'Б', 'б',
        'Ć', 'ć', 'Ç', 'ç', 'Č', 'č', 'Ĉ', 'ĉ', 'Ċ', 'ċ', 'Ч', 'ч', 'Χ', 'χ',
        'Ḑ', 'ḑ', 'Ď', 'ď', 'Ḋ', 'ḋ', 'Đ', 'đ', 'Ð', 'ð', 'Д', 'д', 'Δ', 'δ',
        'Ǳ', 'ǲ', 'ǳ', 'Ǆ', 'ǅ', 'ǆ',
        'È', 'è', 'É', 'é', 'Ě', 'ě', 'Ê', 'ê', 'Ë', 'ë', 'Ē', 'ē', 'Ĕ', 'ĕ', 'Ę', 'ę', 'Ė', 'ė', 'Ʒ', 'ʒ', 'Ǯ', 'ǯ', 'Е', 'е', 'Э', 'э', 'Ε', 'ε', 'ё', 'є', 'Є', 'έ', 'Έ',
        'Ḟ', 'ḟ', 'ƒ', 'Ф', 'ф', 'Φ', 'φ',
        'ﬁ', 'ﬂ',
        'Ǵ', 'ǵ', 'Ģ', 'ģ', 'Ǧ', 'ǧ', 'Ĝ', 'ĝ', 'Ğ', 'ğ', 'Ġ', 'ġ', 'Ǥ', 'ǥ', 'Г', 'г', 'Γ', 'γ',
        'Ĥ', 'ĥ', 'Ħ', 'ħ', 'Ж', 'ж', 'Х', 'х', 'Ή', 'ή',
        'Ì', 'ì', 'Í', 'í', 'Î', 'î', 'Ĩ', 'ĩ', 'Ï', 'ï', 'Ī', 'ī', 'Ĭ', 'ĭ', 'Į', 'į', 'İ', 'ı', 'И', 'и', 'Η', 'η', 'Ι', 'ι', 'і', 'І', 'ї', 'Ї', 'ί', 'ϊ', 'Ί', 'Ϊ', 'ΐ',
        'Ĳ', 'ĳ',
        'Ĵ', 'ĵ',
        'Ḱ', 'ḱ', 'Ķ', 'ķ', 'Ǩ', 'ǩ', 'К', 'к', 'Κ', 'κ',
        'Ĺ', 'ĺ', 'Ļ', 'ļ', 'Ľ', 'ľ', 'Ŀ', 'ŀ', 'Ł', 'ł', 'Л', 'л', 'Λ', 'λ',
        'Ǉ', 'ǈ', 'ǉ',
        'Ṁ', 'ṁ', 'М', 'м', 'Μ', 'μ',
        'Ń', 'ń', 'Ņ', 'ņ', 'Ň', 'ň', 'Ñ', 'ñ', 'ŉ', 'Ŋ', 'ŋ', 'Н', 'н', 'Ν', 'ν',
        'Ǌ', 'ǋ', 'ǌ',
        'Ò', 'ò', 'Ó', 'ó', 'Ô', 'ô', 'Õ', 'õ', 'Ö', 'ö', 'Ō', 'ō', 'Ŏ', 'ŏ', 'Ø', 'ø', 'Ő', 'ő', 'Ǿ', 'ǿ', 'О', 'о', 'Ο', 'ο', 'Ω', 'ω', 'ό', 'ώ', 'Ό', 'Ώ',
        'Œ', 'œ',
        'Ṗ', 'ṗ', 'П', 'п', 'Π', 'π', 'Ψ', 'ψ',
        'Ŕ', 'ŕ', 'Ŗ', 'ŗ', 'Ř', 'ř', 'Р', 'р', 'Ρ', 'ρ',
        'Ś', 'ś', 'Ş', 'ş', 'Š', 'š', 'Ŝ', 'ŝ', 'Ṡ', 'ṡ', 'ſ', 'ß', 'С', 'с', 'Ш', 'ш', 'Щ', 'щ', 'Σ', 'σ', 'ς',
        'Ţ', 'ţ', 'Ť', 'ť', 'Ṫ', 'ṫ', 'Ŧ', 'ŧ', 'Þ', 'þ', 'Т', 'т', 'Ц', 'ц', 'Θ', 'θ', 'Τ', 'τ',
        'Ù', 'ù', 'Ú', 'ú', 'Û', 'û', 'Ũ', 'ũ', 'Ü', 'ü', 'Ů', 'ů', 'Ū', 'ū', 'Ŭ', 'ŭ', 'Ų', 'ų', 'Ű', 'ű', 'У', 'у',
        'В', 'в', 'Β', 'β',
        'Ẁ', 'ẁ', 'Ẃ', 'ẃ', 'Ŵ', 'ŵ', 'Ẅ', 'ẅ',
        'Ξ', 'ξ',
        'Ỳ', 'ỳ', 'Ý', 'ý', 'Ŷ', 'ŷ', 'Ÿ', 'ÿ', 'Й', 'й', 'Ы', 'ы', 'Ю', 'ю', 'Я', 'я', 'Υ', 'υ', 'ύ', 'ϋ', 'Ύ', 'Ϋ', 'ΰ',
        'Ź', 'ź', 'Ž', 'ž', 'Ż', 'ż', 'З', 'з', 'Ζ', 'ζ',
        'Æ', 'æ', 'Ǽ', 'ǽ', 'а', 'А',
        'ь', 'ъ', 'Ъ', 'Ь',
    );
}

function pb_latinChars()
{
    return array(
        'A', 'a', 'A', 'a', 'A', 'a', 'A', 'a', 'A', 'a', 'A', 'a', 'A', 'a', 'A', 'a', 'A', 'a', 'A', 'a', 'A', 'a', 'A', 'a', 'a', 'A',
        'B', 'b', 'B', 'b',
        'C', 'c', 'C', 'c', 'C', 'c', 'C', 'c', 'C', 'c', 'CH', 'ch', 'CH', 'ch',
        'D', 'd', 'D', 'd', 'D', 'd', 'D', 'd', 'D', 'd', 'D', 'd', 'D', 'd',
        'DZ', 'Dz', 'dz', 'DZ', 'Dz', 'dz',
        'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'e', 'e', 'E', 'e', 'E',
        'F', 'f', 'f', 'F', 'f', 'F', 'f',
        'fi', 'fl',
        'G', 'g', 'G', 'g', 'G', 'g', 'G', 'g', 'G', 'g', 'G', 'g', 'G', 'g', 'G', 'g', 'G', 'g',
        'H', 'h', 'H', 'h', 'ZH', 'zh', 'H', 'h', 'H', 'h',
        'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'i', 'I', 'i', 'I', 'i', 'i', 'I', 'I', 'i',
        'IJ', 'ij',
        'J', 'j',
        'K', 'k', 'K', 'k', 'K', 'k', 'K', 'k', 'K', 'k',
        'L', 'l', 'L', 'l', 'L', 'l', 'L', 'l', 'L', 'l', 'L', 'l', 'L', 'l',
        'LJ', 'Lj', 'lj',
        'M', 'm', 'M', 'm', 'M', 'm',
        'N', 'n', 'N', 'n', 'N', 'n', 'N', 'n', 'n', 'N', 'n', 'N', 'n', 'N', 'n',
        'NJ', 'Nj', 'nj',
        'O', 'o', 'O', 'o', 'O', 'o', 'O', 'o', 'O', 'o', 'O', 'o', 'O', 'o', 'O', 'o', 'O', 'o', 'O', 'o', 'O', 'o', 'O', 'o', 'O', 'o', 'o', 'o', 'O', 'O',
        'OE', 'oe',
        'P', 'p', 'P', 'p', 'P', 'p', 'PS', 'ps',
        'R', 'r', 'R', 'r', 'R', 'r', 'R', 'r', 'R', 'r',
        'S', 's', 'S', 's', 'S', 's', 'S', 's', 'S', 's', 's', 'ss', 'S', 's', 'SH', 'sh', 'SHCH', 'shch', 'S', 's', 's',
        'T', 't', 'T', 't', 'T', 't', 'T', 't', 'T', 't', 'T', 't', 'TS', 'ts', 'TH', 'th', 'T', 't',
        'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u',
        'V', 'v', 'V', 'v',
        'W', 'w', 'W', 'w', 'W', 'w', 'W', 'w',
        'X', 'x',
        'Y', 'y', 'Y', 'y', 'Y', 'y', 'Y', 'y', 'Y', 'y', 'Y', 'y', 'YU', 'yu', 'YA', 'ya', 'Y', 'y', 'y', 'y', 'Y', 'Y', 'y',
        'Z', 'z', 'Z', 'z', 'Z', 'z', 'Z', 'z', 'Z', 'z',
        'AE', 'ae', 'AE', 'ae', 'a', 'A',
        '', '', '', '',
    );
}

function pb_convertNonLatinToLatin($str)
{

    $converted_name = str_replace(pb_nonLatinChars(), pb_latinChars(), $str);

    // for extra check if any char is not ascii, ignore it.
    $conv_name = iconv('utf-8', 'ASCII//IGNORE', $converted_name);

    //replace any no digit in piraeus accepted chars lantin and /:_().,+-
    $pattern = '/([^a-zA-Z| \/:_().,+-]*?)/';
    $name = preg_replace($pattern, '', $conv_name);

    return $name;

}
function piraeusbank_plugin_action_links($links, $file)
{
    static $this_plugin;

    if (!$this_plugin) {
        $this_plugin = plugin_basename(__FILE__);
    }

    if ($file == $this_plugin) {
        $settings_link = '<a href="' . get_bloginfo('wpurl') . '/wp-admin/admin.php?page=wc-settings&tab=checkout&section=WC_Piraeusbank_Gateway">Settings</a>';
        array_unshift($links, $settings_link);
    }
    return $links;
}

function piraeus_woocommerce_states($states)
{
    $states['CY'] = array(
        '04' => __('Ammochostos', 'woo-payment-gateway-for-piraeus-bank'),
        '06' => __('Keryneia', 'woo-payment-gateway-for-piraeus-bank'),
        '03' => __('Larnaka', 'woo-payment-gateway-for-piraeus-bank'),
        '01' => __('Lefkosia', 'woo-payment-gateway-for-piraeus-bank'),
        '02' => __('Lemesos', 'woo-payment-gateway-for-piraeus-bank'),
        '05' => __('Pafos', 'woo-payment-gateway-for-piraeus-bank'),
    );
    $states['DE'] = array(
        'BW' => __('Baden-Württemberg', 'woo-payment-gateway-for-piraeus-bank'),
        'BY' => __('Bayern', 'woo-payment-gateway-for-piraeus-bank'),
        'BE' => __('Berlin', 'woo-payment-gateway-for-piraeus-bank'),
        'BB' => __('Brandenburg', 'woo-payment-gateway-for-piraeus-bank'),
        'HB' => __('Bremen', 'woo-payment-gateway-for-piraeus-bank'),
        'HH' => __('Hamburg', 'woo-payment-gateway-for-piraeus-bank'),
        'HE' => __('Hessen', 'woo-payment-gateway-for-piraeus-bank'),
        'MV' => __('Mecklenburg-Vorpommern', 'woo-payment-gateway-for-piraeus-bank'),
        'NI' => __('Niedersachsen', 'woo-payment-gateway-for-piraeus-bank'),
        'NW' => __('Nordrhein-Westfalen', 'woo-payment-gateway-for-piraeus-bank'),
        'RP' => __('Rheinland-Pfalz', 'woo-payment-gateway-for-piraeus-bank'),
        'SL' => __('Saarland', 'woo-payment-gateway-for-piraeus-bank'),
        'SN' => __('Sachsen', 'woo-payment-gateway-for-piraeus-bank'),
        'ST' => __('Sachsen-Anhalt', 'woo-payment-gateway-for-piraeus-bank'),
        'SH' => __('Schleswig-Holstein', 'woo-payment-gateway-for-piraeus-bank'),
        'TH' => __('Thüringen', 'woo-payment-gateway-for-piraeus-bank'),
    );
    // __('Piraeus Bank Gateway', 'woo-payment-gateway-for-piraeus-bank')
    return $states;
}
