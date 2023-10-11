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

        // uwApiUtils::logfile("Local API : " . $_GET['mode']);

        if(!$_GET['mode'])
            $_GET['mode'] = 'next';

                    
        $facture = new Facture($this->db);
        $facture->initAsSpecimen();
        $facture->type=0;

        $societe = new societe($this->db);
        $societe->setMysoc($conf);

        $module = new mod_facture_synchrone($this->db);
        
        $res = $module->getNextValue($societe, $facture, $_GET['mode'], 'distant');

        uwApiUtils::logfile("Local API - send value : " . $res );
        return $res;

    } 
    
}
