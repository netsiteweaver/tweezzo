<?php
/**
 * Copied from https://stackoverflow.com/questions/12228644/how-to-detect-light-colors-with-php
 * @author https://stackoverflow.com/users/994054/peter
 * 
 * Color difference is determined by the following formula: 
 * (maximum (Red value 1, Red value 2) - minimum (Red value 1, Red value 2)) + 
 * (maximum (Green value 1, Green value 2) - 
 * minimum (Green value 1, Green value 2)) + 
 * (maximum (Blue value 1, Blue value 2) - 
 * minimum (Blue value 1, Blue value 2)) 
 * 
 */
function HTMLToRGB( $htmlCode ) {

    /*$formula = (maximum (Red value 1, Red value 2) - minimum (Red value 1, Red value 2)) + 
                (maximum (Green value 1, Green value 2) - minimum (Green value 1, Green value 2)) + 
                (maximum (Blue value 1, Blue value 2) - minimum (Blue value 1, Blue value 2)) */
    if ( $htmlCode[ 0 ] == '#' )
    $htmlCode = substr( $htmlCode, 1 );

    if ( strlen( $htmlCode ) == 3 ) {
        $htmlCode = $htmlCode[ 0 ] . $htmlCode[ 0 ] . $htmlCode[ 1 ] . $htmlCode[ 1 ] . $htmlCode[ 2 ] . $htmlCode[ 2 ];
    }

    $r = hexdec( $htmlCode[ 0 ] . $htmlCode[ 1 ] );
    $g = hexdec( $htmlCode[ 2 ] . $htmlCode[ 3 ] );
    $b = hexdec( $htmlCode[ 4 ] . $htmlCode[ 5 ] );

    return $b + ( $g << 0x8 ) + ( $r << 0x10 );
}

function RGBToHSL( $RGB ) {
    $r = 0xFF & ( $RGB >> 0x10 );
    $g = 0xFF & ( $RGB >> 0x8 );
    $b = 0xFF & $RGB;

    $r = ( ( float )$r ) / 255.0;
    $g = ( ( float )$g ) / 255.0;
    $b = ( ( float )$b ) / 255.0;

    $maxC = max( $r, $g, $b );
    $minC = min( $r, $g, $b );

    $l = ( $maxC + $minC ) / 2.0;

    if ( $maxC == $minC ) {
        $s = 0;
        $h = 0;
    } else {
        if ( $l < .5 ) {
            $s = ( $maxC - $minC ) / ( $maxC + $minC );
        } else {
            $s = ( $maxC - $minC ) / ( 2.0 - $maxC - $minC );
        }
        if ( $r == $maxC )
        $h = ( $g - $b ) / ( $maxC - $minC );
        if ( $g == $maxC )
        $h = 2.0 + ( $b - $r ) / ( $maxC - $minC );
        if ( $b == $maxC )
        $h = 4.0 + ( $r - $g ) / ( $maxC - $minC );

        $h = $h / 6.0;

    }

    $h = ( int )round( 255.0 * $h );
    $s = ( int )round( 255.0 * $s );
    $l = ( int )round( 255.0 * $l );

    return ( object ) Array( 'hue' => $h, 'saturation' => $s, 'lightness' => $l );
}

function isLight( $color , $lightnessTreshold = "") {
    if(empty($lightnessTreshold)) $lightnessTreshold = 220;
    $rgb = HTMLToRGB($color);
    $hsl = RGBToHSL($rgb);
    echo $hsl->lightness;
    if($hsl->lightness > $lightnessTreshold) return true;
    return false;
}

function isDark( $color , $darknessTreshold = "") {
    if(empty($darknessTreshold)) $darknessTreshold = 150;
    $rgb = HTMLToRGB($color);
    $hsl = RGBToHSL($rgb);
    echo "[".$hsl->lightness."]";
    if($hsl->lightness < $darknessTreshold) return true;
    return false;
}