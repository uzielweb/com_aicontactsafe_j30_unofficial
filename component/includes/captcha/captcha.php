<?php
/**
 * Script para la generación de CAPTCHAS
 *
 * @author  Jose Rodriguez <jose.rodriguez@exec.cl>
 * @license GPLv3
 * @link    http://code.google.com/p/cool-php-captcha
 * @package captcha
 *
 *
 * Modified for use with joomla
 * @author Alex Dobrin <alex@algis.ro>
 *
 */

/**
 * @version     $Id$ 2.0.15 0
 * @package     Joomla
 * @copyright   Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
 * @license     GNU/GPL, see LICENSE.php
 *
 * added/fixed in version 2.0.12
 * - fixed the problem with making the letters to small
 * added/fixed in version 2.0.15
 * - fixed the problem with generating only one letter in the CAPTCHA code
 *
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

/**
 * SimpleCaptcha class
 *
 */
class SimpleCaptcha {

    /** Width of the image */
    var $width  = 200;

    /** Height of the image */
    var $height = 70;

    /** Dictionary word file (empty for randnom text) */
    var $wordsFile = null;

    /** Min word length (for non-dictionary random text generation) */
    var $minWordLength = 5;

    /**
     * Max word length (for non-dictionary random text generation)
     * 
     * Used for dictionary words indicating the word-length
     * for font-size modification purposes
     */
    var $maxWordLength = 8;

    /** Sessionname to store the original text */
    var $session_var = 'captcha';

    /** Background color in RGB-array */
    var $backgroundColor = array(255, 255, 255);
    /** Background transparent */
    var $backgroundTransparent = false;

    /** Foreground colors in RGB-array */
    var $colors = array(
        array(27,78,181), // blue
        array(22,163,35), // green
        array(214,36,7),  // red
    );

    /** Shadow color in RGB-array or false */
    var $shadowColor = false; //array(0, 0, 0);

    /**
     * Font configuration
     *
     * - font: TTF file
     * - spacing: relative pixel space between character
     * - minSize: min font size
     * - maxSize: max font size
     */
    var $fonts = null;

    /** Wave configuracion in X and Y axes */
    var $Yperiod    = 12;
    var $Yamplitude = 14;
    var $Xperiod    = 11;
    var $Xamplitude = 5;

    /** letter rotation clockwise */
    var $maxRotation = 8;

    /**
     * Internal image size factor (for better image quality)
     * 1: low, 2: medium, 3: high
     */
    var $scale = 3;

    /** 
     * Blur effect for better image quality (but slower image processing).
     * Better image results with scale=3
     */
    var $blur = false;

    /** Debug? */
    var $debug = false;
    
    /** Image format: jpeg or png */
    var $imageFormat = 'png';

    /** align CAPTCHA code 0 - none, 1 - left, 2 - center, 3 - right */
    var $align_captcha = 0;

    /** GD image */
    var $im;

