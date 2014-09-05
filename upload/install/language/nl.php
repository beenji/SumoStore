<?php
// SumoStore Installation Language File
$language = array();

// Main
$language['INSTALL_TITLE']              = 'Installatie';
$language['STEP_1_SHORT_TITLE']         = 'Licentie';
$language['STEP_2_SHORT_TITLE']         = 'Checks';
$language['STEP_3_SHORT_TITLE']         = 'Configuratie';
$language['STEP_4_SHORT_TITLE']         = 'Voltooing';
$language['PREVIOUS_STEP']              = 'Vorige stap';
$language['NEXT_STEP']                  = 'Volgende stap';
$language['PHP_EXTENSIONS']             = 'PHP extensies';
$language['PHP_SETTINGS']               = 'PHP instellingen';
$language['PHP_VERSION']                = 'PHP versie';
$language['CURRENT']                    = 'Huidig';
$language['REQUIRED']                   = 'Benodigd';
$language['STATUS']                     = 'Status';
$language['OFF']                        = 'Uit';
$language['ON']                         = 'Aan';
$language['FILE']                       = 'Bestand';
$language['FILES']                      = 'Bestanden';
$language['DIRECTORY']                  = 'Map';
$language['DIRECTORIES']                = 'Mappen';
$language['MIA']                        = 'Niet gevonden!';
$language['FNW']                        = 'Onbeschrijfbaar';
$language['HOSTNAME']                   = 'Hostnaam';
$language['USERNAME']                   = 'Gebruikersnaam';
$language['PASSWORD']                   = 'Wachtwoord';
$language['DATABASE']                   = 'Database naam';
$language['PREFIX']                     = 'Database prefix';
$language['LICENSE_KEY']                = 'Licentie sleutel';
$language['LICENSE_INFO']               = 'U kunt de licentie sleutel downloaden op <a href="http://www.sumostore.nl/" target="_blank">www.sumostore.net</a><br />De licentiecode is nodig om bepaalde apps te kunnen gebruiken.';
$language['STORE_NAME']                 = 'Winkel naam';
$language['STORE_MAIL']                 = 'Winkel email adres';
$language['COUNTRY']                    = 'Land';
$language['ZONE']                       = 'Provincie (zone)';
$language['ZONE_VIA_ADMIN']             = 'Voer ik later in via de administratie';
$language['CATEGORY']                   = 'Winkel branche';

// Step 1
$language['STEP_1_TITLE']               = 'Stap 1 - Licentie overeenkomst';
$language['STEP_1_WARNING']             = 'U dient akkoord te gaan met de voorwaarden voor u gebruik kunt maken van SumoStore!';
$language['STEP_1_AGREE_TITLE']         = 'SumoStore voorwaarden';
$language['STEP_1_AGREE_CONTENT']       = 'U kunt de SumoStore voorwaarden bekijken (en eventueel in PDF downloaden) via onze website: <a href="http://sumostore.net/legal/algemene-voorwaarden/">http://sumostore.net/legal/algemene-voorwaarden/</a>';
$language['STEP_1_AGREE_CHECKBOX']      = 'Ik ga akkoord met de licentieovereenkomst en de algemene voorwaarden van SumoStore';

// Step 2
$language['STEP_2_TITLE']               = 'Stap 2 - Checks';
$language['STEP_2_INFO']                = 'Onderdelen 1 en 2 worden (in de meeste gevallen) door de systeembeheerder (webhoster) ingesteld. Als onderdelen 3 en/of 4 fouten veroorzaken kunt u deze zelf oplossen door de juiste schrijfrechten in te stellen. <a href="http://www.sumostore.nl/ondersteuning/kennisbank/installatie-vragen">Kijk voor meer informatie in de kennisbank.</a>';
$language['STEP_2_PART_1']              = '1. Dit zijn de vereiste instellingen om SumoStore te installeren. Controleer deze instellingen.';
$language['STEP_2_PART_2']              = '2. Dit zijn de PHP extensies die vereist zijn om SumoStore te installeren. Controleer of deze allemaal zijn geinstalleerd.';
$language['STEP_2_PART_3']              = '3. Dit zijn de bestanden die schrijfrechten nodig hebben om SumoStore te installeren.';
$language['STEP_2_PART_4']              = '4. Dit zijn de mappen die schrijfrechten nodig hebben om SumoStore te installeren.';

