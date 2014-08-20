<?php

/*
  Plugin Name: MLM
  Plugin URI: http://wpbinarymlm.com
  Description: Binary Network
  Version: 1.0
  Author: Tradebooster
  Author URI: http://wpbinarymlm.com
  License: GPL
 */
// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

if (!function_exists('mlm_core_set_charset')) {

    function mlm_core_set_charset() {
        global $wpdb;
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

        /* MLM Component DB Schemea */
        if (!empty($wpdb->charset)) {
            return "DEFAULT CHARACTER SET $wpdb->charset";
        }
        return '';
    }

}
if (!function_exists('mlm_core_get_table_prefix')) {

    function mlm_core_get_table_prefix() {
        global $wpdb;
        return $wpdb->base_prefix;
    }

}

function mlm_core_install_users() {
    global $wpdb;
    $charset_collate = mlm_core_set_charset();
    $table_prefix = mlm_core_get_table_prefix();

    $sql[] = "CREATE TABLE IF NOT EXISTS  {$table_prefix}mlm_users 
			(
				id BIGINT(20) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
				user_id BIGINT(20) NOT NULL COMMENT 'foreign key of the {$table_prefix}users table',
				username VARCHAR( 60 ) NOT NULL ,
				user_key VARCHAR( 15 ) NOT NULL ,
				parent_key VARCHAR( 15 ) NOT NULL ,
				sponsor_key VARCHAR( 15 ) NOT NULL ,
				leg ENUM(  '1',  '0' ) NOT NULL COMMENT '1 indicate right leg and 0 indicate left leg',
				payment_status ENUM( '1','0','2' ) NOT NULL DEFAULT '0' COMMENT '1 indicate paid and 0 indicate unpaid 2 Indicates special paid',
				banned ENUM(  '1',  '0' ) NOT NULL DEFAULT '0',
				KEY index_user_key (user_key),
				KEY index_parent_key (parent_key),
				KEY index_sponsor_key (sponsor_key),
				UNIQUE (username)
			) {$charset_collate} AUTO_INCREMENT=1";

    dbDelta($sql);
}

function mlm_core_modify_mlm_users() {
    global $wpdb;
    $charset_collate = mlm_core_set_charset();
    $table_prefix = mlm_core_get_table_prefix();
    $sql = "ALTER TABLE   {$table_prefix}mlm_users "
            . "modify  payment_status ENUM( '1','0','2' ) NOT NULL DEFAULT '0' COMMENT '1 indicate paid and 0 indicate unpaid 2 Indicates special paid',"
            . "{$charset_collate}";
    $results = $wpdb->get_results("desc {$table_prefix}mlm_users");
    foreach ($results as $key => $value) {
        $fileds[] = $value->Field;
    }
    if (in_array('payment_status', $fileds)) {
        $wpdb->query($sql);
    }
}

function mlm_core_update_mlm_users() {
    global $wpdb;
    $charset_collate = mlm_core_set_charset();
    $table_prefix = mlm_core_get_table_prefix();
    $results = $wpdb->get_results("desc {$table_prefix}mlm_users");
    foreach ($results as $key => $value) {
        $fileds[] = $value->Field;
    }
    if (!in_array('payment_date', $fileds)) {
        $wpdb->query("ALTER TABLE   {$table_prefix}mlm_users ADD  payment_date datetime NOT NULL  default '0000-00-00 00:00:00'AFTER  leg, {$charset_collate}");
    }
    if (!in_array('product_price', $fileds)) {
        $wpdb->query("ALTER TABLE   {$table_prefix}mlm_users ADD product_price int(11) default 0 AFTER payment_status, {$charset_collate} ");
    }
}

/* * *** this function is used to update the mlm_user to add the product_price column in table table ******* */



/* * ******************** end of epin update table function for version 2.7 ******************** */

function mlm_core_install_leftleg() {
    $charset_collate = mlm_core_set_charset();
    $table_prefix = mlm_core_get_table_prefix();

    $sql[] = "CREATE TABLE IF NOT EXISTS  {$table_prefix}mlm_leftleg
			(
				id BIGINT(20) NOT NULL AUTO_INCREMENT PRIMARY KEY,
				pkey VARCHAR(15) NOT NULL,
				ukey VARCHAR(15) NOT NULL,
				commission_status ENUM('1','0') NOT NULL DEFAULT '0',
				status ENUM('1','0') NOT NULL DEFAULT '0',
				KEY index_pkey (pkey),
				KEY index_ukey (ukey)
			) {$charset_collate} AUTO_INCREMENT=1";
    dbDelta($sql);
}

function mlm_core_update_mlm_leftleg() {
    global $wpdb, $table_prefix;
    $sql = "ALTER TABLE   {$table_prefix}mlm_leftleg "
            . "ADD payout_id int(11) default 0 AFTER  commission_status";
    $results = $wpdb->get_results("desc {$table_prefix}mlm_leftleg");
    foreach ($results as $key => $value) {
        $fileds[] = $value->Field;
    }
    if (!in_array('payout_id', $fileds)) {
        $wpdb->query($sql);
    }
}

function mlm_core_install_rightleg() {
    $charset_collate = mlm_core_set_charset();
    $table_prefix = mlm_core_get_table_prefix();

    $sql[] = "CREATE TABLE IF NOT EXISTS  {$table_prefix}mlm_rightleg
			(
				id BIGINT(20) NOT NULL AUTO_INCREMENT PRIMARY KEY,
				pkey VARCHAR(15) NOT NULL,
				ukey VARCHAR(15) NOT NULL,
				commission_status ENUM('1','0') NOT NULL DEFAULT '0',
				status ENUM('1','0') NOT NULL DEFAULT '0',
				KEY index_pkey(pkey),
				KEY index_ukey(ukey)
			) {$charset_collate} AUTO_INCREMENT=1";

    dbDelta($sql);
}

function mlm_core_update_mlm_rightleg() {
    global $wpdb, $table_prefix;
    $sql = "ALTER TABLE   {$table_prefix}mlm_rightleg "
            . "ADD payout_id int(11) default 0 AFTER  commission_status";
    $results = $wpdb->get_results("desc {$table_prefix}mlm_rightleg");
    foreach ($results as $key => $value) {
        $fileds[] = $value->Field;
    }
    if (!in_array('payout_id', $fileds)) {
        $wpdb->query($sql);
    }
}