    function __construct($config = array()) {

		$folder_captcha_class = JPATH_SITE.DS.'components'.DS.'com_aicontactsafe'.DS.'includes'.DS.'captcha';

		if( (int)$config['width'] > 0 ) {
			$this->width = (int)$config['width'];
		}
		if( (int)$config['height'] > 0 ) {
			$this->height = (int)$config['height'];
		}

		if ( $config['use_random_letters'] == 1 ) {
			$this->wordsFile = null;
		} else {
			if (is_null($this->wordsFile)) {
				$language_file = $folder_captcha_class.DS.'words'.DS.'en.txt';
				if ( strlen($config['lang']) > 0 && is_file($folder_captcha_class.DS.'words'.DS.$config['lang'].'.txt') ) {
					$language_file = $folder_captcha_class.DS.'words'.DS.$config['lang'].'.txt';
				}
				$this->wordsFile = $language_file;
			}
		}

		if( (int)$config['minWordLength'] > 0 ) {
			$this->minWordLength = (int)$config['minWordLength'];
		}
		if( (int)$config['maxWordLength'] > 0 ) {
			$this->maxWordLength = (int)$config['maxWordLength'];
		}

		if( strlen($config['session_var']) > 0 ) {
			$this->session_var = $config['session_var'];
		}

		if( is_array($config['backgroundColor']) ) {
			$this->backgroundColor = $config['backgroundColor'];
		}
		if( isset($config['backgroundTransparent']) ) {
			$this->backgroundTransparent = $config['backgroundTransparent'];
		}
		if( is_array($config['colors']) ) {
			$this->colors = $config['colors'];
		}

		if (is_null($this->fonts)) {
			$this->fonts = array(
				'Antykwa'  => array('spacing' => -3, 'minSize' => 27, 'maxSize' => 30, 'font' => $folder_captcha_class.DS.'fonts'.DS.'AntykwaBold.ttf'),
				'Candice'  => array('spacing' =>-1.5,'minSize' => 28, 'maxSize' => 31, 'font' => $folder_captcha_class.DS.'fonts'.DS.'Candice.ttf'),
				'DingDong' => array('spacing' => -2, 'minSize' => 24, 'maxSize' => 30, 'font' => $folder_captcha_class.DS.'fonts'.DS.'Ding-DongDaddyO.ttf'),
				'Duality'  => array('spacing' => -2, 'minSize' => 30, 'maxSize' => 38, 'font' => $folder_captcha_class.DS.'fonts'.DS.'Duality.ttf'),
				'Jura'     => array('spacing' => -2, 'minSize' => 28, 'maxSize' => 32, 'font' => $folder_captcha_class.DS.'fonts'.DS.'Jura.ttf'),
				'Times'    => array('spacing' => -2, 'minSize' => 28, 'maxSize' => 34, 'font' => $folder_captcha_class.DS.'fonts'.DS.'TimesNewRomanBold.ttf'),
				'VeraSans' => array('spacing' => -1, 'minSize' => 20, 'maxSize' => 28, 'font' => $folder_captcha_class.DS.'fonts'.DS.'VeraSansBold.ttf')
			);
		}
		if( (int)$config['align_captcha'] > 0 ) {
			$this->align_captcha = (int)$config['align_captcha'];
		}
    }

	function SimpleCaptcha($config = array()) {
		$this->__construct($config);
	}

    function CreateImage() {
        $ini = microtime(true);

        /** Initialization */
        $this->ImageAllocate();
        
        /** Text insertion */
        $text = $this->GetCaptchaText();
        $fontcfg  = $this->fonts[array_rand($this->fonts)];
        $this->WriteText($text, $fontcfg);

		$session = JFactory::getSession();
		$session->set( $this->session_var , $text );

        /** Transformations */
        $this->WaveImage();
        if ($this->blur) {
            imagefilter($this->im, IMG_FILTER_GAUSSIAN_BLUR);
        }
        $this->ReduceImage();


        if ($this->debug) {
            imagestring($this->im, 1, 1, $this->height-8,
                "$text {$fontcfg['font']} ".round((microtime(true)-$ini)*1000)."ms",
                $this->GdFgColor
            );
        }

        /** Set the background transparent if it is set that way */
        $this->TransparentBackground();

        /** Output */
        $this->WriteImage();
        $this->Cleanup();
    }

    /**
     * Creates the image resources
     */
    function ImageAllocate() {
        // Cleanup
        if (!empty($this->im)) {
            imagedestroy($this->im);
        }

        $this->im = imagecreatetruecolor($this->width*$this->scale, $this->height*$this->scale);

        // Background color
        $this->GdBgColor = imagecolorallocate($this->im,
            $this->backgroundColor[0],
            $this->backgroundColor[1],
            $this->backgroundColor[2]
        );
        imagefilledrectangle($this->im, 0, 0, $this->width*$this->scale, $this->height*$this->scale, $this->GdBgColor);

        // Foreground color
        $color           = $this->colors[mt_rand(0, sizeof($this->colors)-1)];
        $this->GdFgColor = imagecolorallocate($this->im, $color[0], $color[1], $color[2]);

        // Shadow color
        if (!empty($this->shadowColor)) {
            $this->GdShadowColor = imagecolorallocate($this->im,
                $this->shadowColor[0],
                $this->shadowColor[1],
                $this->shadowColor[2]
            );
        }
    }

    /**
     * Text generation
     *
     * @return string Text
     */
    function GetCaptchaText() {
        $text = $this->GetDictionaryCaptchaText();
        if (!$text) {
            $text = $this->GetRandomCaptchaText();
        }
        return $text;
    }

    /**
     * Random text generation
     *
     * @return string Text
     */
    function GetRandomCaptchaText($length = null) {
        if (empty($length)) {
            $length = rand($this->minWordLength, $this->maxWordLength);
        }

        $words  = "abcdefghijlmnopqrstvwyz";
        $vocals = "aeiou";

        $text  = "";
        $vocal = rand(0, 1);
        for ($i=0; $i<$length; $i++) {
            if ($vocal) {
                $text .= substr($vocals, mt_rand(0, 4), 1);
            } else {
                $text .= substr($words, mt_rand(0, 22), 1);
            }
            $vocal = !$vocal;
        }
        return $text;
    }

