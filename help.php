<?php

/**
 * Media Rename Addon.
 *
 * @author tobias[at]daeschner[dot]de Tobias Daeschner
 *
 * @package redaxo5
 */

?>
<b>Media Rename Addon</b>

<br><br>
<strong>Addon zur Umbenennung von Dateinamen im Medienpool</strong>
<br><br>
Das AddOn hängt sich in den EP ```MEDIA_UPDATED``` ein und erstellt in der Detailansicht einer Datei ein zusätzliches Textfeld "gwünschter Dateiname", über das ein Umbenennen des Dateinamens ermöglicht wird.<br>
Wird eine Datei bearbeitet und es wird ein neuer Dateiname angegeben, versucht das AddOn, die Datei umzubenennen.
<br><br>
Sollte der Dateiname bereits existieren, wird die Umbenennung abgebrochen.
Existiert der Dateiname noch nicht, wird die Datei umbenannt und sämtliche Referenzen in der Datenbank entsprechend aktualisiert.
<br><br>
Fehler nicht ausgeschlossen - Nutzung auf eigene Gefahr ;o)