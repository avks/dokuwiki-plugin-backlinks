<?php
/**
 * Syntax Plugin Backlinks
 * 
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author     Michael Klier <chi@chimeric.de>
 */

if(!defined('DOKU_INC')) define('DOKU_INC',realpath(dirname(__FILE__).'/../../').'/');
if(!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN',DOKU_INC.'lib/plugins/');
if(!defined('DW_LF')) define('DW_LF',"\n");

require_once(DOKU_PLUGIN.'syntax.php');

/**
 * All DokuWiki plugins to extend the parser/rendering mechanism
 * need to inherit from this class
 */
class syntax_plugin_backlinks extends DokuWiki_Syntax_Plugin {


    /**
     * General Info
     */
    function getInfo(){
        return array(
            'author' => 'Michael Klier',
            'email'  => 'chi@chimeric.de',
            'date'   => '2006-10-12',
            'name'   => 'Backlinks',
            'desc'   => 'Displays backlinks to a given page.',
            'url'    => 'http://www.chimeric.de/dokuwiki/plugins/backlinks'
        );
    }

    /**
     * Syntax Type
     *
     * Needs to return one of the mode types defined in $PARSER_MODES in parser.php
     */
    function getType()  { return 'substition'; }
    function getPType() { return 'block'; }
    function getSort()  { return 304; }
    
    /**
     * Connect pattern to lexer
     */
    function connectTo($mode) {
        $this->Lexer->addSpecialPattern('\{\{backlinks>.+?\}\}',$mode,'plugin_backlinks');
    }

    /**
     * Handler to prepare matched data for the rendering process
     */
    function handle($match, $state, $pos, &$handler){
        global $ID;

        @require_once(DOKU_INC.'inc/fulltext.php');

        $id = substr($match,12,-2); //strip {{backlinks> from start and }} from end

        if($id == '.') $id = $ID;

        resolve_pageid(getNS($ID),$id,$exits);

        $backlinks = ft_backlinks($id);

        return ($backlinks);
    }

    /**
     * Handles the actual output creation.
     */
    function render($mode, &$renderer, $backlinks) {

        if($mode == 'xhtml'){
            $renderer->info['cache'] = false;

            if(!empty($backlinks)) {

                $renderer->doc .= '<div id="plugin__backlinks">' . DW_LF;
                $renderer->doc .= '<ul class="idx">';

                foreach($backlinks as $backlink){
                    $renderer->doc .= '<li><div class="li">';
                    $renderer->doc .= html_wikilink(':'.$backlink,$conf['useheading']?NULL:preg_replace("/.*?:/",'',$backlink));
                    $renderer->doc .= '</div></li>';
                }

                $renderer->doc .= '</ul>';
                $renderer->doc .= '</div>' . DW_LF;
            }

            return true;
        }
        return false;
    }
}
//Setup VIM: ex: et ts=4 enc=utf-8 :