function mlm_core_install_country() {
    $charset_collate = mlm_core_set_charset();
    $table_prefix = mlm_core_get_table_prefix();

    $sql[] = "CREATE TABLE IF NOT EXISTS  {$table_prefix}mlm_country
			(
				id BIGINT(20) NOT NULL AUTO_INCREMENT PRIMARY KEY,
				iso CHAR(2) NOT NULL,
				name VARCHAR(80) NOT NULL,
				iso3 CHAR(3) DEFAULT NULL,
				numcode SMALLINT(6) DEFAULT NULL
			) {$charset_collate} AUTO_INCREMENT=1";

    dbDelta($sql);
}

function mlm_core_insert_into_country() {
    global $wpdb;
    $table_prefix = mlm_core_get_table_prefix();

    $insert = "INSERT INTO {$table_prefix}mlm_country (`id`, `iso`, `name`, `iso3`, `numcode`) VALUES
				(1, 'AF', 'Afghanistan', 'AFG', 4),
				(2, 'AL', 'Albania', 'ALB', 8),
				(3, 'DZ', 'Algeria', 'DZA', 12),
				(4, 'AS', 'American Samoa', 'ASM', 16),
				(5, 'AD', 'Andorra', 'AND', 20),
				(6, 'AO', 'Angola', 'AGO', 24),
				(7, 'AI', 'Anguilla', 'AIA', 660),
				(8, 'AQ', 'Antarctica', NULL, NULL),
				(9, 'AG', 'Antigua and Barbuda', 'ATG', 28),
				(10, 'AR', 'Argentina', 'ARG', 32),
				(11, 'AM', 'Armenia', 'ARM', 51),
				(12, 'AW', 'Aruba', 'ABW', 533),
				(13, 'AU', 'Australia', 'AUS', 36),
				(14, 'AT', 'Austria', 'AUT', 40),
				(15, 'AZ', 'Azerbaijan', 'AZE', 31),
				(16, 'BS', 'Bahamas', 'BHS', 44),
				(17, 'BH', 'Bahrain', 'BHR', 48),
				(18, 'BD', 'Bangladesh', 'BGD', 50),
				(19, 'BB', 'Barbados', 'BRB', 52),
				(20, 'BY', 'Belarus', 'BLR', 112),
				(21, 'BE', 'Belgium', 'BEL', 56),
				(22, 'BZ', 'Belize', 'BLZ', 84),
				(23, 'BJ', 'Benin', 'BEN', 204),
				(24, 'BM', 'Bermuda', 'BMU', 60),
				(25, 'BT', 'Bhutan', 'BTN', 64),
				(26, 'BO', 'Bolivia', 'BOL', 68),
				(27, 'BA', 'Bosnia and Herzegovina', 'BIH', 70),
				(28, 'BW', 'Botswana', 'BWA', 72),
				(29, 'BV', 'Bouvet Island', NULL, NULL),
				(30, 'BR', 'Brazil', 'BRA', 76),
				(31, 'IO', 'British Indian Ocean Territory', NULL, NULL),
				(32, 'BN', 'Brunei Darussalam', 'BRN', 96),
				(33, 'BG', 'Bulgaria', 'BGR', 100),
				(34, 'BF', 'Burkina Faso', 'BFA', 854),
				(35, 'BI', 'Burundi', 'BDI', 108),
				(36, 'KH', 'Cambodia', 'KHM', 116),
				(37, 'CM', 'Cameroon', 'CMR', 120),
				(38, 'CA', 'Canada', 'CAN', 124),
				(39, 'CV', 'Cape Verde', 'CPV', 132),
				(40, 'KY', 'Cayman Islands', 'CYM', 136),
				(41, 'CF', 'Central African Republic', 'CAF', 140),
				(42, 'TD', 'Chad', 'TCD', 148),
				(43, 'CL', 'Chile', 'CHL', 152),
				(44, 'CN', 'China', 'CHN', 156),
				(45, 'CX', 'Christmas Island', NULL, NULL),
				(46, 'CC', 'Cocos (Keeling) Islands', NULL, NULL),
				(47, 'CO', 'Colombia', 'COL', 170),
				(48, 'KM', 'Comoros', 'COM', 174),
				(49, 'CG', 'Congo', 'COG', 178),
				(50, 'CD', 'Congo, the Democratic Republic of the', 'COD', 180),
				(51, 'CK', 'Cook Islands', 'COK', 184),
				(52, 'CR', 'Costa Rica', 'CRI', 188),
				(53, 'CI', 'Cote D''Ivoire', 'CIV', 384),
				(54, 'HR', 'Croatia', 'HRV', 191),
				(55, 'CU', 'Cuba', 'CUB', 192),
				(56, 'CY', 'Cyprus', 'CYP', 196),
				(57, 'CZ', 'Czech Republic', 'CZE', 203),
				(58, 'DK', 'Denmark', 'DNK', 208),
				(59, 'DJ', 'Djibouti', 'DJI', 262),
				(60, 'DM', 'Dominica', 'DMA', 212),
				(61, 'DO', 'Dominican Republic', 'DOM', 214),
				(62, 'EC', 'Ecuador', 'ECU', 218),
				(63, 'EG', 'Egypt', 'EGY', 818),
				(64, 'SV', 'El Salvador', 'SLV', 222),
				(65, 'GQ', 'Equatorial Guinea', 'GNQ', 226),
				(66, 'ER', 'Eritrea', 'ERI', 232),
				(67, 'EE', 'Estonia', 'EST', 233),
				(68, 'ET', 'Ethiopia', 'ETH', 231),
				(69, 'FK', 'Falkland Islands (Malvinas)', 'FLK', 238),
				(70, 'FO', 'Faroe Islands', 'FRO', 234),
				(71, 'FJ', 'Fiji', 'FJI', 242),
				(72, 'FI', 'Finland', 'FIN', 246),
				(73, 'FR', 'France', 'FRA', 250),
				(74, 'GF', 'French Guiana', 'GUF', 254),
				(75, 'PF', 'French Polynesia', 'PYF', 258),
				(76, 'TF', 'French Southern Territories', NULL, NULL),
				(77, 'GA', 'Gabon', 'GAB', 266),
				(78, 'GM', 'Gambia', 'GMB', 270),
				(79, 'GE', 'Georgia', 'GEO', 268),
				(80, 'DE', 'Germany', 'DEU', 276),
				(81, 'GH', 'Ghana', 'GHA', 288),
				(82, 'GI', 'Gibraltar', 'GIB', 292),
				(83, 'GR', 'Greece', 'GRC', 300),
				(84, 'GL', 'Greenland', 'GRL', 304),
				(85, 'GD', 'Grenada', 'GRD', 308),
				(86, 'GP', 'Guadeloupe', 'GLP', 312),
				(87, 'GU', 'Guam', 'GUM', 316),
				(88, 'GT', 'Guatemala', 'GTM', 320),
				(89, 'GN', 'Guinea', 'GIN', 324),
				(90, 'GW', 'Guinea-Bissau', 'GNB', 624),
				(91, 'GY', 'Guyana', 'GUY', 328),
				(92, 'HT', 'Haiti', 'HTI', 332),
				(93, 'HM', 'Heard Island and Mcdonald Islands', NULL, NULL),
				(94, 'VA', 'Holy See (Vatican City State)', 'VAT', 336),
				(95, 'HN', 'Honduras', 'HND', 340),
				(96, 'HK', 'Hong Kong', 'HKG', 344),
				(97, 'HU', 'Hungary', 'HUN', 348),
				(98, 'IS', 'Iceland', 'ISL', 352),
				(99, 'IN', 'India', 'IND', 356),
				(100, 'ID', 'Indonesia', 'IDN', 360),
				(101, 'IR', 'Iran, Islamic Republic of', 'IRN', 364),
				(102, 'IQ', 'Iraq', 'IRQ', 368),
				(103, 'IE', 'Ireland', 'IRL', 372),
				(104, 'IL', 'Israel', 'ISR', 376),
				(105, 'IT', 'Italy', 'ITA', 380),
				(106, 'JM', 'Jamaica', 'JAM', 388),
				(107, 'JP', 'Japan', 'JPN', 392),
				(108, 'JO', 'Jordan', 'JOR', 400),
				(109, 'KZ', 'Kazakhstan', 'KAZ', 398),
				(110, 'KE', 'Kenya', 'KEN', 404),
				(111, 'KI', 'Kiribati', 'KIR', 296),
				(112, 'KP', 'Korea, Democratic People''s Republic of', 'PRK', 408),
				(113, 'KR', 'Korea, Republic of', 'KOR', 410),
				(114, 'KW', 'Kuwait', 'KWT', 414),
				(115, 'KG', 'Kyrgyzstan', 'KGZ', 417),
				(116, 'LA', 'Lao People''s Democratic Republic', 'LAO', 418),
				(117, 'LV', 'Latvia', 'LVA', 428),
				(118, 'LB', 'Lebanon', 'LBN', 422),
				(119, 'LS', 'Lesotho', 'LSO', 426),
				(120, 'LR', 'Liberia', 'LBR', 430),
				(121, 'LY', 'Libyan Arab Jamahiriya', 'LBY', 434),
				(122, 'LI', 'Liechtenstein', 'LIE', 438),
				(123, 'LT', 'Lithuania', 'LTU', 440),
				(124, 'LU', 'Luxembourg', 'LUX', 442),
				(125, 'MO', 'Macao', 'MAC', 446),
				(126, 'MK', 'Macedonia, the Former Yugoslav Republic of', 'MKD', 807),
				(127, 'MG', 'Madagascar', 'MDG', 450),
				(128, 'MW', 'Malawi', 'MWI', 454),
				(129, 'MY', 'Malaysia', 'MYS', 458),
				(130, 'MV', 'Maldives', 'MDV', 462),
				(131, 'ML', 'Mali', 'MLI', 466),
				(132, 'MT', 'Malta', 'MLT', 470),
				(133, 'MH', 'Marshall Islands', 'MHL', 584),
				(134, 'MQ', 'Martinique', 'MTQ', 474),
				(135, 'MR', 'Mauritania', 'MRT', 478),
				(136, 'MU', 'Mauritius', 'MUS', 480),
				(137, 'YT', 'Mayotte', NULL, NULL),
				(138, 'MX', 'Mexico', 'MEX', 484),
				(139, 'FM', 'Micronesia, Federated States of', 'FSM', 583),
				(140, 'MD', 'Moldova, Republic of', 'MDA', 498),
				(141, 'MC', 'Monaco', 'MCO', 492),
				(142, 'MN', 'Mongolia', 'MNG', 496),
				(143, 'MS', 'Montserrat', 'MSR', 500),
				(144, 'MA', 'Morocco', 'MAR', 504),
				(145, 'MZ', 'Mozambique', 'MOZ', 508),
				(146, 'MM', 'Myanmar', 'MMR', 104),
				(147, 'NA', 'Namibia', 'NAM', 516),
				(148, 'NR', 'Nauru', 'NRU', 520),
				(149, 'NP', 'Nepal', 'NPL', 524),
				(150, 'NL', 'Netherlands', 'NLD', 528),
				(151, 'AN', 'Netherlands Antilles', 'ANT', 530),
				(152, 'NC', 'New Caledonia', 'NCL', 540),
				(153, 'NZ', 'New Zealand', 'NZL', 554),
				(154, 'NI', 'Nicaragua', 'NIC', 558),
				(155, 'NE', 'Niger', 'NER', 562),
				(156, 'NG', 'Nigeria', 'NGA', 566),
				(157, 'NU', 'Niue', 'NIU', 570),
				(158, 'NF', 'Norfolk Island', 'NFK', 574),
				(159, 'MP', 'Northern Mariana Islands', 'MNP', 580),
				(160, 'NO', 'Norway', 'NOR', 578),
				(161, 'OM', 'Oman', 'OMN', 512),
				(162, 'PK', 'Pakistan', 'PAK', 586),
				(163, 'PW', 'Palau', 'PLW', 585),
				(164, 'PS', 'Palestinian Territory, Occupied', NULL, NULL),
				(165, 'PA', 'Panama', 'PAN', 591),
				(166, 'PG', 'Papua New Guinea', 'PNG', 598),
				(167, 'PY', 'Paraguay', 'PRY', 600),
				(168, 'PE', 'Peru', 'PER', 604),
				(169, 'PH', 'Philippines', 'PHL', 608),
				(170, 'PN', 'Pitcairn', 'PCN', 612),
				(171, 'PL', 'Poland', 'POL', 616),
				(172, 'PT', 'Portugal', 'PRT', 620),
				(173, 'PR', 'Puerto Rico', 'PRI', 630),
				(174, 'QA', 'Qatar', 'QAT', 634),
				(175, 'RE', 'Reunion', 'REU', 638),
				(176, 'RO', 'Romania', 'ROM', 642),
				(177, 'RU', 'Russian Federation', 'RUS', 643),
				(178, 'RW', 'Rwanda', 'RWA', 646),
				(179, 'SH', 'Saint Helena', 'SHN', 654),
				(180, 'KN', 'Saint Kitts and Nevis', 'KNA', 659),
				(181, 'LC', 'Saint Lucia', 'LCA', 662),
				(182, 'PM', 'Saint Pierre and Miquelon', 'SPM', 666),
				(183, 'VC', 'Saint Vincent and the Grenadines', 'VCT', 670),
				(184, 'WS', 'Samoa', 'WSM', 882),
				(185, 'SM', 'San Marino', 'SMR', 674),
				(186, 'ST', 'Sao Tome and Principe', 'STP', 678),
				(187, 'SA', 'Saudi Arabia', 'SAU', 682),
				(188, 'SN', 'Senegal', 'SEN', 686),
				(189, 'CS', 'Serbia and Montenegro', NULL, NULL),
				(190, 'SC', 'Seychelles', 'SYC', 690),
				(191, 'SL', 'Sierra Leone', 'SLE', 694),
				(192, 'SG', 'Singapore', 'SGP', 702),
				(193, 'SK', 'Slovakia', 'SVK', 703),
				(194, 'SI', 'Slovenia', 'SVN', 705),
				(195, 'SB', 'Solomon Islands', 'SLB', 90),
				(196, 'SO', 'Somalia', 'SOM', 706),
				(197, 'ZA', 'South Africa', 'ZAF', 710),
				(198, 'GS', 'South Georgia and the South Sandwich Islands', NULL, NULL),
				(199, 'ES', 'Spain', 'ESP', 724),
				(200, 'LK', 'Sri Lanka', 'LKA', 144),
				(201, 'SD', 'Sudan', 'SDN', 736),
				(202, 'SR', 'Suriname', 'SUR', 740),
				(203, 'SJ', 'Svalbard and Jan Mayen', 'SJM', 744),
				(204, 'SZ', 'Swaziland', 'SWZ', 748),
				(205, 'SE', 'Sweden', 'SWE', 752),
				(206, 'CH', 'Switzerland', 'CHE', 756),
				(207, 'SY', 'Syrian Arab Republic', 'SYR', 760),
				(208, 'TW', 'Taiwan, Province of China', 'TWN', 158),
				(209, 'TJ', 'Tajikistan', 'TJK', 762),
				(210, 'TZ', 'Tanzania, United Republic of', 'TZA', 834),
				(211, 'TH', 'Thailand', 'THA', 764),
				(212, 'TL', 'Timor-Leste', NULL, NULL),
				(213, 'TG', 'Togo', 'TGO', 768),
				(214, 'TK', 'Tokelau', 'TKL', 772),
				(215, 'TO', 'Tonga', 'TON', 776),
				(216, 'TT', 'Trinidad and Tobago', 'TTO', 780),
				(217, 'TN', 'Tunisia', 'TUN', 788),
				(218, 'TR', 'Turkey', 'TUR', 792),
				(219, 'TM', 'Turkmenistan', 'TKM', 795),
				(220, 'TC', 'Turks and Caicos Islands', 'TCA', 796),
				(221, 'TV', 'Tuvalu', 'TUV', 798),
				(222, 'UG', 'Uganda', 'UGA', 800),
				(223, 'UA', 'Ukraine', 'UKR', 804),
				(224, 'AE', 'United Arab Emirates', 'ARE', 784),
				(225, 'GB', 'United Kingdom', 'GBR', 826),
				(226, 'US', 'United States', 'USA', 840),
				(227, 'UM', 'United States Minor Outlying Islands', NULL, NULL),
				(228, 'UY', 'Uruguay', 'URY', 858),
				(229, 'UZ', 'Uzbekistan', 'UZB', 860),
				(230, 'VU', 'Vanuatu', 'VUT', 548),
				(231, 'VE', 'Venezuela', 'VEN', 862),
				(232, 'VN', 'Viet Nam', 'VNM', 704),
				(233, 'VG', 'Virgin Islands, British', 'VGB', 92),
				(234, 'VI', 'Virgin Islands, U.s.', 'VIR', 850),
				(235, 'WF', 'Wallis and Futuna', 'WLF', 876),
				(236, 'EH', 'Western Sahara', 'ESH', 732),
				(237, 'YE', 'Yemen', 'YEM', 887),
				(238, 'ZM', 'Zambia', 'ZMB', 894),
				(239, 'ZW', 'Zimbabwe', 'ZWE', 716)";
    $results = $wpdb->get_results("select * from {$table_prefix}mlm_country");
    $rows = $wpdb->num_rows;
    if (empty($rows) || $rows < 239) {
        $wpdb->query($insert);
    }
}