    /**
     * Random dictionary word generation
     *
     * @param boolean $extended Add extended "fake" words
     * @return string Word
     */
    function GetDictionaryCaptchaText($extended = false) {
        if (empty($this->wordsFile)) {
            return false;
        }
		// open the words file
        $fp     = fopen($this->wordsFile, "r");
		// get the length of the words file
        $length = filesize($this->wordsFile);
        if (!$length) {
            return false;
        }
		// move the pointer to a random position
        $line   = rand(0,$length-1);
        if (fseek($fp, $line) == -1) {
            return false;
        }
		// move the pointer to the ned of the current line
        $text = fgets($fp);
		// if the end of the files is reached move the pointer to the first line
		if (feof($fp)) {
			rewind($fp);
		}
		// read the text to use as the captcha code
        $text = fgets($fp);
		// remove the spaces
		$text = trim($text);
        fclose($fp);


        /** Change ramdom volcals */
        if ($extended) {
            $text   = str_split($text, 1);
            $vocals = array('a', 'e', 'i', 'o', 'u');
            foreach ($text as $i => $char) {
                if (mt_rand(0, 1) && in_array($char, $vocals)) {
                    $text[$i] = $vocals[mt_rand(0, 4)];
                }
            }
            $text = implode('', $text);
        }

        return $text;
    }

    /**
     * Text insertion
     */
    function WriteText($text, $fontcfg = array()) {
        if (empty($fontcfg)) {
            // Select the font configuration
            $fontcfg  = $this->fonts[array_rand($this->fonts)];
        }
        $fontfile = $fontcfg['font'];

        /** Increase font-size for shortest words: 9% for each glyp missing */
        $lettersMissing = $this->maxWordLength-strlen($text);
        $fontSizefactor = 1+($lettersMissing*0.09);

        // Text generation (char by char)
		// get the image box
        $length = strlen($text);

		// run imagettfbbox once because of a bug in GD ( or PHP ) that makes it return wrong coordinates
		$current_letter = array();
		$current_letter['letter'] = substr($text, 0, 1);
		$current_letter['degree'] = rand($this->maxRotation*-1, $this->maxRotation);
		$current_letter['fontsize'] = rand($fontcfg['minSize'], $fontcfg['maxSize'])*$this->scale*$fontSizefactor;
		$current_letter['fontfile'] = $fontfile;
		$letter_box = imagettfbbox($current_letter['fontsize'], $current_letter['degree'], $current_letter['fontfile'], $current_letter['letter']);

		// letters to write
		$letters = array();
        for ($i=0; $i<$length; $i++) {
			$current_letter = array();
			$current_letter['letter'] = substr($text, $i, 1);
            $current_letter['degree'] = rand($this->maxRotation*-1, $this->maxRotation);
            $current_letter['fontsize'] = rand($fontcfg['minSize'], $fontcfg['maxSize'])*$this->scale*$fontSizefactor;
            $current_letter['fontfile'] = $fontfile;
			$letter_box = imagettfbbox($current_letter['fontsize'], $current_letter['degree'], $current_letter['fontfile'], $current_letter['letter']);
			$current_letter['width']  = max($letter_box[2] - $letter_box[0], $letter_box[4] - $letter_box[6]);
			$current_letter['height'] = max($letter_box[1] - $letter_box[7], $letter_box[3] - $letter_box[5]);
			$current_letter['top']  = min($letter_box[5], $letter_box[7]);
			$current_letter['left']  = min($letter_box[0], $letter_box[7]);
			$letters[] = $current_letter;
		}
		switch( $this->align_captcha ) {
			case 0:
				// not aligned
				$x = 20*$this->scale;
				$y = round(($this->height*27/40)*$this->scale);
				break;
			case 1:
				// left aligned
				$letters = $this->checkFontSize( $letters );
				$dimm = $this->getCodeDimmensions( $letters );
				$x = ($letters[0]['left']<0?$letters[0]['left']*-1:0);
				$y = $this->height*$this->scale - floor(($this->height*$this->scale-$dimm['height'])/2);
				break;
			case 2:
				// center aligned
				$letters = $this->checkFontSize( $letters );
				$dimm = $this->getCodeDimmensions( $letters );
				$x = floor(($this->width*$this->scale-$dimm['width'])/2);
				$y = $this->height*$this->scale - floor(($this->height*$this->scale-$dimm['height'])/2);
				break;
			case 3:
				// right aligned
				$letters = $this->checkFontSize( $letters );
				$dimm = $this->getCodeDimmensions( $letters );
				$x = floor($this->width*$this->scale-$dimm['width']);
				$y = $this->height*$this->scale - floor(($this->height*$this->scale-$dimm['height'])/2);
				break;
		}

		foreach($letters as $letter) {
			if ($this->shadowColor) {
				imagettftext($this->im, $letter['fontsize'], $letter['degree'], $x+$this->scale, $y+$this->scale, $this->GdShadowColor, $letter['fontfile'], $letter['letter']);
			}
            imagettftext($this->im, $letter['fontsize'], $letter['degree'], $x, $y, $this->GdFgColor, $letter['fontfile'], $letter['letter']);
            $x += $letter['width']+1;
		}
    }

