<?php
/**
 * Plugin xkcd: display xkcs comic
 *
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author     Carlo Perassi <carlo@perassi.org>
 * @author     Zahno Silvan <zaswiki@gmail.com>
 */

// based on http://wiki.splitbrain.org/plugin:tutorial

// must be run within Dokuwiki
if (!defined('DOKU_INC')) die();

if (!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN', DOKU_INC . 'lib/plugins/');
require_once(DOKU_PLUGIN . 'syntax.php');

/**
 * All DokuWiki plugins to extend the parser/rendering mechanism
 * need to inherit from this class
 */
class syntax_plugin_xkcd extends DokuWiki_Syntax_Plugin {
    function getInfo() {
        return array(
        'author'  => 'Zahno Silvan, Carlo Perassi',
        'email'   => 'zaswiki@gmail.com',
        'date'    => '2013-04-16',
        'name'    => 'xkcd Plugin',
        'desc'    => 'It displays the xkcd treee times a week. Using RSS feed',
        'url'     => 'https://xkcd.com/rss.xml'
        );
    }

    private function _listhd() {
        require_once(DOKU_INC . 'inc/HTTPClient.php');

        $url = 'https://xkcd.com/rss.xml';
        $ch = new DokuHTTPClient();
        $piece = $ch->get($url);
        $xml = simplexml_load_string($piece);
        
        $comicURL = $xml->channel->item->link;

        $description = (string) $xml->channel->item->description;
        $description = html_entity_decode($description, ENT_NOQUOTES);
        $feed_contents = $description;
        // Not used anymore because of new xml format
        //$dom = new DOMDocument();
        //$dom->loadXML($description);
        //$imgSrc = $dom->childNodes->item(0)->attributes->getNamedItem('src' )->value;
        //$imgTitle = $dom->childNodes->item(0)->attributes->getNamedItem('title' )->value;
        //$feed_contents = "<a href=\"$comicURL\"><img src=\"$imgSrc\" title=\"$imgTitle\" /></a>\n";
  
        return $feed_contents;
    }


    function connectTo($mode) {
        $this->Lexer->addSpecialPattern('\[xkcd\]', $mode, 'plugin_xkcd');
    }

    function getType() { return 'substition'; }

    function getSort() { return 667; }

    function handle($match, $state, $pos, &$handler) {
        return array($match, $state, $pos);
    }

    function render($mode, &$renderer, $data) {

        if ($mode == 'xhtml') {
            $renderer->doc .= $this->_listhd();
            return true;
        }
        return false;
    }
}