function mlm_core_install_currency() {
    $charset_collate = mlm_core_set_charset();
    $table_prefix = mlm_core_get_table_prefix();

    $sql[] = "CREATE TABLE IF NOT EXISTS  {$table_prefix}mlm_currency 
			(
				id BIGINT(20) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
				iso3 VARCHAR (5) NOT NULL,
				currency VARCHAR( 60 ) NOT NULL
			) {$charset_collate} AUTO_INCREMENT=1";

    dbDelta($sql);
}

function mlm_core_insert_into_currency() {
    global $wpdb, $table_prefix;

    $insert = "INSERT INTO {$table_prefix}mlm_currency (`id`, `iso3`, `currency`) VALUES
				(1, 'AED', 'Emirati Dirham'),
				(2, 'AFN', 'Afghan Afghani'),
				(3, 'ALL', 'Albanian Lek'),
				(4, 'AMD', 'Armenian Dram'),
				(5, 'ANG', 'Dutch Guilder'),
				(6, 'AOA', 'Angolan Kwanza'),
				(7, 'ARS', 'Argentine Peso'),
				(8, 'AUD', 'Australian Dollar'),
				(9, 'AWG', 'Aruban or Dutch Guilder'),
				(10, 'AZN', 'Azerbaijani New Manat'),
				(11, 'BAM', 'Bosnian Convertible Marka'),
				(12, 'BBD', 'Barbadian or Bajan Dollar'),
				(13, 'BDT', 'Bangladeshi Taka'),
				(14, 'BGN', 'Bulgarian Lev'),
				(15, 'BHD', 'Bahraini Dinar'),
				(16, 'BIF', 'Burundian Franc'),
				(17, 'BMD', 'Bermudian Dollar'),
				(18, 'BND', 'Bruneian Dollar'),
				(19, 'BOB', 'Bolivian Boliviano'),
				(20, 'BRL', 'Brazilian Real'),
				(21, 'BSD', 'Bahamian Dollar'),
				(22, 'BTN', 'Bhutanese Ngultrum'),
				(23, 'BWP', 'Botswana Pula'),
				(24, 'BYR', 'Belarusian Ruble'),
				(25, 'BZD', 'Belizean Dollar'),
				(26, 'CAD', 'Canadian Dollar'),
				(27, 'CDF', 'Congolese Franc'),
				(28, 'CHF', 'Swiss Franc'),
				(29, 'CLP', 'Chilean Peso'),
				(30, 'CNY', 'Chinese Yuan Renminbi'),
				(31, 'COP', 'Colombian Peso'),
				(32, 'CRC', 'Costa Rican Colon'),
				(33, 'CUC', 'Cuban Convertible Peso'),
				(34, 'CUP', 'Cuban Peso'),
				(35, 'CVE', 'Cape Verdean Escudo'),
				(36, 'CZK', 'Czech Koruna'),
				(37, 'DJF', 'Djiboutian Franc'),
				(38, 'DKK', 'Danish Krone'),
				(39, 'DOP', 'Dominican Peso'),
				(40, 'DZD', 'Algerian Dinar'),
				(41, 'EGP', 'Egyptian Pound'),
				(42, 'ERN', 'Eritrean Nakfa'),
				(43, 'ETB', 'Ethiopian Birr'),
				(44, 'EUR', 'Euro'),
				(45, 'FJD', 'Fijian Dollar'),
				(46, 'FKP', 'Falkland Island Pound'),
				(47, 'GBP', 'British Pound'),
				(48, 'GEL', 'Georgian Lari'),
				(49, 'GGP', 'Guernsey Pound'),
				(50, 'GHS', 'Ghanaian Cedi'),
				(51, 'GIP', 'Gibraltar Pound'),
				(52, 'GMD', 'Gambian Dalasi'),
				(53, 'GNF', 'Guinean Franc'),
				(54, 'GTQ', 'Guatemalan Quetzal'),
				(55, 'GYD', 'Guyanese Dollar'),
				(56, 'HKD', 'Hong Kong Dollar'),
				(57, 'HNL', 'Honduran Lempira'),
				(58, 'HRK', 'Croatian Kuna'),
				(59, 'HTG', 'Haitian Gourde'),
				(60, 'HUF', 'Hungarian Forint'),
				(61, 'IDR', 'Indonesian Rupiah'),
				(62, 'ILS', 'Israeli Shekel'),
				(63, 'IMP', 'Isle of Man Pound'),
				(64, 'INR', 'Indian Rupee'),
				(65, 'IQD', 'Iraqi Dinar'),
				(66, 'IRR', 'Iranian Rial'),
				(67, 'ISK', 'Icelandic Krona'),
				(68, 'JEP', 'Jersey Pound'),
				(69, 'JMD', 'Jamaican Dollar'),
				(70, 'JOD', 'Jordanian Dinar'),
				(71, 'JPY', 'Japanese Yen'),
				(72, 'KES', 'Kenyan Shilling'),
				(73, 'KGS', 'Kyrgyzstani Som'),
				(74, 'KHR', 'Cambodian Riel'),
				(75, 'KMF', 'Comoran Franc'),
				(76, 'KPW', 'North Korean Won'),
				(77, 'KRW', 'South Korean Won'),
				(78, 'KWD', 'Kuwaiti Dinar'),
				(79, 'KYD', 'Caymanian Dollar'),
				(80, 'KZT', 'Kazakhstani Tenge'),
				(81, 'LAK', 'Lao or Laotian Kip'),
				(82, 'LBP', 'Lebanese Pound'),
				(83, 'LKR', 'Sri Lankan Rupee'),
				(84, 'LRD', 'Liberian Dollar'),
				(85, 'LSL', 'Basotho Loti'),
				(86, 'LTL', 'Lithuanian Litas'),
				(87, 'LVL', 'Latvian Lat'),
				(88, 'LYD', 'Libyan Dinar'),
				(89, 'MAD', 'Moroccan Dirham'),
				(90, 'MDL', 'Moldovan Leu'),
				(91, 'MGA', 'Malagasy Ariary'),
				(92, 'MKD', 'Macedonian Denar'),
				(93, 'MMK', 'Burmese Kyat'),
				(94, 'MNT', 'Mongolian Tughrik'),
				(95, 'MOP', 'Macau Pataca'),
				(96, 'MRO', 'Mauritanian Ouguiya'),
				(97, 'MUR', 'Mauritian Rupee'),
				(98, 'MVR', 'Maldivian Rufiyaa'),
				(99, 'MWK', 'Malawian Kwacha'),
				(100, 'MXN', 'Mexican Peso'),
				(101, 'MYR', 'Malaysian Ringgit'),
				(102, 'MZN', 'Mozambican Metical'),
				(103, 'NAD', 'Namibian Dollar'),
				(104, 'NGN', 'Nigerian Naira'),
				(105, 'NIO', 'Nicaraguan Cordoba'),
				(106, 'NOK', 'Norwegian Krone'),
				(107, 'NPR', 'Nepalese Rupee'),
				(108, 'NZD', 'New Zealand Dollar'),
				(109, 'OMR', 'Omani Rial'),
				(110, 'PAB', 'Panamanian Balboa'),
				(111, 'PEN', 'Peruvian Nuevo Sol'),
				(112, 'PGK', 'Papua New Guinean Kina'),
				(113, 'PHP', 'Philippine Peso'),
				(114, 'PKR', 'Pakistani Rupee'),
				(115, 'PLN', 'Polish Zloty'),
				(116, 'PYG', 'Paraguayan Guarani'),
				(117, 'QAR', 'Qatari Riyal'),
				(118, 'RON', 'Romanian New Leu'),
				(119, 'RSD', 'Serbian Dinar'),
				(120, 'RUB', 'Russian Ruble'),
				(121, 'RWF', 'Rwandan Franc'),
				(122, 'SAR', 'Saudi or Saudi Arabian Riyal'),
				(123, 'SBD', 'Solomon Islander Dollar'),
				(124, 'SCR', 'Seychellois Rupee'),
				(125, 'SDG', 'Sudanese Pound'),
				(126, 'SEK', 'Swedish Krona'),
				(127, 'SGD', 'Singapore Dollar'),
				(128, 'SHP', 'Saint Helenian Pound'),
				(129, 'SLL', 'Sierra Leonean Leone'),
				(130, 'SOS', 'Somali Shilling'),
				(131, 'SPL', 'Seborgan Luigino'),
				(132, 'SRD', 'Surinamese Dollar'),
				(133, 'STD', 'Sao Tomean Dobra'),
				(134, 'SVC', 'Salvadoran Colon'),
				(135, 'SYP', 'Syrian Pound'),
				(136, 'SZL', 'Swazi Lilangeni'),
				(137, 'THB', 'Thai Baht'),
				(138, 'TJS', 'Tajikistani Somoni'),
				(139, 'TMT', 'Turkmenistani Manat'),
				(140, 'TND', 'Tunisian Dinar'),
				(141, 'TOP', 'Tongan Pa''anga'),
				(142, 'TRY', 'Turkish Lira'),
				(143, 'TTD', 'Trinidadian Dollar'),
				(144, 'TVD', 'Tuvaluan Dollar'),
				(145, 'TWD', 'Taiwan New Dollar'),
				(146, 'TZS', 'Tanzanian Shilling'),
				(147, 'UAH', 'Ukrainian Hryvna'),
				(148, 'UGX', 'Ugandan Shilling'),
				(149, 'USD', 'US Dollar'),
				(150, 'UYU', 'Uruguayan Peso'),
				(151, 'UZS', 'Uzbekistani Som'),
				(152, 'VEF', 'Venezuelan Bolivar Fuerte'),
				(153, 'VND', 'Vietnamese Dong'),
				(154, 'VUV', 'NiVanuatu Vatu'),
				(155, 'WST', 'Samoan Tala'),
				(156, 'XAF', 'Central African CFA Franc BEAC'),
				(157, 'XAG', 'Silver Ounce'),
				(158, 'XAU', 'Gold Ounce'),
				(159, 'XCD', 'East Caribbean Dollar'),
				(160, 'XDR', 'IMF Special Drawing Rights'),
				(161, 'XOF', 'CFA Franc'),
				(162, 'XPD', 'Palladium Ounce'),
				(163, 'XPF', 'CFP Franc'),
				(164, 'XPT', 'Platinum Ounce'),
				(165, 'YER', 'Yemeni Rial'),
				(166, 'ZAR', 'South African Rand'),
				(167, 'ZMK', 'Zambian Kwacha'),
				(168, 'ZWD', 'Zimbabwean Dollar')";

    $results = $wpdb->get_results("select * from {$table_prefix}mlm_currency");
    $rows = $wpdb->num_rows;
    if (empty($rows) || $rows < 168) {
        $wpdb->query($insert);
    }
}