	function getCodeDimmensions( $letters = array() ) {
		$dimm = array();
		$dimm['width'] = $letters[0]['left']<0?$letters[0]['left']*-1:0;
		$dimm['height'] = 0;
		foreach($letters as $letter) {
			$dimm['width'] += $letter['width'] + 1;
			$dimm['height'] = max($dimm['height'],$letter['height']);
		}
		return $dimm;
	}

	function checkFontSize( $letters = array() ) {
		$dimm = $this->getCodeDimmensions( $letters );

		if ($dimm['width'] <= $this->width*$this->scale && $dimm['height'] <= $this->height*$this->scale) {
			// found the correct dimensions
		} else {
			$letters = $this->changeFontSize($letters,-1);
			$letters = $this->checkFontSize($letters);
		}
		return $letters;
	}

	function changeFontSize( $letters = array(), $val = 0 ) {
		foreach($letters as $key=>$letter) {
			$letters[$key]['fontsize'] = $letters[$key]['fontsize'] + $val;
			$letter_box = imagettfbbox($letters[$key]['fontsize'], $letters[$key]['degree'], $letters[$key]['fontfile'], $letters[$key]['letter']);
			$letters[$key]['width']  = max($letter_box[2] - $letter_box[0], $letter_box[4] - $letter_box[6]);
			$letters[$key]['height'] = max($letter_box[1] - $letter_box[7], $letter_box[3] - $letter_box[5]);
		}
		return $letters;
	}

    /**
     * Wave filter
     */
    function WaveImage() {
        // X-axis wave generation
        $xp = $this->scale*$this->Xperiod*rand(1,3);
        $k = rand(0, 100);
        for ($i = 0; $i < ($this->width*$this->scale); $i++) {
            imagecopy($this->im, $this->im,
                $i-1, sin($k+$i/$xp) * ($this->scale*$this->Xamplitude),
                $i, 0, 1, $this->height*$this->scale);
        }

        // Y-axis wave generation
        $k = rand(0, 100);
        $yp = $this->scale*$this->Yperiod*rand(1,2);
        for ($i = 0; $i < ($this->height*$this->scale); $i++) {
            imagecopy($this->im, $this->im,
                sin($k+$i/$yp) * ($this->scale*$this->Yamplitude), $i-1,
                0, $i, $this->width*$this->scale, 1);
        }
    }

    /**
     * Reduce the image to the final size
     */
    function ReduceImage() {
        // Reduzco el tamaño de la imagen
        $imResampled = imagecreatetruecolor($this->width, $this->height);
        imagecopyresampled($imResampled, $this->im,
            0, 0, 0, 0,
            $this->width, $this->height,
            $this->width*$this->scale, $this->height*$this->scale
        );
        imagedestroy($this->im);
        $this->im = $imResampled;
    }

    /**
     * Set the background transparent if it is set that way
     */
    function TransparentBackground() {
		if ($this->backgroundTransparent) {
			imagecolortransparent($this->im, $this->GdBgColor);
		}
	}

    /**
     * File generation
     */
    function WriteImage() {
        if ($this->imageFormat == 'png') {
            header("Content-type: image/png");
            imagepng($this->im);
        } else {
            header("Content-type: image/jpeg");
            imagejpeg($this->im, null, 80);
        }
    }

    /**
     * Cleanup
     */
    function Cleanup() {
        imagedestroy($this->im);
    }
}
