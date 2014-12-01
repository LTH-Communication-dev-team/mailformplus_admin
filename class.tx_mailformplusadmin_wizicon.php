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
                        'icon' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath('examples') . 'Resources/Public/Images/PiErrorWizard.png',
                        'title' => 'Formhandler Easy setup',
                        'description' => 'Formhandler Easy setup',
                        'params' => '&defVals[tt_content][CType]=list&&defVals[tt_content][list_type]=mailformplusadmin'
                );

                return $wizardItems;
        }
}