function mlm_core_install_commission() {
    global $wpdb;
    $charset_collate = mlm_core_set_charset();
    $table_prefix = mlm_core_get_table_prefix();

    $sql[] = "CREATE TABLE IF NOT EXISTS  {$table_prefix}mlm_commission
			(
				id BIGINT(20) NOT NULL AUTO_INCREMENT PRIMARY KEY,
				date_notified datetime NOT NULL,
				parent_id BIGINT(20) NOT NULL,
				child_ids VARCHAR( 60 ) NOT NULL,
				amount DOUBLE( 6,2 ) NOT NULL DEFAULT 0.00 ,
				payout_id int(11) NOT NULL DEFAULT '0',
				KEY index_parentid (parent_id)
			) {$charset_collate} AUTO_INCREMENT=1";

    dbDelta($sql);
}

function mlm_core_install_bonus() {
    global $wpdb;
    $charset_collate = mlm_core_set_charset();
    $table_prefix = mlm_core_get_table_prefix();

    $sql[] = "CREATE TABLE IF NOT EXISTS  {$table_prefix}mlm_bonus
			(
				id BIGINT(20) NOT NULL AUTO_INCREMENT PRIMARY KEY,
				date_notified datetime NOT NULL,
				mlm_user_id BIGINT(20) NOT NULL,
				amount DOUBLE( 6,2 ) NOT NULL DEFAULT 0.00,
				payout_id int(11) NOT NULL DEFAULT '0',
				KEY index_mlm_user_id (mlm_user_id)
			) {$charset_collate} AUTO_INCREMENT=1";

    dbDelta($sql);
}

