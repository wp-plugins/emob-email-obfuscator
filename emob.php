<?php
/*
Plugin Name: Email Obfuscator
Plugin URI: http://paxoo.com/wp-emob
Description: Based on <a href="http://macromates.com/blog/2007/obfuscating-emails-revisited/">Allan Odgaard's Ruby script</a> for TextMate, this plugin will automatically make any detected email address within posts and comments harder for spam harvesters to detect.
Version: 1.1
License: GPL
Author: Billy Halsey
Author URI: http://paxoo.com
*/

// =========================================================================
// = Hide your email address!                                              =
// =                                                                       =
// = Planned features:                                                     =
// =  - Customizable friendly text output                                  =
// =========================================================================


function emob_hexify_mailto($mailto)
{
    $m = preg_replace('/mailto:/', '', $mailto);
    $hexified = '';
    for ($i=0; $i < strlen($m); $i++) { 
        $hexified .= '%' . strtoupper(base_convert(ord($m[$i]), 10, 16));
    }
    return $hexified;
}
// ----------------------------------------------------------------------
// ABOVE IS GOLDEN
// ----------------------------------------------------------------------

function emob_readable_mail($address)
{
    $addr_pattern =
        '/([A-Z0-9._%+-]+)@([A-Z0-9.-]+)\.([A-Z]{2,4})/i';
    $addr_readable =
        '$1 {at} $2(.)$3';
    return preg_replace($addr_pattern, $addr_readable, $address);
}
// ----------------------------------------------------------------------
// ABOVE IS GOLDEN
// ----------------------------------------------------------------------

function emob_obfusc_mail($address)
{
    // Requires: PHP >= 4.2.0
    return str_rot13($address);
}

function emob_makelink($mailto, $id, $js = false)
{
    $hexlink  = emob_hexify_mailto($mailto);
    $readable = emob_readable_mail($mailto);
    $obfusc   = emob_obfusc_mail($mailto);
    
    if ($js) {
        $link = "mailto:$hexlink";
    } else {
        $link = "<a href=\"mailto:$hexlink\" id=\"emob-$obfusc-$id\">$readable</a>";
    }
    return $link;
}

function emob_addJScript($address, $id)
{
    $obfusc   = emob_obfusc_mail($address);
    $readable = emob_readable_mail($address);
    $link     = emob_makelink($address, $id, true);
    
    $emob_js = <<<EJS
<script type="text/javascript">
    var mailNode = document.getElementById('emob-$obfusc-$id');
    var linkNode = document.createElement('a');
    linkNode.setAttribute('href', "$link");
    tNode = document.createTextNode("$readable");
    linkNode.appendChild(tNode);
    linkNode.setAttribute('id', "emob-$obfusc-$id");
    mailNode.parentNode.replaceChild(linkNode, mailNode);
</script>
EJS;
    return $emob_js;
}

function emob_replace($content)
{
    $mailto_pattern = '/mailto:[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}/i';
    preg_match_all($mailto_pattern, $content, $mailtos);
    for ($i=0; $i < count($mailtos); $i++) { 
        $mto[0][$i] = "mailto:" . emob_hexify_mailto($mailtos[0][$i]);
    }
    $cont = str_replace($mailtos[0], $mto[0], $content);
    // ----------------------------------------------------------------------
    // ABOVE IS GOLDEN
    // ----------------------------------------------------------------------
    
    $addr_pattern = '/([A-Z0-9._%+-]+)@([A-Z0-9.-]+)\.([A-Z]{2,4})/i';
    preg_match_all($addr_pattern, $cont, $addresses);
    $the_addrs = $addresses[0];
    for ($a=0; $a < count($the_addrs); $a++) {
            $r = rand(10, 99);  // Avoid collisions in 'id' attributes namespace!
            $obfusc = emob_obfusc_mail($the_addrs[$a]);
            $repaddr[$a] = "<span id=\"emob-$obfusc-$r\">" . 
                emob_readable_mail($the_addrs[$a]) . "</span>";
            $repaddr[$a] .= emob_addJScript($the_addrs[$a], $r);
        }
    
    $cc = str_replace($the_addrs, $repaddr, $cont);
    return $cc;
}

add_filter('the_content', 'emob_replace');
add_filter('the_excerpt', 'emob_replace');
add_filter('comment_text', 'emob_replace');
add_filter('author_email', 'emob_replace');
add_filter('comment_email', 'emob_replace');

?>