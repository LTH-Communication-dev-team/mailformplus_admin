<?php
class tx_mailformplusadmin_wizicon {

        /**
         * Processing the wizard items array
         *
         * @param array $wizardItems The wizard items
         * @return array Modified array with wizard items
         */
        function proc($wizardItems)     {
                $wizardItems['plugins_tx_mailformplusadmin'] = array(
                        'icon' => t3lib_extMgm::extRelPath('mailformplus_admin') . 'pi2/ce_wiz.gif',
                        'title' => 'Formhandler Easy setup',
                        'description' => 'Formhandler Easy setup',
                        'params' => '&defVals[tt_content][CType]=list&defVals[tt_content][list_type]=mailformplus_admin_pi2'
                );

                return $wizardItems;
        }
}