/* * **************************************************************
  Payout Master Table
 * ************************************************************** */

function mlm_core_install_payout_master() {
    global $wpdb;
    $charset_collate = mlm_core_set_charset();
    $table_prefix = mlm_core_get_table_prefix();

    $sql[] = "CREATE TABLE IF NOT EXISTS {$table_prefix}mlm_payout_master
			(
				id int(10) unsigned NOT NULL AUTO_INCREMENT,
				date date NOT NULL,
				PRIMARY KEY (`id`)
			) {$charset_collate} AUTO_INCREMENT=1";

    dbDelta($sql);
}

/* * **************************************************************
  Payout Table
 * ************************************************************** */

function mlm_core_install_payout() {
    global $wpdb;
    $charset_collate = mlm_core_set_charset();
    $table_prefix = mlm_core_get_table_prefix();

    $sql[] = "CREATE TABLE IF NOT EXISTS {$table_prefix}mlm_payout
			(
				  id int(10) unsigned NOT NULL AUTO_INCREMENT,
				  user_id bigint(20) NOT NULL,
				  date date NOT NULL,
				  payout_id int(11) NOT NULL,
				  commission_amount double(10,2) DEFAULT '0.00',
				  bonus_amount double(10,2) DEFAULT '0.00',
				  banktransfer_code varchar(10) DEFAULT NULL,
				  cheque_no varchar(10) DEFAULT NULL,
				  cheque_date date DEFAULT NULL,
				  bank_name varchar(50) DEFAULT NULL,
				  user_bank_name varchar(50) DEFAULT NULL,
				  user_bank_account_no varchar(10) DEFAULT NULL,
				  tax double(10,2) DEFAULT '0.00',
				  service_charge double(10,2) DEFAULT '0.00',
				  dispatch_date date DEFAULT NULL,
				  courier_name varchar(20) DEFAULT NULL,
				  awb_no varchar(20) DEFAULT NULL,
				  PRIMARY KEY (`id`)
			) {$charset_collate} AUTO_INCREMENT=1";

    dbDelta($sql);
}

