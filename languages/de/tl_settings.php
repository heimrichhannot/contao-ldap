<?php

$arrLang = &$GLOBALS['TL_LANG']['tl_settings'];

/**
 * Fields
 */
$arrLang['addLdapForMembers']   = ['LDAP für Mitglieder aktivieren', 'Aktivieren Sie hier den Login via Ldap für Mitglieder.'];
$arrLang['addLdapForUsers']     = ['LDAP für Benutzer aktivieren', 'Aktivieren Sie hier den Login via Ldap für Benutzer.'];
$arrLang['host']                = ['Host', 'IP oder Domain des LDAP-Servers'];
$arrLang['base']                = ['Base', 'Startpunkt (BaseDN) für die Suche innerhalb des LDAP-Baums'];
$arrLang['port']                = ['Port', 'Standard: 389.'];
$arrLang['personFilter'][0]     = 'Suchfilter für Personen';
$arrLang['personFilter'][1]     =
    'Hinweis: Bitte filtern Sie so, dass das Benutzername-Feld nur einmal vorkommt bzw. das erste Vorkommen das relevante ist (Beispiel: "(&(objectClass=person)(objectClass=posixAccount)(mail=*@domain.com))")';
$arrLang['personFieldMapping']  = [
    'Abbildung von LDAP- auf Contao-Felder (Benutzername ist bereits oben festgelegt)',
    'Legen Sie hier fest, wie bestehende LDAP-Personenfelder auf Contao-Felder abgebildet werden (Beispiel: givenname -> firstname).'
];
$arrLang['ldapField']           = ['LDAP-Feld(er)', 'Einzelfeldname oder Muster (z. B. "%field% %other_field%")'];
$arrLang['contaoField']         = ['Contao-Feld', ''];
$arrLang['defaultPersonValues'] = [
    'Standardwerte',
    'Legen Sie hier Standardwerte fest, die neuen personen zugewiesen werden sollen.'
];
$arrLang['field']               = ['Feld', ''];
$arrLang['defaultValue']        = ['Wert', ''];
$arrLang['groupFilter']         = ['Suchfilter für Gruppen', 'Beispiel: "(&(objectClass=group))".'];
$arrLang['ldapUsernameField']   = ['LDAP-Benutzernamefeld', 'Name des Attributs, das als Benutzername verwendet wird (Standard: uid).'];
$arrLang['authMethod']          = ['Verschlüsselung', 'Legen Sie hier die Art der Verschlüsselung fest.'];
$arrLang['binddn']              = [
    'Bind DN',
    'Suchfilter für den Benutzer, der für die Anmeldung am Server genutzt werden soll (Beispiel: CN=ldapadmin,CN=users,DC=sampledomain,DC=com).'
];
$arrLang['password']            = ['Bind DN Passwort', 'Geben Sie das Passwort für den Bind DN-Benutzer an.'];
$arrLang['groups']              = ['Zu importierende Gruppen', 'Legen Sie fest, welche LDAP-Mitgliedergruppen verfügbar sein sollen.'];
$arrLang['skipLdapUsernames']   = [
    'Folgende LDAP-Benutzernamen ignorieren',
    'Ignorieren Sie eine oder mehrere Benutzer bestimmter Benutzernamen (mehrere durch Komma trennen). Grundlage ist das Feld mit dem Namen, der in LDAP-Benutzernamen eingegeben wurde.'
];

/**
 * References
 */
$GLOBALS['TL_LANG']['tl_settings']['ldap_legend'] = 'LDAP';