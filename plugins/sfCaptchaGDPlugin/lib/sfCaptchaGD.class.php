<?php
/**
 * sfCaptchaGD class.
 *
 * @package    sfCaptchaGD
 * @subpackage sfCaptchaGD
 * @author     Alex Kubyshkin <glint@techinfo.net.ru>
 * @version    1.0.3
 */
class sfCaptchaGD 
{
 
  public  $securityCode;
  private $border_color;
  private $background_color;
  private $fonts;
  private $fonts_dir;
  private $fontSize;
  private $fontColor;
  private $chars;
  private $codeLength;
  private $image_width;
  private $image_height;

  function __construct() {
      $this->border_color = sfConfig::get('app_sf_captchagd_border_color', "000000");
      $this->background_color = sfConfig::get('app_sf_captchagd_background_color', "DDDDDD");
      $this->fonts = sfConfig::get('app_sf_captchagd_fonts', array("akbar/akbar.ttf", "brushcut/BRUSHCUT.TTF", "molten/molten.ttf", "planet_benson/Planetbe.ttf", "whoobub/WHOOBUB_.TTF"));
      $this->fonts_dir = sfConfig::get('app_sf_captchagd_fonts_dir', sfConfig::get('sf_plugins_dir').'/sfCaptchaGDPlugin/data/fonts/');
      $this->fontSize = sfConfig::get('app_sf_captchagd_font_size', "16");
      $this->fontColor = sfConfig::get('app_sf_captchagd_font_color', array("252525", "8b8787", "550707", "3526E6", "88531E"));
      $this->chars = sfConfig::get('app_sf_captchagd_chars', "123456789");
      $this->codeLength = sfConfig::get('app_sf_captchagd_length', 4);
      $this->image_width = sfConfig::get('app_sf_captchagd_image_width', 100);
      $this->image_height = sfConfig::get('app_sf_captchagd_image_height', 30);
  }
    
  
  public function simpleRandString($length, $list) {
    /*
     * Generate random string
     * 
    */
    mt_srand((double)microtime()*1000000);
 
    $newstring = "";
 
    if ($length > 0) {
        while (strlen($newstring) < $length) {
            $newstring .= $list[mt_rand(0, strlen($list)-1)];
        }
    }
    return $newstring;
  }
 
  private function allocateColor($img, $color = ""){
    return imagecolorallocate($img,
                hexdec(substr($color, 0, 2)),
                hexdec(substr($color, 2, 2)), 
                hexdec(substr($color, 4, 2))
                );
  }
  
  public function generateImage($securityCode = NULL)
  {
    $context = sfContext::getInstance();
    $l = $context->getLogger();
    if ($context->getRequest()->getGetParameter('reload') || ! $securityCode || sfConfig::get('app_sf_captchagd_force_new_captcha', false)){
      $this->securityCode = $this->simpleRandString($this->codeLength, $this->chars);
    } else {
      $this->securityCode = $securityCode;
    }
    $context->getUser()->setAttribute('captcha', $this->securityCode);
 
    $this->img = imagecreatetruecolor($this->image_width, $this->image_height);
    $bc_color = $this->allocateColor($this->img, $this->background_color);
    $border_color = $this->allocateColor($this->img, $this->border_color);
    imagefill($this->img, 0, 0, $bc_color);
    imagerectangle($this->img, 0, 0, $this->image_width - 1, $this->image_height - 1, $border_color);
 
    foreach($this->fontColor as $fcolor)
    {
        $color[] = $this->allocateColor($this->img, $fcolor);
    }
 
    $fw = imagefontwidth($this->fontSize) + $this->image_width / 30;
    $fh = imagefontheight($this->fontSize);
 
    // create a new string with a blank space between each letter so it looks better
    $newstr = "";
    for ($i = 0; $i < strlen($this->securityCode); $i++) {
        $newstr .= $this->securityCode[$i] ." ";
    }
 
    // remove the trailing blank
    $newstr = trim($newstr);
 
    // center the string 
    $x = ($this->image_width * 0.95 - strlen($newstr) * $fw ) / 2;
 
    // create random lines over text
    $stripe_size_max = $this->image_height / 3;
    for($i = 0; $i < 15; $i++){
        $x2 = rand(0, $this->image_width);
        $y2 = rand(0, $this->image_height);
        imageline($this->img, $x2, $y2, $x2 + rand(-$stripe_size_max, $stripe_size_max), $y2 + rand(-$stripe_size_max, $stripe_size_max), $color[rand(0, count($color) - 1)]);
    }
    
    // output each character at a random height and standard horizontal spacing
    for ($i = 0; $i < strlen($newstr); $i++) {
        $hz = $fh + ($this->image_height - $fh) / 2 + mt_rand(-$this->image_height / 10, $this->image_height / 10);
 
        // randomize rotation
        $rotate = rand(-25, 25);
 
        // randomize font size
        $newFontSize = $this->fontSize + $this->fontSize * (rand(0, 3) / 10);
 
        $a = imagettftext($this->img, $newFontSize, $rotate, $x + ($fw * $i), $hz, $color[rand(0, count($color) - 1)], $this->fonts_dir.$this->fonts[rand(0, count($this->fonts) - 1)], $newstr[$i]);
    }
    $context->getResponse()->setContentType('image/gif');
    imagegif($this->img);
    imagedestroy($this->img);
  }
}
?>