/* * ********************************** ePins Table *********************************** */

function mlm_core_install_epins() {
    global $wpdb;
    $charset_collate = mlm_core_set_charset();
    $table_prefix = mlm_core_get_table_prefix();

    $sql[] = "CREATE TABLE IF NOT EXISTS  {$table_prefix}mlm_epins
			(
				id BIGINT(20) NOT NULL AUTO_INCREMENT PRIMARY KEY,
				epin_no  VARCHAR( 60 ) NOT NULL,
				point_status  boolean NOT NULL COMMENT '1=>Regular,0=>free',
				date_generated  datetime NOT NULL,
				user_key  VARCHAR(15) NOT NULL DEFAULT 0,
				date_used  datetime NOT NULL,
				status boolean NOT NULL DEFAULT 0) {$charset_collate} AUTO_INCREMENT=1";
    dbDelta($sql);
}

function mlm_core_alter_epins() {
    global $wpdb;
    $charset_collate = mlm_core_set_charset();
    $table_prefix = mlm_core_get_table_prefix();

    $results = $wpdb->get_results("desc {$table_prefix}mlm_epins");
    foreach ($results as $key => $value) {
        $fileds[] = $value->Field;
    }
    if (!in_array('p_id', $fileds)) {
        $wpdb->query("ALTER TABLE   {$table_prefix}mlm_epins ADD p_id int NOT NULL  AFTER id");
    }
}

