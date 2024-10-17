mediapool_rename
=======================

Das AddOn hängt sich in den EP ```MEDIA_UPDATED``` ein. Wird eine Datei bearbeitet und es wird ein neuer Dateiname angegeben, versucht das AddOn, die Datei umzubenennen.

Sollte der Dateiname bereits existieren, wird die Umbenennung abgebrochen.
Existiert der Dateiname noch nicht, wird die Datei umbenannt und sämtliche Referenzen in der Datenbank entsprechend aktualisiert.