<?php

/**
 * @file
 * Currency hook implementations.
 */

/**
 * Implements hook_currency_info().
 */
function currency_currency_info() {
  $currencies = array(
    new Currency(array(
      'code' => 'AFN',
      'sign' => '؋',
      'title' => t('Afghanistani Afghani'),
    )),
    new Currency(array(
      'code' => 'ALL',
      'sign' => 'Lek',
      'title' => t('Albanian Lek'),
    )),
    new Currency(array(
      'code' => 'DZD',
      'sign' => 'دج',
      'title' => t('Algerian Dinar'),
    )),
    new Currency(array(
      'code' => 'ARS',
      'sign' => '$',
      'title' => t('Argentine Peso'),
    )),
    new Currency(array(
      'code' => 'AWG',
      'sign' => 'ƒ',
      'title' => t('Aruba Florin'),
    )),
    new Currency(array(
      'code' => 'AUD',
      'sign' => '$',
      'title' => t('Australian Dollar'),
    )),
    new Currency(array(
      'code' => 'AZN',
      'sign' => 'ман',
      'title' => t('Azerbaijan New Maneat'),
    )),
    new Currency(array(
      'code' => 'BSD',
      'sign' => 'D',
      'title' => t('Bahamian Dollar'),
    )),
    new Currency(array(
      'code' => 'BHD',
      'sign' => '.د.ب',
      'title' => t('Bahraini Dinar'),
    )),
    new Currency(array(
      'code' => 'BDT',
      'sign' => '৳, ৲',
      'title' => t('Bangladeshi Taka'),
    )),
    new Currency(array(
      'code' => 'BBD',
      'sign' => 'Bds$',
      'title' => t('Barbadian Dollar'),
    )),
    new Currency(array(
      'code' => 'BYR',
      'sign' => 'p.',
      'title' => t('Belarus Ruble'),
    )),
    new Currency(array(
      'code' => 'BZD',
      'sign' => 'BZ$',
      'title' => t('Belize Dollar'),
    )),
    new Currency(array(
      'code' => 'BMD',
      'sign' => '$',
      'title' => t('Bermuda Dollar'),
    )),
    new Currency(array(
      'code' => 'BTN',
      'sign' => 'Nu.',
      'title' => t('Bhutanese Ngultrum'),
    )),
    new Currency(array(
      'code' => 'BOB',
      'sign' => '$b',
      'title' => t('Bolivian Boliviano'),
    )),
    new Currency(array(
      'code' => 'BAM',
      'sign' => 'KM',
      'title' => t('Bosnia and Herzegovina Convertible Marka'),
    )),
    new Currency(array(
      'code' => 'BWP',
      'sign' => 'P',
      'title' => t('Botswana Pula'),
    )),
    new Currency(array(
      'code' => 'BRL',
      'sign' => 'R$',
      'title' => t('Brazilian Real'),
    )),
    new Currency(array(
      'code' => 'GBP',
      'sign' => '£',
      'title' => t('British Pound'),
    )),
    new Currency(array(
      'code' => 'BND',
      'sign' => '$',
      'title' => t('Brunei Dollar'),
    )),
    new Currency(array(
      'code' => 'BGN',
      'sign' => 'лв',
      'title' => t('Bulgarian Lev'),
    )),
    new Currency(array(
      'code' => 'BIF',
      'sign' => 'FBu',
      'title' => t('Burundi Franc'),
    )),
    new Currency(array(
      'code' => 'KHR',
      'sign' => '៛',
      'title' => t('Cambodia Riel'),
    )),
    new Currency(array(
      'code' => 'CAD',
      'sign' => '$',
      'title' => t('Canadian Dollar'),
    )),
    new Currency(array(
      'code' => 'CVE',
      'sign' => 'Esc',
      'title' => t('Cape Verdean Escudo'),
    )),
    new Currency(array(
      'code' => 'KYD',
      'sign' => '$',
      'title' => t('Cayman Islands Dollar'),
    )),
    new Currency(array(
      'code' => 'XOF',
      'sign' => 'F',
      'title' => t('CFA Franc'),
    )),
    new Currency(array(
      'code' => 'XAF',
      'sign' => 'F',
      'title' => t('CFA Franc'),
    )),
    new Currency(array(
      'code' => 'CLP',
      'sign' => '$',
      'title' => t('Chilean Peso'),
    )),
    new Currency(array(
      'code' => 'CNY',
      'sign' => '元',
      'title' => t('Chinese Yuan'),
    )),
    new Currency(array(
      'code' => 'COP',
      'sign' => '$',
      'title' => t('Colombian Peso'),
    )),
    new Currency(array(
      'code' => 'KMF',
      'sign' => 'F',
      'title' => t('Comoros Franc'),
    )),
    new Currency(array(
      'code' => 'CRC',
      'sign' => '₡',
      'title' => t('Costa Rica Colon'),
    )),
    new Currency(array(
      'code' => 'HRK',
      'sign' => 'kn',
      'title' => t('Croatian Kuna'),
    )),
    new Currency(array(
      'code' => 'CUP',
      'sign' => '₱',
      'title' => t('Cuban Peso'),
    )),
    new Currency(array(
      'code' => 'CYP',
      'sign' => '£',
      'title' => t('Cyprus Pound'),
    )),
    new Currency(array(
      'code' => 'CZK',
      'sign' => 'Kč',
      'title' => t('Czech Koruna'),
    )),
    new Currency(array(
      'code' => 'DKK',
      'sign' => 'kr',
      'title' => t('Danish Krone'),
    )),
    new Currency(array(
      'code' => 'DJF',
      'sign' => 'Fdj',
      'title' => t('Dijiboutian Franc'),
    )),
    new Currency(array(
      'code' => 'DOP',
      'sign' => 'RD$',
      'title' => t('Dominican Peso'),
    )),
    new Currency(array(
      'code' => 'XCD',
      'sign' => '$',
      'title' => t('East Caribbean Dollar'),
    )),
    new Currency(array(
      'code' => 'EGP',
      'sign' => 'LE',
      'title' => t('Egyptian Pound'),
    )),
    new Currency(array(
      'code' => 'SVC',
      'sign' => '$',
      'title' => t('El Salvador Colon'),
    )),
    new Currency(array(
      'code' => 'ERN',
      'sign' => 'Nfk',
      'title' => t('Eritrean Nakfa'),
    )),
    new Currency(array(
      'code' => 'EEK',
      'sign' => 'kr',
      'title' => t('Estonian Kroon'),
    )),
    new Currency(array(
      'code' => 'ETB',
      'sign' => 'Br',
      'title' => t('Ethiopian Birr'),
    )),
    new Currency(array(
      'code' => 'EUR',
      'sign' => '€',
      'title' => t('Euro'),
    )),
    new Currency(array(
      'code' => 'FKP',
      'sign' => '£',
      'title' => t('Falkland Islands Pound'),
    )),
    new Currency(array(
      'code' => 'FJD',
      'sign' => '$',
      'title' => t('Fiji Dollar'),
    )),
    new Currency(array(
      'code' => 'GMD',
      'sign' => 'D',
      'title' => t('Gambian Dalasi'),
    )),
    new Currency(array(
      'code' => 'GHC',
      'sign' => '¢',
      'title' => t('Ghanian Cedi'),
    )),
    new Currency(array(
      'code' => 'GIP',
      'sign' => '£',
      'title' => t('Gibraltar Pound'),
    )),
    new Currency(array(
      'code' => 'XAU',
      'sign' => 'XAU',
      'title' => t('Gold Ounces'),
    )),
    new Currency(array(
      'code' => 'GTQ',
      'sign' => 'Q',
      'title' => t('Guatemala Quetzal'),
    )),
    new Currency(array(
      'code' => 'GGP',
      'sign' => '£',
      'title' => t('Guernsey Pound'),
    )),
    new Currency(array(
      'code' => 'GNF',
      'sign' => 'FG',
      'title' => t('Guinea Franc'),
    )),
    new Currency(array(
      'code' => 'GYD',
      'sign' => '$',
      'title' => t('Guyana Dollar'),
    )),
    new Currency(array(
      'code' => 'HTG',
      'sign' => 'G',
      'title' => t('Haiti Gourde'),
    )),
    new Currency(array(
      'code' => 'HNL',
      'sign' => 'L',
      'title' => t('Honduras Lempira'),
    )),
    new Currency(array(
      'code' => 'HKD',
      'sign' => 'HK$',
      'title' => t('Hong Kong Dollar'),
    )),
    new Currency(array(
      'code' => 'HUF',
      'decimals' => 0,
      'sign' => 'Ft',
      'title' => t('Hungarian Forint'),
    )),
    new Currency(array(
      'code' => 'ISK',
      'sign' => 'kr',
      'title' => t('Iceland Krona'),
    )),
    new Currency(array(
      'code' => 'INR',
      'sign' => '₨',
      'title' => t('Indian Rupee'),
    )),
    new Currency(array(
      'code' => 'IDR',
      'sign' => 'Rp',
      'title' => t('Indonesian Rupiah'),
    )),
    new Currency(array(
      'code' => 'IRR',
      'sign' => '﷼',
      'title' => t('Iran Rial'),
    )),
    new Currency(array(
      'code' => 'IQD',
      'sign' => 'ع.د',
      'title' => t('Iraqi Dinar'),
    )),
    new Currency(array(
      'code' => 'ILS',
      'sign' => '₪',
      'title' => t('Israeli Shekel'),
    )),
    new Currency(array(
      'code' => 'JMD',
      'sign' => 'J$',
      'title' => t('Jamaican Dollar'),
    )),
    new Currency(array(
      'code' => 'JPY',
      'decimals' => 0,
      'sign' => '¥',
      'title' => t('Japanese Yen'),
    )),
    new Currency(array(
      'code' => 'JOD',
      'sign' => 'din.',
      'title' => t('Jordanian Dinar'),
    )),
    new Currency(array(
      'code' => 'KZT',
      'sign' => 'лв',
      'title' => t('Kazakhstan Tenge'),
    )),
    new Currency(array(
      'code' => 'KES',
      'sign' => 'KSh',
      'title' => t('Kenyan Shilling'),
    )),
    new Currency(array(
      'code' => 'KRW',
      'sign' => '₩',
      'title' => t('Korean Won'),
    )),
    new Currency(array(
      'code' => 'KWD',
      'sign' => 'د.ك',
      'title' => t('Kuwaiti Dinar'),
    )),
    new Currency(array(
      'code' => 'KGS',
      'sign' => 'лв',
      'title' => t('Kyrgyzstan Som'),
    )),
    new Currency(array(
      'code' => 'LAK',
      'sign' => '₭',
      'title' => t('Lao Kip'),
    )),
    new Currency(array(
      'code' => 'LVL',
      'sign' => 'Ls',
      'title' => t('Latvian Lat'),
    )),
    new Currency(array(
      'code' => 'LBP',
      'sign' => '£',
      'title' => t('Lebanese Pound'),
    )),
    new Currency(array(
      'code' => 'LSL',
      'sign' => 'M',
      'title' => t('Lesotho Loti'),
    )),
    new Currency(array(
      'code' => 'LRD',
      'sign' => '$',
      'title' => t('Liberian Dollar'),
    )),
    new Currency(array(
      'code' => 'LYD',
      'sign' => 'ل.د',
      'title' => t('Libyan Dinar'),
    )),
    new Currency(array(
      'code' => 'LTL',
      'sign' => 'Lt',
      'title' => t('Lithuanian Lita'),
    )),
    new Currency(array(
      'code' => 'MOP',
      'sign' => 'MOP$',
      'title' => t('Macau Pataca'),
    )),
    new Currency(array(
      'code' => 'MKD',
      'sign' => 'ден',
      'title' => t('Macedonian Denar'),
    )),
    new Currency(array(
      'code' => 'MGA',
      'decimals' => 0,
      'sign' => 'Ar',
      'title' => t('Malagasy ariary'),
    )),
    new Currency(array(
      'code' => 'MWK',
      'sign' => 'MK',
      'title' => t('Malawian Kwacha'),
    )),
    new Currency(array(
      'code' => 'MYR',
      'sign' => 'RM',
      'title' => t('Malaysian Ringgit'),
    )),
    new Currency(array(
      'code' => 'MVR',
      'sign' => 'Rf',
      'title' => t('Maldives Rufiyaa'),
    )),
    new Currency(array(
      'code' => 'MTL',
      'sign' => 'Lm',
      'title' => t('Maltese Lira'),
    )),
    new Currency(array(
      'code' => 'MRO',
      'decimals' => 0,
      'sign' => 'UM',
      'title' => t('Mauritania Ougulya'),
    )),
    new Currency(array(
      'code' => 'MUR',
      'sign' => '₨',
      'title' => t('Mauritius Rupee'),
    )),
    new Currency(array(
      'code' => 'MXN',
      'sign' => '$',
      'title' => t('Mexican Peso'),
    )),
    new Currency(array(
      'code' => 'MDL',
      'sign' => 'lei',
      'title' => t('Moldovan Leu'),
    )),
    new Currency(array(
      'code' => 'MNT',
      'sign' => '₮',
      'title' => t('Mongolian Tugrik'),
    )),
    new Currency(array(
      'code' => 'MAD',
      'sign' => 'د.م.',
      'title' => t('Moroccan Dirham'),
    )),
    new Currency(array(
      'code' => 'MZM',
      'sign' => 'MT',
      'title' => t('Mozambique Metical'),
    )),
    new Currency(array(
      'code' => 'MMK',
      'sign' => 'K',
      'title' => t('Myanmar Kyat'),
    )),
    new Currency(array(
      'code' => 'NAD',
      'sign' => '$',
      'title' => t('Namibian Dollar'),
    )),
    new Currency(array(
      'code' => 'NPR',
      'sign' => '₨',
      'title' => t('Nepalese Rupee'),
    )),
    new Currency(array(
      'code' => 'ANG',
      'sign' => 'ƒ',
      'title' => t('Neth Antilles Guilder'),
    )),
    new Currency(array(
      'code' => 'NZD',
      'sign' => '$',
      'title' => t('New Zealand Dollar'),
    )),
    new Currency(array(
      'code' => 'NIO',
      'sign' => 'C$',
      'title' => t('Nicaragua Cordoba'),
    )),
    new Currency(array(
      'code' => 'NGN',
      'sign' => '₦',
      'title' => t('Nigerian Naira'),
    )),
    new Currency(array(
      'code' => 'KPW',
      'sign' => '₩',
      'title' => t('North Korean Won'),
    )),
    new Currency(array(
      'code' => 'NOK',
      'sign' => 'kr',
      'title' => t('Norwegian Krone'),
    )),
    new Currency(array(
      'code' => 'OMR',
      'sign' => '﷼',
      'title' => t('Omani Rial'),
    )),
    new Currency(array(
      'code' => 'XPF',
      'sign' => 'F',
      'title' => t('Pacific Franc'),
    )),
    new Currency(array(
      'code' => 'PKR',
      'sign' => '₨',
      'title' => t('Pakistani Rupee'),
    )),
    new Currency(array(
      'code' => 'XPD',
      'sign' => 'XPD',
      'title' => t('Palladium Ounces'),
    )),
    new Currency(array(
      'code' => 'PAB',
      'sign' => 'B/.',
      'title' => t('Panama Balboa'),
    )),
    new Currency(array(
      'code' => 'PGK',
      'sign' => 'K',
      'title' => t('Papua New Guinea Kina'),
    )),
    new Currency(array(
      'code' => 'PYG',
      'sign' => 'Gs',
      'title' => t('Paraguayan Guarani'),
    )),
    new Currency(array(
      'code' => 'PEN',
      'sign' => 'S/.',
      'title' => t('Peruvian Nuevo Sol'),
    )),
    new Currency(array(
      'code' => 'PHP',
      'sign' => 'Php',
      'title' => t('Philippine Peso'),
    )),
    new Currency(array(
      'code' => 'XPT',
      'sign' => 'XPT',
      'title' => t('Platinum Ounces'),
    )),
    new Currency(array(
      'code' => 'PLN',
      'sign' => 'zł',
      'title' => t('Polish Zloty'),
    )),
    new Currency(array(
      'code' => 'QAR',
      'sign' => '﷼',
      'title' => t('Qatar Rial'),
    )),
    new Currency(array(
      'code' => 'RON',
      'sign' => 'lei',
      'title' => t('Romanian New Leu'),
    )),
    new Currency(array(
      'code' => 'RUB',
      'sign' => 'руб.',
      'title' => t('Russian Rouble'),
    )),
    new Currency(array(
      'code' => 'RWF',
      'sign' => 'RF',
      'title' => t('Rwandese Franc'),
    )),
    new Currency(array(
      'code' => 'WST',
      'sign' => 'WS$',
      'title' => t('Samoan Tala'),
    )),
    new Currency(array(
      'code' => 'STD',
      'sign' => 'Db',
      'title' => t('Sao Tome Dobra'),
    )),
    new Currency(array(
      'code' => 'SAR',
      'sign' => '﷼',
      'title' => t('Saudi Arabian Riyal'),
    )),
    new Currency(array(
      'code' => 'SCR',
      'sign' => '₨',
      'title' => t('Seychelles Rupee'),
    )),
    new Currency(array(
      'code' => 'RSD',
      'sign' => 'Дин.',
      'title' => t('Serbian Dinar'),
    )),
    new Currency(array(
      'code' => 'SLL',
      'sign' => 'Le',
      'title' => t('Sierra Leone Leone'),
    )),
    new Currency(array(
      'code' => 'XAG',
      'sign' => 'XAG',
      'title' => t('Silver Ounces'),
    )),
    new Currency(array(
      'code' => 'SGD',
      'sign' => '$',
      'title' => t('Singapore Dollar'),
    )),
    new Currency(array(
      'code' => 'SKK',
      'sign' => 'SIT',
      'title' => t('Slovak Koruna'),
    )),
    new Currency(array(
      'code' => 'SBD',
      'sign' => '$',
      'title' => t('Solomon Islands Dollar'),
    )),
    new Currency(array(
      'code' => 'SOS',
      'sign' => 'S',
      'title' => t('Somali Shilling'),
    )),
    new Currency(array(
      'code' => 'ZAR',
      'sign' => 'R',
      'title' => t('South African Rand'),
    )),
    new Currency(array(
      'code' => 'LKR',
      'sign' => '₨',
      'title' => t('Sri Lanka Rupee'),
    )),
    new Currency(array(
      'code' => 'SHP',
      'sign' => '£',
      'title' => t('St Helena Pound'),
    )),
    new Currency(array(
      'code' => 'SDG',
      'sign' => 'SDG',
      'title' => t('Sudanese Pound'),
    )),
    new Currency(array(
      'code' => 'SRD',
      'sign' => '$',
      'title' => t('Surinam Dollar'),
    )),
    new Currency(array(
      'code' => 'SZL',
      'sign' => 'E',
      'title' => t('Swaziland Lilageni'),
    )),
    new Currency(array(
      'code' => 'SEK',
      'sign' => 'kr',
      'title' => t('Swedish Krona'),
    )),
    new Currency(array(
      'code' => 'CHF',
      'sign' => 'CHF',
      'title' => t('Swiss Franc'),
    )),
    new Currency(array(
      'code' => 'SYP',
      'sign' => '£',
      'title' => t('Syrian Pound'),
    )),
    new Currency(array(
      'code' => 'TWD',
      'sign' => 'NT$',
      'title' => t('Taiwan Dollar'),
    )),
    new Currency(array(
      'code' => 'TZS',
      'sign' => 'TZS',
      'title' => t('Tanzanian Shilling'),
    )),
    new Currency(array(
      'code' => 'THB',
      'sign' => '฿',
      'title' => t('Thai Baht'),
    )),
    new Currency(array(
      'code' => 'TOP',
      'sign' => 'T$',
      'title' => t("Tonga Pa'anga"),
    )),
    new Currency(array(
      'code' => 'TTD',
      'sign' => 'TT$',
      'title' => t('Trinidad & Tobago Dollar'),
    )),
    new Currency(array(
      'code' => 'TND',
      'sign' => 'د.ت',
      'title' => t('Tunisian Dinar'),
    )),
    new Currency(array(
      'code' => 'TRY',
      'sign' => 'TL',
      'title' => t('Turkish Lira'),
    )),
    new Currency(array(
      'code' => 'USD',
      'sign' => '$',
      'title' => t('U.S. Dollar'),
    )),
    new Currency(array(
      'code' => 'AED',
      'sign' => 'د.إ',
      'title' => t('UAE Dirham'),
    )),
    new Currency(array(
      'code' => 'UGX',
      'sign' => 'USh',
      'title' => t('Ugandan Shilling'),
    )),
    new Currency(array(
      'code' => 'UAH',
      'sign' => 'грн.',
      'title' => t('Ukraine Hryvnia'),
    )),
    new Currency(array(
      'code' => 'UYU',
      'sign' => '$U',
      'title' => t('Uruguayan New Peso'),
    )),
    new Currency(array(
      'code' => 'UZS',
      'sign' => 'лв',
      'title' => t('Uzbekistan Sum'),
    )),
    new Currency(array(
      'code' => 'VUV',
      'sign' => 'Vt',
      'title' => t('Vanuatu Vatu'),
    )),
    new Currency(array(
      'code' => 'VEB',
      'sign' => 'Bs',
      'title' => t('Venezuelan Bolivar'),
    )),
    new Currency(array(
      'code' => 'VND',
      'sign' => '₫',
      'title' => t('Vietnam Dong'),
    )),
    new Currency(array(
      'code' => 'YER',
      'sign' => '﷼',
      'title' => t('Yemen Riyal'),
    )),
    new Currency(array(
      'code' => 'YUM',
      'sign' => 'дин',
      'title' => t('Yugoslav Dinar'),
    )),
    new Currency(array(
      'code' => 'ZMK',
      'sign' => 'ZK',
      'title' => t('Zambian Kwacha'),
    )),
    new Currency(array(
      'code' => 'ZWD',
      'sign' => 'Z$',
      'title' => t('Zimbabwe Dollar'),
    )),
  );

  // Key currencies by their ISO 4217 codes, which act as their machine names.
  $currencies_keyed = array();
  foreach ($currencies as $currency) {
    $currencies_keyed[$currency->code] = $currency;
  }

  return $currencies_keyed;
}
