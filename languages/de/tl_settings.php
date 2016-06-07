<?php

/**
 * Fields
 */
$GLOBALS['TL_LANG']['tl_settings']['ldap'][0] = 'LDAP aktivieren';
$GLOBALS['TL_LANG']['tl_settings']['ldap'][1] = 'Den Login über LDAP aktivieren.';

$GLOBALS['TL_LANG']['tl_settings']['ldap_host'][0] = 'Host';
$GLOBALS['TL_LANG']['tl_settings']['ldap_host'][1] = 'IP oder Hostnamen des LDAP-Server.';

$GLOBALS['TL_LANG']['tl_settings']['ldap_base'][0] = 'Base';
$GLOBALS['TL_LANG']['tl_settings']['ldap_base'][1] = 'Startpunkt (BaseDN) für die Suche innerhalb des LDAP-Trees festlegen.';

$GLOBALS['TL_LANG']['tl_settings']['ldap_port'][0] = 'Port';
$GLOBALS['TL_LANG']['tl_settings']['ldap_port'][1] = 'Den LDAP Port festlegen (Standard: 389).';

$GLOBALS['TL_LANG']['tl_settings']['ldap_filter_person'][0] = 'Suchfilter für Personen';
$GLOBALS['TL_LANG']['tl_settings']['ldap_filter_person'][1] = 'Verfeinern der Suche von Mitgliedern (Beispiel: "(&(objectClass=person)(objectClass=posixAccount))").';

$GLOBALS['TL_LANG']['tl_settings']['ldap_filter_group'][0] = 'Suchfilter für Gruppen';
$GLOBALS['TL_LANG']['tl_settings']['ldap_filter_group'][1] = 'Verfeinern der Suche von Gruppen (Beispiel: "(&(objectClass=group))").';

$GLOBALS['TL_LANG']['tl_settings']['ldap_uid'][0] = 'Uid';
$GLOBALS['TL_LANG']['tl_settings']['ldap_uid'][1] = 'Das Attribut des Loginnamen festlegen (Standard: uid).';

$GLOBALS['TL_LANG']['tl_settings']['ldap_method'][0] = 'Verschlüsselung';
$GLOBALS['TL_LANG']['tl_settings']['ldap_method'][1] = 'Legen Sie die Verbindungssicherheit fest.';

$GLOBALS['TL_LANG']['tl_settings']['ldap_binddn'][0] = 'Bind DN';
$GLOBALS['TL_LANG']['tl_settings']['ldap_binddn'][1] = 'Hinterlegen sie Anmeldung am Server, (Beispiel: CN=ldapadmin,CN=users,DC=sampledomain,DC=com).';

$GLOBALS['TL_LANG']['tl_settings']['ldap_password'][0] = 'Bind Passwort';
$GLOBALS['TL_LANG']['tl_settings']['ldap_password'][1] = 'Geben Sie das Passwort für den Bind DN an.';

$GLOBALS['TL_LANG']['tl_settings']['ldap_groups'][0] = 'Mitgliedergruppen';
$GLOBALS['TL_LANG']['tl_settings']['ldap_groups'][1] = 'Legen Sie fest, welche LDAP-Mitgliedergruppen verfügbar sein sollen.';

$GLOBALS['TL_LANG']['tl_settings']['ldap_uid_skip'][0] = 'Folgende Uid\'s ignorieren';
$GLOBALS['TL_LANG']['tl_settings']['ldap_uid_skip'][1] = 'Folgende LDAP Uids werden beim Import übergangen (mehrer durch Komma trennen).';

/**
 * References
 */
$GLOBALS['TL_LANG']['tl_settings']['ldap_legend'] = 'LDAP';