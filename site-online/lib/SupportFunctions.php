<?php
define("BASIC_REGEX_VALIDATION", "/^[a-zA-Z0-9-_\. !?+:]*$/");
define("BLACKLIST_SPECIAL_CHARS", "/^[^`|'$#\\\\<>\\/]*$/");

function curl_action($server, $params, $raw=false, $follow_redirects = false, $http_code = false, $post_method=1, $auth = null, $headers=null){
	$post = curl_init();
	curl_setopt($post, CURLOPT_URL, $url = $server.(!$post_method ? (($pos = mb_strpos($server,"?")) === false ? "?" : "&").http_build_query($params) : ""));
	if ($post_method){
		if ($post_method == "PUT"){
			curl_setopt($post, CURLOPT_CUSTOMREQUEST, "PUT");
		}else{
			curl_setopt($post, CURLOPT_POST, $post_method);
		}
		curl_setopt($post, CURLOPT_POSTFIELDS, is_array($params) ? http_build_query($params) : $params);
	}
    if ($auth){
        curl_setopt($post, CURLOPT_USERPWD, $auth);
    }
	curl_setopt($post, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($post, CURLOPT_FOLLOWLOCATION, $follow_redirects);
	if (is_array($headers)){
		curl_setopt($post, CURLOPT_HTTPHEADER, $headers);
	}

   //error_log("cURL to: ".$server.(!$post_method ? "?".http_build_query($params) : "")." with post params: ".(is_array($params) ? http_build_query($params) : $params));

	$result = curl_exec($post);

	if ($http_code)
		$http_code = curl_getinfo($post, CURLINFO_HTTP_CODE);

	curl_close($post);
	if ($raw)
		return $result;
	else{
		$res_arr = json_decode($result, true);
		if ($http_code)
			$res_arr["_http_code"] = $http_code;
		return $res_arr;
	}
}
function regex_check($string, $regex, $ref="-unspecified-", $msg=null){
    if (preg_match($regex, $string, $output) !== 1) {
        throw new Exception ($msg ? $msg : "Input params on ".$ref." are incorrectly formatted - [".$string."] is not understood");
    }
}
function clean_vars($key, $value){
    if (is_array($value)){
			regex_check($key, "/^[a-zA-Z_-]+$/"); //make sure the key is kosher
			foreach ($value as $a=>$b){
				clean_vars($a, $b);
			}
			return;
		}
    switch ($key){
        case "action": //shouldn't be more than a-z with underscores
            regex_check($value, "/^[a-z_]+$/", $key);
            break;
        case "email":
				case "EMAIL":
        case "adultemail":
            regex_check($value, '/^(?!(?:(?:\x22?\x5C[\x00-\x7E]\x22?)|(?:\x22?[^\x5C\x22]\x22?)){255,})(?!(?:(?:\x22?\x5C[\x00-\x7E]\x22?)|(?:\x22?[^\x5C\x22]\x22?)){65,}@)(?:(?:[\x21\x23-\x27\x2A\x2B\x2D\x2F-\x39\x3D\x3F\x5E-\x7E]+)|(?:\x22(?:[\x01-\x08\x0B\x0C\x0E-\x1F\x21\x23-\x5B\x5D-\x7F]|(?:\x5C[\x00-\x7F]))*\x22))(?:\.(?:(?:[\x21\x23-\x27\x2A\x2B\x2D\x2F-\x39\x3D\x3F\x5E-\x7E]+)|(?:\x22(?:[\x01-\x08\x0B\x0C\x0E-\x1F\x21\x23-\x5B\x5D-\x7F]|(?:\x5C[\x00-\x7F]))*\x22)))*@(?:(?:(?!.*[^.]{64,})(?:(?:(?:xn--)?[a-z0-9]+(?:-[a-z0-9]+)*\.){1,126}){1,}(?:(?:[a-z][a-z0-9]*)|(?:(?:xn--)[a-z0-9]+))(?:-[a-z0-9]+)*)|(?:\[(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){7})|(?:(?!(?:.*[a-f0-9][:\]]){7,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?)))|(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){5}:)|(?:(?!(?:.*[a-f0-9]:){5,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3}:)?)))?(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))(?:\.(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))){3}))\]))$/iD', $key);
            break;
        case "miner_hash":
            regex_check($value, "/^[a-f0-9]+$/", $key);
            break;
        case "referrer":
            regex_check($value, "/^[A-Z0-9]*$/", $key);
            break;
        case "timezone":
            regex_check($value, "/^[A-Za-z-_\/0-9]*$/", $key);
            break;
        case "start_time":
        case "fired_at":
            regex_check($value, "/^[0-9-\s:]+$/", $key);
            break;
        case "expiry_date":
            regex_check($value, "/^(0[1-9]|1[0-2])\/?([0-9]{4}|[0-9]{2})$/", $key);
            break;
        case "timestamp":
            regex_check($value, "/^[0-9]+$/", $key);
            break;
        case "contact":
        case "token":
            //ignore as this is evaluated in the appropriate method
            break;
        default:
            //Aggressive mode - if we didn't ask for it, then it should just be basic chars
            regex_check($value, BASIC_REGEX_VALIDATION, $key);
            break;
    }
}
function validate_email($input){
	//Lookup the email, and see if it is OK

	list($user, $domain) = explode('@', strtolower($input["email"]));

	$spam_domains = array("0815.ru","10minutemail.co.za","10minutemail.com","33mail.com","6ip.us","armyspy.com","binkmail.com","boun.cr","bobmail.info","brennendesreich.de","bund.us","cachedot.net","cashforcarsbristol.co.uk","ce.mintemail.com","chammy.info","clrmail.com","cuvox.de","dacoolest.com","dayrep.com","devnullmail.com","discard.email","discardmail.com","discardmail.de","dispomail.eu","dispostable.com","dodgit.com","drdrb.com","eelmail.com","einrot.com","emailproxsy.com","fleckens.hu","getairmail.com","grr.la","guerrillamail.biz","guerrillamail.com","guerrillamail.de","guerrillamail.net","guerrillamail.org","guerrillamailblock.com","gustr.com","harakirimail.com","hulapla.de","hushmail.com","imgof.com","imgv.de","inboxproxy.com","incognitomail.org","jourrapide.com","lags.us","letthemeatspam.com","maildrop.cc","mailforspam.com","mailhub.pw","mailimate.com","mailinator.com","mailinator.net","mailinator2.com","mailnesia.com","mailnull.com","mailproxsy.com","mailtothis.com","meltmail.com ","mintemail.com","my10minutemail.com","mynetstore.de","mytrashmail.com","nonspam.eu","nonspammer.de","notmailinator.com","qoika.com","reallymymail.com","reconmail.com","rhyta.com","s0ny.net","safetymail.info","sendspamhere.com","sharedmailbox.org","sharklasers.com","sogetthis.com","soodonims.com","spam4.me","spamavert.com","spambog.com","spambog.de","spambog.ru","spambooger.com","spambox.us","spamgourmet.com","spamherelots.com","spamhereplease.com","spamhole.com","spamstack.net","spamthisplease.com","stonerfans.com","streetwisemail.com","superrito.com","suremail.info","tafmail.com","teewars.org","teleworm.us","thehighlands.co.uk","thisisnotmyrealemail.com","throwawayemailaddress.com","tradermail.info","trbvm.com","value-mycar.co.uk","veryrealemail.com","yopmail.com","zippymail.info ","zxcvbnm.co.uk");

	$valid_domains = array("google.com", "gmail.com", "me.com", "hotmail.com", "yahoo.com", "outlook.com");

	return array("status"=>( !(in_array($domain, $spam_domains)) && (in_array($domain, $valid_domains) || checkdnsrr($domain, "MX"))));
}

function returnInternationPhonePref($country_code)
{
    $countryArray = array(
        'AD' => array('name' => 'ANDORRA', 'code' => '376'),
        'AE' => array('name' => 'UNITED ARAB EMIRATES', 'code' => '971'),
        'AF' => array('name' => 'AFGHANISTAN', 'code' => '93'),
        'AG' => array('name' => 'ANTIGUA AND BARBUDA', 'code' => '1268'),
        'AI' => array('name' => 'ANGUILLA', 'code' => '1264'),
        'AL' => array('name' => 'ALBANIA', 'code' => '355'),
        'AM' => array('name' => 'ARMENIA', 'code' => '374'),
        'AN' => array('name' => 'NETHERLANDS ANTILLES', 'code' => '599'),
        'AO' => array('name' => 'ANGOLA', 'code' => '244'),
        'AQ' => array('name' => 'ANTARCTICA', 'code' => '672'),
        'AR' => array('name' => 'ARGENTINA', 'code' => '54'),
        'AS' => array('name' => 'AMERICAN SAMOA', 'code' => '1684'),
        'AT' => array('name' => 'AUSTRIA', 'code' => '43'),
        'AU' => array('name' => 'AUSTRALIA', 'code' => '61'),
        'AW' => array('name' => 'ARUBA', 'code' => '297'),
        'AZ' => array('name' => 'AZERBAIJAN', 'code' => '994'),
        'BA' => array('name' => 'BOSNIA AND HERZEGOVINA', 'code' => '387'),
        'BB' => array('name' => 'BARBADOS', 'code' => '1246'),
        'BD' => array('name' => 'BANGLADESH', 'code' => '880'),
        'BE' => array('name' => 'BELGIUM', 'code' => '32'),
        'BF' => array('name' => 'BURKINA FASO', 'code' => '226'),
        'BG' => array('name' => 'BULGARIA', 'code' => '359'),
        'BH' => array('name' => 'BAHRAIN', 'code' => '973'),
        'BI' => array('name' => 'BURUNDI', 'code' => '257'),
        'BJ' => array('name' => 'BENIN', 'code' => '229'),
        'BL' => array('name' => 'SAINT BARTHELEMY', 'code' => '590'),
        'BM' => array('name' => 'BERMUDA', 'code' => '1441'),
        'BN' => array('name' => 'BRUNEI DARUSSALAM', 'code' => '673'),
        'BO' => array('name' => 'BOLIVIA', 'code' => '591'),
        'BR' => array('name' => 'BRAZIL', 'code' => '55'),
        'BS' => array('name' => 'BAHAMAS', 'code' => '1242'),
        'BT' => array('name' => 'BHUTAN', 'code' => '975'),
        'BW' => array('name' => 'BOTSWANA', 'code' => '267'),
        'BY' => array('name' => 'BELARUS', 'code' => '375'),
        'BZ' => array('name' => 'BELIZE', 'code' => '501'),
        'CA' => array('name' => 'CANADA', 'code' => '1'),
        'CC' => array('name' => 'COCOS (KEELING) ISLANDS', 'code' => '61'),
        'CD' => array('name' => 'CONGO, THE DEMOCRATIC REPUBLIC OF THE', 'code' => '243'),
        'CF' => array('name' => 'CENTRAL AFRICAN REPUBLIC', 'code' => '236'),
        'CG' => array('name' => 'CONGO', 'code' => '242'),
        'CH' => array('name' => 'SWITZERLAND', 'code' => '41'),
        'CI' => array('name' => 'COTE D IVOIRE', 'code' => '225'),
        'CK' => array('name' => 'COOK ISLANDS', 'code' => '682'),
        'CL' => array('name' => 'CHILE', 'code' => '56'),
        'CM' => array('name' => 'CAMEROON', 'code' => '237'),
        'CN' => array('name' => 'CHINA', 'code' => '86'),
        'CO' => array('name' => 'COLOMBIA', 'code' => '57'),
        'CR' => array('name' => 'COSTA RICA', 'code' => '506'),
        'CU' => array('name' => 'CUBA', 'code' => '53'),
        'CV' => array('name' => 'CAPE VERDE', 'code' => '238'),
        'CX' => array('name' => 'CHRISTMAS ISLAND', 'code' => '61'),
        'CY' => array('name' => 'CYPRUS', 'code' => '357'),
        'CZ' => array('name' => 'CZECH REPUBLIC', 'code' => '420'),
        'DE' => array('name' => 'GERMANY', 'code' => '49'),
        'DJ' => array('name' => 'DJIBOUTI', 'code' => '253'),
        'DK' => array('name' => 'DENMARK', 'code' => '45'),
        'DM' => array('name' => 'DOMINICA', 'code' => '1767'),
        'DO' => array('name' => 'DOMINICAN REPUBLIC', 'code' => '1809'),
        'DZ' => array('name' => 'ALGERIA', 'code' => '213'),
        'EC' => array('name' => 'ECUADOR', 'code' => '593'),
        'EE' => array('name' => 'ESTONIA', 'code' => '372'),
        'EG' => array('name' => 'EGYPT', 'code' => '20'),
        'ER' => array('name' => 'ERITREA', 'code' => '291'),
        'ES' => array('name' => 'SPAIN', 'code' => '34'),
        'ET' => array('name' => 'ETHIOPIA', 'code' => '251'),
        'FI' => array('name' => 'FINLAND', 'code' => '358'),
        'FJ' => array('name' => 'FIJI', 'code' => '679'),
        'FK' => array('name' => 'FALKLAND ISLANDS (MALVINAS)', 'code' => '500'),
        'FM' => array('name' => 'MICRONESIA, FEDERATED STATES OF', 'code' => '691'),
        'FO' => array('name' => 'FAROE ISLANDS', 'code' => '298'),
        'FR' => array('name' => 'FRANCE', 'code' => '33'),
        'GA' => array('name' => 'GABON', 'code' => '241'),
        'GB' => array('name' => 'UNITED KINGDOM', 'code' => '44'),
        'GD' => array('name' => 'GRENADA', 'code' => '1473'),
        'GE' => array('name' => 'GEORGIA', 'code' => '995'),
        'GH' => array('name' => 'GHANA', 'code' => '233'),
        'GI' => array('name' => 'GIBRALTAR', 'code' => '350'),
        'GL' => array('name' => 'GREENLAND', 'code' => '299'),
        'GM' => array('name' => 'GAMBIA', 'code' => '220'),
        'GN' => array('name' => 'GUINEA', 'code' => '224'),
        'GQ' => array('name' => 'EQUATORIAL GUINEA', 'code' => '240'),
        'GR' => array('name' => 'GREECE', 'code' => '30'),
        'GT' => array('name' => 'GUATEMALA', 'code' => '502'),
        'GU' => array('name' => 'GUAM', 'code' => '1671'),
        'GW' => array('name' => 'GUINEA-BISSAU', 'code' => '245'),
        'GY' => array('name' => 'GUYANA', 'code' => '592'),
        'HK' => array('name' => 'HONG KONG', 'code' => '852'),
        'HN' => array('name' => 'HONDURAS', 'code' => '504'),
        'HR' => array('name' => 'CROATIA', 'code' => '385'),
        'HT' => array('name' => 'HAITI', 'code' => '509'),
        'HU' => array('name' => 'HUNGARY', 'code' => '36'),
        'ID' => array('name' => 'INDONESIA', 'code' => '62'),
        'IE' => array('name' => 'IRELAND', 'code' => '353'),
        'IL' => array('name' => 'ISRAEL', 'code' => '972'),
        'IM' => array('name' => 'ISLE OF MAN', 'code' => '44'),
        'IN' => array('name' => 'INDIA', 'code' => '91'),
        'IQ' => array('name' => 'IRAQ', 'code' => '964'),
        'IR' => array('name' => 'IRAN, ISLAMIC REPUBLIC OF', 'code' => '98'),
        'IS' => array('name' => 'ICELAND', 'code' => '354'),
        'IT' => array('name' => 'ITALY', 'code' => '39'),
        'JM' => array('name' => 'JAMAICA', 'code' => '1876'),
        'JO' => array('name' => 'JORDAN', 'code' => '962'),
        'JP' => array('name' => 'JAPAN', 'code' => '81'),
        'KE' => array('name' => 'KENYA', 'code' => '254'),
        'KG' => array('name' => 'KYRGYZSTAN', 'code' => '996'),
        'KH' => array('name' => 'CAMBODIA', 'code' => '855'),
        'KI' => array('name' => 'KIRIBATI', 'code' => '686'),
        'KM' => array('name' => 'COMOROS', 'code' => '269'),
        'KN' => array('name' => 'SAINT KITTS AND NEVIS', 'code' => '1869'),
        'KP' => array('name' => 'KOREA DEMOCRATIC PEOPLES REPUBLIC OF', 'code' => '850'),
        'KR' => array('name' => 'KOREA REPUBLIC OF', 'code' => '82'),
        'KW' => array('name' => 'KUWAIT', 'code' => '965'),
        'KY' => array('name' => 'CAYMAN ISLANDS', 'code' => '1345'),
        'KZ' => array('name' => 'KAZAKSTAN', 'code' => '7'),
        'LA' => array('name' => 'LAO PEOPLES DEMOCRATIC REPUBLIC', 'code' => '856'),
        'LB' => array('name' => 'LEBANON', 'code' => '961'),
        'LC' => array('name' => 'SAINT LUCIA', 'code' => '1758'),
        'LI' => array('name' => 'LIECHTENSTEIN', 'code' => '423'),
        'LK' => array('name' => 'SRI LANKA', 'code' => '94'),
        'LR' => array('name' => 'LIBERIA', 'code' => '231'),
        'LS' => array('name' => 'LESOTHO', 'code' => '266'),
        'LT' => array('name' => 'LITHUANIA', 'code' => '370'),
        'LU' => array('name' => 'LUXEMBOURG', 'code' => '352'),
        'LV' => array('name' => 'LATVIA', 'code' => '371'),
        'LY' => array('name' => 'LIBYAN ARAB JAMAHIRIYA', 'code' => '218'),
        'MA' => array('name' => 'MOROCCO', 'code' => '212'),
        'MC' => array('name' => 'MONACO', 'code' => '377'),
        'MD' => array('name' => 'MOLDOVA, REPUBLIC OF', 'code' => '373'),
        'ME' => array('name' => 'MONTENEGRO', 'code' => '382'),
        'MF' => array('name' => 'SAINT MARTIN', 'code' => '1599'),
        'MG' => array('name' => 'MADAGASCAR', 'code' => '261'),
        'MH' => array('name' => 'MARSHALL ISLANDS', 'code' => '692'),
        'MK' => array('name' => 'MACEDONIA, THE FORMER YUGOSLAV REPUBLIC OF', 'code' => '389'),
        'ML' => array('name' => 'MALI', 'code' => '223'),
        'MM' => array('name' => 'MYANMAR', 'code' => '95'),
        'MN' => array('name' => 'MONGOLIA', 'code' => '976'),
        'MO' => array('name' => 'MACAU', 'code' => '853'),
        'MP' => array('name' => 'NORTHERN MARIANA ISLANDS', 'code' => '1670'),
        'MR' => array('name' => 'MAURITANIA', 'code' => '222'),
        'MS' => array('name' => 'MONTSERRAT', 'code' => '1664'),
        'MT' => array('name' => 'MALTA', 'code' => '356'),
        'MU' => array('name' => 'MAURITIUS', 'code' => '230'),
        'MV' => array('name' => 'MALDIVES', 'code' => '960'),
        'MW' => array('name' => 'MALAWI', 'code' => '265'),
        'MX' => array('name' => 'MEXICO', 'code' => '52'),
        'MY' => array('name' => 'MALAYSIA', 'code' => '60'),
        'MZ' => array('name' => 'MOZAMBIQUE', 'code' => '258'),
        'NA' => array('name' => 'NAMIBIA', 'code' => '264'),
        'NC' => array('name' => 'NEW CALEDONIA', 'code' => '687'),
        'NE' => array('name' => 'NIGER', 'code' => '227'),
        'NG' => array('name' => 'NIGERIA', 'code' => '234'),
        'NI' => array('name' => 'NICARAGUA', 'code' => '505'),
        'NL' => array('name' => 'NETHERLANDS', 'code' => '31'),
        'NO' => array('name' => 'NORWAY', 'code' => '47'),
        'NP' => array('name' => 'NEPAL', 'code' => '977'),
        'NR' => array('name' => 'NAURU', 'code' => '674'),
        'NU' => array('name' => 'NIUE', 'code' => '683'),
        'NZ' => array('name' => 'NEW ZEALAND', 'code' => '64'),
        'OM' => array('name' => 'OMAN', 'code' => '968'),
        'PA' => array('name' => 'PANAMA', 'code' => '507'),
        'PE' => array('name' => 'PERU', 'code' => '51'),
        'PF' => array('name' => 'FRENCH POLYNESIA', 'code' => '689'),
        'PG' => array('name' => 'PAPUA NEW GUINEA', 'code' => '675'),
        'PH' => array('name' => 'PHILIPPINES', 'code' => '63'),
        'PK' => array('name' => 'PAKISTAN', 'code' => '92'),
        'PL' => array('name' => 'POLAND', 'code' => '48'),
        'PM' => array('name' => 'SAINT PIERRE AND MIQUELON', 'code' => '508'),
        'PN' => array('name' => 'PITCAIRN', 'code' => '870'),
        'PR' => array('name' => 'PUERTO RICO', 'code' => '1'),
        'PT' => array('name' => 'PORTUGAL', 'code' => '351'),
        'PW' => array('name' => 'PALAU', 'code' => '680'),
        'PY' => array('name' => 'PARAGUAY', 'code' => '595'),
        'QA' => array('name' => 'QATAR', 'code' => '974'),
        'RO' => array('name' => 'ROMANIA', 'code' => '40'),
        'RS' => array('name' => 'SERBIA', 'code' => '381'),
        'RU' => array('name' => 'RUSSIAN FEDERATION', 'code' => '7'),
        'RW' => array('name' => 'RWANDA', 'code' => '250'),
        'SA' => array('name' => 'SAUDI ARABIA', 'code' => '966'),
        'SB' => array('name' => 'SOLOMON ISLANDS', 'code' => '677'),
        'SC' => array('name' => 'SEYCHELLES', 'code' => '248'),
        'SD' => array('name' => 'SUDAN', 'code' => '249'),
        'SE' => array('name' => 'SWEDEN', 'code' => '46'),
        'SG' => array('name' => 'SINGAPORE', 'code' => '65'),
        'SH' => array('name' => 'SAINT HELENA', 'code' => '290'),
        'SI' => array('name' => 'SLOVENIA', 'code' => '386'),
        'SK' => array('name' => 'SLOVAKIA', 'code' => '421'),
        'SL' => array('name' => 'SIERRA LEONE', 'code' => '232'),
        'SM' => array('name' => 'SAN MARINO', 'code' => '378'),
        'SN' => array('name' => 'SENEGAL', 'code' => '221'),
        'SO' => array('name' => 'SOMALIA', 'code' => '252'),
        'SR' => array('name' => 'SURINAME', 'code' => '597'),
        'ST' => array('name' => 'SAO TOME AND PRINCIPE', 'code' => '239'),
        'SV' => array('name' => 'EL SALVADOR', 'code' => '503'),
        'SY' => array('name' => 'SYRIAN ARAB REPUBLIC', 'code' => '963'),
        'SZ' => array('name' => 'SWAZILAND', 'code' => '268'),
        'TC' => array('name' => 'TURKS AND CAICOS ISLANDS', 'code' => '1649'),
        'TD' => array('name' => 'CHAD', 'code' => '235'),
        'TG' => array('name' => 'TOGO', 'code' => '228'),
        'TH' => array('name' => 'THAILAND', 'code' => '66'),
        'TJ' => array('name' => 'TAJIKISTAN', 'code' => '992'),
        'TK' => array('name' => 'TOKELAU', 'code' => '690'),
        'TL' => array('name' => 'TIMOR-LESTE', 'code' => '670'),
        'TM' => array('name' => 'TURKMENISTAN', 'code' => '993'),
        'TN' => array('name' => 'TUNISIA', 'code' => '216'),
        'TO' => array('name' => 'TONGA', 'code' => '676'),
        'TR' => array('name' => 'TURKEY', 'code' => '90'),
        'TT' => array('name' => 'TRINIDAD AND TOBAGO', 'code' => '1868'),
        'TV' => array('name' => 'TUVALU', 'code' => '688'),
        'TW' => array('name' => 'TAIWAN, PROVINCE OF CHINA', 'code' => '886'),
        'TZ' => array('name' => 'TANZANIA, UNITED REPUBLIC OF', 'code' => '255'),
        'UA' => array('name' => 'UKRAINE', 'code' => '380'),
        'UG' => array('name' => 'UGANDA', 'code' => '256'),
        'US' => array('name' => 'UNITED STATES', 'code' => '1'),
        'UY' => array('name' => 'URUGUAY', 'code' => '598'),
        'UZ' => array('name' => 'UZBEKISTAN', 'code' => '998'),
        'VA' => array('name' => 'HOLY SEE (VATICAN CITY STATE)', 'code' => '39'),
        'VC' => array('name' => 'SAINT VINCENT AND THE GRENADINES', 'code' => '1784'),
        'VE' => array('name' => 'VENEZUELA', 'code' => '58'),
        'VG' => array('name' => 'VIRGIN ISLANDS, BRITISH', 'code' => '1284'),
        'VI' => array('name' => 'VIRGIN ISLANDS, U.S.', 'code' => '1340'),
        'VN' => array('name' => 'VIET NAM', 'code' => '84'),
        'VU' => array('name' => 'VANUATU', 'code' => '678'),
        'WF' => array('name' => 'WALLIS AND FUTUNA', 'code' => '681'),
        'WS' => array('name' => 'SAMOA', 'code' => '685'),
        'XK' => array('name' => 'KOSOVO', 'code' => '381'),
        'YE' => array('name' => 'YEMEN', 'code' => '967'),
        'YT' => array('name' => 'MAYOTTE', 'code' => '262'),
        'ZA' => array('name' => 'SOUTH AFRICA', 'code' => '27'),
        'ZM' => array('name' => 'ZAMBIA', 'code' => '260'),
        'ZW' => array('name' => 'ZIMBABWE', 'code' => '263')
    );

    $pref = '';
    if (isset($countryArray[$country_code]))
    {
        $pref = $countryArray[$country_code]['code'];
    }
    return $pref;
}


function checkPhoneNumber($pref, $num)
    {
        if (strlen($num) < 6)
        {
            $arr_ret = array();
            $arr_ret['error'] = 'shortsignupdata';
            return $arr_ret;
        }

        $num = str_replace(' ', '', $num);
        $num = str_replace('-', '', $num);

        $firstChar = substr($num, 0, 1);
        $first2Chars = substr($num, 0, 2);


        if ($firstChar == '+')
        {
            $num = substr($num, 1);

            $ck = substr($num, 0, strlen($pref));
            if ($ck != $pref)
            {
                $arr_ret = array();
                $arr_ret['error'] = 'invalidpref';
                return $arr_ret;
            }
        }
        else if ($first2Chars == '00')
        {
            $num = substr($num, 2);
            $ck = substr($num, 0, strlen($pref));
            if ($ck != $pref)
            {
                $arr_ret = array();
                $arr_ret['error'] = 'invalidpref';
                return $arr_ret;
            }
        }
        else
        {
            $num = $pref . $num;
        }
        if (is_numeric($num) == false)
        {
            $arr_ret = array();
            $arr_ret['error'] = 'invalidnumber';
            return $arr_ret;
        }

        $num_only = substr($num, strlen($pref));

        $arr_ret = array();
        $arr_ret['number'] = $num_only;
        $arr_ret['prefix'] = $pref;
        return $arr_ret;
    }

function sign_request($data)
{
    $secret = 'xxxxxxxxxxx';
    $timestamp = microtime(true) * 10000;
    $data["timestamp"] = (int)$timestamp;
    return array(
        "data" => $data,
        "auth" => hash_hmac("sha1", json_encode($data), $secret . (int)$timestamp)
    );
}
function format_array_to_html_table($arr, $border=0){
	$ml = "<table border=".$border.">";
	foreach ($arr as $title=>$value){
		$ml .= "<tr><td><b>".ucwords(str_replace("_", " ", $title))."</b></td><td>".$value."</td></tr>";
	}
	$ml .= "</table>";
	return $ml;
}
function format_array_to_email_text($arr){
	$ml = "\n";
	foreach ($arr as $title=>$value){
		$ml .= ucwords(str_replace("_", " ", $title)).": ".$value."\n";
	}
	return $ml;
}
function send_email($details){
	//Visit this URL for sending parameter info: https://documentation.mailgun.com/en/latest/api-sending.html#sending
	$details["from"] = $details["from"] ?: DEFAULT_EMAIL_SENDER;
	//send via Mailgun using cURL
	$post = curl_init();
	curl_setopt($post, CURLOPT_URL, MAILGUN_API_URL);
	curl_setopt($post, CURLOPT_POST, 1);
	curl_setopt($post, CURLOPT_POSTFIELDS, http_build_query($details));
	curl_setopt($post, CURLOPT_USERPWD, "api:".MAILGUN_API_KEY);
	curl_setopt($post, CURLOPT_RETURNTRANSFER, 1);
	$result = curl_exec($post);
	$http_code = curl_getinfo($post, CURLINFO_HTTP_CODE);
	$res_arr = json_decode($result, true);
	$res_arr["_http_code"] = $http_code;
	return $res_arr;
}

function format_slack_rich_message($message, $fields, $customisations=array()){
	$formatted_fields = array();
	foreach ($fields as $title=>$value){
		$formatted_fields[] = array("title"=>ucwords(str_replace("_", " ", $title)), "value"=>$value, "short"=>strlen($value) < 15 ? true : false);
	}
	$customisations["attachments"] = array(array(
		"fallback"=>$message,
		"pretext"=>$message,
		"color"=>$color,
		"fields"=>$formatted_fields
	));
	return $customisations;
}
function send_slack_message($details){
	//ref: https://neobael.slack.com/services/B01F73PPDC4#service_setup
		$post = curl_init();
	curl_setopt($post, CURLOPT_URL, SLACK_WEBHOOK_URL);
	curl_setopt($post, CURLOPT_POST, 1);
	curl_setopt($post, CURLOPT_POSTFIELDS, json_encode($details));
	curl_setopt($post, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
	curl_setopt($post, CURLOPT_RETURNTRANSFER, 1);
	$result = curl_exec($post);
	$http_code = curl_getinfo($post, CURLINFO_HTTP_CODE);
	$res_arr = json_decode($result, true);
	$res_arr["_http_code"] = $http_code;
	return $res_arr;
}
function nblog($arr){
	$backtrace = debug_backtrace();
	error_log(date("Y-m-d H:i:s").": ".print_r($arr, true)."\n".json_encode($backtrace)."\n");
}
function getClientIP(){
	if (isset($_SERVER["HTTP_CF_CONNECTING_IP"])){
		$_SERVER['REMOTE_ADDR'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
	};
	foreach (array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR') as $key){
		if (array_key_exists($key, $_SERVER)){
			foreach (explode(',', $_SERVER[$key]) as $ip){
				$ip = trim($ip);
				if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false){
					$full = explode(":", $ip);
					return $full[0];
				};
			};
		};
	};
	return "0.0.0.0"; //false;
}
