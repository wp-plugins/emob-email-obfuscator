<?php
/*
Plugin Name: Email Obfuscator
Plugin URI: http://paxoo.com/wp-emob
Description: Based on <a href="http://macromates.com/blog/2007/obfuscating-emails-revisited/">Allan Odgaard's Ruby script</a> for TextMate, this plugin will automatically make any detected email address within posts and comments harder for spam harvesters to detect.
Version: 0.2
License: GPL
Author: Billy Halsey
Author URI: http://paxoo.com
*/

// =========================================================================
// = Hide your email address!                                              =
// =                                                                       =
// = Planned features:                                                     =
// =  - Auto address linking                                               =
// =  - Customizable friendly text output                                  =
// =========================================================================

function emob_replace($content)
{
    $addr_pattern =
        '/([A-Z0-9._%+-]+)@([A-Z0-9.-]+)\.([A-Z]{2,4})/i';
    $addr_readable =
        '$1 [at] $2 -dot- $3';
        
    // Find all email addresses in the given $content
    $addresses = array();
    preg_match_all($addr_pattern, $content, $addresses);
    /*
        A note on the $addresses array:
        * $0 = full address
        * $1 = username portion
        * $2 = portion before the top-level domain
        * $3 = top-level domain
        * 
        * Example:   joe.shmoe@example.org
        * $0 = joe.shmoe@example.org
        * $1 = joe.shmoe    (! -- NOTE the period doesn't matter)
        * $2 = example
        * $3 = org
        * 
        * Also, $addresses is a 2-dimensional array. So if, for example,
        * $contents contains two addresses, joe@schmoe.org, and 
        * george@curious.com, then the contents will be as follows:
        * 
        * $addresses[0][0] = 'joe@schmoe.org'
        * $addresses[0][1] = 'george@curious.com'
        * $addresses[1][0] = 'joe'
        * $addresses[1][1] = 'george'
        * $addresses[2][0] = 'schmoe'
        * $addresses[2][1] = 'curious'
        * $addresses[3][0] = 'org'
        * $addresses[3][1] = 'com'
    */
    
    $the_addrs = $addresses[0];
    // Convert the addresses to human-readable form
    preg_replace($addr_pattern, $addr_readable, $the_addrs);
    
    // construct unique DOM-addressable objects for each address
    $emob_crypt = array();
    for ($i=0; $i < count($the_addrs); $i++) { 
        // Get the ROT-13 of the address
        $r13 = str_rot13($the_addrs[$i]);
        $readable = preg_replace($addr_pattern, $addr_readable,
            $the_addrs[$i]);
        $emob_crypt[$i] = <<<EMOBC
<abbr id="emob-$r13" title="$readable">$r13<span class="emob-info">&#xa0;(mouse over for correct address)</span></abbr>
<script type="text/javascript">
    var abbr_id = document.getElementById("emob-$r13");
    var eaddr = abbr_id.firstChild.data.replace(/\ \[at\]\ /g, '@');
    eaddr = eaddr.replace(/\ -dot-\ /, '.');
    var newAddr = eaddr.replace(/[a-zA-Z]/g, function(c){return String.fromCharCode((c<="Z"?90:122)>=(c=c.charCodeAt(0)+13)?c:c-26);});
    var orig = abbr_id.firstChild;
    // Delete the inside span; we don't need it and it's confusing
    var expl = abbr_id.getElementsByTagName("span");
    for (var i = expl.length - 1; i >= 0; i--){
        abbr_id.removeChild(expl[i]);
    };
    var txtn = document.createTextNode(newAddr);
    abbr_id.insertBefore(txtn, orig);
    abbr_id.removeChild(orig);
</script>
EMOBC;
    }
    $cont = str_replace($the_addrs, $emob_crypt, $content);
    return $cont;
}

add_filter('the_content', 'emob_replace');
add_filter('the_excerpt', 'emob_replace');
add_filter('comment_text', 'emob_replace');
add_filter('author_email', 'emob_replace');
add_filter('comment_email', 'emob_replace');
