<?php

class rex_mediapool_rename
{

    /* 
    called by extension point MEDIA_FORM_EDIT:
    */

    public static function deleteMetaInfo(rex_extension_point $ep)
    {

        /* 
        run through all "rename" meta-info-fields and delete content from non empty fields:
        */

    	$newPost = rex_sql::factory();
		$newPost->setTable(rex::getTablePrefix().'media');
		$newPost->setValue('med_mediapool_rename','');

		try {
		  $newPost->update();
		} catch (rex_sql_exception $e) {
		  echo rex_view::warning($e->getMessage());
		}

    }

    /* 
    called by extension point MEDIA_UPDATED:
    */

    public static function processUpdatedMedia(rex_extension_point $ep)
    {

        if($data = static::getDataByFilename($ep->getParam('filename')))
        {
            $qry = "SELECT * FROM `" . rex::getTablePrefix() . "media` WHERE `filename` = '" . $ep->getParam('filename') . "'";
            $sql = rex_sql::factory();
            $sql->setQuery($qry);
            if($result = $sql->getArray())
            {
                $result = $result[0];

                // ----- split filename into name and extension
                $NFILENAME = $result['filename'];
				if (strrpos($NFILENAME, '.') != '') {
				    $NFILE_NAME = substr($NFILENAME, 0, strlen($NFILENAME) - (strlen($NFILENAME) - strrpos($NFILENAME, '.')));
				    $NFILE_EXT = substr($NFILENAME, strrpos($NFILENAME, '.'), strlen($NFILENAME) - strrpos($NFILENAME, '.'));
				} else {
				    $NFILE_NAME = $NFILENAME;
				    $NFILE_EXT = '';
				}

                // ----- new filename
                $rename = $result['med_mediapool_rename'];
		        $rename = rex_string::normalize($rename, '_', '.-');

		        // ----- delete meta info contents
		        /* $sqly = "UPDATE `" . rex::getTablePrefix() . "media` SET `med_mediapool_rename` = '' WHERE `filename` = '" . $ep->getParam('filename') . "'";
		        $sqly = rex_sql::factory();
		        $sqly->setQuery($sqly); 
		        <-- moved to MEDIA_FORM_EDIT extension point method -->
		        */

		        // ----- check if sanitized filename is empty
		        if($rename != ''){

	                $oldFile = $NFILENAME;
			        $newFile = $rename.$NFILE_EXT;

			    	$newPost = rex_sql::factory();
					$newPost->setTable(rex::getTablePrefix().'media'); // rex_foo_bar
					$newPost->setValue('med_mediapool_rename','');

					try {
					  $newPost->update();
					} catch (rex_sql_exception $e) {
					  echo rex_view::warning($e->getMessage());
					}

			        // ----- check if new filename exists already
		            $qry = "SELECT * FROM `" . rex::getTablePrefix() . "media` WHERE `filename` = '" . $newFile . "'";
		            $sql = rex_sql::factory();
		            $sql->setQuery($qry);
		            if($result = $sql->getArray())
		            {
		            	$error = "FEHLER: Der Dateiname existiert bereits. Bitte wählen Sie einen anderen Dateinamen.";
		            	echo rex_view::error($error);
		            	return;
		            }


			        // ----- rename file in media folder
			        rename(rex_path::media().$oldFile, rex_path::media().$newFile);

			        // ----- iterate through all tables in db
			        $qry = "SHOW TABLES";
			        $sql = rex_sql::factory();
			        $sql->setQuery($qry);

			        if($result = $sql->getArray())
			        {
			        	for($i=0;$i<count($result);$i++){
				        	$table = current($result[$i]);

			        		// ----- iterate through all fields in table
					        $qry2 = "SHOW FIELDS FROM ".$table.";";
					        $sql2 = rex_sql::factory();
					        $sql2->setQuery($qry2);

					        if($result2 = $sql2->getArray())
					        {
						        for($j=0;$j<count($result2);$j++){
						        	$field = current($result2[$j]);

						        	// ----- update single fields where string matches
						        	//$update = "UPDATE ".$table." SET `".$field."` = REPLACE(`".$field."`, '".$oldFile."', '".$newFile."');";
						        	//$update = "UPDATE ".$table." SET `".$field."` = REPLACE(`".$field."`, '".$oldFile."', '".$newFile."');";
						        	//$update = "UPDATE ".$table." SET `".$field."` = trim(REPLACE(concat(' ',`".$field."`,' '),' ".$oldFile." ',' ".$newFile." '));";
						        	// ----- took a while to find the finally fitting query ;o)

								// FIX 2024-10-17 REXEXP mit [[:<:]] doesnt work in Mysql 8
								// old:
								//$update = "UPDATE ".$table." SET `".$field."` = CASE WHEN `".$field."` REGEXP '[[:<:]]".$oldFile."[[:>:]]' THEN REPLACE(`".$field."`,'".$oldFile."','".$newFile."') END WHERE `".$field."` REGEXP '[[:<:]]".$oldFile."[[:>:]]';";
						        	//$updateSql = rex_sql::factory();
						        	//$updateSql->setQuery($update);
								// fixed:
								$update = "UPDATE ".$table."
										SET `".$field."` = CASE 
										 WHEN CONVERT(`".$field."` USING utf8mb4) REGEXP '\\\\b".$oldFile."\\\\b' 
										 THEN REPLACE(`".$field."`, '".$oldFile."', '".$newFile."') 
									    END 
									    WHERE CONVERT(`".$field."` USING utf8mb4) REGEXP '\\\\b".$oldFile."\\\\b';";
							    	$updateSql = rex_sql::factory();
							    	// update mit try/catch
							    	try {
									$updateSql->setQuery($update);
							    	} catch (rex_sql_exception $e) {
									echo rex_view::warning($e->getMessage());
							    	}

						        }
						    }
					    }
					    // ----- delete cache for correctly referencing the new file name on all pages
					    rex_delete_cache();
			        }
			    }
                unset($field);

            }
            unset($result, $qry, $sql, $qry2, $updateSql);
        }
        unset($data);
    }

    /* 
    method for fetching file data:
    */

    public static function getDataByFilename($filename)
    {
        if($media = rex_media::get($filename))
        {
            if($media->fileExists())
            {
                return $media;
            }
        }

        return null;
    }

}