/* * ****************************************** ePins Table **************************** */

function mlm_core_update_payout() {
    global $wpdb;
    $charset_collate = mlm_core_set_charset();
    $table_prefix = mlm_core_get_table_prefix();

    $results = $wpdb->get_results("desc {$table_prefix}mlm_payout");
    foreach ($results as $key => $value) {
        $fileds[] = $value->Field;
    }
    if (!in_array('withdrawal_initiated', $fileds)) {
        $wpdb->query("ALTER TABLE   {$table_prefix}mlm_payout ADD withdrawal_initiated BOOLEAN NOT NULL DEFAULT '0' COMMENT '0=>No, 1=> Yes' AFTER bonus_amount");
    }
    $results = $wpdb->get_results("desc {$table_prefix}mlm_payout");
    foreach ($results as $key => $value) {
        $fileds[] = $value->Field;
    }
    if (!in_array('withdrawal_initiated_comment', $fileds)) {
        $wpdb->query("ALTER TABLE   {$table_prefix}mlm_payout ADD  withdrawal_initiated_comment VARCHAR( 200 ) NOT NULL AFTER  withdrawal_initiated");
    }
    $results = $wpdb->get_results("desc {$table_prefix}mlm_payout");
    foreach ($results as $key => $value) {
        $fileds[] = $value->Field;
    }
    if (!in_array('withdrawal_initiated_date', $fileds)) {
        $wpdb->query("ALTER TABLE   {$table_prefix}mlm_payout ADD withdrawal_initiated_date DATE NOT NULL AFTER withdrawal_initiated_comment");
    }
    $results = $wpdb->get_results("desc {$table_prefix}mlm_payout");
    foreach ($results as $key => $value) {
        $fileds[] = $value->Field;
    }
    if (!in_array('payment_mode', $fileds)) {
        $wpdb->query("ALTER TABLE   {$table_prefix}mlm_payout ADD payment_mode VARCHAR( 100 ) NOT NULL AFTER bonus_amount");
    }
    $results = $wpdb->get_results("desc {$table_prefix}mlm_payout");
    foreach ($results as $key => $value) {
        $fileds[] = $value->Field;
    }
    if (!in_array('payment_processed', $fileds)) {
        $wpdb->query("ALTER TABLE   {$table_prefix}mlm_payout ADD payment_processed BOOLEAN NOT NULL DEFAULT '0' COMMENT '0=>No, 1=> Yes' AFTER withdrawal_initiated_date");
    }
    $results = $wpdb->get_results("desc {$table_prefix}mlm_payout");
    foreach ($results as $key => $value) {
        $fileds[] = $value->Field;
    }
    if (!in_array('payment_processed_date', $fileds)) {
        $wpdb->query("ALTER TABLE   {$table_prefix}mlm_payout ADD payment_processed_date DATE NOT NULL AFTER payment_processed");
    }
    $results = $wpdb->get_results("desc {$table_prefix}mlm_payout");
    foreach ($results as $key => $value) {
        $fileds[] = $value->Field;
    }
    if (!in_array('beneficiary', $fileds)) {
        $wpdb->query("ALTER TABLE   {$table_prefix}mlm_payout ADD beneficiary VARCHAR( 100 ) NOT NULL AFTER payment_processed_date");
    }
    $results = $wpdb->get_results("desc {$table_prefix}mlm_payout");
    foreach ($results as $key => $value) {
        $fileds[] = $value->Field;
    }
    if (!in_array('other_comments', $fileds)) {
        $wpdb->query("ALTER TABLE   {$table_prefix}mlm_payout ADD other_comments VARCHAR( 100 ) NOT NULL AFTER user_bank_account_no");
    }
    $results = $wpdb->get_results("desc {$table_prefix}mlm_payout");
    foreach ($results as $key => $value) {
        $fileds[] = $value->Field;
    }
    if (!in_array('referral_commission_amount', $fileds)) {
        $wpdb->query("ALTER TABLE   {$table_prefix}mlm_payout ADD referral_commission_amount DOUBLE( 10, 2 ) NOT NULL DEFAULT '0.00' AFTER `commission_amount`");
    }
    $results = $wpdb->get_results("desc {$table_prefix}mlm_payout");
    foreach ($results as $key => $value) {
        $fileds[] = $value->Field;
    }
    if (!in_array('total_amt', $fileds)) {
        $wpdb->query("ALTER TABLE   {$table_prefix}mlm_payout ADD total_amt VARCHAR( 100 ) NOT NULL DEFAULT '0.00' AFTER bonus_amount");
    }
    $results = $wpdb->get_results("desc {$table_prefix}mlm_payout");
    foreach ($results as $key => $value) {
        $fileds[] = $value->Field;
    }
    if (!in_array('capped_amt', $fileds)) {
        $wpdb->query("ALTER TABLE   {$table_prefix}mlm_payout ADD capped_amt VARCHAR( 100 ) NOT NULL DEFAULT '0.00'  AFTER total_amt");
    }
    $results = $wpdb->get_results("desc {$table_prefix}mlm_payout");
    foreach ($results as $key => $value) {
        $fileds[] = $value->Field;
    }
    if (!in_array('cap_limit', $fileds)) {
        $wpdb->query("ALTER TABLE   {$table_prefix}mlm_payout ADD cap_limit VARCHAR( 100 ) NOT NULL DEFAULT '0.00' AFTER capped_amt");
    }
}

