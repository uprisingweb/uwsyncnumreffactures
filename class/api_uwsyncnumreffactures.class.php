<?php

use Luracast\Restler\RestException;

require_once DOL_DOCUMENT_ROOT . "/custom/uwsyncnumreffactures/core/modules/facture/mod_facture_synchrone.php";
require_once DOL_DOCUMENT_ROOT . "/custom/uwsyncnumreffactures/class/uwapiutils.class.php";
require_once DOL_DOCUMENT_ROOT . "/societe/class/societe.class.php";
require_once DOL_DOCUMENT_ROOT . "/compta/facture/class/facture.class.php";

require_once DOL_DOCUMENT_ROOT . "/api/class/api.class.php";//A ENLEVER

/**
 * API class for uwsyncnumreffactures myobject
 *
 * @smart-auto-routing false
 * @access protected
 * @class  DolibarrApiAccess {@requires user,external}
 */
class UwSyncNumRefFactures extends DolibarrApi
{

    public function  __construct()
    {
        global $db;
        $this->db = $db;

        
        
    }

    /**
     *
     * @url GET    nextnumreffacture
     *
     * @return string
     *
     * @throws RestException
     *
     */
    public function nextnumreffacture()
    {
        global $conf;

        uwApiUtils::logfile("Local API - mode " . $_GET['mode'] . " - type " . $_GET['type']);

        if(!$_GET['mode'])
            $_GET['mode'] = 'next';
        if(!$_GET['type'])
            $_GET['type'] = 0;

                    
        $facture = new Facture($this->db);
        $facture->initAsSpecimen();
        $facture->type=$_GET['type'];

        $societe = new societe($this->db);
        $societe->setMysoc($conf);

        $module = new mod_facture_synchrone($this->db);
        
        $res = $module->getNextValue($societe, $facture, $_GET['mode'], 'distant');

        uwApiUtils::logfile("Local API - send value : " . $res );
        return $res;

    } 
    
}
