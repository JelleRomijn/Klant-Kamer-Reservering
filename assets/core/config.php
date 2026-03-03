<?php
// Configuration values for the application
// Define the allowed subnets (CIDR) that are permitted to register.
// Update these entries to match your building's Wi-Fi network ranges.
$ALLOWED_SUBNETS = [
    // Example private network ranges (adjust to your environment):
    '192.168.1.0/24',
    '10.0.0.0/8',
    '172.16.0.0/12',
    // Allow localhost testing (IPv4 loopback)
    '127.0.0.0/8',
];

/**
 * Check if an IP address is inside a given CIDR subnet
 * @param string $ip
 * @param string $cidr
 * @return bool
 */
function ip_in_cidr($ip, $cidr) {
    list($subnet, $mask) = explode('/', $cidr);
    $ip_decimal = sprintf('%u', ip2long($ip));
    $subnet_decimal = sprintf('%u', ip2long($subnet));
    $mask_decimal = $mask === '0' ? 0 : ~((1 << (32 - (int)$mask)) - 1);
    // normalize to unsigned
    $mask_decimal = sprintf('%u', $mask_decimal & 0xFFFFFFFF);
    return (($ip_decimal & $mask_decimal) === ($subnet_decimal & $mask_decimal));
}

/**
 * Check if given IP is allowed by $ALLOWED_SUBNETS
 * @param string $ip
 * @return bool
 */
function is_ip_allowed($ip) {
    global $ALLOWED_SUBNETS;
    // Normalize IPv6 loopback to IPv4 loopback for local testing
    if ($ip === '::1') {
        $ip = '127.0.0.1';
    }
    foreach ($ALLOWED_SUBNETS as $cidr) {
        if (ip_in_cidr($ip, $cidr)) return true;
    }
    return false;
}

?>