// Step 3
$language['STEP_3_TITLE']               = 'Stap 3 - Configuratie';
$language['STEP_3_PART_1']              = '1. Geef uw database gegevens op.';
$language['STEP_3_PART_2']              = '2. Geef uw administratie gegevens op.';
$language['STEP_3_PART_3']              = '3. Geef uw webwinkel gegevens op.';

// Step 3: validation translations
$language['ERROR_DB_HOSTNAME']          = 'U moet de hostnaam van de MySQL server opgeven.';
$language['ERROR_DB_USERNAME']          = 'U moet een database gebruikersnaam opgeven.';
$language['ERROR_DB_PREFIX']            = 'De database prefix mag niet leeg zijn!';
$language['ERROR_DB_DBNAME']            = 'De database naam kan niet leeg zijn.';
$language['ERROR_DB_CONNECTION']        = 'Er kon geen verbinding worden gemaakt met de MySQL server. Controleer de hostnaam, gebruikersnaam en wachtwoord en probeer opnieuw.';
$language['ERROR_DB_DBSELECT']          = 'De opgegeven database kon niet worden gekozen.';
$language['ERROR_USERNAME_EMPTY']       = 'U moet een gebruikersnaam opgeven voor het administrator account.';
$language['ERROR_USERNAME_WEAK']        = 'De gekozen gebruikersnaam is erg zwak en kan erg makkelijk worden geraden door hackers. U dient een andere gebruikersnaam op te geven.';
$language['ERROR_PASSWORD_EMPTY']       = 'U moet een wachtwoord opgeven voor het administrator account.';
$language['ERROR_PASSWORD_WEAK']        = 'Het gekozen wachtwoord is te makkelijk te raden door hackers. U dient een ander wachtwoord op te geven.';
$language['ERROR_EMAIL_INVALID']        = 'Het email adres voor het administrator account lijkt ongeldig te zijn.';
$language['ERROR_CATEGORY']             = 'U dient een branche op te geven voor uw webwinkel.';
$language['ERROR_COUNTRY']              = 'In welk land bevind de webwinkel zich?';
$language['ERROR_STORE_NAME']           = 'Wat is de naam van uw webwinkel?';
$language['ERROR_STORE_MAIL']           = 'Wat is het email adres van uw webwinkel?';
$language['ERROR_LICENSE_INVALID']      = 'De licentie sleutel is ongeldig.';

// Step 4
$language['STEP_4_TITLE']               = 'Stap 4 - Voltooing';
$language['STEP_4_INSTALL_DIR']         = 'Vergeet niet om direct de map "install" te verwijderen!';
$language['STEP_4_SUCCESS']             = 'Gefeliciteerd! U kunt nu gebruik maken van SumoStore! We wensen u veel plezier met het opbouwen van uw webwinkel. U kunt nu verder gaan met het doorvoeren van de benodigde instellingen via de administratie.';
$language['STEP_4_BACKEND']             = 'Naar de administratie';

// Footer
$language['FOOTER_HOME_LINK']           = 'http://www.sumostore.nl/';
$language['FOOTER_HOME_NAME']           = 'Project home';
$language['FOOTER_DOCS_LINK']           = 'http://www.sumostore.nl/ondersteuning/kennisbank/';
$language['FOOTER_DOCS_NAME']           = 'Kennisbank';
$language['FOOTER_COMS_LINK']           = 'http://community.sumostore.net/';
$language['FOOTER_COMS_NAME']           = 'Support forum';