function mlm_core_update_payout_master() {
    global $wpdb;
    $charset_collate = mlm_core_set_charset();
    $table_prefix = mlm_core_get_table_prefix();

    $sql = "ALTER TABLE   {$table_prefix}mlm_payout_master ADD cap_limit float NOT NULL DEFAULT '0'  AFTER date,{$charset_collate}";
    $results = $wpdb->get_results("desc {$table_prefix}mlm_payout_master");
    foreach ($results as $key => $value) {
        $fileds[] = $value->Field;
    }
    if (!in_array('cap_limit', $fileds)) {
        $wpdb->query($sql);
    }
}

function mlm_core_install_refe_comm() {
    global $wpdb;
    $charset_collate = mlm_core_set_charset();
    $table_prefix = mlm_core_get_table_prefix();

    $sql[] = "CREATE TABLE IF NOT EXISTS  {$table_prefix}mlm_referral_commission
			(
				id BIGINT(20) NOT NULL AUTO_INCREMENT PRIMARY KEY,
				date_notified datetime NOT NULL,
				sponsor_id BIGINT(20) NOT NULL,
				child_id VARCHAR( 60 ) NOT NULL,
				amount DOUBLE( 6,2 ) NOT NULL DEFAULT 0.00 ,
				payout_id int(11) NOT NULL DEFAULT '0',
				KEY index_sponsorid (sponsor_id),
				UNIQUE(child_id)
			) {$charset_collate} AUTO_INCREMENT=1";
    dbDelta($sql);
}

function mlm_core_install_product_price() {
    global $wpdb;
    $charset_collate = mlm_core_set_charset();
    $table_prefix = mlm_core_get_table_prefix();

    $sql[] = "CREATE TABLE IF NOT EXISTS  {$table_prefix}mlm_product_price(
                    p_id BIGINT(20) NOT NULL AUTO_INCREMENT PRIMARY KEY,
                    product_name  VARCHAR( 255 ) NOT NULL,
                    product_price  INT(11) NOT NULL DEFAULT '0') {$charset_collate} AUTO_INCREMENT=1";
    dbDelta($sql);
    if ($wpdb->get_var("select count(p_id)as count from {$table_prefix}mlm_product_price") < 1) {
        $wpdb->query("INSERT INTO {$table_prefix}mlm_product_price set product_name='Free ePin',product_price=0");
    }
}

function net_amount_payout() {
    global $wpdb, $table_prefix;
    $wpdb->query("update {$table_prefix}mlm_payout set total_amt = commission_amount+referral_commission_amount+bonus_amount,
	 				capped_amt= commission_amount+referral_commission_amount+bonus_amount-service_charge-tax where cap_limit=0.00");
}

function mlm_core_delete_users_data() {
    global $wpdb;
    $table_prefix = mlm_core_get_table_prefix();
    $sql = "SELECT user_id FROM {$table_prefix}mlm_users";

    $results = $wpdb->get_results($sql);
    $user_id = '';

    foreach ($results as $row) {
        $user_id .= $row->user_id . ",";
    }
    $user_id = substr($user_id, 0, -1);
    if (!empty($user_id)) {
        $wpdb->query("DELETE FROM {$table_prefix}users WHERE ID IN ($user_id)");
        $wpdb->query("DELETE FROM {$table_prefix}usermeta WHERE user_id IN ($user_id)");
    }
}

function mlm_core_drop_tables() {
    global $wpdb;
    $table_prefix = mlm_core_get_table_prefix();
    mlm_core_delete_users_data();
    $wpdb->query("DROP TABLE {$table_prefix}mlm_users");
    $wpdb->query("DROP TABLE {$table_prefix}mlm_leftleg");
    $wpdb->query("DROP TABLE {$table_prefix}mlm_rightleg");
    $wpdb->query("DROP TABLE {$table_prefix}mlm_country");
    $wpdb->query("DROP TABLE {$table_prefix}mlm_currency");
    $wpdb->query("DROP TABLE {$table_prefix}mlm_commission");
    $wpdb->query("DROP TABLE {$table_prefix}mlm_bonus");
    $wpdb->query("DROP TABLE {$table_prefix}mlm_payout");
    $wpdb->query("DROP TABLE {$table_prefix}mlm_payout_master");
    $wpdb->query("DROP TABLE {$table_prefix}mlm_referral_commission");
    $wpdb->query("DROP TABLE {$table_prefix}mlm_epins");
}

?>
