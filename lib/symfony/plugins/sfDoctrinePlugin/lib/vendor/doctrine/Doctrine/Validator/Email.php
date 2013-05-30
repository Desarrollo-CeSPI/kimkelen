<?php
/*
 *  $Id: Email.php 5801 2009-06-02 17:30:27Z piccoloprincipe $
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the LGPL. For more information, see
 * <http://www.phpdoctrine.org>.
 */

/**
 * Doctrine_Validator_Email
 *
 * @package     Doctrine
 * @subpackage  Validator
 * @license     http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @link        www.phpdoctrine.org
 * @since       1.0
 * @version     $Revision: 5801 $
 * @author      Konsta Vesterinen <kvesteri@cc.hut.fi>
 */
class Doctrine_Validator_Email
{
    /**
     * checks if given value is a valid email address
     *
     * @link http://iamcal.com/publish/articles/php/parsing_email/pdf/
     * @param mixed $value
     * @return boolean
     */
    public function validate($value)
    {
        if (is_null($value)) {
            return true;
        }
        if (isset($this->args)) {
            $parts = explode('@', $value);
        
            if (isset($parts[1]) && $parts[1] && function_exists('checkdnsrr')) {
                if ( ! checkdnsrr($parts[1], 'MX')) {
                    return false;
                }
            }
        }

        $e = explode('.', $value);
        $tld = end($e);
        if (preg_match("/[^a-zA-Z]/", $tld)) {
            return false;
        }

        $qtext = '[^\\x0d\\x22\\x5c\\x80-\\xff]';
        $dtext = '[^\\x0d\\x5b-\\x5d\\x80-\\xff]';
        $atom = '[^\\x00-\\x20\\x22\\x28\\x29\\x2c\\x2e\\x3a-\\x3c\\x3e\\x40\\x5b-\\x5d\\x7f-\\xff]+';
        $quotedPair = '\\x5c[\\x00-\\x7f]';
        $domainLiteral = "\\x5b($dtext|$quotedPair)*\\x5d";
        $quotedString = "\\x22($qtext|$quotedPair)*\\x22";
        $domainRef = $atom;
        $subDomain = "($domainRef|$domainLiteral)";
        $word = "($atom|$quotedString)";
        $domain = "$subDomain(\\x2e$subDomain)+";
        /*
          following pseudocode to allow strict checking - ask pookey about this if you're puzzled

          if ($this->getValidationOption('strict_checking') == true) {
              $domain = "$sub_domain(\\x2e$sub_domain)*";
          }
        */
        $localPart = "$word(\\x2e$word)*";
        $addrSpec = "$localPart\\x40$domain";

        return (bool) preg_match("!^$addrSpec$!D", $value);
    }
}